<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('ConnectDB.php');
include('header.php');
include('navbar.php');

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$is_visual = (isset($_GET['search_mode']) && $_GET['search_mode'] === 'visual');
$ids = isset($_GET['ids']) ? $_GET['ids'] : '';
$score = isset($_GET['score']) ? floatval($_GET['score']) : 0;
$show_popular = isset($_GET['show_popular']) ? true : false;

echo "<!-- Search Debug -->\n";
echo "<!-- is_visual: " . ($is_visual ? 'true' : 'false') . " -->\n";
echo "<!-- ids: $ids -->\n";
echo "<!-- score: $score -->\n";
echo "<!-- keyword: " . htmlspecialchars($keyword) . " -->\n";

$sql = "";
$result = null;

if ($is_visual && !empty($ids) && $ids !== 'none' && preg_match('/^[0-9,]+$/', $ids)) {
    $sql = "SELECT p.*, sel.seller_name 
            FROM products p
            LEFT JOIN sellers sel ON p.seller_id = sel.seller_id
            WHERE p.product_id IN ($ids)
            ORDER BY FIELD(p.product_id, $ids)";
    echo "<!-- Visual search with specific IDs -->\n";
}
elseif ($is_visual && (empty($ids) || $ids === 'none' || $show_popular)) {
    $sql = "SELECT p.*, sel.seller_name 
            FROM products p
            LEFT JOIN sellers sel ON p.seller_id = sel.seller_id
            ORDER BY p.product_id DESC
            LIMIT 12";
    echo "<!-- Showing popular products -->\n";
}
elseif (!empty($keyword)) {
    $escaped_keyword = mysqli_real_escape_string($conn, $keyword);
    $words = explode(' ', $keyword);
    $searchConditions = [];
    
    foreach ($words as $word) {
        $word = trim($word);
        if (!empty($word)) {
            $escaped_word = mysqli_real_escape_string($conn, $word);
            $searchConditions[] = "(p.product_name LIKE '%$escaped_word%' 
                                   OR p.description LIKE '%$escaped_word%' 
                                   OR sel.seller_name LIKE '%$escaped_word%')";
        }
    }
    
    if (count($searchConditions) > 0) {
        $finalCondition = implode(' OR ', $searchConditions);
        $sql = "SELECT p.*, sel.seller_name 
                FROM products p
                LEFT JOIN sellers sel ON p.seller_id = sel.seller_id
                WHERE $finalCondition
                GROUP BY p.product_id
                ORDER BY (p.product_name LIKE '%$escaped_keyword%') DESC,
                         p.rating DESC";
        echo "<!-- Keyword search: $keyword -->\n";
    } else {
        $sql = "SELECT p.*, sel.seller_name 
                FROM products p 
                LEFT JOIN sellers sel ON p.seller_id = sel.seller_id 
                ORDER BY p.product_id DESC 
                LIMIT 20";
    }
}
else {
    $sql = "SELECT p.*, sel.seller_name 
            FROM products p 
            LEFT JOIN sellers sel ON p.seller_id = sel.seller_id 
            ORDER BY p.product_id DESC 
            LIMIT 20";
    echo "<!-- Showing recent products -->\n";
}

if ($sql) {
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        echo "<!-- SQL Error: " . mysqli_error($conn) . " -->\n";
    }
}
?>

<section style="padding: 20px 40px; max-width: 1400px; margin: 0 auto;">
    
    <?php if ($is_visual): ?>
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px 30px; border-radius: 15px; margin-bottom: 30px;">
            <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                <div style="font-size: 2.5rem;">🔍</div>
                <div style="flex: 1;">
                    <h3 style="margin: 0 0 5px 0; color: white;">AI Visual Search</h3>
                    <?php if ($score > 0): ?>
                        <p style="margin: 0; color: rgba(255,255,255,0.9);">
                            Similarity Score: <strong><?php echo round($score * 100, 2); ?>%</strong>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div style="margin-bottom: 30px;">
        <?php if ($is_visual && ($show_popular || empty($ids) || $ids === 'none')): ?>
            <h1 style="font-size: 1.8rem; color: #333;">📸 No Exact Match Found</h1>
            <p style="color: #666;">Here are some popular products you might like:</p>
        <?php elseif ($is_visual && $score > 0.85): ?>
            <h1 style="font-size: 1.8rem; color: #333;">✨ Perfect Match Found!</h1>
            <p style="color: #666;">Found products matching your image</p>
        <?php elseif ($is_visual): ?>
            <h1 style="font-size: 1.8rem; color: #333;">🔍 Similar Products</h1>
            <p style="color: #666;">Products similar to your image</p>
        <?php elseif (!empty($keyword)): ?>
            <h1 style="font-size: 1.8rem; color: #333;">Search Results for "<?php echo htmlspecialchars($keyword); ?>"</h1>
            <p style="color: #666;">Found <?php echo $result ? mysqli_num_rows($result) : 0; ?> products</p>
        <?php else: ?>
            <h1 style="font-size: 1.8rem; color: #333;">🛍️ All Products</h1>
        <?php endif; ?>
    </div>

    <div class="product-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px;">
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <?php include('product_card.php'); ?>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 60px; grid-column: 1/-1; background: #f9f9f9; border-radius: 15px;">
                <div style="font-size: 4rem; margin-bottom: 20px;">🔍</div>
                <h3 style="color: #333;">No products found</h3>
                <p style="color: #666;">Try a different search term or browse our categories.</p>
                <a href="HomePage.php" style="display: inline-block; margin-top: 20px; padding: 10px 25px; background: #ceb9a0; color: white; text-decoration: none; border-radius: 25px;">Back to Home</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
@media (max-width: 768px) {
    section {
        padding: 15px !important;
    }
    .product-container {
        gap: 12px !important;
        grid-template-columns: repeat(2, 1fr) !important;
    }
}
</style>

<?php include('footer.php'); ?>
