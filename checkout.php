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

<div style="max-width: 1400px; margin: 40px auto; padding: 0 40px; font-family: 'Segoe UI', sans-serif;">
    
    <div id="custom-toast"></div>

    <h1 style="font-size: 3rem; color: #4b310b; margin-bottom: 40px; font-weight: bold;">🛡️ Order Confirmation</h1>

    <div style="display: flex; gap: 40px; align-items: flex-start;">
        
        <div style="flex: 2; background: white; border-radius: 25px; box-shadow: 0 15px 50px rgba(0,0,0,0.05); padding: 40px;">
            <h3 style="margin-top: 0; margin-bottom: 25px; color: #666; border-bottom: 2px solid #f8f1e9; padding-bottom: 15px;">Selected Products</h3>
            
            <?php foreach($items as $item): ?>
            <div style="display: flex; align-items: center; gap: 30px; padding: 25px 0; border-bottom: 1px solid #f0f0f0;">
                <img src="<?php echo $item['image_main']; ?>" style="width: 120px; height: 120px; object-fit: cover; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                <div style="flex-grow: 1;">
                    <h2 style="margin: 0; font-size: 1.6rem; color: #333;"><?php echo $item['product_name']; ?></h2>
                    <p style="color: #8e5c12; font-size: 1.1rem; margin: 10px 0;">
                        <span style="background: #f8f1e9; padding: 5px 15px; border-radius: 20px;">Color: <?php echo $item['selected_color']; ?></span>
                        <span style="margin-left: 15px;">Quantity: <b><?php echo $item['quantity']; ?></b></span>
                    </p>
                </div>
                <div style="font-size: 1.5rem; font-weight: bold; color: #333;">
                    RM <?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="flex: 1; background: #fffdf9; border-radius: 25px; box-shadow: 0 15px 50px rgba(0,0,0,0.08); padding: 40px; border: 1px solid #f1e4d5; position: sticky; top: 100px;">
            <h3 style="margin-top: 0; color: #4b310b;">Payment Summary</h3>
            <hr style="border: none; border-top: 1px dashed #ceb9a0; margin: 25px 0;">
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 1.2rem;">
                <span style="color: #666;">Total Items</span>
                <span style="font-weight: 600;"><?php echo count($items); ?></span>
            </div>

            <div style="display: flex; justify-content: space-between; margin-bottom: 30px;">
                <span style="font-size: 1.4rem; font-weight: bold; color: #333;">Grand Total</span>
                <span style="font-size: 2.2rem; font-weight: 900; color: #E53935;">RM <?php echo number_format($total_amount, 2); ?></span>
            </div>

            <button onclick="processPayment()" style="width: 100%; padding: 22px; background: #4b310b; color: white; border: none; border-radius: 15px; cursor: pointer; font-size: 1.3rem; font-weight: bold; margin-bottom: 20px; transition: 0.3s; box-shadow: 0 10px 20px rgba(75, 49, 11, 0.2);">
                Confirm & Pay Now
            </button>
            
            <a href="cart.php" style="display: block; text-align: center; padding: 18px; color: #ceb9a0; text-decoration: none; font-weight: 600; font-size: 1.1rem;">
                <i class="fa-solid fa-rotate-left"></i> Change Selection
            </a>
        </div>

    </div>
</div>

<style>
#custom-toast {
    position: fixed;
    top: 30px;                
    left: 50%;                
    transform: translateX(-50%);
    background-color: #4b310b;
    color: white;
    padding: 18px 40px;
    border-radius: 50px;     
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    display: none;
    z-index: 9999;
    font-size: 1.1rem;
    font-weight: bold;
    min-width: 300px;
    text-align: center;
}

#custom-toast.show {
    display: block;
    animation: slideDown 0.5s ease-out; 
}

@keyframes slideDown {
    from { opacity: 0; transform: translate(-50%, -50px); }
    to { opacity: 1; transform: translate(-50%, 0); }
}
</style>

<script>
    
function showToast(message) {
    const toast = document.getElementById('custom-toast');
    toast.innerText = message;
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

function processPayment() {
    const ids = <?php echo json_encode($ids); ?>;
    
    if (!ids || ids.length === 0) return;

    fetch('process_payment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ cart_ids: ids })
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            showToast("Order Placed Successfully!"); 
            
            setTimeout(() => {
                window.location.href = "HomePage.php";
            }, 2000);
        } else {
            alert("Error processing payment.");
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert("Server error, please try again.");
    });
}
</script>
<?php include('footer.php'); ?>