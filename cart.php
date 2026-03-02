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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .cart-page-wrapper {
            max-width: 1600px;
            margin: 50px auto;
            padding: 0 40px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .cart-title {
            font-size: 2.8rem;
            color: #4b310b;
            margin-bottom: 40px;
            font-weight: bold;
        }

        .modern-cart-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 20px;
        }

        .modern-cart-table thead th {
            background-color: #f8f1e9;
            color: #4b310b;
            padding: 25px;
            font-size: 1.1rem;
            text-transform: uppercase;
            border: none;
        }

        .modern-cart-table thead th:first-child { 
            border-radius: 15px 0 0 15px; 
        }

        .modern-cart-table thead th:last-child { 
            border-radius: 0 15px 15px 0; 
        }

        .cart-row {
            background: #fff;
            box-shadow: 0 8px 25px rgba(0,0,0,0.05);
            transition: 0.3s;
        }

        .cart-row:hover { background: #fafafafa; }

        .cart-row td {
            padding: 30px 20px;
            vertical-align: middle;
        }

        .cart-row td:first-child { 
            border-radius: 15px 0 0 15px; 
        }

        .cart-row td:last-child { 
            border-radius: 0 15px 15px 0; 
        }

        .cart-checkbox {
            width: 25px;
            height: 25px;
            cursor: pointer;
            accent-color: #4b310b;
        }

        .product-cell {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .cart-img {
            width: 130px; 
            height: 130px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .product-name {
            font-size: 1.4rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }

        .product-color {
            color: #8e5c12;
            font-size: 1rem;
            font-weight: 500;
        }

        .subtotal-text {
            font-size: 1.4rem;
            font-weight: bold;
            color: #E53935;
        }

        .remove-link {
            color: #ff4d4d;
            text-decoration: none;
            font-size: 1.5rem;
            transition: 0.3s;
        }

        .remove-link:hover { 
            color: #b71c1c; 
        }

        .cart-footer {
            margin-top: 50px;
            background: #fff;
            padding: 40px;
            border-radius: 20px;
            display: flex;
            justify-content: space-between; 
            align-items: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            position: sticky;
            bottom: 20px;
        }

        .btn-checkout {
            background-color: #4b310b; 
            color: #fff !important;    
            padding: 18px 60px;
            border: none;
            border-radius: 12px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            opacity: 0.5; 
            pointer-events: none;
        }

        .btn-checkout.active {
            opacity: 1;
            pointer-events: auto;
            box-shadow: 0 5px 15px rgba(75, 49, 11, 0.3);
        }

        .btn-checkout:hover { 
            transform: translateY(-3px); 
            background-color: #6d4a16; 
        }

        .btn-update {
            background-color: transparent;
            color: #4b310b;
            padding: 15px 35px;
            border: 2px solid #4b310b; 
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-update:hover {
            background-color: #4b310b;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(75, 49, 11, 0.2);
        }

        .btn-update i {
            font-size: 1.2rem;
        }

        .total-amount {
            font-size: 3rem;
            color: #E53935;
            font-weight: bold;
            margin-left: 20px;
        }
    </style>
</head>
<body>

<div class="cart-page-wrapper">
    <h1 class="cart-title">Your Shopping Bag</h1>

    <?php if ($result->num_rows > 0): ?>
        <form action="checkout.php" method="POST" id="cart-form">
            <table class="modern-cart-table">
                <thead>
                    <tr>
                        <th width="5%"><input type="checkbox" id="select-all" onclick="toggleAll(this)" class="cart-checkbox"></th>
                        <th width="40%">Product Details</th>
                        <th width="15%">Price</th>
                        <th width="15%">Quantity</th>
                        <th width="15%">Subtotal</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): 
                        $subtotal = $row['price'] * $row['quantity'];
                    ?>
                    <tr class="cart-row">
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
                        
                        <td align="center">
                        <span class="unit-price" style="font-size: 1.2rem;">
                            RM <?php echo number_format($row['price'], 2); ?>
                        </span>
                    </td>

                    <td align="center">
                        <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <button type="button" onclick="stepQty(<?php echo $row['id']; ?>, -1)" 
                                    style="width:28px; height:28px; border-radius:5px; border:1px solid #ceb9a0; background:white; cursor:pointer;">-</button>
                            
                            <input type="number" 
                                id="qty-<?php echo $row['id']; ?>" 
                                name="qty[<?php echo $row['id']; ?>]" 
                                value="<?php echo $row['quantity']; ?>" 
                                min="1" 
                                onchange="syncSubtotal(<?php echo $row['id']; ?>, <?php echo $row['price']; ?>)"
                                style="width: 50px; text-align: center; border: 1px solid #ddd; padding: 5px; border-radius: 5px; font-weight: bold;">
                            
                            <button type="button" onclick="stepQty(<?php echo $row['id']; ?>, 1)" 
                                    style="width:28px; height:28px; border-radius:5px; border:1px solid #ceb9a0; background:#f8f1e9; cursor:pointer;">+</button>
                        </div>
                    </td>

                        <td align="center">
                            <span class="subtotal-text">RM <?php echo number_format($subtotal, 2); ?></span>
                        </td>
                        <td align="center">
                            <a href="remove_from_cart.php?id=<?php echo $row['id']; ?>" class="remove-link" onclick="return confirm('Remove this item?')">
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

                <div style="display: flex; align-items: center; gap: 40px;">
                    <div style="text-align: right;">
                        <span style="font-size: 1.2rem; color: #666;">Selected Total (<span id="count">0</span> items):</span>
                        <div class="total-amount" id="live-total">RM 0.00</div>
                    </div>

                    <button type="submit" class="btn-checkout" id="checkout-btn">
                        <i class="fa-solid fa-credit-card"></i> Checkout Selected
                    </button>

                    <button type="button" onclick="ajaxUpdateCart()" class="btn-update">
                        <i class="fa-solid fa-arrows-rotate"></i> Update Bag
                    </button>
                </div>
            </div>     
        </form>

        

    <?php else: ?>
        <div style="text-align: center; padding: 120px 20px; background: #fff; border-radius: 20px;">
            <i class="fa-solid fa-cart-shopping" style="font-size: 5rem; color: #eee; margin-bottom: 30px;"></i>
            <h2 style="color: #999;">Your cart is empty...</h2>
            <a href="HomePage.php" style="display: inline-block; margin-top: 25px; background: #ceb9a0; color: #fff; padding: 15px 40px; border-radius: 12px; text-decoration: none; font-weight: bold;">
                Start Shopping
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
    function stepQty(cartId, delta) {
        const input = document.getElementById('qty-' + cartId);
        let newVal = parseInt(input.value) + delta;
        if (newVal < 1) newVal = 1;
        input.value = newVal;
        
        input.dispatchEvent(new Event('change'));
    }

    function syncSubtotal(cartId, price) {
        const input = document.getElementById('qty-' + cartId);
        const newQty = parseInt(input.value);
        const newSubtotal = price * newQty;
        
        const row = input.closest('.cart-row');
        row.querySelector('.subtotal-text').innerText = 'RM ' + newSubtotal.toFixed(2);
        
        const checkbox = row.querySelector('.item-checkbox');
        checkbox.setAttribute('data-subtotal', newSubtotal);
        
        updateSelection();
    }

    function updateSelection() {
        let total = 0;
        let count = 0;
        const checkboxes = document.querySelectorAll('.item-checkbox:checked');
        const checkoutBtn = document.getElementById('checkout-btn');
        
        checkboxes.forEach(cb => {
            total += parseFloat(cb.getAttribute('data-subtotal'));
            count++;
        });

        document.getElementById('live-total').innerText = 'RM ' + total.toLocaleString(undefined, {minimumFractionDigits: 2});
        document.getElementById('count').innerText = count;
        
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
        });
        updateSelection();
    }

    function ajaxUpdateCart() {
    const form = document.getElementById('cart-form');
    const formData = new FormData(form);

    fetch('update_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        showToast("Bag updated successfully! ✨");
    })
    .catch(error => {
        console.error('Error:', error);
        showToast("Failed to update cart. ❌");
    });
}
</script>

<?php include('footer.php'); ?>
</body>
</html>