<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'client'){
    header("Location: index.php");
    exit();
}

include 'config/database.php';
include 'includes/header.php';

$user_id = $_SESSION['user_id'];

// Get client orders with product info
$stmt = $pdo->prepare("
    SELECT o.*, p.name as product_name, p.price, p.image, p.category 
    FROM orders o 
    JOIN products p ON o.product_id = p.id 
    WHERE o.client_id = ? 
    ORDER BY o.order_date DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

// Calculate order stats
$total_orders = count($orders);
$pending_orders = 0;
$approved_orders = 0;
foreach($orders as $order) {
    if($order['status'] == 'pending') $pending_orders++;
    if($order['status'] == 'approved') $approved_orders++;
}
?>

<div class="client-dashboard">
    <h1>My Orders Dashboard 📦</h1>
    
    <!-- Order Statistics -->
    <div class="order-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 30px 0;">
        <div class="stat-card" style="background: #fff; padding: 20px; border-radius: 10px; text-align: center; border: 2px solid #000;">
            <h3 style="font-size: 2.5em; margin: 0; color: #000;"><?php echo $total_orders; ?></h3>
            <p style="color: #666;">Total Orders</p>
        </div>
        <div class="stat-card" style="background: #fff; padding: 20px; border-radius: 10px; text-align: center; border: 2px solid #000;">
            <h3 style="font-size: 2.5em; margin: 0; color: #000;"><?php echo $pending_orders; ?></h3>
            <p style="color: #666;">Pending</p>
        </div>
        <div class="stat-card" style="background: #fff; padding: 20px; border-radius: 10px; text-align: center; border: 2px solid #000;">
            <h3 style="font-size: 2.5em; margin: 0; color: #000;"><?php echo $approved_orders; ?></h3>
            <p style="color: #666;">Approved</p>
        </div>
    </div>

    <!-- Order History -->
    <div class="orders-list" style="background: #fff; padding: 30px; border-radius: 15px; border: 2px solid #000;">
        <h2>Order History 📋</h2>
        
        <?php if($orders): ?>
            <?php foreach($orders as $order): ?>
            <div class="order-card" style="padding: 20px; border-bottom: 1px solid #eee;">
                <div class="order-header" style="display: flex; justify-content: space-between; align-items: start;">
                    <div class="order-product">
                        <h4 style="margin: 0 0 10px 0; color: #000;"><?php echo htmlspecialchars($order['product_name']); ?></h4>
                        <div class="order-details">
                            <p style="margin: 5px 0; color: #666;"><strong>Order #:</strong> <?php echo $order['id']; ?></p>
                            <p style="margin: 5px 0; color: #666;"><strong>Quantity:</strong> <?php echo $order['quantity']; ?></p>
                            <p style="margin: 5px 0; color: #666;"><strong>Price:</strong> ₱<?php echo number_format($order['price'], 2); ?> each</p>
                            <p style="margin: 5px 0; color: #666;"><strong>Total:</strong> ₱<?php echo number_format($order['total_price'], 2); ?></p>
                            <p style="margin: 5px 0; color: #666;"><strong>Category:</strong> <?php echo htmlspecialchars($order['category']); ?></p>
                        </div>
                        <div class="order-meta" style="color: #888; font-size: 0.9em; margin-top: 10px;">
                            <small>Ordered: <?php echo date('M d, Y h:i A', strtotime($order['order_date'])); ?></small>
                        </div>
                    </div>
                    <span class="status-badge status-<?php echo $order['status']; ?>" 
                          style="padding: 8px 15px; border-radius: 20px; font-weight: bold; text-transform: uppercase;">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; color: #666;">
                <h3>📭 No Orders Yet</h3>
                <p>You haven't placed any orders yet.</p>
                <p><a href="products.php" style="color: #000; font-weight: bold;">🛍️ Start shopping now!</a></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.status-pending { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
.status-approved { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
.status-shipped { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.status-delivered { background: #28a745; color: white; border: 1px solid #1e7e34; }
.status-cancelled { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

.client-dashboard { max-width: 1200px; margin: 100px auto 50px; padding: 0 20px; }
</style>

<?php include 'includes/footer.php'; ?>