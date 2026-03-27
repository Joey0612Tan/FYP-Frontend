<?php
$compare_count = 0;
$user_id = 1;

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM compare_list
    WHERE user_id = $user_id
");

if ($row = $result->fetch_assoc()) {
    $compare_count = $row['total'];
}

$cart_count = 0;
$result = $conn->query("
    SELECT SUM(quantity) AS total
    FROM cart
    WHERE user_id = $user_id
");
if ($row = $result->fetch_assoc()) {
    $cart_count = $row['total'] ?? 0;
}
?>

<nav class="navbar">
    <div class="navbar-left">
        <a href="HomePage.php" class="logo">🧸MyShop</a>
    </div>

    <div class="navbar-center">
        <form method="GET" action="Search.php" id="searchForm" style="display:flex; gap:5px; position: relative;">
            <input type="text" name="keyword" id="mainSearchInput" class="search-input" placeholder="Search products..." required>
            <button type="button" class="search-btn" onclick="confirmAndSearch()">🔍</button>
            
            <button type="button" class="search-btn" onclick="toggleCameraOptions(event)">📸</button>
        </form>
        
        <input type="file" id="ai-upload-input" accept="image/*" style="display:none;" onchange="handleAICamera(this)">
        <input type="file" id="ai-camera-input" accept="image/*" capture="environment" style="display:none;" onchange="handleAICamera(this)">
        <div id="ai-status-bar" style="display:none; position: absolute; margin-top: 5px;"></div>
    </div>

    <div class="navbar-right">
        <a href="cart.php" class="cart-btn">
            🛒 <span class="btn-text hide-text">Cart</span> <span class="badge" id="cart-count"><?php echo $cart_count ?? 0; ?></span>
        </a>
    
        <a href="compare_list.php" class="compare-btn">
            ⚖️ <span class="btn-text hide-text">Compare</span> <span class="badge" id="compare-count"><?php echo $compare_count ?? 0; ?></span>
        </a>
    
        <button class="profile-btn">
            👤 <span class="btn-text hide-text">Account</span>
        </button>
    </div>
</nav>

<div id="camera-options" style="
    display:none; 
    position: fixed; 
    top: 50%; 
    left: 50%; 
    transform: translate(-50%, -50%);
    background: white; 
    border: 1px solid #ceb9a0; 
    padding: 20px; 
    border-radius: 12px; 
    box-shadow: 0 10px 25px rgba(0,0,0,0.2); 
    z-index: 10001; 
    width: 280px;
">
    <div style="text-align: center; margin-bottom: 15px;">
        <strong style="color: #4b310b; font-size: 16px;">📸 AI Image Search</strong>
    </div>
    <div style="display: flex; flex-direction: column; gap: 10px;">
        <button type="button" onclick="event.stopPropagation(); triggerCamera('camera')" style="background: #ceb9a0; color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-size: 16px;">📷 Take a Photo</button>
        <button type="button" onclick="event.stopPropagation(); triggerCamera('gallery')" style="background: #f5f5f5; border: 1px solid #ddd; padding: 12px; border-radius: 8px; cursor: pointer; font-size: 16px;">🖼️ From Gallery</button>
        <button type="button" onclick="toggleCameraOptions(event)" style="background: none; border: none; padding: 8px; color: #999; cursor: pointer; margin-top: 5px;">Cancel</button>
    </div>
</div>

<script>
function confirmAndSearch() {
    const kw = document.getElementById('mainSearchInput').value;
    if (kw.trim() !== '') {
        window.location.href = `Search.php?keyword=${encodeURIComponent(kw)}`;
    } else {
        alert('Please enter a search keyword');
    }
}

function toggleCameraOptions(event) {
    event.preventDefault();
    event.stopPropagation();
    const menu = document.getElementById('camera-options');
    if (menu) {
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }
}

function triggerCamera(type) {
    const menu = document.getElementById('camera-options');
    if (menu) menu.style.display = 'none';
    
    if (type === 'camera') {
        document.getElementById('ai-camera-input').click();
    } else {
        document.getElementById('ai-upload-input').click();
    }
}

async function handleAICamera(input) {
    if (!input.files || input.files.length === 0) return;

    const file = input.files[0];
    const statusBar = document.getElementById('ai-status-bar');
    
    if (statusBar) {
        statusBar.style.display = 'block';
        statusBar.innerHTML = `✨ AI Vision is analyzing... <div class="ai-progress-line"></div>`;
    }

    try {
        const compressedBlob = await compressImage(file);
        const formData = new FormData();
        formData.append('image', compressedBlob, 'image.jpg');

        const response = await fetch('https://fyp-ai-backend.onrender.com/visual_search', {
            method: 'POST',
            mode: 'cors',
            body: formData
        });

        const data = await response.json();
        console.log('AI Response:', data);

        if (data.status === 'success' && data.matches && data.matches.length > 0) {
            const ids = data.matches.join(',');
            const score = data.top_score || 0;
            window.location.href = `Search.php?ids=${ids}&search_mode=visual&score=${score}`;
        } else {
            console.log('No matches found, showing popular products');
            
            if (statusBar) {
                statusBar.innerHTML = "😓 No exact matches. Showing popular products instead...";
            }
            
            setTimeout(() => {
                window.location.href = `Search.php?search_mode=visual&show_popular=1`;
            }, 1500);
        }

    } catch (err) {
        console.error("AI Search Error:", err);
        if (statusBar) {
            statusBar.innerHTML = "❌ AI Connection Failed. Showing popular products...";
        }
        setTimeout(() => {
            window.location.href = `Search.php?show_popular=1`;
        }, 1500);
    }
}

function compressImage(file) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.src = URL.createObjectURL(file);
        img.onload = () => {
            const canvas = document.createElement('canvas');
            let width = img.width;
            let height = img.height;
            const maxSize = 512;
            
            if (width > height && width > maxSize) {
                height = (height * maxSize) / width;
                width = maxSize;
            } else if (height > maxSize) {
                width = (width * maxSize) / height;
                height = maxSize;
            }
            
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);
            
            canvas.toBlob((blob) => {
                if (blob) resolve(blob);
                else reject(new Error("Canvas to Blob failed"));
            }, 'image/jpeg', 0.85);
            
            URL.revokeObjectURL(img.src);
        };
        img.onerror = reject;
    });
}

document.addEventListener('click', function(e) {
    const menu = document.getElementById('camera-options');
    const cameraBtn = document.querySelector('.search-btn[onclick*="toggleCameraOptions"]');
    
    if (menu && menu.style.display === 'block') {
        if (!menu.contains(e.target) && e.target !== cameraBtn && !cameraBtn.contains(e.target)) {
            menu.style.display = 'none';
        }
    }
});
</script>
