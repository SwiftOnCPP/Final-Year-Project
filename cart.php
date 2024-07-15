<?php
session_start();

// Check if form submitted to add item to cart
if (isset($_POST['add_to_cart'])) {
    $item_name = $_POST['item_name'];
    $item_price = $_POST['item_price'];
    $item_quantity = $_POST['item_quantity'];

    // Initialize cart if not already
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add item to cart
    $item = [
        'name' => $item_name,
        'price' => $item_price,
        'quantity' => $item_quantity
    ];

    $_SESSION['cart'][] = $item;
}
?>

<h2>Shopping Cart</h2>

<?php
// Display cart contents and calculate totals
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;
        echo '<p>'.$item['name'].' - Price: '.$item['price'].' - Quantity: '.$item['quantity'].' - Subtotal: '.$subtotal.'</p>';
    }
    echo '<p>Total: RM '.$total.'</p>';
} else {
    echo '<p>Your cart is empty.</p>';
}
?>

<a href="menu.php">Continue Shopping</a>
<a href="checkout.php">Proceed to Checkout</a>
