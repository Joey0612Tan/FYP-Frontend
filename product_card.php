<style>
   .product-container{
   display:grid;
   grid-template-columns:repeat(auto-fill,minmax(350px,1fr));
   gap:70px;
   padding:40px;
   margin:0 auto;
   max-width:1400px;
   }
   
   .product-card{
   border:1px solid #ddd;
   padding:15px;
   text-align:center;
   background:#fff;
   border-radius:12px;
   display:flex;
   flex-direction:column;
   align-items:center;
   justify-content:space-between;
   width:100%;
   height:550px;
   cursor:pointer;
   transition:border .3s, transform .2s;
   position:relative;
   }
   
   .product-card:hover{
   border:2px solid #947b54ff;
   transform:translateY(-3px);
   }
   
   .product-card img{
   width:300px;
   height:300px;
   object-fit:cover;
   border-radius:4px;
   margin-bottom:1px;
   flex-shrink:0;
   }
   
   .product-card h3{
   font-size:1.8rem;
   font-weight:bold;
   margin:1px 0 5px;
   color:#333;
   display:-webkit-box;
   -webkit-line-clamp:2;
   -webkit-box-orient:vertical;
   overflow:hidden;
   text-overflow:ellipsis;
   min-height:3.2rem;
   }
   
   .product-card p{
   font-size:1.3rem;
   margin:4px 0;
   color:#555;
   }
   
   .seller-rating{
   display:flex;
   justify-content:space-between;
   align-items:center;
   width:250px;
   font-size:1.3rem;
   color:#555;
   margin:5px 0;
   }
   
   .rating{
   color:#ecd53a;
   font-weight:600;
   }
   
   .product-card .price{   
   font-size:2rem;
   font-weight:bold;   
   color:#E53935;   
   }
   
   .product-actions{
   display:flex;
   justify-content:space-between;
   gap:25px;   
   margin-top:auto;   
   }
   
   .product-actions button{   
   flex:1;   
   padding:8px 0;   
   font-size:1.3rem;
   font-weight:600;   
   border:none;
   border-radius:25px;   
   cursor:pointer;   
   color:#fff;   
   display:flex;
   align-items:center;
   justify-content:center;   
   gap:5px;   
   transition:.3s;   
   box-shadow:0 2px 5px rgba(0,0,0,.2);   
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
      
   /* ✅ in-list 状态样式 */
   .add-compare.in-list{   
   background:linear-gradient(135deg,#9E9E9E,#757575) !important;
   cursor:not-allowed;
   transform:none !important;
   box-shadow:none;
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
   width:60%;
   max-width:700px;   
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
   font-size:2rem;
   color:#333;
   margin-bottom:15px;
   }
   
   #review-text{
   font-size:1.1rem;   
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
   font-size:1.2rem;   
   opacity:0;
   pointer-events:none;   
   transition:.35s;   
   z-index:9999;   
   white-space:nowrap; /* ✅ 防止 toast 文字换行 */
   }
   
   .toast.show{
   opacity:1;
   transform:translateX(-50%) translateY(0);
   }
   
   /* ============================================
      📱 手机端响应式（核心调整区域）
   ============================================ */
   @media (max-width:768px){
   
   .product-container{
   grid-template-columns:repeat(2,1fr);
   gap:12px;       /* ✅ 间距稍微宽松一点 */
   padding:10px;
   }
   
   .product-card{
   height:auto;
   min-height:280px;
   padding:10px 8px;
   border-radius:10px;
   }
   
   .product-card img{
   width:100%;
   height:120px;   /* ✅ 图片高度略提升，更好看 */
   object-fit:cover;
   border-radius:6px;
   }
   
   .product-card h3{
   font-size:0.85rem;
   min-height:2.2rem;
   margin:6px 0 3px;
   }
   
   .seller-rating{
   width:100%;
   font-size:0.72rem;
   flex-direction:column;  /* ✅ 竖向排，避免挤压 */
   align-items:flex-start;
   gap:2px;
   padding:0 2px;
   }
   
   .product-card .price{
   font-size:1rem;
   margin:4px 0;
   }
   
   .product-actions{
   flex-direction:row;    /* ✅ 手机端改为横向排列，两按钮并排 */
   gap:6px;
   width:100%;
   margin-top:8px;
   }
   
   .product-actions button{
   font-size:0.65rem;     /* ✅ 字体再小一点适应小屏 */
   padding:6px 4px;
   border-radius:20px;
   gap:3px;
   box-shadow:0 1px 3px rgba(0,0,0,.15);
   }

   /* ✅ 手机端 toast 字体缩小，避免超出屏幕 */
   .toast{
   font-size:0.85rem;
   padding:10px 16px;
   max-width:90vw;
   white-space:normal;
   text-align:center;
   }

   /* ✅ 手机端 modal 更紧凑 */
   .modal-content{
   width:90%;
   padding:16px;
   margin:15% auto;
   }

   .modal-content h3{
   font-size:1.3rem;
   }

   #review-text{
   font-size:0.9rem;
   }

   }
</style>

<body>

<div class="product-card" data-id="<?php echo $row['product_id']; ?>">
   <img src="<?php echo htmlspecialchars($row['image_main']); ?>">
   <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>

   <div class="seller-rating">
      <span>Seller: <?php echo htmlspecialchars($row['seller_name']); ?></span>
      <span class="rating">⭐ <?php echo htmlspecialchars($row['rating']); ?></span>
   </div>

   <p class="price">RM <?php echo number_format($row['price'],2); ?></p>

   <div class="product-actions">
      <button class="review-summary">
         Summarize Review
      </button>

      <button
         class="add-compare <?php echo $is_in_list ? 'in-list' : ''; ?>"
         data-inlist="<?php echo $is_in_list ? '1' : '0'; ?>"
         onclick="handleCompare(this, <?php echo $row['product_id']; ?>)">
         <?php echo $is_in_list ? '✔ In List' : '+ Add to Compare'; ?>
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

   /* ── 点击卡片跳转（排除按钮区域） ── */
   document.querySelectorAll('.product-card').forEach(card => {
      card.addEventListener('click', (e) => {
         if (e.target.closest('.review-summary') ||
             e.target.closest('.add-compare')) return;
         const id = card.dataset.id;
         window.location.href = `product_details.php?id=${id}`;
      });
   });

   /* ── Summarize Review 弹窗 ── */
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

   /* ── Add to Compare 逻辑（200 / 409 分流） ── */
   function handleCompare(btn, productId) {

      /* 本地已标记 in-list → 直接 toast，不发请求 */
      if (btn.dataset.inlist === "1") {
         showToast("⚠️ Already in compare list");
         return;
      }

      fetch(`add_to_compare.php?id=${productId}`)
         .then(res => {

            if (res.status === 200) {
               /* ✅ 成功加入 */
               showToast("✅ Product added to compare list successfully!");
               btn.innerHTML    = "✔ In List";
               btn.dataset.inlist = "1";
               btn.classList.add("in-list");
            }
            else if (res.status === 409) {
               /* ⚠️ 服务端回报已存在 → 同步本地状态 + toast */
               showToast("⚠️ Already in compare list");
               btn.innerHTML    = "✔ In List";
               btn.dataset.inlist = "1";
               btn.classList.add("in-list");
            }
            else {
               showToast("❌ Error adding product. Please try again.");
            }
         })
         .catch(() => showToast("❌ Connection error. Please try again."));
   }

   /* ── Toast 工具函数 ── */
   function showToast(message) {
      const toast = document.getElementById("toast");
      toast.innerText = message;
      toast.classList.add("show");
      setTimeout(() => toast.classList.remove("show"), 2500);
   }

   /* ── 点击 modal 背景关闭 ── */
   window.addEventListener('click', (e) => {
      const modal = document.getElementById('review-modal');
      if (e.target === modal) modal.style.display = "none";
   });

   /* ── 点击 × 关闭 ── */
   document.querySelector('.close').addEventListener('click', () => {
      document.getElementById('review-modal').style.display = "none";
   });

</script>
