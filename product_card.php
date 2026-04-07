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
    gap: 25px;
    padding: 20px;
    margin: 0 auto;
    max-width: 1400px;
    width: 100%;
    box-sizing: border-box;
}

.product-card {
    border: 1px solid #eee;
    padding: 14px;
    text-align: center;
    background: #fff;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    justify-content: space-between;
    width: 100%;
    height: auto;
    min-height: 420px;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    box-sizing: border-box;
}

.product-card:hover {
    border-color: #ceb9a0;
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}

.product-image-wrapper {
    width: 100%;
    aspect-ratio: 1/1;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 10px;
    flex-shrink: 0;
    background: #f5f5f5;
}

.product-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-card h3 {
    font-size: 1rem;
    font-weight: 600;
    margin: 8px 0 6px;
    color: #333;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    min-height: 2.4rem;
    line-height: 1.4;
}

.seller-rating {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    font-size: 0.8rem;
    color: #666;
    margin: 6px 0;
    gap: 8px;
}

.seller-info {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 65%;
}

.rating {
    color: #f5b342;
    font-weight: 600;
    white-space: nowrap;
}

.product-card .price {
    font-size: 1.3rem;
    font-weight: bold;
    color: #E53935;
    margin: 8px 0;
}

.product-actions {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 10px;
    width: 100%;
}

.product-actions button {
    flex: 1;
    padding: 10px 8px;
    font-size: 0.85rem;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s;
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

.toast {
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    background: #4CAF50;
    color: white;
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 0.85rem;
    opacity: 0;
    transition: 0.3s;
    z-index: 10001;
    white-space: nowrap;
    pointer-events: none;
}

.toast.show {
    opacity: 1;
    transform: translateX(-50%) translateY(-10px);
}

.toast.error {
    background: #f44336;
}

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(2px);
}

.modal-content {
    background: #fff;
    margin: 10% auto;
    padding: 25px 30px;
    border-radius: 16px;
    width: 90%;
    max-width: 550px;
    position: relative;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.close {
    position: absolute;
    top: 12px;
    right: 15px;
    cursor: pointer;
    font-size: 1.5rem;
    font-weight: bold;
    color: #999;
    transition: 0.2s;
}

.close:hover {
    color: #E53935;
}

.modal-content h3 {
    font-size: 1.3rem;
    color: #4b310b;
    margin-bottom: 15px;
}

#review-text {
    font-size: 0.9rem;
    line-height: 1.6;
    color: #555;
    max-height: 400px;
    overflow-y: auto;
}

.loading-spinner {
    text-align: center;
    padding: 40px;
    color: #4b310b;
}

.loading-spinner i {
    font-size: 2rem;
    margin-bottom: 12px;
    color: #ceb9a0;
}

@media (max-width: 768px) {
    .product-container {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        padding: 12px;
    }

    .product-card {
        padding: 10px;
        min-height: 360px;
    }

    .product-card h3 {
        font-size: 0.85rem;
        min-height: 2rem;
    }

    .seller-rating {
        font-size: 0.7rem;
        flex-wrap: wrap;
        gap: 4px;
    }
    
    .seller-info {
        max-width: 100%;
        white-space: normal;
        word-break: break-word;
    }

    .product-card .price {
        font-size: 1.1rem;
        margin: 6px 0;
    }

    .product-actions {
        gap: 6px;
    }

    .product-actions button {
        padding: 8px 4px;
        font-size: 0.7rem;
    }
    
    .product-actions button i {
        font-size: 0.7rem;
    }

    .modal-content {
        margin: 25% auto;
        padding: 18px;
        width: 95%;
    }
    
    .modal-content h3 {
        font-size: 1rem;
    }
    
    .toast {
        bottom: 80px;
        font-size: 0.75rem;
        white-space: normal;
        text-align: center;
        max-width: 80%;
    }
    
    .loading-spinner {
        padding: 30px;
    }
    
    .loading-spinner i {
        font-size: 1.5rem;
    }
}

</style>

<div id="review-modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('review-modal').style.display='none'">&times;</span>
        <h3><i class="fa-solid fa-chart-simple"></i> 📊 Summarize Review</h3>
        <div id="review-text">
            <div class="loading-spinner">
                <i class="fa-solid fa-spinner fa-pulse"></i><br>
                Loading reviews...
            </div>
        </div>
    </div>
</div>

<div class="product-card" data-id="<?php echo $current_id; ?>" onclick="if(!event.target.closest('button')) window.location.href='Product_details.php?id=<?php echo $current_id; ?>'">
    <div class="product-image-wrapper">
        <img src="<?php echo htmlspecialchars($row['image_main']); ?>" alt="Product" loading="lazy">
    </div>
    
    <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>

    <div class="seller-rating">
        <span class="seller-info"><i class="fa-solid fa-store"></i> <?php echo htmlspecialchars($row['seller_name']); ?></span>
        <span class="rating"><i class="fa-solid fa-star"></i> <?php echo htmlspecialchars($row['rating']); ?></span>
    </div>

    <p class="price">RM <?php echo number_format($row['price'], 2); ?></p>

    <div class="product-actions">
        <button class="review-summary" onclick="event.stopPropagation(); showReviewSummary(<?php echo $current_id; ?>)">
            <i class="fa-solid fa-robot"></i> AI Review
        </button>
    
        <button 
            class="add-compare <?php echo $is_in_list ? 'in-list' : ''; ?>" 
            data-inlist="<?php echo $is_in_list ? '1' : '0'; ?>"
            onclick="handleCompare(this, <?php echo $current_id; ?>, event)">
            <i class="fa-solid <?php echo $is_in_list ? 'fa-check' : 'fa-plus'; ?>"></i>
            <?php echo $is_in_list ? ' Compare' : ' Compare'; ?>
        </button>
    </div>
</div>

<script>
function showReviewSummary(productId) {
    const modal = document.getElementById('review-modal');
    const reviewText = document.getElementById('review-text');
    
    if (!modal) {
        showToast('Error: Review modal not found', true);
        return;
    }
    
    modal.style.display = 'block';
    
    reviewText.innerHTML = '<div class="loading-spinner"><i class="fa-solid fa-spinner fa-pulse"></i><br>AI is analyzing reviews...</div>';
    
    fetch(`get_review_summary.php?id=${productId}`)
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
            reviewText.innerHTML = '<div style="color: #E53935; text-align: center; padding: 40px;"><i class="fa-solid fa-circle-exclamation"></i><br>Error fetching review summary<br><small>Please try again later</small></div>';
            showToast('Failed to load reviews', true);
        });
}

function handleCompare(btn, productId, e) {
    e.stopPropagation();

    if (btn.dataset.inlist === "1") {
        showToast("Already in compare list", true);
        return;
    }

    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-pulse"></i> Adding...';
    btn.style.pointerEvents = "none";
    btn.style.opacity = "0.7";

    fetch(`add_to_compare.php?id=${productId}`)
        .then(response => {
            if (response.status === 200) {
                showToast("Added to compare list!");
                setTimeout(() => { 
                    window.location.reload(); 
                }, 800);
            } else if (response.status === 409) {
                showToast("Already in compare list", true);
                btn.innerHTML = originalHTML;
                btn.style.pointerEvents = "auto";
                btn.style.opacity = "1";
            } else {
                throw new Error(`Server returned ${response.status}`);
            }
        })
        .catch(error => {
            console.error('Compare error:', error);
            showToast("Error adding to compare list", true);
            btn.innerHTML = originalHTML;
            btn.style.pointerEvents = "auto";
            btn.style.opacity = "1";
        });
}

function showToast(message, isError = false) {
    let toast = document.getElementById("toast");
    
    if (!toast) {
        toast = document.createElement("div");
        toast.id = "toast";
        toast.className = "toast";
        document.body.appendChild(toast);
    }

    toast.innerHTML = message;
    toast.className = "toast" + (isError ? " error" : "");
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
