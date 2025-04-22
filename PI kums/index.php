<?php
// index.php - Homepage
require_once "config.php";

// Get featured products
$sql = "SELECT * FROM products ORDER BY id DESC LIMIT 3";
$result = mysqli_query($conn, $sql);

$page_title = "Welcome";
include "header.php";
?>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons are already imported in styles.css -->
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<div class="hero-section bg-dark text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4">Welcome to <?php echo SITE_NAME; ?></h1>
                <p class="lead">Experience the finest coffee and delicious treats in town.</p>
                <a href="menu.php" class="btn btn-primary btn-lg mt-3">Explore Our Menu</a>
            </div>
            <div class="col-md-6">
                <!-- buat ganti bg -->
                <img src="images/coffee-banner.jpg" alt="Coffee Shop" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <h2 class="text-center mb-4">Featured Products</h2>
    
    <div class="row">
        <?php while($product = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="<?php echo $product['image_url']; ?>" class="card-img-top" alt="<?php echo $product['name']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $product['name']; ?></h5>
                        <p class="card-text"><?php echo $product['description']; ?></p>
                        <p class="card-text text-primary">$<?php echo number_format($product['price'], 2); ?></p>
                        <a href="menu.php" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    
    <div class="text-center mt-4">
        <a href="menu.php" class="btn btn-outline-primary">View All Products</a>
    </div>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-cup-hot fs-1 text-primary"></i>
                    <h5 class="card-title mt-3">Quality Coffee</h5>
                    <p class="card-text">We use only premium coffee beans, freshly roasted to perfection.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-clock fs-1 text-primary"></i>
                    <h5 class="card-title mt-3">Fast Service</h5>
                    <p class="card-text">Quick preparation without sacrificing quality - perfect for busy days.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-emoji-smile fs-1 text-primary"></i>
                    <h5 class="card-title mt-3">Friendly Atmosphere</h5>
                    <p class="card-text">Relax in our cozy environment with friendly staff and great ambiance.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
<script src="js/script.js"></script>