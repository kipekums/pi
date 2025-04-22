<?php
// Include necessary files
require_once 'config.php';
require_once 'functions.php';
require_once 'db.php';

// Check if user is logged in
if (!isLoggedIn()) {
    setFlashMessage('Please login to view your cart', 'warning');
    header('Location: login.php');
    exit;
}

$page_title = 'Shopping Cart';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart actions
if (isset($_POST['action'])) {
    $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
    
    switch ($_POST['action']) {
        case 'update':
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            if ($quantity <= 0) {
                // Remove item if quantity is 0 or negative
                unset($_SESSION['cart'][$item_id]);
                setFlashMessage('Item removed from cart', 'info');
            } else {
                $_SESSION['cart'][$item_id] = $quantity;
                setFlashMessage('Cart updated successfully', 'success');
            }
            break;
            
        case 'remove':
            if (isset($_SESSION['cart'][$item_id])) {
                unset($_SESSION['cart'][$item_id]);
                setFlashMessage('Item removed from cart', 'info');
            }
            break;
            
        case 'clear':
            $_SESSION['cart'] = [];
            setFlashMessage('Cart cleared', 'info');
            break;
    }
    
    // Redirect to prevent form resubmission
    header('Location: cart.php');
    exit;
}

include 'header.php';

// Get cart items details from database
$cart_items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $item_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($item_ids), '?'));
    
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id IN ($placeholders)");
    $stmt->execute($item_ids);
    
    $menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($menu_items as $item) {
        $quantity = $_SESSION['cart'][$item['id']];
        $subtotal = $item['price'] * $quantity;
        $total += $subtotal;
        
        $cart_items[] = [
            'id' => $item['id'],
            'name' => $item['name'],
            'price' => $item['price'],
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
}
?>

<div class="container mt-4 mb-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4">Shopping Cart</h1>
            
            <?php if (empty($cart_items)): ?>
                <div class="alert alert-info">
                    Your cart is empty. <a href="menu.php">Browse our menu</a> to add items.
                </div>
            <?php else: ?>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_items as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                                        <td>
                                            <form method="post" class="d-inline quantity-form">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                                <div class="input-group input-group-sm" style="width: 120px;">
                                                    <button type="button" class="btn btn-outline-secondary quantity-btn" data-change="-1">-</button>
                                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="form-control text-center quantity-input">
                                                    <button type="button" class="btn btn-outline-secondary quantity-btn" data-change="1">+</button>
                                                </div>
                                            </form>
                                        </td>
                                        <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                                        <td>
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="action" value="remove">
                                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i> Remove
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Total:</th>
                                        <th>$<?php echo number_format($total, 2); ?></th>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-3">
                            <form method="post">
                                <input type="hidden" name="action" value="clear">
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="bi bi-x"></i> Clear Cart
                                </button>
                            </form>
                            
                            <div>
                                <a href="menu.php" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-left"></i> Continue Shopping
                                </a>
                                <a href="checkout.php" class="btn btn-success ms-2">
                                    <i class="bi bi-credit-card"></i> Proceed to Checkout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle quantity buttons
    document.querySelectorAll('.quantity-btn').forEach(button => {
        button.addEventListener('click', function() {
            const change = parseInt(this.dataset.change);
            const input = this.closest('.input-group').querySelector('.quantity-input');
            let value = parseInt(input.value) + change;
            
            // Ensure quantity is at least 1
            if (value < 1) value = 1;
            
            input.value = value;
            
            // Auto-submit the form when quantity changes
            this.closest('form').submit();
        });
    });
});
</script>

<?php include 'footer.php'; ?>