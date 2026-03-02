<?php
include('ConnectDB.php');
include 'header.php';    
include('navbar.php');

$search_mode = $_GET['search_mode'] ?? 'text';
$keyword = $_GET['keyword'] ?? ''; 

$sql = "SELECT * FROM products WHERE product_name LIKE ? OR description LIKE ?";
$stmt = $conn->prepare($sql);
$search_term = "%$keyword%";
$stmt->bind_param("ss", $search_term, $search_term);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container" style="padding: 40px;">
    <h2>
        <?php echo ($search_mode === 'visual') ? '📷 AI Visual Search Results' : '🔍 Search Results'; ?>
    </h2>
    <p>Showing results for: <strong><?php echo htmlspecialchars($keyword); ?></strong></p>

    <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-top: 30px;">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="product-card" style="border: 1px solid #ddd; padding: 15px; border-radius: 10px; text-align: center;">
                    <img src="<?php echo htmlspecialchars($row['image_main']); ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: 5px;">
                    <h3 style="margin: 10px 0;"><?php echo $row['product_name']; ?></h3>
                    <p style="color: #8e5c12; font-weight: bold;">$<?php echo $row['price']; ?></p>
                    <a href="product_details.php?id=<?php echo $row['product_id']; ?>" class="btn-primary" style="text-decoration: none; background: #ceb9a0; color: white; padding: 8px 15px; border-radius: 5px; display: inline-block;">View Detail</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 50px;">
                <p style="font-size: 1.2rem; color: #666;">Oops! No matching products found. Try another photo? 🧸</p>
            </div>
        <?php endif; ?>
    </div>
</div>