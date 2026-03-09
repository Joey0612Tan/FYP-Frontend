    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .product-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); 
            gap: 50px;
            padding: 40px;
            margin-left: 90px;
            max-width: 1400px;
        }

        .product-card {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            background: #fff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            flex-direction: column;
            justify-content: space-between;
            width: 100%;
            height: 550px; 
            cursor: pointer; 
            transition: border 0.3s, transform 0.2s;
            position: relative;
        }

        .product-card:hover {
        border: 2px solid #947b54ff; 
        transform: translateY(-3px); 
        }

        .product-card img {
            width: 300px;       
            height: 300px;    
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 1px;
            flex-shrink: 0;
        }

        .product-card h3 {
            font-size: 1.8rem;  
            font-weight: bold;
            margin: 1px 0 5px;
            color: #333;
            -webkit-line-clamp: 2;
            min-height: 3.2rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;         
            -webkit-box-orient: vertical;   
            overflow: hidden;               
            text-overflow: ellipsis;
        }

        .product-card p {
            font-size: 1.3rem;
            margin: 4px 0;
            color: #555; 
        }

        .card-actions {
        margin-top: auto; 
        padding-top: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

        .seller-rating {
        display: flex;
        justify-content: space-between; 
        align-items: center;
        width: 250;
        font-size: 1.3rem;
        color: #555;
        margin: 5px 0;
        }

        .rating {
        color: #ecd53aff;
        font-weight: 600;
        }


        .product-card .price {
        font-size: 2rem;
        font-weight: bold;
        color: #E53935; 
        }

        .product-actions {
        display: flex;
        justify-content: space-between;
        gap: 25px;
        margin-top: auto;
        }

        .product-actions button {
        flex: 1;
        padding: 8px 0;
        font-size: 1.3rem;
        font-weight: 600;
        border: none;
        border-radius: 25px;       
        cursor: pointer;
        color: #fff;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;                 
        box-shadow: 0 2px 5px rgba(0,0,0,0.2); 
        }

        .review-summary {
        background: linear-gradient(135deg, #FFC107, #FFB300);
        color: #333;
        }
        .review-summary:hover {
        background: linear-gradient(135deg, #FFB300, #FFA000);
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.25);
        }

        .add-compare  {
        background: linear-gradient(135deg, #2196F3, #1E88E5);
        }
        .add-compare:hover  {
        background: linear-gradient(135deg, #1E88E5, #1976D2);
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.25);
        }

        .add-compare   { background-color: #2196F3; }
        .review-summary { background-color: #FFC107; color: #333; }

        .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0; top: 0;
        width: 100%; height: 100%;
        background-color: rgba(0,0,0,0.6); 
        backdrop-filter: blur(2px); 
        animation: fadeIn 0.3s ease;
        }

        .modal-content {
        background: #fff;
        margin: 5% auto;
        padding: 25px 30px;
        border-radius: 12px; 
        width: 60%; 
        max-width: 700px; 
        position: relative;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        transform: scale(0.8);
        animation: scaleIn 0.3s forwards;
        }

        .close {
        position: absolute; 
        top: 12px; 
        right: 15px; 
        cursor: pointer; 
        font-size: 1.6rem; 
        font-weight: bold;
        color: #555;
        transition: color 0.2s ease;
        }

        .close:hover {
        color: #E53935;
        }

        @keyframes fadeIn {
        from {opacity:0;}
        to {opacity:1;}
        }

        @keyframes scaleIn {
        from {transform: scale(0.8);}
        to {transform: scale(1);}
        }

        .modal-content h3 {
        font-size: 2rem;
        color: #333;
        margin-bottom: 15px;
    }

        #review-text {
        font-size: 1.1rem;
        line-height: 1.6;
        color: #555;
        max-height: 400px;
        overflow-y: auto; 
        padding-right: 5px;
        }

        .toast {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: #4CAF50;
        color: #fff;
        padding: 12px 20px;
        border-radius: 25px;
        font-size: 1.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        opacity: 0;
        pointer-events: none;
        transition: all 0.4s ease;
        }

        .toast.show {
        opacity: 1;
        transform: translateY(-10px);
        pointer-events: auto;
        }

        @media (max-width: 768px) {
            .product-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); 
            gap: 10px;
            padding: 10px;
            margin-left: 5px;
        }

        .product-card {
            border: 0.75px solid #ddd;
            padding: 10px;
            text-align: center;
            background: #fff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            flex-direction: column;
            justify-content: space-between;
            width: 100%;
            height: 380px; 
            cursor: pointer; 
            transition: border 0.3s, transform 0.2s;
            position: relative;
        }

        .product-card:hover {
        border: 1px solid #947b54ff; 
        transform: translateY(-3px); 
        }

        .product-card img {
            width: 200px;       
            height: 200px;    
            object-fit: cover;
            border-radius: 2.8px;
            margin-bottom: 1px;
            flex-shrink: 0;
        }

        .product-card h3 {
            font-size: 1.3rem;  
            font-weight: bold;
            margin: 1px 0 5px;
            color: #333;
            -webkit-line-clamp: 2;
            min-height: 2.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;         
            -webkit-box-orient: vertical;   
            overflow: hidden;               
            text-overflow: ellipsis;
        }

        .product-card p {
            font-size: 1.0rem;
            margin: 3px 0;
            color: #555; 
        }

        .card-actions {
        margin-top: auto; 
        padding-top: 15px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

        .seller-rating {
        display: flex;
        justify-content: space-between; 
        align-items: center;
        width: 180;
        font-size: 1.0rem;
        color: #555;
        margin: 3.5px 0;
        }

        .rating {
        color: #ecd53aff;
        font-weight: 400;
        }


        .product-card .price {
        font-size: 1.5rem;
        font-weight: bold;
        color: #E53935; 
        }

        .product-actions {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        margin-top: auto;
        }

        .product-actions button {
        flex: 1;
        padding: 6px 0;
        font-size: 1.0rem;
        font-weight: 600;
        border: none;
        border-radius: 18px;       
        cursor: pointer;
        color: #fff;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;                 
        box-shadow: 0 2px 5px rgba(0,0,0,0.2); 
        }

        .review-summary {
        background: linear-gradient(135deg, #FFC107, #FFB300);
        color: #333;
        }
        .review-summary:hover {
        background: linear-gradient(135deg, #FFB300, #FFA000);
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.25);
        }

        .add-compare  {
        background: linear-gradient(135deg, #2196F3, #1E88E5);
        }
        .add-compare:hover  {
        background: linear-gradient(135deg, #1E88E5, #1976D2);
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.25);
        }

        .add-compare   { background-color: #2196F3; }
        .review-summary { background-color: #FFC107; color: #333; }
    }
    </style>

    <body>
        
<div class="product-card" data-id="<?php echo $row['product_id']; ?>">
    <img src="<?php echo htmlspecialchars($row['image_main']); ?>">
    <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>           

    <div class="seller-rating">
        <span class="seller">Seller: <?php echo htmlspecialchars($row['seller_name']); ?></span>
        <span class="rating">⭐ <?php echo htmlspecialchars($row['rating']); ?></span>
    </div>

    <p class="price">RM <?php echo number_format($row['price'], 2); ?></p>

    <div class="product-actions">
        <button class="review-summary">Summarize Review</button>
        <button class="add-compare" onclick="window.location.href='add_to_compare.php?id=<?php echo $row['product_id']; ?>'">
            Add to Compare List
        </button>
    </div>
</div>      

<div id="review-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Product Reviews Summary</h3>
        <p id="review-text">Loading...</p>
    </div>
</div>

<div id="toast" class="toast"></div>

<script>
document.querySelectorAll('.product-card').forEach(card=>{
    card.addEventListener('click', (e)=>{
        if(e.target.closest('.review-summary') || e.target.closest('.add-compare')) return;

        const productId = card.dataset.id;
        window.location.href = `Product_details.php?id=${productId}`;
    });
});


document.querySelectorAll('.review-summary').forEach(btn=>{
    btn.addEventListener('click',(e)=>{
        e.stopPropagation(); 
        const card = e.target.closest('.product-card');
        const productId = card.dataset.id;

        const modal = document.getElementById('review-modal');
        const reviewText = document.getElementById('review-text');

        reviewText.innerHTML = '<div class="spinner">🌀 <b>Gemma 3</b>  is analyzing reviews...</div>';
        modal.style.display = 'block';

        fetch(`get_review_summary.php?id=${productId}`)
            .then(res => res.text())
            .then(data => {
                reviewText.innerHTML = data;
            })
            .catch(err => {
                reviewText.innerHTML = 'Error fetching summary.';
            });
    });
});

window.addEventListener('click', (e) => {
    const modalIds = ['review-modal', 'ai-modal', 'compare-modal'];

    modalIds.forEach(id => {
        const modal = document.getElementById(id);
        if (modal && e.target === modal) {
            modal.style.display = 'none';
        }
    });
});

document.querySelectorAll('.add-compare').forEach(btn=>{
    btn.addEventListener('click',(e)=>{
        e.preventDefault();
        e.stopPropagation();
        showToast('✅ Product successfully added to compare list!');
    });
});

function showToast(message) {
    const toast = document.getElementById('toast');
    toast.innerText = message;
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
    }, 2000);
}

</script>





