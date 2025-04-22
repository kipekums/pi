<?php
// admin_products.php - Admin Products Management
require_once "config.php";

// Make sure only admins can access this page
if (!isAdmin()) {
    setFlashMessage('danger', 'You do not have permission to access that page.');
    header('Location: index.php');
    exit();
}

// Handle product deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $product_id = $_GET['delete'];
    
    // First check if product exists
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 1) {
        // Delete the product
        $sql = "DELETE FROM products WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        
        if (mysqli_stmt_execute($stmt)) {
            setFlashMessage('success', 'Product deleted successfully.');
        } else {
            setFlashMessage('danger', 'Error deleting product: ' . mysqli_error($conn));
        }
    } else {
        setFlashMessage('danger', 'Product not found.');
    }
    
    // Redirect to refresh the page without the delete parameter
    header('Location: admin_products.php');
    exit();
}

// Get category filter if set
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

// Get all products with optional category filter
if (!empty($category_filter)) {
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.category_id = ?
            ORDER BY p.id DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $category_filter);
    mysqli_stmt_execute($stmt);
    $products = mysqli_stmt_get_result($stmt);
} else {
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            ORDER BY p.id DESC";
    $products = mysqli_query($conn, $sql);
}

// Get all categories for filter dropdown
$sql = "SELECT * FROM categories ORDER BY name";
$categories = mysqli_query($conn, $sql);

$page_title = "Manage Products";
include "header.php";
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Manage Products</h1>
        <a href="admin_product_add.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add New Product
        </a>
    </div>
    
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="admin_products.php" class="row g-3">
                <div class="col-md-6">
                    <label for="category" class="form-label">Filter by Category</label>
                    <select name="category" id="category" class="form-select" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        <?php while($category = mysqli_fetch_assoc($categories)): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo ($category_filter == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo $category['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <a href="admin_products.php" class="btn btn-outline-secondary">Reset Filter</a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Products List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($product = mysqli_fetch_assoc($products)): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td>
                                <?php if (!empty($product['image_url'])): ?>
                                    <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>" width="50" height="50" class="img-thumbnail">
                                <?php else: ?>
                                    <span class="text-muted">No image</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $product['name']; ?></td>
                            <td><?php echo $product['category_name'] ?? 'Uncategorized'; ?></td>
                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                            <td>
                                <?php if($product['in_stock']): ?>
                                    <span class="badge bg-success">In Stock</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Out of Stock</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="admin_product_edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <a href="admin_products.php?delete=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this product?')">
                                    <i class="bi bi-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if(mysqli_num_rows($products) == 0): ?>
                        <tr>
                            <td colspan="7" class="text-center">No products found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>