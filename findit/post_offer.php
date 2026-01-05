<?php 
session_start();
include 'header.php'; 
include 'db_connect.php'; 

// 1. SECURITY: Only allow logged-in RESTAURANTS
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'restaurant') {
    $_SESSION['status'] = "error";
    $_SESSION['message'] = "Access Denied. Only registered restaurants can post offers.";
    header("Location: index.php"); 
    exit();
}
?>

<style>
    .offer-container {
        max-width: 600px;
        margin: 120px auto 60px;
        padding: 40px;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid #f0f0f0;
    }
    .section-title { text-align: center; font-size: 1.8rem; font-weight: 800; color: #333; margin-bottom: 10px; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #444; }
    .modern-input { width: 100%; padding: 12px 16px; font-size: 1rem; border: 2px solid #e1e1e1; border-radius: 12px; background: #f9f9f9; }
    .btn-submit { width: 100%; padding: 14px; background: #28a745; color: white; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; }
</style>

<div class="offer-container">
    <h2 class="section-title">Post Food Offer</h2>

    <form action="offer_action.php" method="POST" enctype="multipart/form-data">
        
        <div class="form-group">
            <label>Food Item Name</label>
            <input type="text" name="Name" class="modern-input" placeholder="e.g. Chicken Rice Box" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="Desc" class="modern-input" rows="4" placeholder="What is included?" required></textarea>
        </div>

        <div class="form-group" style="display:flex; gap:15px;">
            <div style="flex:1;">
                <label>Pickup Location</label>
                <input type="text" name="location" class="modern-input" placeholder="e.g. Dhanmondi Branch" required>
            </div>
            <div style="flex:1;">
                <label>Pickup Time</label>
                <input type="text" name="Time" class="modern-input" placeholder="e.g. 8:00 PM - 10:00 PM" required>
            </div>
        </div>

        <div class="form-group" style="display:flex; gap:15px;">
            <div style="flex:1;">
                <label>Original Price (Tk)</label>
                <input type="number" name="OriginalPrice" class="modern-input" placeholder="500">
            </div>
            <div style="flex:1;">
                <label style="color:#28a745;">Discounted Price (Tk)</label>
                <input type="number" name="DiscountedPrice" class="modern-input" placeholder="250" required>
            </div>
        </div>

        <div class="form-group">
            <label>Valid Until (Date)</label>
            <input type="date" name="event_Date" class="modern-input" required>
        </div>

        <div class="form-group">
            <label>Food Image</label>
            <input type="file" name="Image" class="modern-input" style="padding:10px;" accept="image/*" required>
        </div>

        <button type="submit" name="submit_offer" class="btn-submit">Publish Offer</button>
    </form>
</div>

<?php include 'footer.php'; ?>