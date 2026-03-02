function toggleCameraOptions(event) {
    if(event) event.preventDefault();
    const menu = document.getElementById('camera-options');
    
    if (menu.style.display === 'none') {
        menu.style.display = 'block';
        menu.style.animation = 'fadeIn 0.3s';
    } else {
        menu.style.display = 'none';
    }
}

function triggerCamera(type) {
    alert("Triggering: " + type); 
    document.getElementById('camera-options').style.display = 'none';
    
    if (type === 'camera') {
        document.getElementById('ai-camera-input').click();
    } else {
        document.getElementById('ai-upload-input').click();
    }
}

async function handleAICamera(input) {
    if (input.files.length === 0) return;

    const statusBar = document.getElementById('ai-status-bar');
    const searchInput = document.getElementById('mainSearchInput');
    
    statusBar.style.display = 'inline-block';
    statusBar.innerHTML = '<span class="loading-dots">✨ Gemma AI is thinking</span>';
    
    const formData = new FormData();
    formData.append('image', input.files[0]);

    try {
        const response = await fetch('http://localhost:5000/analyze', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.keywords) {
            const cleanKeyword = data.keywords.replace(/,/g, ' '); 
            
            statusBar.innerText = `✅ Found: ${cleanKeyword}`;
            searchInput.value = cleanKeyword; 
            
            setTimeout(() => {
                document.getElementById('searchForm').submit();
            }, 1000);
        }
    } catch (err) {
        statusBar.innerText = "❌ Search failed.";
        console.error(err);
    }
}

statusBar.style.display = 'block';
statusBar.innerHTML = `
    <div style="color: #ceb9a0; font-size: 12px; font-weight: bold;">✨ Gemma is analyzing image...</div>
    <div class="ai-progress-bar"></div>
`;

document.addEventListener('click', function(e) {
    const menu = document.getElementById('camera-options');
    if (!e.target.closest('.search-btn')) {
        menu.style.display = 'none';
    }
});