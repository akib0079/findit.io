<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'header.php'; 
include 'db_connect.php'; 

// --- FILTER LOGIC (Keep existing logic) ---
$whereClauses = ["Status = 'Active'", "event_Date >= CURDATE()"];
$params = [];
$types = "";

$search = isset($_GET['search']) ? $_GET['search'] : '';
if (!empty($search)) {
    $whereClauses[] = "(Name LIKE ? OR `Desc` LIKE ?)";
    $searchTerm = "%" . $search . "%";
    $params[] = $searchTerm; $params[] = $searchTerm; $types .= "ss";
}

$minPrice = isset($_GET['min_price']) ? $_GET['min_price'] : '';
if (!empty($minPrice)) {
    $whereClauses[] = "DiscountedPrice >= ?";
    $params[] = $minPrice; $types .= "d";
}

$maxPrice = isset($_GET['max_price']) ? $_GET['max_price'] : '';
if (!empty($maxPrice)) {
    $whereClauses[] = "DiscountedPrice <= ?";
    $params[] = $maxPrice; $types .= "d";
}

$whereSql = implode(" AND ", $whereClauses);
$sql = "SELECT f.*, u.full_name as RestaurantName, u.contact_no 
        FROM tbl_food f 
        LEFT JOIN tbl_users u ON f.res_ID = u.ID 
        WHERE $whereSql 
        ORDER BY f.event_Date ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$result = $stmt->get_result();
?>

<style>
    /* ... [Keep existing Page Layout & Sidebar CSS] ... */
    .food-page-container { display: flex; max-width: 1200px; margin: 0 auto; padding: 160px 20px; gap: 30px; min-height: 80vh; }
    .sidebar { flex: 0 0 300px; width: 30%; }
    .filter-card { background: white; padding: 25px; border-radius: 16px; border: 1px solid #eee; box-shadow: 0 5px 20px rgba(0,0,0,0.03); position: sticky; top: 100px; }
    .filter-title { font-size: 1.2rem; font-weight: 800; color: #333; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0; }
    .filter-group { margin-bottom: 20px; }
    .filter-group label { display: block; font-weight: 600; color: #555; margin-bottom: 8px; font-size: 0.9rem; }
    .filter-input { width: 100%; padding: 10px 15px; border: 2px solid #e1e1e1; border-radius: 10px; font-size: 0.95rem; box-sizing: border-box; }
    .btn-apply { width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; }
    .btn-reset { display: block; text-align: center; margin-top: 10px; color: #666; font-size: 0.9rem; text-decoration: none; }
    .content-area { flex: 1; }
    .offers-header { margin-bottom: 25px; }
    .offers-header h1 { font-size: 2rem; color: #222; margin: 0; font-weight: 800; }
    
    /* Food Grid & Card */
    .food-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; }
    .food-card { background: white; border-radius: 16px; overflow: hidden; border: 1px solid #eaeaea; transition: transform 0.2s, box-shadow 0.2s; display: flex; flex-direction: column; }
    .food-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
    .food-img-wrapper { height: 180px; position: relative; background: #f8f9fa; }
    .food-img { width: 100%; height: 100%; object-fit: cover; }
    .discount-badge { position: absolute; top: 15px; right: 15px; background: #ff4757; color: white; padding: 5px 10px; border-radius: 20px; font-weight: 700; font-size: 0.8rem; }
    .food-content { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
    .res-name { font-size: 0.8rem; color: #888; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; }
    .food-title { font-size: 1.2rem; margin: 5px 0 10px; color: #333; font-weight: 700; }
    .price-box { margin-bottom: 15px; display: flex; align-items: baseline; gap: 8px; }
    .final-price { font-size: 1.3rem; color: #28a745; font-weight: 800; }
    .old-price { font-size: 0.95rem; color: #aaa; text-decoration: line-through; }
    
    /* NEW: View Details Button */
    .btn-view-details {
        width: 100%; padding: 10px; background: #f8f9fa; color: #333; border: 1px solid #ddd;
        border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: auto; transition: 0.2s;
    }
    .btn-view-details:hover { background: #e2e6ea; border-color: #ccc; }

    /* --- MODAL STYLES --- */
    .modal-overlay {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.6); z-index: 3000; justify-content: center; align-items: center;
        backdrop-filter: blur(5px);
    }
    .modal-box {
        background: white; width: 90%; max-width: 600px; border-radius: 20px; overflow: hidden;
        box-shadow: 0 20px 50px rgba(0,0,0,0.2); animation: slideUp 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        display: flex; flex-direction: column; max-height: 90vh;
    }
    .modal-img { width: 100%; height: 250px; object-fit: cover; background: #f1f1f1; }
    .modal-content { padding: 30px; overflow-y: auto; }
    .modal-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px; }
    .modal-title { font-size: 1.8rem; margin: 0; color: #222; font-weight: 800; }
    .modal-close { background: none; border: none; font-size: 2rem; color: #999; cursor: pointer; line-height: 1; }
    
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; background: #f8f9fa; padding: 15px; border-radius: 12px; }
    .info-item strong { display: block; font-size: 0.75rem; color: #666; text-transform: uppercase; letter-spacing: 0.5px; }
    .info-item span { font-size: 0.95rem; color: #333; font-weight: 600; }
    
    .modal-desc { line-height: 1.6; color: #555; margin-bottom: 25px; }
    .modal-actions { padding: 20px; border-top: 1px solid #eee; text-align: right; }
    
    @media (max-width: 768px) { .food-page-container { flex-direction: column; } .sidebar { width: 100%; } }
    @keyframes slideUp { from { transform: translateY(50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>

<div class="food-page-container">
    
    <aside class="sidebar">
        <div class="filter-card">
            <div class="filter-title">Filter Offers</div>
            <form action="" method="GET">
                <div class="filter-group">
                    <label>Search Food</label>
                    <input type="text" name="search" class="filter-input" placeholder="e.g. Rice, Burger..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="filter-group">
                    <label>Price Range (Tk)</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="number" name="min_price" class="filter-input" placeholder="Min" value="<?php echo htmlspecialchars($minPrice); ?>">
                        <input type="number" name="max_price" class="filter-input" placeholder="Max" value="<?php echo htmlspecialchars($maxPrice); ?>">
                    </div>
                </div>
                <button type="submit" class="btn-apply">Apply Filters</button>
                <a href="food_offers.php" class="btn-reset">Reset All</a>
            </form>
        </div>
    </aside>

    <main class="content-area">
        <div class="offers-header">
            <h1>Fresh Food Offers</h1>
            <p>Save money and reduce waste by grabbing these delicious deals.</p>
        </div>

        <div class="food-grid">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // Extract Data
                    $name = htmlspecialchars($row['Name']);
                    $resName = htmlspecialchars($row['RestaurantName'] ?? 'Unknown Restaurant');
                    $contact = htmlspecialchars($row['contact_no'] ?? 'N/A');
                    $img = "uploads/" . htmlspecialchars($row['Image']);
                    $desc = htmlspecialchars($row['Desc']);
                    $price = $row['DiscountedPrice'];
                    $orig = $row['OriginalPrice'];
                    $date = date("M d, Y", strtotime($row['event_Date']));
                    $time = htmlspecialchars($row['Time']);
                    $loc = htmlspecialchars($row['location']);
                    
                    if (empty($row['Image']) || !file_exists($img)) { $img = "https://via.placeholder.com/400x250?text=Food+Offer"; }

                    // Calculate Discount %
                    $off = 0;
                    if($orig > 0) { $off = round((($orig - $price) / $orig) * 100); }
            ?>
                <div class="food-card">
                    <div class="food-img-wrapper">
                        <?php if($off > 0): ?>
                            <span class="discount-badge">-<?php echo $off; ?>% OFF</span>
                        <?php endif; ?>
                        <img src="<?php echo $img; ?>" class="food-img">
                    </div>
                    
                    <div class="food-content">
                        <div class="res-name">Name : <?php echo $resName; ?></div>
                        <h3 class="food-title"><?php echo $name; ?></h3>
                        
                        <div class="price-box">
                            <span class="final-price">Tk <?php echo $price; ?></span>
                            <?php if($orig > $price): ?>
                                <span class="old-price">Tk <?php echo $orig; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <button class="btn-view-details" 
                            data-name="<?php echo $name; ?>"
                            data-res="<?php echo $resName; ?>"
                            data-price="<?php echo $price; ?>"
                            data-orig="<?php echo $orig; ?>"
                            data-date="<?php echo $date; ?>"
                            data-time="<?php echo $time; ?>"
                            data-loc="<?php echo $loc; ?>"
                            data-desc="<?php echo $desc; ?>"
                            data-img="<?php echo $img; ?>"
                            data-contact="<?php echo $contact; ?>"
                            onclick="openFoodModal(this)">
                            View Details
                        </button>
                    </div>
                </div>
            <?php
                }
            } else {
                echo "<div style='grid-column: 1/-1; text-align: center; padding: 50px; color: #888;'><h3>No offers found.</h3></div>";
            }
            ?>
        </div>
    </main>
</div>

<div class="modal-overlay" id="foodModal">
    <div class="modal-box">
        <img src="" id="mImg" class="modal-img">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <span id="mRes" style="font-size: 0.85rem; color: #666; font-weight: 700; text-transform: uppercase;">RESTAURANT NAME</span>
                    <h2 id="mTitle" class="modal-title">Food Name</h2>
                </div>
                <button class="modal-close" onclick="closeFoodModal()">×</button>
            </div>
            
            <div class="info-grid">
                <div class="info-item"><strong>Price</strong> <span style="color:#28a745;">Tk <span id="mPrice">0</span></span></div>
                <div class="info-item"><strong>Pickup Time</strong> <span id="mTime">--</span></div>
                <div class="info-item"><strong>Valid Until</strong> <span id="mDate">--</span></div>
                <div class="info-item"><strong>Location</strong> <span id="mLoc">--</span></div>
            </div>
            
            <div class="modal-desc">
                <h4>What's Included?</h4>
                <p id="mDesc">Description goes here...</p>
            </div>

            <div class="info-item" style="background:#e7f1ff; padding:10px; border-radius:8px; text-align:center;">
                <strong>Contact Restaurant</strong>
                <span id="mContact" style="display:block; font-size:1.1rem; color:#007bff;">017XXXXXXXX</span>
            </div>
        </div>
        <div class="modal-actions">
            <button class="btn-view-details" onclick="closeFoodModal()" style="width:auto; padding: 10px 30px;">Close</button>
        </div>
    </div>
</div>

<script>
    // Open Modal and Populate Data
    function openFoodModal(btn) {
        const d = btn.dataset;
        
        document.getElementById('mImg').src = d.img;
        document.getElementById('mTitle').innerText = d.name;
        document.getElementById('mRes').innerText = d.res;
        document.getElementById('mPrice').innerText = d.price;
        document.getElementById('mTime').innerText = d.time;
        document.getElementById('mDate').innerText = d.date;
        document.getElementById('mLoc').innerText = d.loc;
        document.getElementById('mDesc').innerText = d.desc;
        document.getElementById('mContact').innerText = d.contact;
        
        document.getElementById('foodModal').style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Stop background scrolling
    }

    function closeFoodModal() {
        document.getElementById('foodModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Close on outside click
    window.onclick = function(e) {
        const modal = document.getElementById('foodModal');
        if (e.target === modal) {
            closeFoodModal();
        }
    }
</script>

<?php include 'footer.php'; ?>