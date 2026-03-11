<?php
// 安全启动 Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// 获取当前已在对比列表中的 ID，默认为空数组
$compare_list = $_SESSION['compare'] ?? [];
?>

<style>
    /* 基础布局 */
    .product-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
        padding: 40px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .product-card {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        min-height: 500px; /* 改用 min-height */
        transition: all 0.3s;
        cursor: pointer;
    }

    .product-card:hover { border: 2px solid #947b54; transform: translateY(-3px); }

    .product-card img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        border-radius: 6px;
    }

    .product-actions {
        margin-top: auto;
        display: flex;
        gap: 10px;
        padding-top: 15px;
    }

    .product-actions button {
        flex: 1;
        padding: 10px;
        border: none;
        border-radius: 20px;
        cursor: pointer;
        color: #fff;
        font-weight: 600;
    }

    /* 已添加的禁用样式 */
    .btn-added {
        background: #ccc !important;
        cursor: not-allowed !important;
        pointer-events: none;
    }
</style>

<?php 
    $p_id = intval($row['product_id']);
    $isAdded = in_array($p_id, $compare_list);
?>
<div class="product-card" data-id="<?php echo $p_id; ?>">
    <img src="<?php echo htmlspecialchars($row['image_main']); ?>">
    <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
    
    <div class="seller-rating">
        <span>Seller: <?php echo htmlspecialchars($row['seller_name']); ?></span>
        <span class="rating">⭐ <?php echo htmlspecialchars($row['rating']); ?></span>
    </div>

    <p class="price">RM <?php echo number_format($row['price'], 2); ?></p>

    <div class="product-actions">
        <button type="button" class="review-summary">Summary</button>
        
        <button type="button" 
                class="add-compare <?php echo $isAdded ? 'btn-added' : ''; ?>">
            <?php echo $isAdded ? 'In List' : 'Add to Compare'; ?>
        </button>
    </div>
</div>

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

        reviewText.innerHTML = '<div class="spinner">🌀 <b>Gemma 3</b> is analyzing reviews...</div>';
        modal.style.display = 'block';

        fetch(`get_review_summary.php?id=${productId}`)
            .then(res => res.text())
            .then(data => { reviewText.innerHTML = data; })
            .catch(err => { reviewText.innerHTML = 'Error fetching summary.'; });
    });
});

function showToast(message) {
    const toast = document.getElementById('toast');
    if (!toast) return;

    toast.classList.remove('show');

    void toast.offsetWidth; 

    toast.innerText = message;
    toast.classList.add('show');

    setTimeout(() => {
        toast.classList.remove('show');
    }, 2500);
}

document.querySelectorAll('.add-compare').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const btn = e.currentTarget;
        const card = btn.closest('.product-card');
        const productId = card.dataset.id;

        if (btn.classList.contains('processing')) return;
        btn.classList.add('processing');

        fetch(`add_to_compare.php?id=${productId}`)
            .then(res => {
                if (res.status === 200) {
                    showToast("✅ Added to compare list!");
                    btn.innerText = "In List";
                    btn.style.background = "#ccc";
                    btn.style.pointerEvents = "none";
                } else if (res.status === 409) {
                    showToast("⚠️ Already in list!");
                } else {
                    showToast("❌ Server Error");
                }
            })
            .catch(() => showToast("❌ Connection error"))
            .finally(() => btn.classList.remove('processing'));
    });
});
    
window.addEventListener('click', (e) => {
    const modalIds = ['review-modal', 'ai-modal', 'compare-modal'];
    modalIds.forEach(id => {
        const modal = document.getElementById(id);
        if (modal && e.target === modal) modal.style.display = 'none';
    });
});

    /*
function showToast(message) {
    const toast = document.getElementById('toast');
    if(!toast) return; 
    toast.innerText = message;
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
    }, 2000);
} */
</script>
