<?php

include('ConnectDB.php');

if (!isset($_GET['id'])) {
    die('No ID received');
}

$product_id = intval($_GET['id']);

$sql = "
SELECT p.*, s.seller_name
FROM products p
JOIN sellers s ON p.seller_id = s.seller_id
WHERE p.product_id = $product_id
";

$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    die('Product not found in DB');
}

$product = $result->fetch_assoc();
?>


include('ConnectDB.php');
session_start();

if (!isset($_GET['id'])) {
    die('Product not found');
}

$product_id = intval($_GET['id']);

$sql = "
SELECT p.*, s.seller_name
FROM products p
JOIN sellers s ON p.seller_id = s.seller_id
WHERE p.product_id = $product_id
";
$result = $conn->query($sql);
$product = $result->fetch_assoc();

if (!$product) {
    die('Product not found');
}

$review_sql = "SELECT * FROM reviews WHERE product_id = $product_id";
$reviews = $conn->query($review_sql);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($product['product_name']); ?></title>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    background: #f9f9f9;
}

.product-detail-container {
    display: flex;
    gap: 60px;
    padding: 60px;
    background: #fff;
}

.product-images img {
    width: 420px;
    height: 420px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.product-info {
    max-width: 500px;
}

.product-info h1 {
    font-size: 2.4rem;
    margin-bottom: 10px;
}

.price {
    font-size: 2rem;
    color: #E53935;
    font-weight: bold;
    margin: 10px 0;
}

.seller-rating {
    display: flex;
    justify-content: space-between;
    margin: 8px 0;
    font-size: 1.1rem;
    color: #555;
}

.rating {
    color: #FF9800;
    font-weight: 600;
}

.add-cart {
    margin-top: 25px;
    padding: 12px 35px;
    font-size: 1.2rem;
    background: #947b54;
    color: #fff;
    border: none;
    border-radius: 25px;
    cursor: pointer;
}

.product-section {
    padding: 40px 60px;
    background: #fff;
    margin-top: 20px;
}

.product-section h2 {
    margin-bottom: 15px;
}

.review-card {
    border-bottom: 1px solid #ddd;
    padding: 15px 0;
}
</style>
</head>

<body>

<div class="product-detail-container">

    <div class="product-images">
        <img src="<?php echo htmlspecialchars($product['image_main']); ?>">
    </div>

    <div class="product-info">
        <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>

        <div class="seller-rating">
            <span>Seller: <?php echo htmlspecialchars($product['seller_name']); ?></span>
            <span class="rating">⭐ <?php echo $product['rating'] ?? '4.5'; ?></span>
        </div>

        <p class="price">RM <?php echo number_format($product['price'], 2); ?></p>

        <button class="add-cart">Add to Cart</button>
    </div>

</div>

<section class="product-section">
    <h2>Description</h2>
    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
</section>

<section class="product-section">
    <h2>Specifications</h2>
    <ul>
        <li><strong>Material:</strong> <?php echo $product['material']; ?></li>
        <li><strong>Color:</strong> <?php echo $product['color']; ?></li>
    </ul>
</section>

<section class="product-section">
    <h2>Reviews</h2>

    <?php if ($reviews->num_rows == 0): ?>
        <p>No reviews yet.</p>
    <?php else: ?>
        <?php while ($r = $reviews->fetch_assoc()): ?>
            <div class="review-card">
                <strong>Rating: <?php echo $r['rating']; ?>/5</strong>
                <p><?php echo htmlspecialchars($r['comment']); ?></p>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</section>

</body>
</html>
