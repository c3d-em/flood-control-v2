<?php
session_start();
include '../config/database.php';

// Check if user is client
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'client') {
    $_SESSION['error'] = 'Please login as client to add items to cart';
    header("Location: ../products.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = intval($_POST['product_id']);
    $client_id = $_SESSION['user_id'];
    
    try {
        // Check if product exists and has stock
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND quantity > 0");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if(!$product) {
            $_SESSION['error'] = 'Product not available or out of stock';
            header("Location: ../products.php");
            exit();
        }
        
        // Calculate total price (default quantity 1)
        $quantity = 1;
        $total_price = $product['price'] * $quantity;
        
        // Create order
        $stmt = $pdo->prepare("INSERT INTO orders (client_id, product_id, quantity, total_price, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->execute([$client_id, $product_id, $quantity, $total_price]);
        
        // Update product quantity
        $new_quantity = $product['quantity'] - $quantity;
        $stmt = $pdo->prepare("UPDATE products SET quantity = ? WHERE id = ?");
        $stmt->execute([$new_quantity, $product_id]);
        
        $_SESSION['success'] = "✅ " . $product['name'] . " added to cart successfully!";
        header("Location: ../products.php");
        
    } catch(PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: ../products.php");
    }
} else {
    header("Location: ../products.php");
}
?>