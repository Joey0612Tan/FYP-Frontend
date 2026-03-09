<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MyShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 40px;
        background: #ceb9a0ff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        position: sticky;
        top: 0;
        z-index: 999;
    }

    .navbar-left .logo {
        font-size: 2.8rem;
        line-height: 64px;
        font-weight: bold;
        color: #f3eeeeff;
        text-decoration: none;
    }

    .navbar-center {
        flex: 1;
        display: flex;
        justify-content: center;
        gap: 5px;
    }

    .search-input {
        width: 800px;
        padding: 11px 18px;
        border: 1px solid #ccc;
        border-radius: 25px;
        font-size: 1.3rem;
    }

    .search-btn {
        font-size: 1.5rem;
        padding: 8px 15px;
        border: none;
        border-radius: 50%;
        background: #eeeae1ff;
        color: #fff;
        cursor: pointer;
    }

    .navbar-right button, .navbar-right a  {
        background: none;
        border: none;
        color: #463a2d;
        font-size: 1.5rem;
        margin-left: 20px;
        position: relative;
        cursor: pointer;
        text-decoration: none; 
    }

    .navbar-right .badge {
        position: absolute;
        top: -5px;
        right: -10px;
        background: #E53935;
        color: #fff;
        font-size: 0.8rem;
        padding: 2px 6px;
        border-radius: 50%;
    }

    #ai-status-bar {
        position: absolute;
        top: 100%; 
        left: 0;
        width: 100%;
        padding: 8px 0;
        text-align: center;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        z-index: 100;
    }

    .ai-progress-line {
        height: 4px;
        width: 80%;
        margin: 5px auto;
        background: #eee;
        overflow: hidden;
        position: relative;
        border-radius: 10px;
    }

    .ai-progress-line::after {
        content: '';
        display: block;
        width: 40%;
        height: 100%;
        background: linear-gradient(90deg, transparent, #ceb9a0, transparent);
        position: absolute;
        left: -100%;
        animation: loading-scan 1.5s infinite;
    }

    .icon-btn{
        font-size:16px;
        padding:6px 8px;
        width:36px;
        height:36px;
        display:flex;
        align-items:center;
        justify-content:center;
    }

    @keyframes loading-scan {
        from { left: -100%; }
        to { left: 100%; }
    }

    @media screen and (max-width: 768px) {
    .navbar {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important; 
        justify-content: space-between !important;
        padding: 5px 8px !important;
        gap: 5px !important;
        width: 100% !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
    }

    .logo {
        font-size: 1.1rem !important; 
        font-weight: bold;
        white-space: nowrap;
        flex-shrink: 0; 
    }
        
    .navbar-center {
        flex: 1 !important;
        min-width: 0 !important; 
        margin: 0 5px !important;
    }

    #searchForm {
        display: flex !important;
        width: 100% !important;
        max-width: 100% !important;
        gap: 3px !important;
        position: static !important; 
    }

    .search-input {
        flex: 1 !important;
        width: 100% !important;
        min-width: 30px !important; 
        padding: 6px 10px !important;
        font-size: 14px !important;
        border-radius: 20px !important;
    }

    .search-btn {
        flex-shrink: 0 !important; 
        padding: 4px 8px !important;
        font-size: 14px !important;
        width: auto !important;
    }

    .navbar-right {
        gap: 3px !important; 
    }

    .navbar-right .hide-text {
        display: none !important; 
    }

    .cart-btn, .compare-btn, .profile-btn {
        padding: 4px !important;
        font-size: 1rem !important;
    }

    .badge {
        font-size: 0.6rem !important; 
        padding: 1px 4px !important;
    }
}

        /*
        .navbar {
            padding: 8px 10px;
            gap: 5px;        
        }

        .navbar-left .logo {
            font-size: 1.5rem !important; 
        }
    
        .navbar-center {
            flex: 1;
            margin: 0 10px; 
        }
        
        .search-input {
            width: 100% !important; 
            padding: 6px 12px !important;
            font-size: 1rem !important;
        }

        .search-input{
            width:120px;
            font-size:14px;
        }
    
        .icon-btn{
            font-size:1.6px;
            padding:1.8px;
        }
    
        .navbar-right a, .navbar-right button {
            font-size: 0 !important;
            margin-left: 8px !important; 
        }
    
        .navbar-right a::before, .navbar-right button::before {
            font-size: 1.4rem !important;
        }
        
        .navbar-right .emoji-icon {
            font-size: 1.4rem !important;
        }
     }*/
    </style>

    <div id="ai-modal" style="display:none; position:fixed; z-index:9999; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); align-items:center; justify-content:center;">
    <div style="background:white; padding:30px; border-radius:20px; width:90%; max-width:400px; text-align:center;">
        <h3 style="margin-top:0;">AI Vision Analysis</h3>
        
        <img id="ai-preview-box" style="width:100%; height:200px; object-fit:contain; background:#f8f1e9; border-radius:10px;">
        
        <div style="margin:20px 0; text-align:left; font-family:monospace; font-size:12px; background:#f4f4f4; padding:10px; border-radius:5px; color:#4b310b;">
            <strong>System Status:</strong> <span style="color:green;">Connected</span><br>
            <strong>Model:</strong> ResNet50 + Gemma-3<br>
            <div id="vector-display" style="word-break:break-all; margin-top:5px;">Extracting vectors...</div>
        </div>

        <button onclick="confirmAndSearch()" class="btn-checkout" style="width:100%;">
            Find Matches <i class="fa-solid fa-magnifying-glass"></i>
        </button>
        <button onclick="document.getElementById('ai-modal').style.display='none'" style="margin-top:10px; background:none; border:none; color:gray; cursor:pointer;">Cancel</button>
    </div>
</div>

<script>

async function handleAICamera(input) {
    if (input.files.length === 0) return;

    const file = input.files[0];
    const statusBar = document.getElementById('ai-status-bar');
    const searchInput = document.getElementById('mainSearchInput');
    
    statusBar.style.display = 'block';
    statusBar.innerHTML = `
        <div style="font-size: 13px; color: #4b310b; font-weight: bold; margin-bottom: 5px;">
            ✨ Deep Intelligence is scanning your image...
        </div>
        <div class="ai-progress-line"></div>
    `;

    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('ai-preview-box').src = e.target.result;
        document.getElementById('ai-modal').style.display = 'flex';
        document.getElementById('vector-display').innerText = "ResNet50: Extracting 2048-dim features...";
    }
    reader.readAsDataURL(file);

    const formData = new FormData();
    formData.append('image', file);

    try {
        const response = await fetch('https://fyp-ai-backend.onrender.com/visual_search', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.status === 'success') {
            const vectorSample = data.vector.slice(0, 8).map(n => n.toFixed(4)).join(', ');
            document.getElementById('vector-display').innerHTML = 
                `<span style="color:#8e5c12; font-weight:bold;">DNA: [${vectorSample}...]</span><br>` +
                `<small style="color:gray;">Deep Features Extracted Successfully</small>`;
            
            statusBar.innerText = "✅ Visual DNA Captured!";
            
        } else {
            throw new Error(data.error || "Extraction failed");
        }
    } catch (err) {
        statusBar.innerText = "❌ AI Server Error";
        document.getElementById('vector-display').innerText = "Error: " + err.message;
        console.error(err);
    }
}

function closeAIModal() {
    document.getElementById('ai-modal').style.display = 'none';
    document.getElementById('cv-upload').value = ''; 
}

function confirmAndSearch() {
    window.location.href = "search_results.php?search_mode=visual";
}
</script>

</head>

















