<?php
$current_id = $row['product_id'];
$u_id = 1; 

$db_check = $conn->query("SELECT 1 FROM compare_list WHERE product_id = $current_id AND user_id = $u_id");
$is_in_list = ($db_check && $db_check->num_rows > 0);
?>

<style>
.product-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 40px;
    padding: 20px;
    margin: 0 auto;
    max-width: 1200px;
    width: 100%;
    box-sizing: border-box;
}

.product-card {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: center;
    background: #fff;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    justify-content: space-between;
    width: 100%;
    height: auto;
    min-height: 480px;
    cursor: pointer;
    transition: border .3s, transform .2s;
    position: relative;
}

.product-card:hover {
    border: 2px solid #947b54ff;
    transform: translateY(-3px);
}

.product-image-wrapper {
    width: 100%;
    aspect-ratio: 1/1;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 8px;
    flex-shrink: 0;
    background: #f5f5f5;
}

.product-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-card h3 {
    font-size: 1.1rem;
    font-weight: bold;
    margin: 8px 0 6px;
    color: #333;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    min-height: 2.5rem;
}

.product-card p {
    font-size: 0.95rem;
    margin: 4px 0;
    color: #555;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.seller-rating {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    font-size: 0.9rem;
    color: #555;
    margin: 6px 0;
    gap: 5px;
}

.rating {
    color: #ecd53a;
    font-weight: 600;
}

.product-card .price {
    font-size: 1.5rem;
    font-weight: bold;
    color: #E53935;
    margin: 6px 0;
}

.product-actions {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: auto;
    width: 100%;
}

.product-actions button {
    flex: 1;
    padding: 8px 6px;
    font-size: 0.9rem;
    font-weight: 600;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    transition: .3s;
    box-shadow: 0 2px 5px rgba(0, 0, 0, .15);
    white-space: nowrap;
}

.review-summary {
    background: linear-gradient(135deg, #FFC107, #FFB300);
    color: #333;
}

.review-summary:hover {
    background: linear-gradient(135deg, #FFB300, #FFA000);
    transform: translateY(-2px);
}

.add-compare {
    background: linear-gradient(135deg, #2196F3, #1E88E5);
}

.add-compare:hover {
    background: linear-gradient(135deg, #1E88E5, #1976D2);
    transform: translateY(-2px);
}

.add-compare.in-list {
    background: linear-gradient(135deg, #9E9E9E, #757575) !important;
    cursor: not-allowed;
    transform: none !important;
}

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, .6);
    backdrop-filter: blur(2px);
}

.modal-content {
    background: #fff;
    margin: 5% auto;
    padding: 25px 30px;
    border-radius: 12px;
    width: 80%;
    max-width: 600px;
    position: relative;
    box-shadow: 0 10px 30px rgba(0, 0, 0, .3);
}

.close {
    position: absolute;
    top: 12px;
    right: 15px;
    cursor: pointer;
    font-size: 1.6rem;
    font-weight: bold;
    color: #555;
}

.close:hover {
    color: #E53935;
}

.modal-content h3 {
    font-size: 1.8rem;
    color: #333;
    margin-bottom: 15px;
}

#review-text {
    font-size: 1rem;
    line-height: 1.6;
    color: #555;
    max-height: 400px;
    overflow-y: auto;
}

.toast {
    position: fixed;
    bottom: -100px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(10px);
    color: #fff;
    padding: 12px 25px;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 500;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    z-index: 10001;
    transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    white-space: nowrap;
}

.toast.show {
    opacity: 1;
    bottom: 50px;
    transform: translateX(-50%) translateY(0);
}

@media (max-width: 768px) {
    .product-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;         
        padding: 10px;     
        margin: 0 auto;
        width: 100%;
        box-sizing: border-box;
    }

    .product-card {
        width: 100%;
        min-height: auto;     
        height: auto;
        padding: 8px;        
        margin: 0;
        display: flex;
        flex-direction: column;
        box-sizing: border-box;
        border: 1px solid #ddd;
    }

    .product-image-wrapper {
        width: 100%;
        aspect-ratio: 1/1;
        margin-bottom: 6px; 
        flex-shrink: 0;
    }

    .product-card h3 {
        font-size: 0.85rem;   
        margin: 4px 0 3px;
        line-height: 1.3;
        min-height: 2.2em;
    }

    .product-card .seller-info {
        font-size: 0.65rem;   
    }

    .seller-rating {
        font-size: 0.7rem;    
        margin: 3px 0;
    }

    .product-card .price {
        font-size: 1rem;      
        margin: 4px 0;
    }

    .product-actions {
        gap: 6px;             
        margin-top: 6px;
    }

    .product-actions button {
        padding: 6px 3px;     
        font-size: 0.7rem;    
        min-height: 32px;     
    }
}
</style>

<div id="review-modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('review-modal').style.display='none'">&times;</span>
        <h3>📊 AI Review Summary</h3>
        <div id="review-text">
            <div class="spinner">🔄 Loading reviews...</div>
        </div>
    </div>
</div>

<div class="product-card" data-id="<?php echo $current_id; ?>" onclick="if(!event.target.closest('button')) window.location.href='Product_details.php?id=<?php echo $current_id; ?>'">
    <div class="product-image-wrapper">
        <img src="<?php echo htmlspecialchars($row['image_main']); ?>" alt="Product" loading="lazy">
    </div>
    
    <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>

    <div class="seller-rating">
        <span class="seller-info">Seller: <?php echo htmlspecialchars($row['seller_name']); ?></span>
        <span class="rating">⭐ <?php echo htmlspecialchars($row['rating']); ?></span>
    </div>

    <p class="price">RM <?php echo number_format($row['price'], 2); ?></p>

    <div class="product-actions">
        <button class="review-summary" onclick="event.stopPropagation(); showReviewSummary(<?php echo $current_id; ?>)">
            📊 Review Summary
        </button>
    
        <button 
            class="add-compare <?php echo $is_in_list ? 'in-list' : ''; ?>" 
            data-inlist="<?php echo $is_in_list ? '1' : '0'; ?>"
            onclick="handleCompare(this, <?php echo $current_id; ?>, event)">
            <?php echo $is_in_list ? '✔ In Compare List' : '+ Compare'; ?>
        </button>
    </div>
</div>

<script>
function showReviewSummary(productId) {
    const modal = document.getElementById('review-modal');
    const reviewText = document.getElementById('review-text');
    
    if (!modal) {
        console.error('Review modal not found');
        showToast('Error: Review modal not found');
        return;
    }
    
    modal.style.display = 'block';
    reviewText.innerHTML = '<div class="spinner">🌀 <b>Gemma 3</b> is analyzing reviews...</div>';
    
    const url = `get_review_summary.php?id=${productId}`;
    
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(data => {
            reviewText.innerHTML = data;
        })
        .catch(err => {
            console.error('Fetch error:', err);
            reviewText.innerHTML = '❌ Error fetching review summary. Please try again later.<br><small>' + err.message + '</small>';
            showToast('Failed to load reviews');
        });
}

function handleCompare(btn, productId, e) {
    e.stopPropagation();

    if (btn.dataset.inlist === "1") {
        showToast("⚠️ Already in compare list");
        return;
    }

    btn.style.pointerEvents = "none";
    btn.style.opacity = "0.7";

    fetch(`add_to_compare.php?id=${productId}`)
        .then(response => {
            if (response.status === 200) {
                showToast("✅ Added to compare list!");
                setTimeout(() => { 
                    window.location.reload(); 
                }, 800);
            } else if (response.status === 409) {
                showToast("⚠️ Already in compare list");
                btn.style.pointerEvents = "auto";
                btn.style.opacity = "1";
            } else {
                throw new Error(`Server returned ${response.status}`);
            }
        })
        .catch(error => {
            console.error('Compare error:', error);
            showToast("❌ Error adding to compare list");
            btn.style.pointerEvents = "auto";
            btn.style.opacity = "1";
        });
}

function showToast(message) {
    let toast = document.getElementById("toast");
    
    if (!toast) {
        toast = document.createElement("div");
        toast.id = "toast";
        toast.className = "toast";
        document.body.appendChild(toast);
    }

    toast.innerHTML = message;
    toast.classList.add("show");

    setTimeout(() => {
        toast.classList.remove("show");
    }, 2500);
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

document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('click', (e) => {
        if (e.target.closest('button')) {
            return;
        }
        
        const productId = card.dataset.id;
        if (productId) {
            window.location.href = `Product_details.php?id=${productId}`;
        }
    });
});
</script>
