<?php
// menu.php - Product menu page
require_once "config.php";

// Get category filter
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Get products
$sql = "SELECT * FROM products";
if (!empty($category)) {
    $sql .= " WHERE category = '" . mysqli_real_escape_string($conn, $category) . "'";
}
$result = mysqli_query($conn, $sql);

// Get categories for filter
$sql_categories = "SELECT DISTINCT category FROM products";
$categories_result = mysqli_query($conn, $sql_categories);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Coffee Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include "header.php"; ?>
    
    <div class="container mt-5">
        <h1 class="mb-4">Our Menu</h1>
        
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="btn-group">
                    <a href="menu.php" class="btn btn-outline-primary <?php echo empty($category) ? 'active' : ''; ?>">All</a>
                    <?php while($cat = mysqli_fetch_assoc($categories_result)) { ?>
                        <a href="menu.php?category=<?php echo urlencode($cat['category']); ?>" 
                           class="btn btn-outline-primary <?php echo $category == $cat['category'] ? 'active' : ''; ?>">
                            <?php echo $cat['category']; ?>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
        
        <div class="row">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while($product = mysqli_fetch_assoc($result)) { ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="<?php echo $product['image_url']; ?>" class="card-img-top" alt="<?php echo $product['name']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $product['name']; ?></h5>
                                <p class="card-text"><?php echo $product['description']; ?></p>
                                <p class="card-text text-primary">$<?php echo number_format($product['price'], 2); ?></p>
                            </div>
                            <div class="card-footer">
                                <form action="add_to_cart.php" method="post">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <div class="input-group mb-3">
                                        <input type="number" name="quantity" class="form-control" value="1" min="1" max="<?php echo $product['stock']; ?>">
                                        <button type="submit" class="btn btn-primary">Add to Cart</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">No products found.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include "footer.php"; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// add_to_cart.php - Add product to cart
require_once "config.php";

// Check if user is logged in
if (!isLoggedIn()) {
    // Save requested product to session for after login
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $_SESSION['cart_redirect'] = [
            'product_id' => $_POST['product_id'],
            'quantity' => $_POST['quantity']
        ];
    }
    redirectTo("login.php");
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    
    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Check if product already in cart
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
    
    // Redirect back to menu
    redirectTo("menu.php");
}
?>

<?php
// cart.php - Shopping cart page
require_once "config.php";

// Check if user is logged in
if (!isLoggedIn()) {
    redirectTo("login.php");
}

// Remove item from cart
if (isset($_GET['remove']) && isset($_SESSION['cart'][$_GET['remove']])) {
    unset($_SESSION['cart'][$_GET['remove']]);
}

// Clear cart
if (isset($_GET['clear'])) {
    unset($_SESSION['cart']);
    redirectTo("cart.php");
}

// Process checkout
if (isset($_POST['checkout']) && !empty($_SESSION['cart'])) {
    // Create order
    $user_id = $_SESSION['user_id'];
    $total_amount = 0;
    
    // Calculate total amount
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $sql = "SELECT price FROM products WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $product = mysqli_fetch_assoc($result);
        $total_amount += $product['price'] * $quantity;
    }
    
    // Insert order
    $sql = "INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "id", $user_id, $total_amount);
    mysqli_stmt_execute($stmt);
    $order_id = mysqli_insert_id($conn);
    
    // Insert order items
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $sql = "SELECT price FROM products WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $product = mysqli_fetch_assoc($result);
        
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiid", $order_id, $product_id, $quantity, $product['price']);
        mysqli_stmt_execute($stmt);
        
        // Update product stock
        $sql = "UPDATE products SET stock = stock - ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $quantity, $product_id);
        mysqli_stmt_execute($stmt);
    }
    
    // Clear cart and redirect to order confirmation
    unset($_SESSION['cart']);
    redirectTo("order_confirmation.php?id=" . $order_id);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Coffee Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include "header.php"; ?>
    
    <div class="container mt-5">
        <h1 class="mb-4">Shopping Cart</h1>
        
        <?php if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grand_total = 0;
                    foreach ($_SESSION['cart'] as $product_id => $quantity): 
                        $sql = "SELECT * FROM products WHERE id = ?";
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, "i", $product_id);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        $product = mysqli_fetch_assoc($result);
                        $total = $product['price'] * $quantity;
                        $grand_total += $total;
                    ?>
                        <tr>
                            <td><?php echo $product['name']; ?></td>
                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo $quantity; ?></td>
                            <td>$<?php echo number_format($total, 2); ?></td>
                            <td>
                                <a href="cart.php?remove=<?php echo $product_id; ?>" class="btn btn-danger btn-sm">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                        <td><strong>$<?php echo number_format($grand_total, 2); ?></strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="cart.php?clear=1" class="btn btn-warning">Clear Cart</a>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <button type="submit"<button type="submit" name="checkout" class="btn btn-success">Proceed to Checkout</button>
                </form>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                Your cart is empty. <a href="menu.php">Continue shopping</a>.
            </div>
        <?php endif; ?>
    </div>
    
    <?php include "footer.php"; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// order_confirmation.php - Order confirmation page
require_once "config.php";

// Check if user is logged in
if (!isLoggedIn()) {
    redirectTo("login.php");
}

// Check if order ID is provided
if (!isset($_GET['id'])) {
    redirectTo("account.php");
}

$order_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Get order details
$sql = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($stmt);
$order_result = mysqli_stmt_get_result($stmt);

// Check if order exists and belongs to user
if (mysqli_num_rows($order_result) == 0) {
    redirectTo("account.php");
}

$order = mysqli_fetch_assoc($order_result);

// Get order items
$sql = "SELECT oi.*, p.name FROM order_items oi 
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$items_result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Coffee Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include "header.php"; ?>
    
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3 class="mb-0">Order Confirmed!</h3>
                    </div>
                    <div class="card-body">
                        <h4>Thank you for your order</h4>
                        <p>Your order #<?php echo $order_id; ?> has been placed successfully.</p>
                        
                        <div class="order-details mt-4">
                            <h5>Order Details</h5>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($item = mysqli_fetch_assoc($items_result)) { ?>
                                        <tr>
                                            <td><?php echo $item['name']; ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                                        <td><strong>$<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <div class="order-status mt-4">
                            <h5>Order Status</h5>
                            <p>Your order is currently <span class="badge bg-warning"><?php echo $order['status']; ?></span></p>
                            <p>We will process your order soon. You can check your order status in your <a href="account.php">account dashboard</a>.</p>
                        </div>
                        
                        <div class="mt-4">
                            <a href="menu.php" class="btn btn-primary">Continue Shopping</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include "footer.php"; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// account.php - User account dashboard
require_once "config.php";

// Check if user is logged in
if (!isLoggedIn()) {
    redirectTo("login.php");
}

// Get user information
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Get user orders
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$orders_result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Coffee Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include "header.php"; ?>
    
    <div class="container mt-5">
        <h1 class="mb-4">My Account</h1>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Account Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?php echo $user['name']; ?></p>
                        <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
                        <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order History</h5>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($orders_result) > 0): ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($order = mysqli_fetch_assoc($orders_result)) { ?>
                                        <tr>
                                            <td><?php echo $order['id']; ?></td>
                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td><span class="badge bg-<?php echo $order['status'] == 'completed' ? 'success' : ($order['status'] == 'pending' ? 'warning' : 'info'); ?>"><?php echo $order['status']; ?></span></td>
                                            <td>
                                                <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-info">View</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-info">
                                You haven't placed any orders yet. <a href="menu.php">Start shopping</a>.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include "footer.php"; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>