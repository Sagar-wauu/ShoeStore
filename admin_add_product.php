<?php
include 'dp.php';
include 'auth.php';

require_admin();

// Ensure products table has expected columns (fix for missing columns in older DBs)
$expected_columns = ['description'];
foreach($expected_columns as $col) {
    $res = $conn->query("SHOW COLUMNS FROM products LIKE '" . $conn->real_escape_string($col) . "'");
    if(!$res || $res->num_rows === 0) {
        // Add missing column(s) safely
        if($col === 'description') {
            $conn->query("ALTER TABLE products ADD COLUMN description TEXT NULL");
        }
    }
}

$product_id = $_GET['id'] ?? null;
$product = null;
$err = '';
$success = '';

if($product_id) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    
    if(!$product) {
        header('Location: admin_products.php');
        exit;
    }
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    $category = trim($_POST['category'] ?? '');
    $brand = trim($_POST['brand'] ?? '');
    $color = trim($_POST['color'] ?? '');
    $size = trim($_POST['size'] ?? '');
    $image = $product['image'] ?? '';
    
    // Validation
    if(!$name) {
        $err = '❌ Product name is required';
    } elseif(!$description) {
        $err = '❌ Description is required';
    } elseif($price < 0) {
        $err = '❌ Price cannot be negative';
    } elseif($price == 0) {
        $err = '❌ Price must be greater than 0';
    } elseif($quantity < 0) {
        $err = '❌ Quantity cannot be negative';
    } elseif(!$category) {
        $err = '❌ Category is required';
    } elseif(!$brand) {
        $err = '❌ Brand is required';
    } elseif(!$color) {
        $err = '❌ Color is required';
    } elseif(!$size) {
        $err = '❌ Size is required';
    } elseif(!isset($_FILES['image']) || $_FILES['image']['error'] === 4) {
        if(!$product_id) {
            $err = '❌ Product image is required for new products';
        }
    } else {
        // Handle file upload
        if($_FILES['image']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $tmp_name = $_FILES['image']['tmp_name'];
            $size_file = $_FILES['image']['size'];
            
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if(!in_array($ext, $allowed)) {
                $err = '❌ Only JPG, PNG, and GIF images are allowed';
            } elseif($size_file > 2 * 1024 * 1024) {
                $err = '❌ Image must be less than 2MB';
            } else {
                $new_filename = 'product_' . time() . '.' . $ext;
                if(move_uploaded_file($tmp_name, 'Product images/' . $new_filename)) {
                    $image = $new_filename;
                } else {
                    $err = '❌ Error uploading image';
                }
            }
        }
        
        if(!$err) {
            if($product_id) {
                // Update
                $stmt = $conn->prepare("
                    UPDATE products 
                    SET name=?, description=?, price=?, quantity=?, category=?, brand=?, color=?, size=?, image=?
                    WHERE id=?
                ");
                // types: name(s), description(s), price(d), quantity(i), category(s), brand(s), color(s), size(s), image(s), id(i)
                $stmt->bind_param('ssdisssssi', $name, $description, $price, $quantity, $category, $brand, $color, $size, $image, $product_id);

                if($stmt->execute()) {
                    $success = '✓ Product updated successfully!';
                    // Refresh product data so the form shows updated values
                    $stmt2 = $conn->prepare("SELECT * FROM products WHERE id=?");
                    $stmt2->bind_param('i', $product_id);
                    $stmt2->execute();
                    $res2 = $stmt2->get_result();
                    $product = $res2->fetch_assoc();
                    $stmt2->close();
                } else {
                    $err = '❌ Error updating product: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                // Insert
                $stmt = $conn->prepare("
                    INSERT INTO products (name, description, price, quantity, category, brand, color, size, image)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                // types: name(s), description(s), price(d), quantity(i), category(s), brand(s), color(s), size(s), image(s)
                $stmt->bind_param('ssdisssss', $name, $description, $price, $quantity, $category, $brand, $color, $size, $image);

                if($stmt->execute()) {
                    $success = '✓ Product added successfully!';
                    $_POST = [];
                } else {
                    $err = '❌ Error adding product: ' . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $product_id ? 'Edit' : 'Add'; ?> Product - ShoeStore Admin</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .admin-container { max-width:800px; margin:0 auto; padding:20px; }
        .admin-nav { background:#1976d2; padding:15px; margin:-20px -20px 20px -20px; }
        .admin-nav a { color:white; text-decoration:none; margin-right:20px; display:inline-block; }
        .admin-nav a:hover { text-decoration:underline; }
        .form-card { background:white; padding:20px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
        .form-group { margin-bottom:20px; }
        label { display:block; margin-bottom:5px; font-weight:bold; }
        input, textarea, select { width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; font-family:inherit; }
        textarea { resize:vertical; min-height:120px; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
        @media (max-width:600px) {
            .form-row { grid-template-columns:1fr; }
        }
        .btn { display:inline-block; padding:10px 20px; background:#1976d2; color:white; text-decoration:none; border-radius:4px; border:none; cursor:pointer; }
        .btn:hover { background:#1565c0; }
        .btn-secondary { background:#666; }
        .btn-secondary:hover { background:#555; }
        .message { padding:10px; border-radius:4px; margin-bottom:20px; }
        .message.success { background:#d4edda; color:#155724; }
        .message.error { background:#f8d7da; color:#721c24; }
        .image-preview { max-width:200px; margin-top:10px; }
        .price-error { color:#d32f2f; font-size:12px; margin-top:5px; display:none; }
    </style>
    <script>
        function validatePrice() {
            const priceInput = document.querySelector('input[name="price"]');
            const errorMsg = document.getElementById('priceError');
            const price = parseFloat(priceInput.value);
            
            if(price < 0) {
                errorMsg.style.display = 'block';
                priceInput.style.borderColor = '#d32f2f';
            } else {
                errorMsg.style.display = 'none';
                priceInput.style.borderColor = '#ddd';
            }
        }
    </script>
    </style>
</head>
<body>


<!-- Admin Navigation -->
<nav class="navbar">
    <div class="logo">🛡️ Admin Panel</div>
    <ul class="nav-links" id="navLinks">
        <li><a href="admin_dashboard.php">Dashboard</a></li>
        <li><a href="admin_products.php">Products</a></li>
        <li><a href="admin_orders.php">Orders</a></li>
        <li><a href="admin_users.php">Users</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
    <div class="menu-icon" onclick="toggleMenu()">☰</div>
</nav>

<div class="admin-container">
    <h1><?php echo $product_id ? 'Edit Product' : 'Add New Product'; ?></h1>
    
    <?php if($err): ?>
        <div class="message error"><?php echo $err; ?></div>
    <?php endif; ?>
    
    <?php if($success): ?>
        <div class="message success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <div class="form-card">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Product Name *</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($product['name'] ?? $_POST['name'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Description *</label>
                <textarea name="description" required><?php echo htmlspecialchars($product['description'] ?? $_POST['description'] ?? ''); ?></textarea>
                <small>Include features, materials, size guide, care instructions, etc.</small>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Price (Rs.) *</label>
                    <input type="number" name="price" step="0.01" value="<?php echo $product['price'] ?? $_POST['price'] ?? ''; ?>" required oninput="validatePrice()">
                    <div id="priceError" class="price-error">❌ Price cannot be negative</div>
                </div>
                
                <div class="form-group">
                    <label>Stock Quantity *</label>
                    <input type="number" name="quantity" value="<?php echo $product['quantity'] ?? $_POST['quantity'] ?? 0; ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Category *</label>
                    <select name="category" required>
                        <option value="">-- Select Category --</option>
                        <?php $catVal = $product['category'] ?? $_POST['category'] ?? ''; ?>
                        <option value="Male" <?php echo (strcasecmp($catVal, 'Male') === 0) ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo (strcasecmp($catVal, 'Female') === 0) ? 'selected' : ''; ?>>Female</option>
                        <option value="Kid" <?php echo (strcasecmp($catVal, 'Kid') === 0) ? 'selected' : ''; ?>>Kid</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Brand *</label>
                    <input type="text" name="brand" value="<?php echo htmlspecialchars($product['brand'] ?? $_POST['brand'] ?? ''); ?>" placeholder="e.g., Nike, Adidas" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Color *</label>
                    <input type="text" name="color" required value="<?php echo htmlspecialchars($product['color'] ?? $_POST['color'] ?? ''); ?>" placeholder="e.g., Black, White">
                </div>

                <div class="form-group">
                    <label>Size *</label>
                    <input type="text" name="size" required value="<?php echo htmlspecialchars($product['size'] ?? $_POST['size'] ?? ''); ?>" placeholder="e.g., 7, 8, 9">
                </div>
            </div>
            
            <div class="form-group">
                <label>Product Image <?php echo !$product_id ? '*' : '(Optional to update)'; ?></label>
                <input type="file" name="image" accept="image/*">
                <?php if($product && $product['image']): ?>
                    <div style="margin-top:10px;">
                        <img src="Product images/<?php echo htmlspecialchars($product['image']); ?>" alt="Current Image" class="image-preview">
                        <p><small>Current image</small></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn"><?php echo $product_id ? 'Update Product' : 'Add Product'; ?></button>
                <a href="admin_products.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
