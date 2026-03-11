<?php
// --- 核心状态检查 (必须放在循环内部) ---
$current_id = $row['product_id'];
// 这里的 'compare' 必须与你的 add_to_compare.php 里的 Session Key 完全一致
$compare_session = isset($_SESSION['compare']) ? $_SESSION['compare'] : [];
$is_in_list = in_array((string)$current_id, $compare_session) || in_array((int)$current_id, $compare_session);
?>

<style>
    /* 网格与卡片基础布局 */
    .product-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 20px;
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .product-card {
        background: #fff;
        border: 1px solid #efefef;
        border-radius: 12px;
        padding: 15px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%; 
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.08);
        border-color: #947b54ff;
    }

    /* 图片锁定为正方形，防止卡片细长 */
    .product-image-wrapper {
        width: 100%;
        aspect-ratio: 1 / 1; 
        border-radius: 8px;
        overflow: hidden;
        background: #f8f8f8;
        margin-bottom: 12px;
    }

    .product-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-card h3 {
        font-size: 1rem;
        margin: 0 0 10px 0;
        height: 2.4em; 
        overflow: hidden;
        line-height: 1.2;
        color: #333;
    }

    .product-card .price {
        font-size: 1.3rem;
        font-weight: bold;
        color: #E53935;
        margin: 8px 0;
    }

    .product-actions {
        margin-top: auto;
        display: flex;
        gap: 8px;
        padding-top: 10px;
    }

    .product-actions button {
        flex: 1;
        padding: 10px 5px;
        font-size: 0.85rem;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }

    /* 按钮颜色逻辑 */
    .review-summary { background: #fbc02d; color: #333; }
    .add-compare { background: #4b310b; color: white; }

    /* --- 关键：已经在列表中的灰色样式 --- */
    .add-compare.in-list {
        background: #e0e0e0 !important;
        color: #888 !important;
        cursor: not-allowed !important;
        box-shadow: none !important;
        transform: none !important;
    }

    /* Modal 与 Toast 修正 */
    .modal {   
        display: none; position: fixed; z-index: 9999;
        left: 0; top: 0; width: 100%; height: 100%;   
        background: rgba(0,0,0,.5); backdrop-filter: blur(3px);   
    }
    .modal-content {
        background: #fff; margin: 10% auto; padding: 25px;
        border-radius: 12px; width: 80%; max-width: 500px; position: relative;
    }
    .toast {
        position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
        background: #333; color: #fff; padding: 12px 24px;
        border-radius: 30px; z-index: 10000; opacity: 0; transition: 0.3s;
    }
    .toast.show { opacity: 1; top: 40px; }
</style>

<div class="product-card" data-id="<?php echo $current_id; ?>">
    <div class="product-image-wrapper">
        <img src="<?php echo htmlspecialchars($row['image_main']); ?>" alt="Product">
    </div>
    
    <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>

    <div class="seller-rating" style="display:flex; justify-content:space-between; font-size:0.85rem; color:#666;">
        <span>Seller: <?php echo htmlspecialchars($row['seller_name']); ?></span>
        <span style="color:#fbc02d;">⭐ <?php echo htmlspecialchars($row['rating']); ?></span>
    </div>

    <p class="price">RM <?php echo number_format($row['price'], 2); ?></p>

    <div class="product-actions">
        <button class="review-summary" onclick="event.stopPropagation(); handleReviewSummary(<?php echo $current_id; ?>)">
            📊 Review
        </button>

        <button 
            class="add-compare <?php echo $is_in_list ? 'in-list' : ''; ?>"
            data-inlist="<?php echo $is_in_list ? '1' : '0'; ?>"
            onclick="event.stopPropagation(); handleCompare(this, <?php echo $current_id; ?>)">
            <?php echo $is_in_list ? '✔ In List' : '+ Compare'; ?>
        </button>
    </div>
</div>

<script>
    // 全局卡片点击跳转
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', (e) => {
            if (!e.target.closest('button')) {
                window.location.href = `product_details.php?id=${card.dataset.id}`;
            }
        });
    });

    // Review Summary 逻辑
    function handleReviewSummary(id) {
        const modal = document.getElementById('review-modal');
        const text = document.getElementById('review-text');
        text.innerHTML = "🌀 <b>Gemma AI</b> is analyzing reviews...";
        modal.style.display = "block";

        fetch(`get_review_summary.php?id=${id}`)
            .then(res => res.text())
            .then(data => { text.innerHTML = data; })
            .catch(() => { text.innerHTML = "❌ Error fetching summary."; });
    }

    // Compare 逻辑 (核心对账与自动刷新)
    function handleCompare(btn, productId) {
        // 第一重校验：如果 HTML 标记已经在列表里
        if (btn.dataset.inlist === "1") {
            showToast("⚠️ This product is already in your compare list!");
            return;
        }

        btn.style.pointerEvents = "none"; // 防止连点

        fetch(`add_to_compare.php?id=${productId}`)
            .then(res => {
                if (res.status === 200 || res.status === 409) {
                    const msg = res.status === 200 ? "✅ Added to compare list!" : "⚠️ Already in list!";
                    showToast(msg);
                    // 成功后延迟刷新，触发 PHP 重新渲染 in-list 状态
                    setTimeout(() => { window.location.reload(); }, 800);
                } else {
                    showToast("❌ System error.");
                    btn.style.pointerEvents = "auto";
                }
            })
            .catch(() => {
                showToast("❌ Connection error.");
                btn.style.pointerEvents = "auto";
            });
    }

    // Modal 关闭逻辑：点击外部或打叉
    window.addEventListener('click', (e) => {
        const modal = document.getElementById('review-modal');
        if (e.target === modal) modal.style.display = "none";
    });

    function showToast(message) {
        let toast = document.getElementById('toast');
        if(!toast) return;
        toast.innerText = message;
        toast.classList.add("show");
        setTimeout(() => { toast.classList.remove("show"); }, 2500);
    }
</script>
