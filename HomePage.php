<?php
session_start();
include('ConnectDB.php');
include('header.php');
include('navbar.php');

$category = isset($_GET['category']) ? $_GET['category'] : '';

$sql = "
    SELECT p.product_id, p.product_name, p.price, p.image_main, p.rating, p.category, s.seller_name
    FROM products p
    JOIN sellers s ON p.seller_id = s.seller_id
";

if ($category != '') {
    $sql .= " WHERE p.category = '" . mysqli_real_escape_string($conn, $category) . "'";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Home Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .banner {
            width: 100%;
            height: 500px; 
            background: url('https://i.pinimg.com/1200x/86/d5/b9/86d5b9a9c5340a020169f8129f6673af.jpg')
                        no-repeat center 75% / cover;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        .banner-text {
            padding-left: 60px;
            color: #463a2d;
        }

        .banner h1 {
            font-size: 3.5rem;
            margin-bottom: 10px;
        }

        .banner p {
            font-size: 1.5rem;
        }

        .categories-section {
        background: #f8f9fa; 
        padding: 20px 0;  
        }

        .section-title {
        text-align: center;  
        font-size: 1.8rem;
        font-weight: 700;
        color: #2d3436;
        margin-bottom: 40px;
        letter-spacing: 1px;
        position: relative;
        }

        .section-title::after {
        content: '';
        display: block;
        width: 50px;
        height: 3px;
        background: #875812;
        margin: 10px auto;
        }

        .categories-grid {
        display: flex;
        justify-content: center; 
        gap: 30px;              
        flex-wrap: wrap;
        }

        .category-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        color: #2d3436;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .category-img-wrapper {
        width: 100px;           
        height: 100px;
        background: #ffffff;    
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
        border: 1px solid #eee;
        }

        .category-img-wrapper img {
        width: 55px;
        height: 55px;
        transition: transform 0.3s;
        }

        .category-item:hover .category-img-wrapper {
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        transform: translateY(-5px);
        }

        .category-item:hover .category-img-wrapper img {
        transform: scale(1.1);
        }

        .category-item span {
        font-size: 1rem;
        font-weight: 600;
        text-transform: uppercase;
        }

        .ai-progress-bar {
            height: 3px;
            width: 100%;
            background: linear-gradient(to right, #ceb9a0 0%, #4b310b 50%, #ceb9a0 100%);
            background-size: 200% auto;
            animation: shine 1.5s linear infinite;
            border-radius: 2px;
            margin-top: 5px;
        }

       #ai-chat-trigger {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: #8e5c12;
            color: white;
            width: 48px;  
            height: 48px; 
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15); 
            z-index: 999999;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
        }
        
        #ai-chat-trigger:hover {
            transform: scale(1.1); 
            background: #4b310b;  
        }
        
        .ai-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: red;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 2px solid white;
        }
        
        #ai-chat-modal {
            display: none;
            position: fixed;
            z-index: 10001;
            right: 20px;
            bottom: 100px; 
            width: 380px;
            max-width: 90vw;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
            flex-direction: column;
        }
        
        .chat-header {
            background: #4b310b;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        #chat-box {
            height: 350px;
            overflow-y: auto;
            padding: 15px;
            background: #fdfaf7;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .msg { 
            padding: 8px 12px; 
            border-radius: 15px; 
            font-size: 0.9rem; 
            max-width: 80%; 
            line-height: 1.4; 
        }
        
        .ai-msg { 
            background: #eee; 
            align-self: flex-start; 
            color: #333; 
        }
        
        .user-msg { 
            background: #ceb9a0; 
            align-self: flex-end; 
            color: white; 
        }
        
        .chat-input-area {
            display: flex;
            padding: 10px;
            border-top: 1px solid #eee;
            gap: 8px;
        }
        
        .chat-input-area input {
            flex: 1;
            border: 1px solid #ddd;
            padding: 8px 12px;
            border-radius: 20px;
            outline: none;
        }
        
        .chat-input-area button {
            background: #4b310b;
            border: none;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
        }
        @keyframes shine {
            to { background-position: 200% center; }
        }

        @media (max-width: 600px) {
            .banner {
            width: auto;
            height: 300; 
            background: url('https://i.pinimg.com/1200x/86/d5/b9/86d5b9a9c5340a020169f8129f6673af.jpg')
                        no-repeat center 75% / cover;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

            .banner-text {
                padding-left: 30px;
                color: #463a2d;
            }
    
            .banner h1 {
                font-size: 2.0rem;
                margin-bottom: 10px;
            }
    
            .banner p {
                font-size: 1.0rem;
            }
    
            .categories-section {
            background: #f8f9fa; 
            padding: 10px 0;  
            }
    
            .section-title {
            text-align: center;  
            font-size: 1.4rem;
            font-weight: 500;
            color: #2d3436;
            margin-bottom: 40px;
            letter-spacing: 0.35px;
            position: relative;
            }
    
            .section-title::after {
            content: '';
            display: block;
            width: 35px;
            height: 1px;
            background: #875812;
            margin: 6px auto;
            }
    
            .categories-grid {
            display: flex;
            justify-content: center; 
            gap: 15px;              
            flex-wrap: wrap;
            }
    
            .category-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #2d3436;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            }
    
            .category-img-wrapper {
            width: 60px;           
            height: 60px;
            background: #ffffff;    
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
            border: 1px solid #eee;
            }
    
            .category-img-wrapper img {
            width: 35px;
            height: 35px;
            transition: transform 0.3s;
            }
    
            .category-item:hover .category-img-wrapper {
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transform: translateY(-5px);
            }
    
            .category-item:hover .category-img-wrapper img {
            transform: scale(1.1);
            }
    
            .category-item span {
            font-size: 0.65rem;
            font-weight: 300;
            text-transform: uppercase;
            }
    
            .ai-progress-bar {
                height: 2px;
                width: 100%;
                background: linear-gradient(to right, #ceb9a0 0%, #4b310b 50%, #ceb9a0 100%);
                background-size: 200% auto;
                animation: shine 1.5s linear infinite;
                border-radius: 2px;
                margin-top: 5px;
            }
        }
    </style>
</head>

<body>
<section class="banner">
    <div class="banner-text">
        <h1>Simple Living</h1>
        <p>Explore your everyday essentials</p> 
    </div>
</section>

        <section class="categories-section">
    <div class="container">
        <h2 class="section-title">CATEGORIES</h2>
        <div class="categories-grid">
            <a href="HomePage.php?category=Bottle" class="category-item">
                <div class="category-img-wrapper">
                    <img src="https://cdn-icons-png.flaticon.com/128/571/571515.png" alt="Bottle">
                </div>
                <span>Bottle</span>
            </a>

            <a href="HomePage.php?category=Bowl" class="category-item">
                <div class="category-img-wrapper">
                    <img src="https://cdn-icons-png.flaticon.com/128/11636/11636057.png" alt="Bowl">
                </div>
                <span>Bowl</span>
            </a>

            <a href="HomePage.php?category=Cup" class="category-item">
                <div class="category-img-wrapper">
                    <img src="https://cdn-icons-png.flaticon.com/128/4229/4229200.png" alt="Cup">
                </div>
                <span>Cup</span>
            </a>

            <a href="HomePage.php" class="category-item">
                <div class="category-img-wrapper">
                    <img src="https://cdn-icons-png.flaticon.com/128/2674/2674486.png" alt="All">
                </div>
                <span>All Products</span>
            </a>
        </div>
    </div>
</section>

<section class="product-list" style="padding:40px 60px;">
    <div class="product-container">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php include('product_card.php'); ?>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No products available.</p>
        <?php endif; ?>
    </div>
</section>

    <div id="ai-chat-trigger" onclick="toggleChat()">
        <span>🤖</span>
    </div>
    
    <div id="ai-chat-modal">
        <div class="chat-header">
            <span><i class="fa-solid fa-robot"></i> AI Shopping Assistant</span>
            <span onclick="toggleChat()" style="cursor:pointer;">&times;</span>
        </div>
        <div id="chat-box">
            <div class="msg ai-msg">Hello! I'm your AI assistant. Describe what you're looking for or your scenario (e.g., "I need a gift for a coffee lover"), and I'll help you find it!</div>
        </div>
        <div class="chat-input-area">
            <input type="text" id="user-input" placeholder="Type your needs here..." onkeypress="if(event.key==='Enter') sendMessage()">
            <button onclick="sendMessage()"><i class="fa-solid fa-paper-plane"></i></button>
        </div>
    </div>

<?php include('footer.php'); ?>
</body>
</html>

<script>
function toggleChat() {
    const modal = document.getElementById('ai-chat-modal');
    modal.style.display = (modal.style.display === 'flex') ? 'none' : 'flex';
}

async function sendMessage() {
    const input = document.getElementById('user-input');
    const box = document.getElementById('chat-box');
    const text = input.value.trim();
    if (!text) return;

    box.innerHTML += `<div class="msg user-msg">${text}</div>`;
    input.value = '';
    box.scrollTop = box.scrollHeight;

    const loadingId = 'loading-' + Date.now();
    box.innerHTML += `<div class="msg ai-msg" id="${loadingId}">AI is thinking...</div>`;
    
    try {
        const response = await fetch('https://fyp-ai-backend.onrender.com/chat_and_search', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: text })
        });
        const data = await response.json();
        
        document.getElementById(loadingId).remove();
        box.innerHTML += `<div class="msg ai-msg">${data.reply}</div>`;
        
        if (data.search_keyword) {
            box.innerHTML += `<div class="msg ai-msg" style="background:#fff3e0; border:1px solid #ffe0b2;">
                🔍 Redirecting you to products for: <b>${data.search_keyword}</b>...
            </div>`;
            setTimeout(() => {
                window.location.href = `SearchResults.php?query=${encodeURIComponent(data.search_keyword)}`;
            }, 2000);
        }
        
        box.scrollTop = box.scrollHeight;
    } catch (err) {
        document.getElementById(loadingId).innerText = "❌ Connection error.";
    }
}
</script>







