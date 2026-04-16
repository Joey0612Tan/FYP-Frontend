<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('ConnectDB.php');
include('header.php');
include('navbar.php');

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$is_visual = (isset($_GET['search_mode']) && $_GET['search_mode'] === 'visual');
$ids = isset($_GET['ids']) ? $_GET['ids'] : '';
$score = isset($_GET['score']) ? floatval($_GET['score']) : 0;
$show_popular = isset($_GET['show_popular']) ? true : false;

echo "<!-- Search Debug -->\n";
echo "<!-- is_visual: " . ($is_visual ? 'true' : 'false') . " -->\n";
echo "<!-- ids: $ids -->\n";
echo "<!-- score: $score -->\n";
echo "<!-- keyword: " . htmlspecialchars($keyword) . " -->\n";

$sql = "";
$result = null;

if ($is_visual && !empty($ids) && $ids !== 'none' && preg_match('/^[0-9,]+$/', $ids)) {
    $sql = "SELECT p.*, sel.seller_name 
            FROM products p
            LEFT JOIN sellers sel ON p.seller_id = sel.seller_id
            WHERE p.product_id IN ($ids)
            ORDER BY FIELD(p.product_id, $ids)";
    echo "<!-- Visual search with specific IDs -->\n";
}
elseif ($is_visual && (empty($ids) || $ids === 'none' || $show_popular)) {
    $sql = "SELECT p.*, sel.seller_name 
            FROM products p
            LEFT JOIN sellers sel ON p.seller_id = sel.seller_id
            ORDER BY p.product_id DESC
            LIMIT 12";
    echo "<!-- Showing popular products -->\n";
}
elseif (!empty($keyword)) {
    $escaped_keyword = mysqli_real_escape_string($conn, $keyword);
    $words = explode(' ', $keyword);
    $searchConditions = [];
    
    foreach ($words as $word) {
    $word = trim($word);
    if (!empty($word)) {
        $escaped_word = mysqli_real_escape_string($conn, $word);
        $searchConditions[] = "(p.product_name LIKE '%$escaped_word%' 
                               OR p.description LIKE '%$escaped_word%' 
                               OR sel.seller_name LIKE '%$escaped_word%'
                               OR EXISTS (
                                   SELECT 1 FROM product_specifications spec 
                                   WHERE spec.product_id = p.product_id 
                                   AND spec.spec_value LIKE '%$escaped_word%'
                               ))";
    }
}

if (count($searchConditions) > 0) {
    $finalCondition = implode(' AND ', $searchConditions); 
    $sql = "SELECT p.*, sel.seller_name 
            FROM products p 
            LEFT JOIN sellers sel ON p.seller_id = sel.seller_id 
            WHERE $finalCondition
            GROUP BY p.product_id
            ORDER BY (p.product_name LIKE '%$escaped_keyword%') DESC, p.rating DESC";
} else {
        $sql = "SELECT p.*, sel.seller_name 
                FROM products p 
                LEFT JOIN sellers sel ON p.seller_id = sel.seller_id 
                ORDER BY p.product_id DESC 
                LIMIT 20";
    }
}
else {
    $sql = "SELECT p.*, sel.seller_name 
            FROM products p 
            LEFT JOIN sellers sel ON p.seller_id = sel.seller_id 
            ORDER BY p.product_id DESC 
            LIMIT 20";
    echo "<!-- Showing recent products -->\n";
}

if ($sql) {
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        echo "<!-- SQL Error: " . mysqli_error($conn) . " -->\n";
    }
}
?>

<section style="padding: 20px 40px; max-width: 1400px; margin: 0 auto;">
    
    <?php if ($is_visual): ?>
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px 30px; border-radius: 15px; margin-bottom: 30px;">
            <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                <div style="font-size: 2.5rem;">🔍</div>
                <div style="flex: 1;">
                    <h3 style="margin: 0 0 5px 0; color: white;">AI Visual Search</h3>
                    <?php if ($score > 0): ?>
                        <p style="margin: 0; color: rgba(255,255,255,0.9);">
                            Similarity Score: <strong><?php echo round($score * 100, 2); ?>%</strong>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div style="margin-bottom: 30px;">
        <?php if ($is_visual && ($show_popular || empty($ids) || $ids === 'none')): ?>
            <h1 style="font-size: 1.8rem; color: #333;">📸 No Exact Match Found</h1>
            <p style="color: #666;">Here are some popular products you might like:</p>
        <?php elseif ($is_visual && $score > 0.85): ?>
            <h1 style="font-size: 1.8rem; color: #333;">✨ Perfect Match Found!</h1>
            <p style="color: #666;">Found products matching your image</p>
        <?php elseif ($is_visual): ?>
            <h1 style="font-size: 1.8rem; color: #333;">🔍 Similar Products</h1>
            <p style="color: #666;">Products similar to your image</p>
        <?php elseif (!empty($keyword)): ?>
            <h1 style="font-size: 1.8rem; color: #333;">Search Results for "<?php echo htmlspecialchars($keyword); ?>"</h1>
            <p style="color: #666;">Found <?php echo $result ? mysqli_num_rows($result) : 0; ?> products</p>
        <?php else: ?>
            <h1 style="font-size: 1.8rem; color: #333;">🛍️ All Products</h1>
        <?php endif; ?>
    </div>

    <div class="product-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px;">
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <?php include('product_card.php'); ?>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 60px; grid-column: 1/-1; background: #f9f9f9; border-radius: 15px;">
                <div style="font-size: 4rem; margin-bottom: 20px;">🔍</div>
                <h3 style="color: #333;">No products found</h3>
                <p style="color: #666;">Try a different search term or browse our categories.</p>
                <a href="HomePage.php" style="display: inline-block; margin-top: 20px; padding: 10px 25px; background: #ceb9a0; color: white; text-decoration: none; border-radius: 25px;">Back to Home</a>
            </div>
        <?php endif; ?>
    </div>

    <div id="ai-chat-trigger" onclick="toggleChat()" style="position: fixed; bottom: 80px; right: 16px; background: #4b310b; color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 15px rgba(0,0,0,0.2); z-index: 999999;">
        <span>🤖</span>
    </div>
    
    <div id="ai-chat-modal" style="display: none; position: fixed; z-index: 10001; right: 20px; bottom: 100px; width: 380px; max-width: 90vw; background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); overflow: hidden; flex-direction: column;">
        <div class="chat-header" style="background: #4b310b; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
            <span><i class="fa-solid fa-robot"></i> AI Shopping Assistant</span>
            <span onclick="toggleChat()" style="cursor:pointer;">&times;</span>
        </div>
        <div id="chat-box" style="height: 350px; overflow-y: auto; padding: 15px; background: #fdfaf7; display: flex; flex-direction: column; gap: 10px;">
            <div class="msg ai-msg" style="background: #eee; padding: 8px 12px; border-radius: 15px; align-self: flex-start; font-size: 0.9rem;">Hello again! How can I help you with these results?</div>
        </div>
        <div class="chat-input-area" style="display: flex; padding: 10px; border-top: 1px solid #eee; gap: 8px;">
            <input type="text" id="user-input" placeholder="Type your needs..." style="flex: 1; border: 1px solid #ddd; padding: 8px 12px; border-radius: 20px; outline: none;" onkeypress="if(event.key==='Enter') sendMessage()">
            <button onclick="sendMessage()" style="background: #4b310b; border: none; color: white; width: 35px; height: 35px; border-radius: 50%; cursor: pointer;"><i class="fa-solid fa-paper-plane"></i></button>
        </div>
    </div>
</section>

<style>
#ai-chat-trigger {
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

#ai-chat-trigger:hover {
    transform: scale(1.1);
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
    word-wrap: break-word;
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
    background: white;
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
    
@media (max-width: 768px) {
    section {
        padding: 15px !important;
    }
    .product-container {
        gap: 12px !important;
        grid-template-columns: repeat(2, 1fr) !important;
    }
}
</style>

<script>
function toggleChat() {
    const modal = document.getElementById('ai-chat-modal');
    modal.style.display = (modal.style.display === 'flex') ? 'none' : 'flex';
}

document.addEventListener('DOMContentLoaded', () => {
    const savedChat = localStorage.getItem('chat_history');
    if (savedChat) {
        document.getElementById('chat-box').innerHTML = savedChat;
        const box = document.getElementById('chat-box');
        box.scrollTop = box.scrollHeight;
    }
});

async function sendMessage() {
    const input = document.getElementById('user-input');
    const box = document.getElementById('chat-box');
    const text = input.value.trim();
    if (!text) return;

    box.innerHTML += `<div class="msg user-msg" style="background: #ceb9a0; color: white; padding: 8px 12px; border-radius: 15px; align-self: flex-end; font-size: 0.9rem; max-width: 80%;">${text}</div>`;
    localStorage.setItem('chat_history', box.innerHTML);
    input.value = '';
    box.scrollTop = box.scrollHeight;

    const loadingId = 'loading-' + Date.now();
    box.innerHTML += `<div class="msg ai-msg" id="${loadingId}" style="background: #eee; padding: 8px 12px; border-radius: 15px; align-self: flex-start; font-size: 0.9rem;">AI is thinking...</div>`;
    
    try {
        const response = await fetch('https://fyp-ai-backend.onrender.com/chat_and_search', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: text })
        });
        const data = await response.json();
        
        document.getElementById(loadingId).remove();
        box.innerHTML += `<div class="msg ai-msg" style="background: #eee; padding: 8px 12px; border-radius: 15px; align-self: flex-start; font-size: 0.9rem; max-width: 80%;">${data.reply}</div>`;
        
        if (data.search_keyword) {
             window.location.href = `Search.php?keyword=${encodeURIComponent(data.search_keyword)}`;
        }
        
        localStorage.setItem('chat_history', box.innerHTML);
        box.scrollTop = box.scrollHeight;
    } catch (err) {
        document.getElementById(loadingId).innerText = "❌ Error connecting AI.";
    }
}
</script>

<?php include('footer.php'); ?>
