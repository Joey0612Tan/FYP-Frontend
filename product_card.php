<?php
// --- 核心对账：直接查数据库 (必须在 while 循环内) ---
$current_id = $row['product_id'];
$u_id = 1; // 必须跟 add_to_compare.php 里的 user_id 一致

$db_check = $conn->query("SELECT 1 FROM compare_list WHERE product_id = $current_id AND user_id = $u_id");
$is_in_list = ($db_check && $db_check->num_rows > 0);
?>

<style>
    .product-card {
        border: 1px solid #ddd;
        padding: 15px;
        background: #fff;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        min-height: 460px;
        transition: all .3s;
        cursor: pointer;
    }
    .product-card:hover { border: 2px solid #947b54ff; transform: translateY(-3px); }
    .product-image-wrapper { width: 100%; aspect-ratio: 1/1; overflow: hidden; border-radius: 8px; margin-bottom: 10px; background: #f5f5f5; }
    .product-image-wrapper img { width: 100%; height: 100%; object-fit: cover; }
    .product-actions { display: flex; gap: 8px; margin-top: auto; }
    .product-actions button { flex: 1; padding: 10px 5px; font-size: 0.85rem; font-weight: 600; border: none; border-radius: 6px; cursor: pointer; color: #fff; }
    
    /* 按钮颜色 */
    .review-summary { background: #FFC107; color: #333; }
    .add-compare { background: #2196F3; }
    
    /* 状态：已经在列表中的灰色样式 */
    .add-compare.in-list {
        background: #ccc !important;
        color: #666 !important;
        cursor: not-allowed !important;
    }
</style>

<div class="product-card" data-id="<?php echo $current_id; ?>" onclick="if(!event.target.closest('button')) window.location.href='Product_details.php?id=<?php echo $current_id; ?>'">
    <div class="product-image-wrapper">
        <img src="<?php echo htmlspecialchars($row['image_main']); ?>" alt="Product">
    </div>
    
    <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>

    <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#555;">
        <span>Seller: <?php echo htmlspecialchars($row['seller_name']); ?></span>
        <span style="color:#ecd53a;">⭐ <?php echo htmlspecialchars($row['rating']); ?></span>
    </div>

    <p style="font-size:1.4rem; font-weight:bold; color:#E53935; margin:8px 0;">RM <?php echo number_format($row['price'], 2); ?></p>

    <div class="product-actions">
        <button class="review-summary" onclick="event.stopPropagation();">📊 Review</button>

        <button 
            class="add-compare <?php echo $is_in_list ? 'in-list' : ''; ?>" 
            data-inlist="<?php echo $is_in_list ? '1' : '0'; ?>"
            onclick="handleCompare(this, <?php echo $current_id; ?>, event)">
            <?php echo $is_in_list ? '✔ In List' : '+ Compare'; ?>
        </button>
    </div>
</div>

<script>
function handleCompare(btn, productId, e) {
    e.stopPropagation();

    if (btn.dataset.inlist === "1") {
        showToast("⚠️ Already in compare list");
        return;
    }

    btn.style.pointerEvents = "none";

    fetch(`add_to_compare.php?id=${productId}`)
        .then(res => {
            if (res.status === 200 || res.status === 409) {
                showToast(res.status === 200 ? "✅ Added to list!" : "⚠️ Already in list");
                // 成功后必须刷新，让 PHP 重新执行数据库查询，改变按钮颜色
                setTimeout(() => { window.location.reload(); }, 800);
            } else {
                showToast("❌ Error adding");
                btn.style.pointerEvents = "auto";
            }
        })
        .catch(() => {
            showToast("❌ Connection error");
            btn.style.pointerEvents = "auto";
        });
}

function showToast(message) {
    const toast = document.getElementById("toast");
    if(!toast) return;
    toast.innerText = message;
    toast.classList.add("show");
    setTimeout(() => toast.classList.remove("show"), 2500);
}
</script>
