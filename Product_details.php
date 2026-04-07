<?php
session_start();
include('ConnectDB.php');
include('header.php');
include('navbar.php');

if (!isset($_GET['id'])) die('Product not found');

$product_id = intval($_GET['id']);

$stmt = $conn->prepare("
    SELECT p.*, s.seller_name
    FROM products p
    JOIN sellers s ON p.seller_id = s.seller_id
    WHERE p.product_id = ?
");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
if (!$product) die('Product not found');

$color_stmt = $conn->prepare("SELECT spec_value FROM product_specifications WHERE product_id = ? AND spec_key = 'Color'");
$color_stmt->bind_param("i", $product_id);
$color_stmt->execute();
$color_result = $color_stmt->get_result();
$colors = [];
while ($row = $color_result->fetch_assoc()) {
    $colors = array_merge($colors, array_map('trim', explode(',', $row['spec_value'])));
}

$spec_stmt = $conn->prepare("SELECT spec_key, spec_value FROM product_specifications WHERE product_id = ? AND spec_key != 'Color'");
$spec_stmt->bind_param("i", $product_id);
$spec_stmt->execute();
$specs = $spec_stmt->get_result();
$spec_list_for_ai = "";
$specs->data_seek(0); 
while($s = $specs->fetch_assoc()){
    $spec_list_for_ai .= $s['spec_key'] . ": " . $s['spec_value'] . ". ";
}

$reviews = $conn->query("SELECT username, rating, comment FROM reviews WHERE product_id = $product_id");

$image_stmt = $conn->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
$image_stmt->bind_param("i", $product_id);
$image_stmt->execute();
$img_res = $image_stmt->get_result();
$additional_images = [];
while($img = $img_res->fetch_assoc()) { $additional_images[] = $img['image_path']; }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title><?php echo htmlspecialchars($product['product_name']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        margin: 0;
        background: #f9f9f9;
        color: #333;
    }

    .container {
    max-width: 1400px;
    margin: 40px auto;
    padding: 0 20px;
    display: flex;
    gap: 60px;
    }

    .product-image-section {
        flex: 1;
        text-align: center;
        background: #fff;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    #main-img {
        width: 100%;
        max-width: 400px;
        height: auto;
        aspect-ratio: 1/1;
        object-fit: cover;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .thumb-container {
        display: flex;
        gap: 12px;
        justify-content: center;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .thumb-container img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 10px;
        cursor: pointer;
        border: 2px solid transparent;
        transition: 0.3s;
    }

    .thumb-container img:hover {
        border-color: #ceb9a0ff;
        transform: scale(1.05);
    }

    .product-info {
        flex: 1;
        background: #fff;
        padding: 24px 20px;
        border-radius: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .product-info h1 {
        font-size: 1.6rem;
        margin-bottom: 10px;
        color: #222;
        line-height: 1.3;
    }

    .seller-tag {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .rating {
        font-size: 0.9rem;
        color: #fbc02d;
        margin-bottom: 15px;
    }

    .price {
        font-size: 2rem;
        color: #E53935;
        font-weight: bold;
        margin: 20px 0;
    }

    .spec-label {
        font-weight: bold;
        margin-top: 15px;
        display: block;
        font-size: 0.95rem;
        color: #555;
    }

    .color-options {
        display: flex;
        gap: 10px;
        margin-top: 10px;
        flex-wrap: wrap;
    }

    .color-btn {
        padding: 8px 20px;
        font-size: 0.9rem;
        border: 1px solid #ddd;
        border-radius: 25px;
        cursor: pointer;
        background: #fff;
        transition: 0.3s;
    }

    .color-btn.active {
        border-color: #ceb9a0ff;
        background: rgb(238, 234, 231);
        color: #ceb9a0ff;
        font-weight: bold;
    }

    .quantity-section {
        margin: 20px 0;
    }

    .quantity-section label {
        font-weight: bold;
        color: #4b310b;
        display: block;
        margin-bottom: 8px;
        font-size: 0.95rem;
    }

    #selected-qty {
        padding: 10px;
        border-radius: 10px;
        border: 1px solid #ceb9a0;
        width: 80px;
        text-align: center;
        font-size: 1rem;
    }

    .action-buttons {
        display: flex;
        gap: 12px;
        margin-top: 25px;
        flex-wrap: wrap;
    }

    .btn {
        flex: 1;
        padding: 14px 12px;
        font-size: 0.95rem;
        font-weight: 600;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: 0.3s;
        min-width: 140px;
    }

    .btn-cart {
        background: #4b310b;
        color: #fff;
    }

    .btn-cart:hover {
        background: #9a6516;
        transform: translateY(-2px);
    }

    .btn-compare {
        background: #f0f0f0;
        color: #333;
        border: 1px solid #ddd;
    }

    .btn-compare:hover {
        background: #ceb9a0ff;
        transform: translateY(-2px);
    }

    .details-section {
        max-width: 1400px;
        margin: 20px auto;
        background: #fff;
        padding: 24px 20px;
        border-radius: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .section-title {
    font-size: 1.4rem;
    color: #222;
    margin-top: 30px;
    margin-bottom: 20px;
    border-left: 4px solid #ceb9a0ff;
    padding-left: 15px;
    }
    
    .section-title:first-of-type {
        margin-top: 0;
    }

    .spec-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }

    .spec-key {
        padding: 12px 12px;
        font-weight: bold;
        width: 35%;
        background: #f9f9f9;
        border-bottom: 1px solid #eee;
        color: #555;
    }

    .spec-value {
        padding: 12px 12px;
        border-bottom: 1px solid #eee;
        color: #333;
        word-break: break-word;
    }

    .description-content {
        font-size: 0.95rem;
        line-height: 1.6;
        color: #444;
        white-space: pre-line;
    }

    .review-item {
        border-bottom: 1px solid #eee;
        padding: 15px 0;
    }

    .review-item strong {
        font-size: 0.95rem;
    }

    .review-item p {
        font-size: 0.9rem;
        margin-top: 8px;
        color: #666;
        line-height: 1.5;
    }

    .review-star {
        color: #fbc02d;
        margin-left: 8px;
        font-size: 0.85rem;
    }

    .toggle-reviews {
        color: #8e5c12;
        cursor: pointer;
        font-weight: bold;
        font-size: 0.9rem;
        margin-top: 15px;
        display: inline-block;
    }

    #ai-assistant-trigger {
        position: fixed;
        bottom: 80px;
        right: 16px;
        background: #4b310b;
        color: white;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 999999;
        transition: 0.3s;
    }

    #ai-assistant-trigger:hover {
        transform: scale(1.05);
    }

    #ai-assistant-trigger span {
        font-size: 22px;
    }

    #deep-analysis-modal {
        display: none;
        position: fixed;
        z-index: 10001;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        padding: 16px;
    }

    #deep-analysis-modal > div {
        background: white;
        margin: 15% auto;
        padding: 20px;
        border-radius: 20px;
        width: 100%;
        max-width: 500px;
        position: relative;
        box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        max-height: 80vh;
        overflow-y: auto;
    }

    #toast {
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        background: #4CAF50;
        color: #fff;
        padding: 10px 20px;
        border-radius: 25px;
        font-size: 0.9rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        opacity: 0;
        pointer-events: none;
        transition: all 0.4s ease;
        z-index: 1000;
        white-space: nowrap;
    }

    #toast.show {
        opacity: 1;
        transform: translateX(-50%) translateY(-10px);
        pointer-events: auto;
    }

    @media screen and (max-width: 768px) {
        .container {
        margin: 12px auto;
        padding: 0 12px;
        gap: 12px;
        flex-direction: column;
        }

        .product-image-section {
            padding: 16px;
        }

        #main-img {
            max-width: 280px;
        }

        .thumb-container img {
            width: 55px;
            height: 55px;
        }

        .product-info {
            padding: 18px 16px;
        }

        .product-info h1 {
            font-size: 1.3rem;
        }

        .price {
            font-size: 1.6rem;
            margin: 15px 0;
        }

        .action-buttons {
            flex-direction: column;
            gap: 10px;
        }

        .btn {
            padding: 12px;
            font-size: 0.9rem;
            min-width: auto;
        }

        .details-section {
            padding: 18px 16px;
            margin: 12px auto;
        }

        .section-title {
        font-size: 1.2rem;
        margin-top: 25px;
        margin-bottom: 15px;
        }
        
        .section-title:first-of-type {
            margin-top: 0;
        }

        .spec-table {
            font-size: 0.85rem;
        }

        .spec-key, .spec-value {
            padding: 10px 8px;
        }

        .description-content {
            font-size: 0.9rem;
        }

        .color-btn {
            padding: 6px 16px;
            font-size: 0.85rem;
        }

        #ai-assistant-trigger {
            bottom: 70px;
            right: 12px;
            width: 45px;
            height: 45px;
        }

        #ai-assistant-trigger span {
            font-size: 20px;
        }

        #deep-analysis-modal > div {
            margin: 20% auto;
            padding: 18px;
        }

        #toast {
            font-size: 0.8rem;
            padding: 8px 16px;
            white-space: nowrap;
        }
    }

    </style>
</head>
<body>

<div id="ai-data" 
     data-name="<?php echo htmlspecialchars($product['product_name']); ?>"
     data-specs="<?php echo htmlspecialchars($spec_list_for_ai); ?>"
     data-desc="<?php echo htmlspecialchars($product['description']); ?>" 
     style="display:none;">
</div>

<div class="container">
    <div class="product-image-section">
        <img id="main-img" src="<?php echo htmlspecialchars($product['image_main']); ?>" alt="Main Image">
        <div class="thumb-container">
            <img src="<?php echo htmlspecialchars($product['image_main']); ?>" onclick="updateImage(this.src)">
            <?php foreach($additional_images as $img): ?>
                <img src="<?php echo htmlspecialchars($img); ?>" onclick="updateImage(this.src)">
            <?php endforeach; ?>
        </div>
    </div>

    <div class="product-info">
        <div class="seller-tag">
            <i class="fa-solid fa-store"></i> <?php echo htmlspecialchars($product['seller_name']); ?>
        </div>
        <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
        <div class="rating">
            ⭐ <?php echo $product['rating']; ?> | 📝 <?php echo $product['review_count']; ?> Reviews
        </div>

        <div class="price">RM <?php echo number_format($product['price'], 2); ?></div>

        <?php if(!empty($colors)): ?>
            <span class="spec-label">Select Color:</span>
            <div class="color-options">
                <?php foreach($colors as $index => $c): ?>
                    <button class="color-btn <?php echo $index === 0 ? 'active' : ''; ?>" onclick="selectColor(this)">
                        <?php echo htmlspecialchars($c); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="quantity-section">
            <label>Quantity:</label>
            <input type="number" id="selected-qty" value="1" min="1">
        </div>

        <div class="action-buttons">
            <button class="btn btn-cart" onclick="handleCart(<?php echo $product['product_id']; ?>)">
                <i class="fa-solid fa-cart-shopping"></i> Add to Cart
            </button>
            <button class="btn btn-compare" onclick="handleCompare(<?php echo $product['product_id']; ?>)">
                <i class="fa-solid fa-arrows-left-right"></i> Compare
            </button>
        </div>
    </div>
</div>

<div id="ai-assistant-trigger" onclick="event.stopPropagation(); runDeepInsight();">
    <span>✨</span>
</div>
    
<div id="deep-analysis-modal">
    <div>
        <span onclick="document.getElementById('deep-analysis-modal').style.display='none'" style="position: absolute; right: 18px; top: 12px; cursor: pointer; font-size: 22px; color: #aaa;">&times;</span>
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
            <span style="font-size: 24px;">🤖</span>
            <h2 style="margin: 0; color: #4b310b; font-size: 1.3rem;">Gemma AI Deep Insights</h2>
        </div>
        <div id="deep-analysis-content" style="font-size: 0.95rem; line-height: 1.7; color: #333; max-height: 400px; overflow-y: auto;">
        </div>
    </div>
</div>

<div class="details-section">
    <h3 class="section-title">Product Specifications</h3>
    <table class="spec-table">
        <?php 
        $specs->data_seek(0); 
        while($s = $specs->fetch_assoc()): ?>
            <tr>
                <td class="spec-key"><?php echo htmlspecialchars($s['spec_key']); ?></td>
                <td class="spec-value"><?php echo htmlspecialchars($s['spec_value']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <h3 class="section-title">Product Description</h3>
    <div class="description-content">
        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
    </div>

    <h3 class="section-title">Customer Reviews</h3>
    <div id="review-list" class="reviews-list">
        <?php if ($reviews->num_rows > 0): ?>
            <div id="initial-reviews">
                <?php 
                $count = 0;
                $reviews->data_seek(0);
                while($rev = $reviews->fetch_assoc()): 
                    $count++;
                    if($count <= 2): ?>
                        <div class="review-item">
                            <strong><?php echo htmlspecialchars($rev['username']); ?></strong> 
                            <span class="review-star">⭐ <?php echo $rev['rating']; ?></span>
                            <p><?php echo htmlspecialchars($rev['comment']); ?></p>
                        </div>
                    <?php endif; ?>
                <?php endwhile; ?>
            </div>

            <div id="more-reviews-container" style="display:none;">
                <?php 
                $reviews->data_seek(0);
                $count = 0;
                while($rev = $reviews->fetch_assoc()): 
                    $count++;
                    if($count > 2): ?>
                        <div class="review-item">
                            <strong><?php echo htmlspecialchars($rev['username']); ?></strong> 
                            <span class="review-star">⭐ <?php echo $rev['rating']; ?></span>
                            <p><?php echo htmlspecialchars($rev['comment']); ?></p>
                        </div>
                    <?php endif; ?>
                <?php endwhile; ?>
            </div>

            <?php if($reviews->num_rows > 2): ?>
                <span class="toggle-reviews" id="review-toggle-btn">Show all reviews</span>
            <?php endif; ?>

        <?php else: ?>
            <p>No reviews yet.</p>
        <?php endif; ?>
    </div>
</div>

<div id="toast"></div>

<script>
function updateImage(src) {
    document.getElementById('main-img').src = src;
}

function selectColor(btn) {
    document.querySelectorAll('.color-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

function showToast(message) {
    const toast = document.getElementById('toast');
    toast.innerText = message;
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

function handleCart(pid) {
    if(!pid) return;

    const activeBtn = document.querySelector('.color-btn.active');
    const color = activeBtn ? activeBtn.innerText.trim() : 'Standard';
    const qty = document.getElementById('selected-qty').value || 1;
    const url = `add_to_cart.php?id=${pid}&color=${encodeURIComponent(color)}&qty=${qty}`;

    fetch(url)
        .then(response => response.text())
        .then(data => {
            showToast('🛒 Added to Cart!');
            
            const cartBadge = document.getElementById('cart-count');
            if(cartBadge) {
                let currentCount = parseInt(cartBadge.innerText) || 0;
                cartBadge.innerText = currentCount + parseInt(qty); 
            }
        })
        .catch(err => console.error('Error:', err));
}

function handleCompare(pid) {
    if(!pid) return;

    fetch('add_to_compare.php?id=' + pid)
        .then(response => response.text())
        .then(data => {
            showToast('🔄 Added to Compare List!');
            const compareBadge = document.getElementById('compare-count');
            if(compareBadge) {
                let currentCount = parseInt(compareBadge.innerText) || 0;
                compareBadge.innerText = currentCount + 1;
            }
        });
}

let reviewsExpanded = false;
const toggleBtn = document.getElementById('review-toggle-btn');
const moreContainer = document.getElementById('more-reviews-container');

if (toggleBtn) {
    toggleBtn.addEventListener('click', function() {
        if (!reviewsExpanded) {
            moreContainer.style.display = 'block';
            toggleBtn.innerText = 'Hide reviews';
            reviewsExpanded = true;
        } else {
            moreContainer.style.display = 'none';
            toggleBtn.innerText = 'Show all reviews';
            reviewsExpanded = false;
        }
    });
}

async function runDeepInsight() {
    console.log("Deep Insight Activation..."); 
    const modal = document.getElementById('deep-analysis-modal');
    const content = document.getElementById('deep-analysis-content');
    
    content.innerHTML = "🌀 <b>Gemma 3</b> is analyzing specifications and reviews...";
    modal.style.display = 'block';

    const dataDiv = document.getElementById('ai-data');
    const reviewsDiv = document.getElementById('review-list'); 

    const pData = {
        name: dataDiv.getAttribute('data-name'),
        specs: dataDiv.getAttribute('data-specs'),
        desc: dataDiv.getAttribute('data-desc'),
        reviews: reviewsDiv ? reviewsDiv.innerText.substring(0, 1000) : "No reviews available."
    };

    try {
        const response = await fetch('https://fyp-ai-backend.onrender.com/analyze_product_deep', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(pData)
        });

        const data = await response.json();
        
        if(data.analysis) {
            content.innerHTML = data.analysis; 
        } else {
            content.innerHTML = "⚠️ Analysis content missing.";
        }
    } catch (err) {
        content.innerHTML = "<div style='color:red; padding:20px;'>❌ Connection Error. Please ensure Flask (app.py) is running on port 5000.</div>";
    }
}

window.addEventListener('click', (e) => {
    const modalIds = ['review-modal', 'deep-analysis-modal', 'compare-modal', 'ai-modal'];

    modalIds.forEach(id => {
        const modal = document.getElementById(id);
        if (modal && e.target === modal) {
            modal.style.display = 'none';
        }
    });
});
</script>

<?php include('footer.php'); ?>
</body>
</html>
