<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

/* ========================
   PRODUCT GRID
======================== */

.product-container{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(320px,1fr));
    gap:30px;
    padding:30px;
    margin-left:90px;
    max-width:1400px;
}


/* ========================
   PRODUCT CARD
======================== */

.product-card{

    border:1px solid #ddd;
    background:#fff;
    border-radius:12px;

    display:flex;
    flex-direction:column;
    justify-content:flex-start;

    text-align:center;
    padding:15px;

    min-height:520px;
    width:100%;

    cursor:pointer;
    position:relative;

    transition:all .25s ease;
}

.product-card:hover{
    border:2px solid #947b54ff;
    transform:translateY(-3px);
}


/* ========================
   IMAGE
======================== */

.product-card img{

    width:260px;
    height:260px;

    object-fit:cover;
    border-radius:6px;

    margin:0 auto 10px;
}


/* ========================
   TITLE
======================== */

.product-card h3{

    font-size:1.6rem;
    font-weight:bold;
    color:#333;

    margin:5px 0;

    display:-webkit-box;
    -webkit-line-clamp:2;
    -webkit-box-orient:vertical;

    overflow:hidden;
}


/* ========================
   PRICE
======================== */

.product-card .price{

    font-size:1.9rem;
    font-weight:bold;

    color:#E53935;

    margin:6px 0;
}


/* ========================
   SELLER RATING
======================== */

.seller-rating{

    display:flex;
    justify-content:space-between;
    align-items:center;

    font-size:1.2rem;
    color:#555;

    margin:4px 0;
}

.rating{
    color:#ecd53a;
    font-weight:600;
}


/* ========================
   ACTION BUTTONS
======================== */

.product-actions{

    margin-top:auto;

    display:flex;
    flex-direction:column;
    gap:10px;
}


.product-actions button{

    padding:10px;

    font-size:1.2rem;
    font-weight:600;

    border:none;
    border-radius:25px;

    cursor:pointer;
    color:#fff;

    transition:.25s;

    box-shadow:0 2px 6px rgba(0,0,0,.2);
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


/* ========================
   MODAL
======================== */

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
    right:15px;
    top:10px;

    font-size:1.6rem;
    cursor:pointer;
}

.close:hover{
    color:#E53935;
}


#review-text{

    font-size:1.1rem;
    line-height:1.6;

    color:#555;

    max-height:400px;
    overflow-y:auto;
}


/* ========================
   TOAST
======================== */

.toast{

    position:fixed;

    bottom:30px;
    left:50%;

    transform:translateX(-50%) translateY(20px);

    background:rgba(0,0,0,.85);
    color:#fff;

    padding:12px 30px;

    border-radius:50px;

    font-size:1.3rem;

    opacity:0;
    pointer-events:none;

    transition:.35s;

    z-index:9999;
}

.toast.show{

    opacity:1;
    transform:translateX(-50%) translateY(0);
}


/* ========================
   MOBILE
======================== */

@media (max-width:768px){

.product-container{

    grid-template-columns:repeat(2,1fr);

    gap:15px;

    padding:15px;

    margin-left:0;
}

.product-card{

    min-height:460px;
}

.product-card img{

    width:150px;
    height:150px;
}

.product-card h3{
    font-size:1.1rem;
}

.product-card .price{
    font-size:1.3rem;
}

.product-actions button{
    font-size:0.9rem;
}

}

</style>


<body>


<!-- PRODUCT CARD -->
<div class="product-card" data-id="<?php echo $row['product_id']; ?>">

<img src="<?php echo htmlspecialchars($row['image_main']); ?>">

<h3><?php echo htmlspecialchars($row['product_name']); ?></h3>

<div class="seller-rating">
<span>Seller: <?php echo htmlspecialchars($row['seller_name']); ?></span>
<span class="rating">⭐ <?php echo htmlspecialchars($row['rating']); ?></span>
</div>

<p class="price">RM <?php echo number_format($row['price'],2); ?></p>

<div class="product-actions">

<button type="button" class="review-summary">
Summarize Review
</button>

<button type="button" class="add-compare">
Add to Compare List
</button>

</div>

</div>


<!-- REVIEW MODAL -->
<div id="review-modal" class="modal">

<div class="modal-content">

<span class="close">&times;</span>

<h3>Product Reviews Summary</h3>

<p id="review-text">Loading...</p>

</div>

</div>


<!-- TOAST -->
<div id="toast" class="toast"></div>



<script>

/* =====================
   CARD CLICK
===================== */

document.querySelectorAll('.product-card').forEach(card=>{

card.addEventListener('click',(e)=>{

if(e.target.closest('.review-summary') || 
   e.target.closest('.add-compare')) return;

const id = card.dataset.id;

window.location.href=`Product_details.php?id=${id}`;

});

});


/* =====================
   REVIEW SUMMARY
===================== */

document.querySelectorAll('.review-summary').forEach(btn=>{

btn.addEventListener('click',(e)=>{

e.stopPropagation();

const card = btn.closest('.product-card');
const id = card.dataset.id;

const modal=document.getElementById('review-modal');
const text=document.getElementById('review-text');

text.innerHTML="🌀 <b>Gemma 3</b> analyzing reviews...";

modal.style.display="block";

fetch(`get_review_summary.php?id=${id}`)

.then(r=>r.text())
.then(data=>text.innerHTML=data)
.catch(()=>text.innerHTML="Error loading summary");

});

});


/* =====================
   TOAST FUNCTION
===================== */

function showToast(message){

const toast=document.getElementById('toast');

toast.textContent=message;

toast.classList.add('show');

setTimeout(()=>{

toast.classList.remove('show');

},2500);

}


/* =====================
   ADD TO COMPARE
===================== */

document.querySelectorAll('.add-compare').forEach(btn=>{

btn.addEventListener('click',function(e){

e.stopPropagation();

const card=btn.closest('.product-card');
const id=card.dataset.id;

fetch(`add_to_compare.php?id=${id}`)

.then(res=>{

if(res.status===200){

showToast("✅ Added to compare list");

btn.innerText="In Compare";
btn.style.background="#aaa";
btn.style.pointerEvents="none";

}

else if(res.status===409){

showToast("⚠️ Already in compare list");

}

else{

showToast("❌ Server error");

}

})

.catch(()=>showToast("❌ Connection error"));

});

});


/* =====================
   CLOSE MODAL
===================== */

document.querySelector('.close').onclick=()=>{
document.getElementById('review-modal').style.display="none";
}

window.onclick=(e)=>{

const modal=document.getElementById('review-modal');

if(e.target===modal){

modal.style.display="none";

}

}

</script>
