<?php
session_start();
include('ConnectDB.php');
include('header.php');
include('navbar.php');

$user_id = 1; 

$sql = "
    SELECT c.id, c.quantity, c.selected_color, p.product_name, p.price, p.image_main 
    FROM cart c
    JOIN products p ON c.product_id = p.product_id
    WHERE c.user_id = $user_id
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Shopping Bag</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
        }

        .cart-page-wrapper {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px 100px 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .cart-title {
            font-size: 2rem;
            color: #4b310b;
            margin-bottom: 20px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cart-title i {
            font-size: 1.8rem;
        }

        /* ========== 电脑端表格样式 ========== */
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .cart-table thead {
            background: #f8f1e9;
        }

        .cart-table th {
            padding: 18px 15px;
            text-align: left;
            color: #4b310b;
            font-weight: 600;
        }

        .cart-table td {
            padding: 20px 15px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }

        .cart-table tr:last-child td {
            border-bottom: none;
        }

        .product-cell {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .cart-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 12px;
        }

        .product-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .product-color {
            font-size: 0.85rem;
            color: #8e5c12;
        }

        .price-text {
            font-weight: 500;
            color: #555;
        }

        .subtotal-text {
            font-weight: bold;
            color: #E53935;
        }

        .qty-control {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .qty-btn {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 1px solid #ceb9a0;
            background: white;
            cursor: pointer;
            font-size: 1.1rem;
            transition: 0.2s;
        }

        .qty-btn:hover {
            background: #f8f1e9;
        }

        .qty-input {
            width: 50px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 6px;
            font-weight: 500;
        }

        .remove-link {
            color: #ff4d4d;
            font-size: 1.2rem;
            transition: 0.2s;
        }

        .remove-link:hover {
            color: #b71c1c;
        }

        .cart-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #4b310b;
        }

        @media screen and (max-width: 768px) {
            .cart-page-wrapper {
                padding: 15px 12px 100px 12px;
                margin: 0;
            }

            .cart-title {
                font-size: 1.6rem;
                margin-bottom: 15px;
            }

            .cart-table thead {
                display: none;
            }

            .cart-table,
            .cart-table tbody,
            .cart-table tr,
            .cart-table td {
                display: block;
            }

            .cart-table tr {
                background: #fff;
                margin-bottom: 12px;
                border-radius: 16px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.05);
                overflow: hidden;
            }

            .cart-table td {
                padding: 12px 15px;
                border-bottom: 1px solid #f5f5f5;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .cart-table td:last-child {
                border-bottom: none;
            }

            .cart-table td:first-child::before {
                content: "Select";
                font-weight: 600;
                color: #4b310b;
                margin-right: 10px;
            }

            .cart-table td:nth-child(2) {
                display: block;
                padding: 15px;
            }

            .cart-table td:nth-child(2)::before {
                display: none;
            }

            .cart-table td:nth-child(3)::before {
                content: "💰 Price";
                font-weight: 600;
                color: #4b310b;
            }

            .cart-table td:nth-child(4)::before {
                content: "📦 Qty";
                font-weight: 600;
                color: #4b310b;
            }

            .cart-table td:nth-child(5)::before {
                content: "💵 Subtotal";
                font-weight: 600;
                color: #4b310b;
            }

            .cart-table td:nth-child(6)::before {
                content: "🗑️";
                font-weight: 600;
                color: #ff4d4d;
            }

            .product-cell {
                flex-direction: row;
                align-items: center;
            }

            .cart-img {
                width: 70px;
                height: 70px;
            }

            .product-name {
                font-size: 0.95rem;
            }

            .qty-control {
                justify-content: flex-end;
            }

            .qty-btn {
                width: 32px;
                height: 32px;
            }

            .qty-input {
                width: 45px;
            }

            .subtotal-text {
                font-size: 1rem;
            }
        }

        .cart-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 -5px 20px rgba(0,0,0,0.1);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            border-top: 1px solid #eee;
        }

        @media screen and (min-width: 769px) {
            .cart-footer {
                position: static;
                margin-top: 30px;
                border-radius: 20px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                background: #fff;
            }
            
            .cart-page-wrapper {
                padding-bottom: 50px;
            }
        }

        @media screen and (max-width: 768px) {
            .cart-footer {
                flex-direction: column;
                gap: 12px;
                padding: 12px 16px;
            }

            .cart-footer > div {
                width: 100%;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .cart-footer .total-amount {
                font-size: 1.5rem;
            }

            .btn-checkout, .btn-update {
                flex: 1;
                justify-content: center;
                padding: 12px;
                font-size: 0.95rem;
            }

            .cart-footer > div:first-child {
                justify-content: center;
            }
        }

        .btn-checkout {
            background-color: #4b310b;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
            opacity: 0.5;
            pointer-events: none;
        }

        .btn-checkout.active {
            opacity: 1;
            pointer-events: auto;
        }

        .btn-checkout:hover {
            transform: translateY(-2px);
            background-color: #6d4a16;
        }

        .btn-update {
            background: transparent;
            border: 2px solid #4b310b;
            color: #4b310b;
            padding: 10px 25px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-update:hover {
            background: #4b310b;
            color: white;
        }

        .total-amount {
            font-size: 1.8rem;
            font-weight: bold;
            color: #E53935;
        }

        .cart-toast {
            position: fixed;
            bottom: 100px;
            left: 50%;
            transform: translateX(-50%);
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 0.9rem;
            opacity: 0;
            transition: 0.3s;
            z-index: 1001;
            white-space: nowrap;
        }

        .cart-toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(-10px);
        }

    </style>
</head>
<body>

<div class="cart-page-wrapper">
    <div class="cart-title">
        <i class="fa-solid fa-bag-shopping"></i>
        Shopping Bag (<?php echo $result->num_rows; ?>)
    </div>

    <?php if ($result->num_rows > 0): ?>
        <form action="checkout.php" method="POST" id="cart-form">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th width="5%"><input type="checkbox" id="select-all" onclick="toggleAll(this)" class="cart-checkbox"></th>
                        <th width="40%">Product</th>
                        <th width="15%">Price</th>
                        <th width="15%">Quantity</th>
                        <th width="15%">Subtotal</th>
                        <th width="10%"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): 
                        $subtotal = $row['price'] * $row['quantity'];
                    ?>
                    <tr class="cart-row" data-id="<?php echo $row['id']; ?>">
                        <td align="center">
                            <input type="checkbox" name="selected_ids[]" value="<?php echo $row['id']; ?>" 
                                   class="item-checkbox cart-checkbox" 
                                   data-subtotal="<?php echo $subtotal; ?>" 
                                   onchange="updateSelection()">
                        </td>
                        <td>
                            <div class="product-cell">
                                <img src="<?php echo htmlspecialchars($row['image_main']); ?>" class="cart-img">
                                <div>
                                    <div class="product-name"><?php echo htmlspecialchars($row['product_name']); ?></div>
                                    <div class="product-color">Color: <?php echo htmlspecialchars($row['selected_color'] ?? 'Standard'); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="price-text">RM <?php echo number_format($row['price'], 2); ?></td>
                        <td>
                            <div class="qty-control">
                                <button type="button" class="qty-btn" onclick="updateQuantity(<?php echo $row['id']; ?>, <?php echo $row['price']; ?>, -1)">-</button>
                                <input type="number" id="qty-<?php echo $row['id']; ?>" value="<?php echo $row['quantity']; ?>" min="1" class="qty-input" readonly>
                                <button type="button" class="qty-btn" onclick="updateQuantity(<?php echo $row['id']; ?>, <?php echo $row['price']; ?>, 1)">+</button>
                            </div>
                        </td>
                        <td class="subtotal-text" id="subtotal-<?php echo $row['id']; ?>">RM <?php echo number_format($subtotal, 2); ?></td>
                        <td>
                            <a href="javascript:void(0)" onclick="removeItem(<?php echo $row['id']; ?>)" class="remove-link">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <div class="cart-footer">
                <div>
                    <a href="HomePage.php" style="text-decoration:none; color:#4b310b; font-weight:600;">
                        <i class="fa-solid fa-arrow-left"></i> Continue Shopping
                    </a>
                </div>
                <div>
                    <div>
                        <span style="color: #666;">Selected (<span id="selected-count">0</span> items):</span>
                        <span class="total-amount" id="live-total">RM 0.00</span>
                    </div>
                    <button type="button" class="btn-update" onclick="ajaxUpdateCart()">
                        <i class="fa-solid fa-arrows-rotate"></i> Update
                    </button>
                    <button type="submit" class="btn-checkout" id="checkout-btn">
                        <i class="fa-solid fa-credit-card"></i> Checkout
                    </button>
                </div>
            </div>
        </form>
    <?php else: ?>
        <div style="text-align: center; padding: 80px 20px; background: #fff; border-radius: 20px;">
            <i class="fa-solid fa-cart-shopping" style="font-size: 4rem; color: #ddd;"></i>
            <h2 style="color: #999; margin-top: 20px;">Your cart is empty</h2>
            <a href="HomePage.php" style="display: inline-block; margin-top: 20px; background: #4b310b; color: #fff; padding: 12px 30px; border-radius: 12px; text-decoration: none;">
                Start Shopping
            </a>
        </div>
    <?php endif; ?>
</div>

<div id="cart-toast" class="cart-toast"></div>

<script>
function showToast(message) {
    const toast = document.getElementById('cart-toast');
    toast.innerText = message;
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
    }, 2500);
}

async function updateQuantity(cartId, price, delta) {
    const input = document.getElementById('qty-' + cartId);
    let newQty = parseInt(input.value) + delta;
    
    if (newQty === 0) {
        const confirmRemove = confirm('Remove this item from cart?');
        if (confirmRemove) {
            await removeItem(cartId);
        }
        return;
    }
    
    if (newQty < 1) return;
    
    input.value = newQty;
    
    const newSubtotal = price * newQty;
    const subtotalSpan = document.getElementById('subtotal-' + cartId);
    subtotalSpan.innerText = 'RM ' + newSubtotal.toFixed(2);
    
    const row = document.querySelector(`.cart-row[data-id="${cartId}"]`);
    const checkbox = row.querySelector('.item-checkbox');
    if (checkbox.checked) {
        checkbox.setAttribute('data-subtotal', newSubtotal);
        updateSelection();
    }
}

async function removeItem(cartId) {
    try {
        const response = await fetch('remove_from_cart_ajax.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + cartId
        });
        const data = await response.json();
        
        if (data.success) {
            const row = document.querySelector(`.cart-row[data-id="${cartId}"]`);
            row.remove();
            showToast('Item removed from cart');
            
            const remainingRows = document.querySelectorAll('.cart-row');
            if (remainingRows.length === 0) {
                location.reload();
            }
            updateSelection();
        } else {
            showToast('Failed to remove item');
        }
    } catch (err) {
        console.error('Error:', err);
        showToast('Error removing item');
    }
}

function updateSelection() {
    let total = 0;
    let count = 0;
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    const checkoutBtn = document.getElementById('checkout-btn');
    
    checkboxes.forEach(cb => {
        total += parseFloat(cb.getAttribute('data-subtotal') || 0);
        count++;
    });
    
    document.getElementById('live-total').innerText = 'RM ' + total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('selected-count').innerText = count;
    
    if (count > 0) {
        checkoutBtn.classList.add('active');
    } else {
        checkoutBtn.classList.remove('active');
    }
}

function toggleAll(source) {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = source.checked;
        const row = cb.closest('.cart-row');
        if (row) {
            const qty = document.getElementById('qty-' + row.getAttribute('data-id')).value;
            const price = parseFloat(row.querySelector('.price-text').innerText.replace('RM ', ''));
            cb.setAttribute('data-subtotal', price * qty);
        }
    });
    updateSelection();
}

async function ajaxUpdateCart() {
    const updates = [];
    const rows = document.querySelectorAll('.cart-row');
    
    rows.forEach(row => {
        const id = row.getAttribute('data-id');
        const qty = document.getElementById('qty-' + id).value;
        updates.push({ id: id, quantity: qty });
    });
    
    try {
        const response = await fetch('update_cart_batch.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ updates: updates })
        });
        const data = await response.json();
        
        if (data.success) {
            showToast('Cart updated successfully!');
            rows.forEach(row => {
                const id = row.getAttribute('data-id');
                const qty = document.getElementById('qty-' + id).value;
                const priceText = row.querySelector('.price-text').innerText;
                const price = parseFloat(priceText.replace('RM ', ''));
                const newSubtotal = price * qty;
                document.getElementById('subtotal-' + id).innerText = 'RM ' + newSubtotal.toFixed(2);
                
                const checkbox = row.querySelector('.item-checkbox');
                if (checkbox.checked) {
                    checkbox.setAttribute('data-subtotal', newSubtotal);
                }
            });
            updateSelection();
        } else {
            showToast('Failed to update cart');
        }
    } catch (err) {
        console.error('Error:', err);
        showToast('Error updating cart');
    }
}

document.querySelectorAll('.cart-row').forEach(row => {
    const id = row.getAttribute('data-id');
    const qty = document.getElementById('qty-' + id).value;
    const priceText = row.querySelector('.price-text').innerText;
    const price = parseFloat(priceText.replace('RM ', ''));
    const checkbox = row.querySelector('.item-checkbox');
    checkbox.setAttribute('data-subtotal', price * qty);
});
</script>

<?php include('footer.php'); ?>
</body>
</html>
