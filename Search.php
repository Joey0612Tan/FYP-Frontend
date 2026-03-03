<?php
include('ConnectDB.php');
include('header.php');
include('navbar.php');

$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';
$is_visual = (isset($_GET['search_mode']) && $_GET['search_mode'] === 'visual');
$ids = isset($_GET['ids']) ? mysqli_real_escape_string($conn, $_GET['ids']) : '';

if ($is_visual) {
    if (!empty($ids) && $ids !== 'none' && preg_match('/^[0-9,]+$/', $ids)) {
        $sql = "SELECT p.*, sel.seller_name 
                FROM products p
                LEFT JOIN sellers sel ON p.seller_id = sel.seller_id
                WHERE p.product_id IN ($ids)
                ORDER BY FIELD(p.product_id, $ids)";
    } else {
        $sql = "SELECT p.*, sel.seller_name 
                FROM products p 
                LEFT JOIN sellers sel ON p.seller_id = sel.seller_id 
                WHERE 1=0";
    }
} 
elseif ($keyword != '') {
    $words = explode(' ', $keyword);
    $searchConditions = [];

    foreach ($words as $word) {
        $word = trim($word);
        if (!empty($word)) {
            $searchConditions[] = "(p.product_name LIKE '%$word%' 
                                   OR p.description LIKE '%$word%' 
                                   OR sel.seller_name LIKE '%$word%')";
        }
    }
    $finalCondition = implode(' OR ', $searchConditions);
    
    $sql = "SELECT p.*, sel.seller_name 
            FROM products p
            LEFT JOIN sellers sel ON p.seller_id = sel.seller_id
            WHERE $finalCondition
            GROUP BY p.product_id
            ORDER BY (p.product_name LIKE '%$keyword%') DESC";
} 
else {
    $sql = "SELECT p.*, sel.seller_name 
            FROM products p 
            LEFT JOIN sellers sel ON p.seller_id = sel.seller_id";
}
$score = $_GET['score'] ?? 0;
$result = mysqli_query($conn, $sql);
?>

<section style="padding:40px 60px;">
    <?php if ($is_visual && $keyword != ''): ?>
        <div style="background: linear-gradient(90deg, #fdfbfb 0%, #ebedee 100%); padding: 15px 25px; border-radius: 12px; border-left: 5px solid #ceb9a0; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 15px;">
            <div style="font-size: 1.5rem;">📸</div>
            <div>
                <h4 style="margin: 0; color: #4b310b;">AI Visual Intelligence</h4>
                <p style="margin: 0; font-size: 0.9rem; color: #8e5c12;">
                    Recognized: <strong>"<?php echo htmlspecialchars($keyword); ?>"</strong>. Found these matches for you!
                </p>
            </div>
        </div>
    <?php endif; ?>

    <h2>
    <?php 
    if ($is_visual) {
        if ($score > 0.85) {
            echo "✨ Perfect Matches Found!";
        } else {
            echo "🧸 We couldn't find the exact item, but look at these similar vibes:";
        }
    } else {
        echo 'Search results for "' . htmlspecialchars($keyword) . '"';
    }
    ?>
</h2>
<p>Debug Score: <?php echo $score; ?></p>
    <div class="product-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; margin-top: 20px;">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php include('product_card.php'); ?>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 50px 0; width: 100%; grid-column: 1/-1;">
                <p style="font-size: 1.2rem; color: #999;">🧸 No products found matching that keyword/image. Try a new input!</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include('footer.php'); ?>

<script>
document.querySelectorAll('.product-card').forEach(card=>{
    card.addEventListener('click', (e)=>{
        if(e.target.closest('.review-summary') || e.target.closest('.add-compare')) return;
        const productId = card.dataset.id;
        window.location.href = `Product_details.php?id=${productId}`;
    });
});

document.querySelectorAll('.review-summary').forEach(btn=>{
    btn.addEventListener('click',(e)=>{
        e.stopPropagation();
        const card = e.target.closest('.product-card');
        const productId = card.dataset.id;
        const modal = document.getElementById('review-modal');
        const reviewText = document.getElementById('review-text');

        fetch(`get_review_summary.php?id=${productId}`)
            .then(res => res.text())
            .then(data => {
                reviewText.innerHTML = data.trim() ? data : 'No review exists yet.';
            });

        modal.style.display = 'block';
    });
});

document.querySelector('.close')?.addEventListener('click', ()=>{
    document.getElementById('review-modal').style.display = 'none';
});

window.addEventListener('click', (e)=>{
    const modal = document.getElementById('review-modal');
    if(e.target === modal) modal.style.display = 'none';
});

function showToast(message) {
    const toast = document.getElementById('toast');
    toast.innerText = message;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 2000);
}

document.querySelectorAll('.add-compare').forEach(btn=>{
    btn.addEventListener('click',(e)=>{
        e.stopPropagation();
        showToast('✅ Product successfully added to compare list!');
    });
});
</script>

