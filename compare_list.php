<?php
session_start();
include('ConnectDB.php');
include('header.php');
include('navbar.php');

$user_id = 1; 

$sql = "
    SELECT p.*, s.seller_name
    FROM compare_list c
    JOIN products p ON c.product_id = p.product_id
    JOIN sellers s ON p.seller_id = s.seller_id
    WHERE c.user_id = ?
    ORDER BY p.category ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
$ai_data_list = [];

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
    $p_id = $row['product_id'];
    $rev_res = $conn->query("SELECT comment FROM reviews WHERE product_id = $p_id LIMIT 3");
    $reviews = [];
    while($r = $rev_res->fetch_assoc()) { $reviews[] = $r['comment']; }
    
    $ai_data_list[] = [
        'name' => $row['product_name'],
        'specs' => $row['description'],
        'category' => $row['category'],
        'seller_name' => $row['seller_name'],
        'price' => $row['price'],
        'rating' => $row['rating'], 
        'reviews' => implode(" | ", $reviews) ?: "No reviews yet."
    ];
}
$js_ai_data = json_encode($ai_data_list);
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Compare Products</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background: #f9f9f9;
        }

        .compare-container { 
            max-width: 95%; 
            margin: 30px auto; 
            padding: 0 20px 80px 20px; 
        }

        .compare-title {
            font-size: 2rem;
            color: #4b310b;
            margin-bottom: 30px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .compare-title i {
            font-size: 1.8rem;
        }

        .compare-grid { 
            display: flex; 
            gap: 20px; 
            overflow-x: auto; 
            padding-bottom: 20px; 
            align-items: stretch; 
        }
        
        .compare-card { 
            flex: 0 0 320px; 
            background: #fff; 
            border-radius: 20px; 
            padding: 20px; 
            box-shadow: 0 5px 20px rgba(0,0,0,0.05); 
            text-align: center; 
            position: relative; 
            transition: 0.3s; 
            border: 1px solid #eee; 
        }

        .compare-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border-color: #ceb9a0; 
        }
        
        .compare-card.selected { 
            border: 2px solid #4b310b; 
            background: #fffdf9; 
        }

        .compare-img { 
            width: 100%; 
            height: 200px; 
            object-fit: cover; 
            border-radius: 15px; 
            margin-bottom: 15px; 
        }

        .compare-name { 
            font-size: 1.2rem; 
            font-weight: bold; 
            color: #4b310b; 
            height: 55px; 
            overflow: hidden; 
            margin-bottom: 12px;
            line-height: 1.3;
        }

        .compare-info-row { 
            padding: 10px 0; 
            border-top: 1px dashed #eee; 
            font-size: 0.9rem; 
        }

        .compare-price { 
            font-size: 1.5rem; 
            color: #E53935; 
            font-weight: bold;
            margin: 8px 0; 
        }
        
        .compare-checkbox { 
            position: absolute; 
            top: 15px; 
            left: 15px; 
            width: 22px;
            height: 22px;
            cursor: pointer; 
            z-index: 5; 
            accent-color: #4b310b;
        }
       
        .btn-common {
            display: block;
            width: 100%;
            padding: 12px 0; 
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            text-align: center; 
            cursor: pointer;
            border: none; 
            transition: 0.3s;
            font-size: 0.9rem;
            box-sizing: border-box; 
        }
        
        .btn-view {
            background: #ceb9a0;
            color: #fff;
            margin-bottom: 10px;
        }
        .btn-view:hover { 
            background: #b8a58e; 
            transform: translateY(-2px);
        }
        
        .btn-remove {
            background: transparent;
            color: #ff4d4d;
            border: 1px solid #ff4d4d;
        }
        .btn-remove:hover { 
            background: #ff4d4d;
            color: #fff;
            transform: translateY(-2px);
        }

        #ai-btn { 
            position: fixed; 
            bottom: 30px; 
            right: 30px; 
            background: #4b310b; 
            color: white; 
            padding: 14px 28px; 
            border-radius: 50px; 
            cursor: pointer; 
            display: none; 
            box-shadow: 0 5px 20px rgba(0,0,0,0.2); 
            z-index: 1000; 
            font-weight: bold;
            font-size: 0.95rem;
            transition: 0.3s;
        }
        
        #ai-btn:hover {
            transform: scale(1.02);
            background: #6d4a16;
        }
        
        .custom-toast {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #4CAF50;
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            display: none;
            z-index: 9999;
            font-size: 0.9rem;
            font-weight: 500;
            white-space: nowrap;
            align-items: center;
            gap: 10px;
        }

        .custom-toast.show {
            display: flex;
            animation: fadeInUp 0.3s ease-out;
        }

        .custom-toast.error {
            background-color: #f44336;
        }

        .custom-toast.warning {
            background-color: #ff9800;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        .compare-modal {
            display: none;
            position: fixed;
            z-index: 10001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
        }

        .compare-modal-content {
            background: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 20px;
            width: 80%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
        }

        .modal-close {
            position: absolute;
            right: 20px;
            top: 15px;
            cursor: pointer;
            font-size: 28px;
            color: #999;
            transition: 0.2s;
        }
        .modal-close:hover {
            color: #333;
        }

        .modal-title {
            color: #4b310b;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .report-body {
            line-height: 1.8;
            font-size: 0.95rem;
            color: #333;
        }

        /* 空状态样式 */
        .empty-compare {
            text-align: center;
            padding: 80px 20px;
            background: #fff;
            border-radius: 20px;
        }

        .empty-compare i {
            font-size: 4rem;
            color: #ddd;
        }

        .empty-compare h2 {
            color: #999;
            margin-top: 20px;
        }

        .empty-compare a {
            display: inline-block;
            margin-top: 20px;
            background: #4b310b;
            color: #fff;
            padding: 12px 30px;
            border-radius: 12px;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .compare-container { 
                margin: 20px auto; 
                padding: 0 12px 80px 12px;
            }

            .compare-title {
                font-size: 1.5rem;
                margin-bottom: 20px;
            }

            .compare-card {
                flex: 0 0 280px;
                padding: 15px;
            }

            .compare-img {
                height: 160px;
            }

            .compare-name {
                font-size: 1rem;
                height: 50px;
            }

            .compare-price {
                font-size: 1.3rem;
            }

            .compare-info-row {
                font-size: 0.8rem;
            }

            .btn-common {
                padding: 10px 0;
                font-size: 0.85rem;
            }

            #ai-btn {
                bottom: 20px;
                right: 20px;
                padding: 12px 20px;
                font-size: 0.85rem;
            }

            .custom-toast {
                bottom: 80px;
                font-size: 0.8rem;
                white-space: normal;
                text-align: center;
                max-width: 90%;
                padding: 10px 20px;
            }

            .compare-modal-content {
                width: 95%;
                margin: 10% auto;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="compare-container">
    <div class="compare-title">
        <i class="fa-solid fa-scale-balanced"></i>
        Smart Comparison (<?php echo count($products); ?>)
    </div>

    <?php if (count($products) > 0): ?>
        <div class="compare-grid">
            <?php foreach($products as $p): ?>
            <div class="compare-card" data-catid="<?php echo $p['category']; ?>">
                <input type="checkbox" class="compare-checkbox" data-name="<?php echo htmlspecialchars($p['product_name']); ?>" onclick="syncSelection(this)">
                
                <img src="<?php echo htmlspecialchars($p['image_main']); ?>" class="compare-img" alt="<?php echo htmlspecialchars($p['product_name']); ?>">
                <div class="compare-name"><?php echo htmlspecialchars($p['product_name']); ?></div>
                
                <div class="compare-price">RM <?php echo number_format($p['price'], 2); ?></div>

                <div class="compare-info-row">
                    <i class="fa-solid fa-store" style="color: #8e5c12; margin-right: 5px;"></i>
                    <?php echo htmlspecialchars($p['seller_name']); ?>
                </div>

                <div class="compare-info-row">
                    <i class="fa-solid fa-tag" style="color: #8e5c12; margin-right: 5px;"></i>
                    <?php echo htmlspecialchars($p['category']); ?>
                </div>

                <div style="margin-top: 20px;">
                    <a href="Product_details.php?id=<?php echo $p['product_id']; ?>" 
                       class="btn-common btn-view">
                       <i class="fa-solid fa-eye"></i> View Details
                    </a>

                    <button type="button" class="btn-common btn-remove" 
                            onclick="removeFromCompare(<?php echo $p['product_id']; ?>, this)">
                        <i class="fa-solid fa-trash-can"></i> Remove
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-compare">
            <i class="fa-solid fa-scale-balanced"></i>
            <h2>No products in compare list</h2>
            <a href="HomePage.php">Browse Products</a>
        </div>
    <?php endif; ?>
</div>

<div id="ai-btn" onclick="triggerAICompare()">
    <i class="fa-solid fa-robot"></i> AI Compare Selected (<span id="count">0</span>)
</div>

<div id="compare-modal" class="compare-modal">
    <div class="compare-modal-content">
        <span class="modal-close" onclick="closeModal()">&times;</span>
        <div class="modal-title">
            <i class="fa-solid fa-brain" style="font-size: 1.5rem;"></i>
            <h2 style="margin: 0;">AI Analysis (Specs + Reviews)</h2>
        </div>
        <hr>
        <div id="report-body" class="report-body"></div>
    </div>
</div>

<div id="custom-toast" class="custom-toast"></div>

<script>
const aiData = <?php echo $js_ai_data; ?>;

function showToast(message, type = 'success') {
    const toast = document.getElementById('custom-toast');
    toast.innerHTML = `<i class="fa-solid ${type === 'success' ? 'fa-circle-check' : (type === 'error' ? 'fa-circle-exclamation' : 'fa-triangle-exclamation')}"></i> ${message}`;
    toast.className = 'custom-toast';
    if (type === 'error') {
        toast.classList.add('error');
    } else if (type === 'warning') {
        toast.classList.add('warning');
    }
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

function syncSelection(cb) {
    const card = cb.closest('.compare-card');
    if (card) {
        card.classList.toggle('selected', cb.checked);
    }
    const checkedCount = document.querySelectorAll('.compare-checkbox:checked').length;
    const btn = document.getElementById('ai-btn');
    btn.style.display = checkedCount >= 2 ? 'flex' : 'none';
    document.getElementById('count').innerText = checkedCount;
}

async function triggerAICompare() {
    const checked = document.querySelectorAll('.compare-checkbox:checked');
    const selected = [];
    const cats = new Set();

    if (checked.length < 2) {
        showToast('Please select at least 2 products to compare', 'warning');
        return;
    }

    checked.forEach(cb => {
        const item = aiData.find(p => p.name === cb.getAttribute('data-name'));
        if (item) {
            selected.push(item);
            cats.add(item.category);
        }
    });

    if (cats.size > 1) {
        showToast('Please select items from the same category for accurate comparison', 'warning');
        return;
    }

    const modal = document.getElementById('compare-modal');
    const body = document.getElementById('report-body');
    modal.style.display = 'block';
    body.innerHTML = '<div style="text-align: center; padding: 40px;"><i class="fa-solid fa-spinner fa-pulse"></i> AI is analyzing specifications and user sentiments...</div>';

    try {
        const res = await fetch('https://fyp-ai-backend.onrender.com/compare_products_ai', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ products: selected })
        });
        const data = await res.json();
        body.innerHTML = data.analysis || '<p style="color: red;">No analysis available</p>';
    } catch (e) {
        body.innerHTML = '<p style="color: red;">❌ AI server connection failed. Please try again later.</p>';
        showToast('AI server connection failed', 'error');
    }
}

function closeModal() { 
    document.getElementById('compare-modal').style.display = 'none'; 
}

function removeFromCompare(productId, btn) {
    const card = btn.closest('.compare-card');
    
    fetch(`remove_from_compare.php?id=${productId}`)
        .then(response => {
            if (response.ok) {
                card.remove();
                const remainingCards = document.querySelectorAll('.compare-card').length;
                if (remainingCards === 0) {
                    location.reload();
                }
                syncSelection(document.querySelector('.compare-checkbox'));
                showToast('Removed from compare list!', 'success');
            } else {
                showToast('Failed to remove item', 'error');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            showToast('Error removing item', 'error');
        });
}

window.addEventListener('click', (e) => {
    const modal = document.getElementById('compare-modal');
    if (modal && e.target === modal) {
        modal.style.display = 'none';
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const checkedCount = document.querySelectorAll('.compare-checkbox:checked').length;
    const btn = document.getElementById('ai-btn');
    if (checkedCount >= 2) {
        btn.style.display = 'flex';
    }
});
</script>

<?php include('footer.php'); ?>
</body>
</html>
