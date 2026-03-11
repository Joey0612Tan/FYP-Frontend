<style>
   .product-container{
   display:grid;
   grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
   gap:20px;
   padding:20px;
   margin:0 auto;
   max-width:1200px;
   width:100%;
   }
   
   .product-card{
   border:1px solid #ddd;
   padding:12px;
   text-align:center;
   background:#fff;
   border-radius:12px;
   display:flex;
   flex-direction:column;
   align-items:center;
   justify-content:space-between;
   width:100%;
   height:auto;
   min-height:480px;
   cursor:pointer;
   transition:border .3s, transform .2s;
   position:relative;
   }
   
   .product-card:hover{
   border:2px solid #947b54ff;
   transform:translateY(-3px);
   }
   

   
   .product-image-wrapper{
   width:100%;
   aspect-ratio:1/1;
   border-radius:8px;
   overflow:hidden;
   margin-bottom:8px;
   flex-shrink:0;
   background:#f5f5f5;
   }
   
   .product-card img{
   width:100%;
   height:100%;
   object-fit:cover;
   }
   
   .product-card h3{
   font-size:1.1rem;
   font-weight:bold;
   margin:8px 0 6px;
   color:#333;
   display:-webkit-box;
   -webkit-line-clamp:2;
   -webkit-box-orient:vertical;
   overflow:hidden;
   text-overflow:ellipsis;
   min-height:2.5rem;
   }
   
   .product-card p{
   font-size:0.95rem;
   margin:4px 0;
   color:#555;
   }
   
   .seller-rating{
   display:flex;
   justify-content:space-between;
   align-items:center;
   width:100%;
   font-size:0.9rem;
   color:#555;
   margin:6px 0;
   gap:5px;
   }
   
   .rating{
   color:#ecd53a;
   font-weight:600;
   }
   
   .product-card .price{   
   font-size:1.5rem;
   font-weight:bold;   
   color:#E53935;
   margin:6px 0;
   }
   
   .product-actions{
   display:flex;
   justify-content:center;
   gap:8px;   
   margin-top:auto;
   width:100%;
   }
   
   .product-actions button{   
   flex:1;   
   padding:8px 6px;   
   font-size:0.9rem;
   font-weight:600;   
   border:none;
   border-radius:6px;   
   cursor:pointer;   
   color:#fff;   
   display:flex;
   align-items:center;
   justify-content:center;   
   gap:4px;   
   transition:.3s;   
   box-shadow:0 2px 5px rgba(0,0,0,.15);
   white-space:nowrap;
   }
   
   .review-summary{   
   background:linear-gradient(135deg,#FFC107,#FFB300);
   color:#333;   
   }
   
   .review-summary:hover{   
   background:linear-gradient(135deg,#FFB300,#FFA000);
   transform:translateY(-2px);   
   }
      
   .add-compare{   
   background:linear-gradient(135deg,#2196F3,#1E88E5);   
   }
   
   .add-compare:hover{   
   background:linear-gradient(135deg,#1E88E5,#1976D2);
   transform:translateY(-2px);   
   }
      
   .add-compare.in-list{   
   background:linear-gradient(135deg,#9E9E9E,#757575) !important;
   cursor:not-allowed;
   transform:none !important;
   }
   
   .modal{   
   display:none;
   position:fixed;   
   z-index:1000;   
   left:0;
   top:0;   
   width:100%;
   height:100%;   
   background:rgba(0,0,0,.6);   
   backdrop-filter:blur(2px);   
   }
   
   .modal-content{   
   background:#fff;   
   margin:5% auto;
   padding:25px 30px;   
   border-radius:12px;   
   width:80%;
   max-width:600px;   
   position:relative;   
   box-shadow:0 10px 30px rgba(0,0,0,.3);   
   }
   
   .close{   
   position:absolute;   
   top:12px;
   right:15px;  
   cursor:pointer;   
   font-size:1.6rem;
   font-weight:bold;   
   color:#555;   
   }
   
   .close:hover{
   color:#E53935;
   }
   
   .modal-content h3{
   font-size:1.8rem;
   color:#333;
   margin-bottom:15px;
   }
   
   #review-text{
   font-size:1rem;   
   line-height:1.6;   
   color:#555;   
   max-height:400px;
   overflow-y:auto;   
   }
   
   .toast{
   position:fixed;   
   top:25px;
   left:50%;   
   transform:translateX(-50%) translateY(-20px);   
   background:#333;
   color:#fff;   
   padding:12px 22px;
   border-radius:30px;
   font-size:1rem;   
   opacity:0;
   pointer-events:none;   
   transition:.35s;   
   z-index:9999;
   white-space:nowrap;
   }
   
   .toast.show{
   opacity:1;
   transform:translateX(-50%) translateY(0);
   }

   @media (max-width:768px){
   
   .product-container{
   grid-template-columns:repeat(2,1fr);
   gap:15px;
   padding:10px;
   max-width:100%;
   }
   
   .product-card{
   min-height:380px;
   padding:8px;
   border-radius:8px;
   }
   
   .product-image-wrapper{
   margin-bottom:6px;
   }
   
   .product-card h3{
   font-size:0.85rem;
   min-height:2rem;
   margin:4px 0 2px;
   }
   
   .seller-rating{
   font-size:0.7rem;
   flex-direction:column;
   align-items:flex-start;
   gap:2px;
   }
   
   .product-card .price{
   font-size:1rem;
   margin:4px 0;
   }
   
   .product-actions{
   gap:6px;
   margin-top:6px;
   }
   
   .product-actions button{
   font-size:0.7rem;
   padding:6px 4px;
   border-radius:4px;
   }

   .toast{
   font-size:0.8rem;
   padding:10px 16px;
   max-width:85vw;
   white-space:normal;
   }

   .modal-content{
   width:90%;
   padding:16px;
   margin:20% auto;
   }

   .modal-content h3{
   font-size:1.3rem;
   }

   #review-text{
   font-size:0.85rem;
   }

   }
</style>

<body>

<div class="product-card" data-id="<?php echo $row['product_id']; ?>">
   
   <div class="product-image-wrapper">
      <img src="<?php echo htmlspecialchars($row['image_main']); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
   </div>
   
   <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>

   <div class="seller-rating">
      <span>Seller: <?php echo htmlspecialchars($row['seller_name']); ?></span>
      <span class="rating">⭐ <?php echo htmlspecialchars($row['rating']); ?></span>
   </div>

   <p class="price">RM <?php echo number_format($row['price'],2); ?></p>

   <div class="product-actions">
      <button class="review-summary">
         📊 Review
      </button>

      <button
         class="add-compare <?php echo (isset($is_in_list) && $is_in_list) ? 'in-list' : ''; ?>"
         data-inlist="<?php echo (isset($is_in_list) && $is_in_list) ? '1' : '0'; ?>"
         onclick="handleCompare(this, <?php e
   
   cho $row['product_id']; ?>)">
         <?php echo (isset($is_in_list) && $is_in_list) ? '✔ In List' : '+ Compare'; ?>
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

   document.querySelectorAll('.product-card').forEach(card => {
      card.addEventListener('click', (e) => {
         if (e.target.closest('.review-summary') ||
             e.target.closest('.add-compare')) return;
         const id = card.dataset.id;
         window.location.href = `product_details.php?id=${id}`;
      });
   });

   /* ── Summarize Review ── */
   document.querySelectorAll('.review-summary').forEach(btn => {
      btn.addEventListener('click', (e) => {
         e.stopPropagation();
         const card = btn.closest('.product-card');
         const id   = card.dataset.id;
         const modal = document.getElementById('review-modal');
         const text  = document.getElementById('review-text');

         text.innerHTML = "🌀 <b>Gemma 3</b> is analyzing reviews...";
         modal.style.display = "block";

         fetch(`get_review_summary.php?id=${id}`)
            .then(res => res.text())
            .then(data => text.innerHTML = data)
            .catch(() => text.innerHTML = "❌ Error fetching summary.");
      });
   });

   function handleCompare(btn, productId) {
    if (btn.dataset.inlist === "1") {
        showToast("⚠️ Already in compare list");
        return;
    }

    btn.style.pointerEvents = "none";

    fetch(`add_to_compare.php?id=${productId}`)
        .then(res => {
            if (res.status === 200 || res.status === 409) {
                const msg = res.status === 200 ? "✅ Added to compare list!" : "⚠️ Already in list";
                showToast(msg);
                
                setTimeout(() => {
                    window.location.reload(); 
                }, 800);
            } else {
                showToast("❌ Error adding product");
                btn.style.pointerEvents = "auto";
            }
        })
        .catch(() => {
            showToast("❌ Connection error");
            btn.style.pointerEvents = "auto";
        });
}

   function showToast(message) {
      const toast = document.getElementById("toast");
      toast.innerText = message;
      toast.classList.add("show");
      setTimeout(() => toast.classList.remove("show"), 2500);
   }

   window.addEventListener('click', (e) => {
      const modal = document.getElementById('review-modal');
      if (e.target === modal) modal.style.display = "none";
   });

   document.querySelector('.close').addEventListener('click', () => {
      document.getElementById('review-modal').style.display = "none";
   });

</script>



