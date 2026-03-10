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
        'reviews' => implode(" | ", $reviews) ?: "No reviews yet."
    ];
}
$js_ai_data = json_encode($ai_data_list);
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .compare-container { 
            max-width: 95%; 
            margin: 40px auto; 
            padding: 0 20px; 
        }

        .compare-grid { 
            display: flex; 
            gap: 20px; 
            overflow-x: auto; 
            padding-bottom: 30px; 
            align-items: stretch; 
        }
        
        .compare-card { 
            flex: 0 0 320px; 
            background: #fff; 
            border-radius: 20px; 
            padding: 25px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
            text-align: center; 
            position: relative; 
            transition: 0.3s; 
            border: 1px solid #eee; 
        }
        .compare-card:hover { 
            transform: translateY(-10px); 
            border-color: #ceb9a0ff; 
        }
        
        .compare-card.selected { 
            border: 2px solid #4b310b; 
            background: #fffdf9; 
        }

        .compare-img { 
            width: 100%; 
            height: 250px; 
            object-fit: cover; 
            border-radius: 15px; 
            margin-bottom: 20px; 
        }

        .compare-name { 
            font-size: 1.4rem; 
            font-weight: bold; 
            color: #4b310b; 
            height: 60px; 
            overflow: hidden; 
            margin-bottom: 15px; 
        }

        .compare-info-row { 
            padding: 12px 0; 
            border-top: 1px dashed #eee; 
            font-size: 1rem; 
        }

        .compare-price { 
            font-size: 1.8rem; 
            color: #E53935; 
            font-weight: bold;
             margin: 10px 0; 
            }
        
        .compare-checkbox { 
            position: absolute; 
            top: 20px; 
            left: 20px; 
            transform: scale(1.5); 
            cursor: pointer; 
            z-index: 5; 
        }
       

        .btn-remove-action { 
            display: block; 
            padding: 12px; 
            background: #ff4d4d; 
            color: #fff; 
            border-radius: 10px; 
            text-decoration: none; 
            font-weight: bold; 
            margin-top: 18px; 
            transition: 0.3s;
        }
        
        .btn-remove-action:hover { 
            background: #cc0000; 
        }

        #ai-btn { 
            position: fixed; 
            bottom: 30px; 
            right: 30px; 
            background: #4b310b; 
            color: white; 
            padding: 15px 30px; 
            border-radius: 50px; 
            cursor: pointer; 
            display: none; 
            box-shadow: 0 10px 20px rgba(0,0,0,0.2); 
            z-index: 1000; 
            font-weight: bold;
        }
                
        #custom-toast {
            visibility: hidden;
            min-width: 350px;
            background-color: #ffffff; 
            color: #d32f2f; 
            text-align: left;
            border-radius: 12px;
            padding: 16px 20px;
            position: fixed;
            z-index: 10002;
            left: 50%;
            top: 30px; 
            transform: translateX(-50%);
            font-weight: 600;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2); 
            border-left: 5px solid #d32f2f; 
            display: flex;
            align-items: center;
            gap: 12px;
            opacity: 0;
            transition: opacity 0.4s, top 0.4s;
        }

        #custom-toast.show {
            visibility: visible;
            opacity: 1;
            top: 50px; 
        }

        .toast-icon {
            font-size: 20px;
        }
        @keyframes fadein { from {bottom: 0; opacity: 0;} to {bottom: 50px; opacity: 1;} }
        @keyframes fadeout { from {bottom: 50px; opacity: 1;} to {bottom: 0; opacity: 0;} }
    
        @media (max-width: 768px) {
        .compare-container { 
            max-width: 95%; 
            margin: 35px auto; 
            padding: 0 20px; 
        }
    }
    </style>
</head>
<body>

<div id="custom-toast">Kindly note</div>

<div class="compare-container">
    <h1 style="color: #4b310b; margin-bottom: 30px;">⚖️ Smart Comparison</h1>

    <div class="compare-grid">
        <?php foreach($products as $p): ?>
        <div class="compare-card" data-catid="<?php echo $p['category']; ?>">
            <input type="checkbox" class="compare-checkbox" data-name="<?php echo htmlspecialchars($p['product_name']); ?>" onclick="syncSelection(this)">
            
            <img src="<?php echo htmlspecialchars($p['image_main']); ?>" class="compare-img">
            <div class="compare-name"><?php echo htmlspecialchars($p['product_name']); ?></div>
            
            <div class="compare-info-row">
                <div class="compare-price">RM <?php echo number_format($p['price'], 2); ?></div>
            </div>

            <div class="compare-info-row">
                <span style="color: #888;">Seller</span><br>
                <strong><?php echo htmlspecialchars($p['seller_name']); ?></strong>
            </div>

            <div style="margin-top: 15px;">
                <a href="Product_details.php?id=<?php echo $p['product_id']; ?>" 
                   style="display: block; padding: 12px; background: #ceb9a0; color: #fff; border-radius: 10px; text-decoration: none; font-weight: bold; margin-bottom: 8px;">
                   View Details
                </a>
                
                <button type="button" class="btn-remove-action" 
                        onclick="removeFromCompare(<?php echo $p['product_id']; ?>, this)">
                    Remove Product
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div id="ai-btn" onclick="triggerAICompare()">✨ AI Compare Selected (<span id="count">0</span>)</div>

<div id="compare-modal" style="display:none; position: fixed; z-index: 10001; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6);">
    <div style="background: white; margin: 5% auto; padding: 30px; border-radius: 20px; width: 80%; max-height: 80vh; overflow-y: auto; position: relative;">
        <span onclick="closeModal()" style="position: absolute; right: 20px; top: 15px; cursor: pointer; font-size: 28px;">&times;</span>
        <h2 style="color: #4b310b;">🤖 AI Analysis (Specs + Reviews)</h2>
        <hr>
        <div id="report-body" style="line-height: 1.8;"></div>
    </div>
</div>

<script>
const aiData = <?php echo $js_ai_data; ?>;

function syncSelection(cb) {
    cb.closest('.compare-card').classList.toggle('selected', cb.checked);
    const checkedCount = document.querySelectorAll('.compare-checkbox:checked').length;
    const btn = document.getElementById('ai-btn');
    btn.style.display = checkedCount >= 2 ? 'block' : 'none';
    document.getElementById('count').innerText = checkedCount;
}

function showToast(msg) {
    const t = document.getElementById('custom-toast');
    t.innerHTML = `<span class="toast-icon">⚠️</span> <div>${msg}</div>`;
    
    t.classList.add("show");
    setTimeout(() => { 
        t.classList.remove("show"); 
    }, 3000);
}

async function triggerAICompare() {
    const checked = document.querySelectorAll('.compare-checkbox:checked');
    const selected = [];
    const cats = new Set();

    checked.forEach(cb => {
        const item = aiData.find(p => p.name === cb.getAttribute('data-name'));
        selected.push(item);
        cats.add(item.category);
    });

    if (cats.size > 1) {
        showToast("<b>Inconsistent Categories Detected</b><br><small style='color:#666; font-weight:400;'>Please select items from the same category for an accurate AI comparison.</small>");
        return;
    }

    const modal = document.getElementById('compare-modal');
    const body = document.getElementById('report-body');
    modal.style.display = 'block';
    body.innerHTML = "🌀 <b>Gemma 3</b> is analyzing specifications and user sentiments...";

    try {
        const res = await fetch('https://fyp-ai-backend.onrender.com/compare_products_ai', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ products: selected })
        });
        const data = await res.json();
        body.innerHTML = data.analysis;
    } catch (e) {
        body.innerHTML = "❌ AI server connection failed.";
    }
}

function closeModal() { document.getElementById('compare-modal').style.display = 'none'; }

window.addEventListener('click', (e) => {
    const modalIds = ['review-modal', 'ai-modal', 'compare-modal'];

    modalIds.forEach(id => {
        const modal = document.getElementById(id);
        if (modal && e.target === modal) {
            modal.style.display = 'none';
        }
    });
});

function removeFromCompare(productId, btn) {
    const card = btn.closest('.compare-card');
    
    fetch(`remove_from_compare.php?id=${productId}`)
        .then(response => {
            if (response.ok) {
                card.remove(); 
                syncSelection(document.querySelector('.compare-checkbox'));
                showToast("✅ Removed from compare list!");
            }
        });
}
    
</script>
<?php include('footer.php'); ?>
</body>

</html>





