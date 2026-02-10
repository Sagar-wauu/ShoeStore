
<?php
// admin_edit_product.php - Admin can edit product details and image
include 'dp.php';
include 'auth.php';
require_admin();

// Get product ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: admin_products.php');
    exit();
}

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id=? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    echo "<p>Product not found.</p>";
    exit();
}

$err = '';
$success = '';
$image_path = $product['image'] ? 'Product images/' . htmlspecialchars($product['image']) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $brand = trim($_POST['brand'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    $desc = trim($_POST['description'] ?? '');
    $new_image = $product['image'];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $img_name = basename($_FILES['image']['name']);
        $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($img_ext, $allowed)) {
            $target = 'Product images/' . uniqid('prod_', true) . '.' . $img_ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $new_image = basename($target);
                $image_path = $target;
            } else {
                $err = 'Failed to upload image.';
            }
        } else {
            $err = 'Invalid image format. Allowed: jpg, jpeg, png, gif.';
        }
    }

    if ($err === '' && ($name === '' || $brand === '' || $category === '' || $price <= 0 || $quantity < 0)) {
        $err = 'All fields are required and must be valid.';
    }

    if ($err === '') {
        $stmt = $conn->prepare("UPDATE products SET name=?, brand=?, category=?, price=?, quantity=?, description=?, image=? WHERE id=?");
        $stmt->bind_param('sssdissi', $name, $brand, $category, $price, $quantity, $desc, $new_image, $id);
        if ($stmt->execute()) {
            $success = 'Product updated successfully!';
            // Refresh product data
            $product = array_merge($product, [
                'name' => $name,
                'brand' => $brand,
                'category' => $category,
                'price' => $price,
                'quantity' => $quantity,
                'description' => $desc,
                'image' => $new_image
            ]);
        } else {
            $err = 'Failed to update product.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Admin</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body { background: #f5f5f5; }
        .form-box { max-width: 500px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.08); }
        .form-box h2 { text-align: center; margin-bottom: 20px; color: #1976d2; }
        .form-box input, .form-box textarea { width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc; font-size: 15px; }
        .form-box button { width: 100%; padding: 12px; background: #1976d2; color: #fff; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; transition: background 0.2s; }
        .form-box button:hover { background: #1565c0; }
        .form-box .error { color: #d32f2f; background: #ffebee; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .form-box .success { color: #388e3c; background: #e8f5e9; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .form-box img { display: block; margin: 0 auto 15px auto; max-width: 180px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .form-box label { font-weight: bold; color: #333; }
    </style>
</head>
<body>
<div class="form-box">
    <h2>Edit Product</h2>
    <?php if($err) echo "<div class='error'>$err</div>"; ?>
    <?php if($success) echo "<div class='success'>$success</div>"; ?>
    <form method="POST" enctype="multipart/form-data">
        <label>Product Name</label>
        <input name="name" value="<?php echo htmlspecialchars($product['name']); ?>" placeholder="Product Name" required>
        <label>Brand</label>
        <input name="brand" value="<?php echo htmlspecialchars($product['brand']); ?>" placeholder="Brand" required>
        <label>Category</label>
        <input name="category" value="<?php echo htmlspecialchars($product['category']); ?>" placeholder="Category" required>
        <label>Price (Rs.)</label>
        <input name="price" type="number" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" placeholder="Price" required>
        <label>Quantity</label>
        <input name="quantity" type="number" value="<?php echo htmlspecialchars($product['quantity']); ?>" placeholder="Quantity" required>
        <label>Description</label>
        <textarea name="description" placeholder="Description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
        <label>Product Image</label>
        <?php if($image_path): ?>
            <img src="<?php echo $image_path; ?>" alt="Current Image">
        <?php endif; ?>
        <input type="file" name="image" accept="image/*">
        <button type="submit">Update Product</button>
    </form>
    <p><a href="admin_products.php">&larr; Back to Products</a></p>
</div>
</body>
</html>
