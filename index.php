<?php 
include 'dp.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Helper: check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}
// include 'auth.php';
// Helper: check if user is admin
function is_admin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shoe Store - Home</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .hero { background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:white; padding:60px 20px; text-align:center; }
        .hero h1 { margin:0; font-size:48px; }
        .hero p { font-size:20px; margin:10px 0 20px 0; }
        .hero .btn { display:inline-block; padding:12px 30px; background:white; color:#667eea; text-decoration:none; border-radius:4px; font-weight:bold; }
        .hero .btn:hover { transform:scale(1.05); }
        .featured { max-width:1200px; margin:40px auto; padding:20px; }
        .featured h2 { text-align:center; font-size:32px; margin-bottom:30px; }
        .featured-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(220px, 1fr)); gap:20px; }
        .card { background:white; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); overflow:hidden; transition:transform 0.2s; }
        .card:hover { transform:translateY(-5px); }
        .card img { width:100%; height:200px; object-fit:cover; }
        .card-body { padding:15px; }
        .card h3 { margin:0 0 10px 0; }
        .card .price { font-size:18px; font-weight:bold; color:#d32f2f; }
        .card .btn { display:inline-block; margin-top:10px; padding:8px 15px; background:#1976d2; color:white; text-decoration:none; border-radius:4px; }
        .card .btn:hover { background:#1565c0; }
        .features { background:#f5f5f5; padding:40px 20px; }
        .features-grid { max-width:1200px; margin:0 auto; display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:20px; }
        .feature { text-align:center; padding:20px; }
        .feature h3 { color:#1976d2; }
        .footer { background:#222; color:white; padding:40px 20px; text-align:center; }
        .footer a { color:#667eea; text-decoration:none; }
        .footer a:hover { text-decoration:underline; }
        
        /* Search Bar Styles */
        .search-container { position:relative; margin:0 20px; }
        .search-input { padding:8px 35px 8px 15px; border-radius:20px; border:none; width:200px; outline:none; background:rgba(255,255,255,0.9); font-size:14px; transition:width 0.3s; color:#333; }
        .search-input:focus { width:250px; background:white; box-shadow:0 0 10px rgba(0,0,0,0.1); }
        .search-suggestions { position:absolute; top:110%; left:0; width:100%; background:white; border-radius:8px; z-index:1000; display:none; box-shadow:0 4px 15px rgba(0,0,0,0.1); overflow:hidden; }
        .suggestion-item { padding:12px 15px; cursor:pointer; border-bottom:1px solid #eee; text-align:left; color:#333; font-size:14px; transition:background 0.2s; }
        .suggestion-item:last-child { border-bottom:none; }
        .suggestion-item:hover { background-color:#f0f7ff; color:#1976d2; font-weight:bold; }
        .search-icon { position:absolute; right:10px; top:50%; transform:translateY(-50%); color:#666; pointer-events:none; }
    </style>
</head>

<body>

<!-- Navigation -->
<nav class="navbar">
    <div class="logo">🛒 ShoeStore
        <?php 
        if(is_logged_in() && !is_admin()) {
            // Fetch user name from database
            $user_id = $_SESSION['user_id'];
            $user_name = '';
            $stmt = $conn->prepare("SELECT name FROM users WHERE id=?");
            $stmt->bind_param('i', $user_id);
            if($stmt->execute()) {
                $stmt->bind_result($user_name);
                $stmt->fetch();
            }
            $stmt->close();
            if($user_name) {
                echo '<span style="font-size:16px; margin-left:15px; color:#1976d2;">Welcome ' . htmlspecialchars($user_name) . '</span>';
            }
        }
        ?>
    </div>
    
    <div class="search-container">
        <input type="text" id="brandSearch" class="search-input" placeholder="Search brand e.g. Nike, Adidas..." autocomplete="off">
        <span class="search-icon">🔍</span>
        <div id="suggestions" class="search-suggestions"></div>
    </div>

    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="cart.php">Cart 🛒</a></li>
        <?php if(is_logged_in()): ?>
            <?php if(is_admin()): ?>
                <li><a href="admin_dashboard.php">👨‍💼 Admin</a></li>
            <?php endif; ?>
            <li><a href="profile.php">👤 Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php endif; ?>
    </ul>
    <div class="menu-icon" onclick="toggleMenu()">☰</div>
</nav>

<!-- Hero Section -->
<header class="hero">
    <h1>🏃 Welcome to ShoeStore</h1>
    <p>Premium shoes for every occasion</p>
    <a href="products.php" class="btn">Shop Now →</a>
</header>

<!-- Featured Products -->
<section class="featured">
    <h2>⭐ Featured Products</h2>
    <div class="featured-grid">
        <?php
        $query = "SELECT * FROM products LIMIT 8";
        $result = $conn->query($query);
        
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "
                <div class='card'>
                    <img src='Product images/".htmlspecialchars($row['image'])."' alt='Product'>
                    <div class='card-body'>
                        <h3>".htmlspecialchars($row['name'])."</h3>
                        <p style='color:#666; margin:5px 0;'>".htmlspecialchars($row['brand'])."</p>
                        <p class='price'>Rs. ".number_format($row['price'], 2)."</p>
                        <a href='product_details.php?id=".$row['id']."' class='btn'>View Details</a>
                    </div>
                </div>
                ";
            }
        } else {
            echo "<p>No products available. Admin needs to add products.</p>";
        }
        ?>
    </div>
</section>

<!-- Features Section -->
<section class="features">
    <div class="features-grid">
        <div class="feature">
            <h3>🚚 Fast Shipping</h3>
            <p>Delivery within 3-5 business days</p>
        </div>
        <div class="feature">
            <h3>✅ Authentic Products</h3>
            <p>100% genuine branded shoes</p>
        </div>
        <div class="feature">
            <h3>💳 Secure Payment</h3>
            <p>Safe payment with eSewa</p>
        </div>
        <div class="feature">
            <h3>🔄 Easy Returns</h3>
            <p>30-day return guarantee</p>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <p>&copy; 2026 Sagar and Dikshyant ShoeStore. All rights reserved.</p>
    <p>
        <a href="#">About Us</a> | 
        <a href="#">Contact</a> | 
        <a href="#">Privacy Policy</a> | 
        <a href="#">Terms & Conditions</a>
    </p>
    <p>Follow us on 
        <a href="#">Facebook</a> | 
        <a href="#">Instagram</a> | 
        <a href="#">Twitter</a>
    </p>
</footer>

<script src="script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('brandSearch');
    const suggestionsBox = document.getElementById('suggestions');

    searchInput.addEventListener('keyup', function(e) {
        // Handle Enter key
        if(e.key === 'Enter') {
            const term = this.value.trim();
            if(term) {
                window.location.href = 'products.php?brand=' + encodeURIComponent(term);
                return;
            }
        }

        const term = this.value.trim();
        
        if(term.length < 1) {
            suggestionsBox.style.display = 'none';
            return;
        }

        fetch('search_suggestions.php?term=' + encodeURIComponent(term))
            .then(response => response.json())
            .then(data => {
                if(data.length > 0) {
                    let html = '';
                    data.forEach(brand => {
                        html += `<div class="suggestion-item" onclick="selectBrand('${brand}')">${brand}</div>`;
                    });
                    suggestionsBox.innerHTML = html;
                    suggestionsBox.style.display = 'block';
                } else {
                    suggestionsBox.style.display = 'none';
                }
            })
            .catch(err => console.error('Error fetching suggestions:', err));
    });

    // Handle Search Icon Click
    document.querySelector('.search-icon').addEventListener('click', function() {
        const term = searchInput.value.trim();
        if(term) {
             window.location.href = 'products.php?brand=' + encodeURIComponent(term);
        }
    });

    // Close suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
            suggestionsBox.style.display = 'none';
        }
    });
});

function selectBrand(brand) {
    window.location.href = 'products.php?brand=' + encodeURIComponent(brand);
}
</script>
</body>
</html>
