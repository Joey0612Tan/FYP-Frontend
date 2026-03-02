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

        @keyframes shine {
            to { background-position: 200% center; }
        }

        /* 只有当屏幕宽度小于 768px（手机/平板）时，以下代码才会执行 */
        @media (max-width: 768px) {
            
            /* 1. 修复 Search Bar 太大的问题 */
            .search-container, .search-bar-form {
                display: flex;
                width: 100% !important;
                padding: 5px;
            }

            input[type="text"].search-input {
                flex: 1; /* 让输入框自动缩放 */
                font-size: 14px; /* 调小字体，防止撑开高度 */
                height: 35px;
            }

            /* 2. 让按钮不再挤在一起 */
            .nav-buttons, .header-actions {
                display: flex;
                gap: 8px; /* 按钮之间的间距 */
                justify-content: space-around;
                margin-top: 10px;
            }

            /* 3. 让 Product Card 变大，且一行显示两个 */
            /* 假设你的产品容器叫 .product-grid，卡片叫 .product-card */
            .product-grid {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important; /* 关键：强制一行两个 */
                gap: 10px !important;
                padding: 10px;
            }

            .product-card {
                width: 100% !important; /* 让它填满网格列 */
                margin: 0 !important;
                padding: 8px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }

            /* 调小产品图片的固定高度，适配手机 */
            .product-card img {
                height: 120px !important; 
                object-fit: cover;
            }

            /* 调小产品名字字体 */
            .product-name {
                font-size: 14px !important;
                height: 40px; /* 固定高度防止排版乱掉 */
                overflow: hidden;
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

<?php include('footer.php'); ?>
</body>
</html>
