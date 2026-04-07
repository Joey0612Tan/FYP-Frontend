<?php
session_start();
include('ConnectDB.php');
include('header.php');
include('navbar.php');

$ids = $_POST['selected_ids'] ?? [];

if(empty($ids)) { 
    header("Location: cart.php"); 
    exit; 
}

$id_list = implode(',', array_map('intval', $ids));
$sql = "SELECT c.id, c.quantity, c.selected_color, p.product_name, p.price, p.image_main 
        FROM cart c JOIN products p ON c.product_id = p.product_id 
        WHERE c.id IN ($id_list)";
$res = $conn->query($sql);

$items = [];
$total_amount = 0;
while($row = $res->fetch_assoc()){
    $items[] = $row;
    $total_amount += $row['price'] * $row['quantity'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background: #f9f9f9;
        }

        .checkout-wrapper {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px 100px 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .checkout-title {
            font-size: 2rem;
            color: #4b310b;
            margin-bottom: 30px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .checkout-title i {
            font-size: 1.8rem;
        }

        .checkout-content {
            display: flex;
            gap: 40px;
            align-items: flex-start;
        }

        .products-section {
            flex: 2;
            background: white;
            border-radius: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 30px;
        }

        .products-section h3 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #666;
            border-bottom: 2px solid #f8f1e9;
            padding-bottom: 15px;
            font-size: 1.2rem;
        }

        .product-item {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .product-item:last-child {
            border-bottom: none;
        }

        .product-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }

        .product-details {
            flex: 1;
        }

        .product-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .product-meta {
            color: #8e5c12;
            font-size: 0.9rem;
        }

        .product-meta span {
            background: #f8f1e9;
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-block;
            margin-right: 10px;
        }

        .product-price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #E53935;
            min-width: 100px;
            text-align: right;
        }

        .summary-section {
            flex: 1;
            background: #fffdf9;
            border-radius: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 30px;
            border: 1px solid #f1e4d5;
            position: sticky;
            top: 100px;
        }

        .summary-section h3 {
            margin-top: 0;
            color: #4b310b;
            font-size: 1.3rem;
        }

        .summary-divider {
            border: none;
            border-top: 1px dashed #ceb9a0;
            margin: 20px 0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 1rem;
            color: #666;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            margin: 20px 0 25px;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }

        .summary-total span:first-child {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
        }

        .summary-total span:last-child {
            font-size: 1.6rem;
            font-weight: bold;
            color: #E53935;
        }

        .btn-pay {
            width: 100%;
            padding: 16px;
            background: #4b310b;
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: bold;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-pay:hover {
            background: #6d4a16;
            transform: translateY(-2px);
        }

        .btn-back {
            display: block;
            text-align: center;
            padding: 14px;
            color: #ceb9a0;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            margin-top: 15px;
        }

        .btn-back:hover {
            color: #8e5c12;
        }

        #custom-toast {
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
            font-size: 0.95rem;
            font-weight: 500;
            white-space: nowrap;
        }

        #custom-toast.show {
            display: block;
            animation: fadeInUp 0.3s ease-out;
        }

        #custom-toast.error {
            background-color: #f44336;
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

        @media screen and (max-width: 768px) {
            .checkout-wrapper {
                padding: 15px 12px 100px 12px;
                margin: 0;
            }

            .checkout-title {
                font-size: 1.5rem;
                margin-bottom: 20px;
            }

            .checkout-content {
                flex-direction: column;
                gap: 20px;
            }

            .products-section {
                padding: 20px;
                order: 1;
            }

            .products-section h3 {
                font-size: 1rem;
            }

            .product-item {
                gap: 12px;
                padding: 15px 0;
            }

            .product-img {
                width: 70px;
                height: 70px;
            }

            .product-name {
                font-size: 0.95rem;
            }

            .product-meta {
                font-size: 0.75rem;
            }

            .product-meta span {
                padding: 3px 10px;
                margin-right: 8px;
            }

            .product-price {
                font-size: 0.95rem;
                min-width: 70px;
            }

            .summary-section {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                border-radius: 20px 20px 0 0;
                padding: 16px 20px;
                z-index: 100;
                background: rgba(255, 253, 249, 0.98);
                backdrop-filter: blur(10px);
                box-shadow: 0 -5px 20px rgba(0,0,0,0.1);
                border-top: 1px solid #f1e4d5;
                top: auto;
            }

            .summary-section h3 {
                font-size: 1rem;
                margin-bottom: 10px;
            }

            .summary-divider {
                margin: 12px 0;
            }

            .summary-row {
                font-size: 0.85rem;
                margin-bottom: 8px;
            }

            .summary-total {
                margin: 10px 0 12px;
                padding-top: 10px;
            }

            .summary-total span:first-child {
                font-size: 0.95rem;
            }

            .summary-total span:last-child {
                font-size: 1.3rem;
            }

            .btn-pay {
                padding: 14px;
                font-size: 1rem;
            }

            .btn-back {
                display: none;
            }

            .summary-section.sticky-fixed {
                position: fixed;
            }
        }

        @media screen and (max-width: 768px) {
            .products-section {
                margin-bottom: 0;
                padding-bottom: 20px;
            }
            
            .product-item:last-child {
                border-bottom: none;
            }
        }
    </style>
</head>
<body>

<div class="checkout-wrapper">
    <div class="checkout-title">
        <i class="fa-solid fa-shield-heart"></i>
        Order Confirmation
    </div>

    <div id="custom-toast"></div>

    <div class="checkout-content">
        <div class="products-section">
            <h3><i class="fa-solid fa-bag-shopping"></i> Selected Products (<?php echo count($items); ?>)</h3>
            
            <?php foreach($items as $item): ?>
            <div class="product-item">
                <img src="<?php echo htmlspecialchars($item['image_main']); ?>" class="product-img" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                <div class="product-details">
                    <div class="product-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                    <div class="product-meta">
                        <span><i class="fa-solid fa-palette"></i> <?php echo htmlspecialchars($item['selected_color']); ?></span>
                        <span><i class="fa-solid fa-cube"></i> Qty: <?php echo $item['quantity']; ?></span>
                    </div>
                </div>
                <div class="product-price">
                    RM <?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="summary-section" id="summarySection">
            <h3><i class="fa-solid fa-receipt"></i> Payment Summary</h3>
            <div class="summary-row">
                <span>Subtotal</span>
                <span>RM <?php echo number_format($total_amount, 2); ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping</span>
                <span>Calculated at next step</span>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-total">
                <span>Grand Total</span>
                <span>RM <?php echo number_format($total_amount, 2); ?></span>
            </div>

            <button class="btn-pay" onclick="processPayment()">
                <i class="fa-solid fa-credit-card"></i> Confirm & Pay Now
            </button>
            
            <a href="cart.php" class="btn-back">
                <i class="fa-solid fa-rotate-left"></i> Change Selection
            </a>
        </div>
    </div>
</div>

<script>
function showToast(message, isError = false) {
    const toast = document.getElementById('custom-toast');
    toast.innerText = message;
    if (isError) {
        toast.classList.add('error');
    } else {
        toast.classList.remove('error');
    }
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

function processPayment() {
    const ids = <?php echo json_encode($ids); ?>;
    
    if (!ids || ids.length === 0) {
        showToast('No items selected', true);
        return;
    }

    const payBtn = document.querySelector('.btn-pay');
    const originalText = payBtn.innerHTML;
    payBtn.innerHTML = '<i class="fa-solid fa-spinner fa-pulse"></i> Processing...';
    payBtn.disabled = true;

    fetch('process_payment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ cart_ids: ids })
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            showToast('Order Placed Successfully! 🎉');
            setTimeout(() => {
                window.location.href = "HomePage.php";
            }, 2000);
        } else {
            showToast(data.message || 'Error processing payment', true);
            payBtn.innerHTML = originalText;
            payBtn.disabled = false;
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showToast('Server error, please try again', true);
        payBtn.innerHTML = originalText;
        payBtn.disabled = false;
    });
}
</script>

<?php include('footer.php'); ?>
</body>
</html>
