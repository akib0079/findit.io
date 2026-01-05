<?php 
include 'header.php'; 
include 'db_connect.php'; 
?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* Global Styles */
    body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
    
    /* Hero Section */
    .browse-hero { background: #ffffff; padding: 160px 20px 40px; text-align: center; border-bottom: 1px solid #eaeaea; margin-bottom: 40px; }
    .section-title { font-size: 2rem; font-weight: 800; color: #1a1a1a; margin-bottom: 10px; }
    .section-subtitle { color: #666; margin-bottom: 30px; }

    /* Filter Bar */
    .filter-bar {
        max-width: 800px; margin: 0 auto; display: flex; gap: 15px; flex-wrap: wrap; position: relative;
    }
    .search-wrapper { flex: 2; min-width: 250px; position: relative; }
    .filter-select {
        flex: 1; min-width: 150px; padding: 16px 20px; border: 2px solid #e1e1e1; border-radius: 50px;
        background: #fff; font-size: 15px; cursor: pointer; outline: none; appearance: none;
        background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23007CB2%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E");
        background-repeat: no-repeat; background-position: right 15px top 50%; background-size: 12px auto;
    }
    .search-input { width: 100%; padding: 16px 20px 16px 50px; border: 2px solid #e1e1e1; border-radius: 50px; outline: none; transition: 0.3s; }
    .search-input:focus, .filter-select:focus { border-color: #007bff; box-shadow: 0 4px 15px rgba(0,123,255,0.1); }
    .search-icon { position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: #999; }
    .spinner { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; border: 2px solid #f3f3f3; border-top: 2px solid #007bff; border-radius: 50%; animation: spin 1s linear infinite; display: none; }

    /* Grid & Card */
    .browse-container { max-width: 1200px; margin: 0 auto; padding: 0 20px 60px; min-height: 60vh; }
    .items-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px; }
    
    .item-card { 
        background: white; border-radius: 20px; overflow: hidden; border: 1px solid #f0f0f0; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.03); transition: all 0.3s ease; display: flex; flex-direction: column; 
    }
    .item-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }
    
    .card-image-wrapper { position: relative; width: 100%; height: 220px; background-color: #f1f1f1; overflow: hidden; }
    .card-image { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
    .item-card:hover .card-image { transform: scale(1.05); }
    
    .status-badge { position: absolute; top: 15px; left: 15px; padding: 6px 14px; border-radius: 30px; font-size: 0.75rem; font-weight: 700; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 6px; z-index: 2; }
    .status-badge::before { content: ''; width: 8px; height: 8px; border-radius: 50%; }
    .status-badge.lost { background: rgba(255, 255, 255, 0.95); color: #d9534f; }
    .status-badge.lost::before { background: #d9534f; }
    .status-badge.found { background: rgba(255, 255, 255, 0.95); color: #28a745; }
    .status-badge.found::before { background: #28a745; }
    
    .card-content { padding: 24px; flex-grow: 1; display: flex; flex-direction: column; }
    .item-title { font-size: 1.25rem; font-weight: 700; color: #222; margin: 0 0 12px 0; }
    
    .meta-row { display: flex; align-items: center; gap: 15px; margin-bottom: 15px; }
    .meta-item { display: flex; align-items: center; gap: 6px; font-size: 0.85rem; color: #666; font-weight: 500; }
    .meta-item svg { width: 16px; height: 16px; opacity: 0.7; }
    
    .item-desc { font-size: 0.95rem; color: #555; line-height: 1.6; margin-bottom: 20px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; flex-grow: 1; }
    
    /* Buttons */
    .button-group { display: flex; gap: 10px; margin-top: auto; }
    
    .btn-claim { 
        flex: 2; padding: 12px; background-color: #007bff; color: white; text-align: center; text-decoration: none; 
        border-radius: 12px; font-weight: 600; cursor: pointer; border: none; font-size: 0.95rem; transition: background 0.2s; 
    }
    .btn-claim:hover { background-color: #0069d9; }

    .btn-quick-view {
        flex: 1; padding: 12px; background-color: #f8f9fa; color: #333; text-align: center; 
        border: 1px solid #dee2e6; border-radius: 12px; cursor: pointer; font-weight: 600; font-size: 0.95rem;
        transition: all 0.2s;
    }
    .btn-quick-view:hover { background-color: #e2e6ea; border-color: #dae0e5; }

    /* --- MODAL STYLES (Shared) --- */
    .modal-overlay-custom {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.6); z-index: 2000;
        justify-content: center; align-items: center;
        backdrop-filter: blur(4px);
    }
    .modal-box {
        background: white; width: 90%; max-width: 600px; padding: 0;
        border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.2);
        animation: slideUp 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        display: flex; flex-direction: column; overflow: hidden; max-height: 90vh;
    }

    /* Quick View Specifics */
    .qv-layout { display: flex; flex-direction: column; }
    .qv-image-container { height: 250px; background: #f1f1f1; position: relative; }
    .qv-image { width: 100%; height: 100%; object-fit: contain; }
    .qv-content { padding: 30px; overflow-y: auto; }
    
    .qv-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px; }
    .qv-title { font-size: 1.8rem; font-weight: 800; color: #1a1a1a; margin: 0; }
    .qv-close { background: none; border: none; font-size: 2rem; color: #999; cursor: pointer; line-height: 1; }
    .qv-close:hover { color: #333; }
    
    .qv-meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px; background: #f8f9fa; padding: 15px; border-radius: 12px; }
    .qv-meta-item strong { display: block; font-size: 0.8rem; color: #666; text-transform: uppercase; letter-spacing: 0.5px; }
    .qv-meta-item span { font-size: 1rem; color: #333; font-weight: 600; }
    
    .qv-description h4 { font-size: 1rem; margin-bottom: 8px; color: #333; }
    .qv-description p { color: #555; line-height: 1.6; font-size: 0.95rem; }

    .qv-actions { padding: 20px 30px; border-top: 1px solid #eee; background: #fff; display: flex; gap: 15px; justify-content: flex-end; }
    
    /* Claim Input Area */
    .claim-input-area { margin-top: 20px; padding: 20px; background: #f8f9fa; border-radius: 12px; display: none; }
    .claim-input-area textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; min-height: 80px; margin: 10px 0; }

    @keyframes slideUp { from { transform: translateY(50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
</style>

<div class="browse-hero">
    <h1 class="section-title">Lost & Found Gallery</h1>
    <p class="section-subtitle">Browse reported items or filter to find exactly what you need.</p>
    
    <div class="filter-bar">
        <div class="search-wrapper">
            <span class="search-icon">🔍</span>
            <input type="text" id="liveSearch" class="search-input" placeholder="Search item name, location...">
            <div class="spinner" id="loadingSpinner"></div>
        </div>

        <select id="typeFilter" class="filter-select">
            <option value="">All Types</option>
            <option value="Lost">Lost Items</option>
            <option value="Found">Found Items</option>
        </select>

        <select id="categoryFilter" class="filter-select">
            <option value="">All Categories</option>
            <?php
            $catSql = "SELECT * FROM tbl_categories";
            $catResult = $conn->query($catSql);
            if ($catResult->num_rows > 0) {
                while($cat = $catResult->fetch_assoc()) {
                    echo '<option value="'.$cat['ID'].'">'.$cat['CategoryName'].'</option>';
                }
            }
            ?>
        </select>
    </div>
</div>

<div class="browse-container">
    <div class="items-grid" id="itemsGrid">
        <?php
        $sql = "SELECT * FROM tbl_items ORDER BY DateLostFound DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // Prepare Variables
                $id = $row['ID'];
                $name = htmlspecialchars($row['ItemName']);
                $loc = htmlspecialchars($row['Location']);
                $desc = htmlspecialchars($row['Description']);
                $date = date("M d, Y", strtotime($row['DateLostFound']));
                $imagePath = "uploads/" . htmlspecialchars($row['Image']);
                $type = htmlspecialchars($row['Type']);
                
                $badgeClass = ($type == 'Found') ? 'status-badge found' : 'status-badge lost';
                $btnText = ($type == 'Found') ? 'Is this yours?' : 'I found this!';
                
                if (empty($row['Image']) || !file_exists($imagePath)) $imagePath = "https://via.placeholder.com/400x300?text=No+Image";

                // Icons
                $iconCalendar = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>';
                $iconMap = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>';
        ?>
            <div class="item-card">
                <div class="card-image-wrapper">
                    <span class="<?php echo $badgeClass; ?>"><?php echo $type; ?></span>
                    <img src="<?php echo $imagePath; ?>" alt="<?php echo $name; ?>" class="card-image">
                </div>
                <div class="card-content">
                    <h3 class="item-title"><?php echo $name; ?></h3>
                    <div class="meta-row">
                        <div class="meta-item"><?php echo $iconCalendar . $date; ?></div>
                        <div class="meta-item"><?php echo $iconMap . $loc; ?></div>
                    </div>
                    <p class="item-desc"><?php echo $desc; ?></p>
                    
                    <div class="button-group">
                        <button class="btn-quick-view" 
                            data-id="<?php echo $id; ?>"
                            data-name="<?php echo $name; ?>"
                            data-type="<?php echo $type; ?>"
                            data-date="<?php echo $date; ?>"
                            data-loc="<?php echo $loc; ?>"
                            data-desc="<?php echo $desc; ?>"
                            data-img="<?php echo $imagePath; ?>"
                            onclick="openQuickView(this)">
                            View
                        </button>
                        
                        <button class="btn-claim" onclick="openClaimModal(<?php echo $id; ?>)">
                            <?php echo $btnText; ?>
                        </button>
                    </div>
                </div>
            </div>
        <?php
            }
        } else {
            echo "<div style='grid-column: 1/-1; text-align: center; padding: 40px; color: #888;'><h3>No items found</h3><p>There are currently no items reported.</p></div>";
        }
        ?>
    </div>
</div>

<div class="modal-overlay-custom" id="quickViewModal">
    <div class="modal-box">
        <div class="qv-image-container">
            <span id="qvBadge" class="status-badge"></span>
            <img src="" id="qvImage" class="qv-image">
        </div>
        <div class="qv-content">
            <div class="qv-header">
                <h2 id="qvTitle" class="qv-title">Item Name</h2>
                <button class="qv-close" onclick="closeModal('quickViewModal')">×</button>
            </div>
            
            <div class="qv-meta-grid">
                <div class="qv-meta-item"><strong>Date</strong> <span id="qvDate">Nov 24, 2025</span></div>
                <div class="qv-meta-item"><strong>Location</strong> <span id="qvLocation">Library</span></div>
                <div class="qv-meta-item"><strong>Type</strong> <span id="qvType">Lost</span></div>
                <div class="qv-meta-item"><strong>ID</strong> <span id="qvID">#123</span></div>
            </div>
            
            <div class="qv-description">
                <h4>Description</h4>
                <p id="qvDesc">Full description goes here...</p>
            </div>
            
            <div id="qvClaimSection" class="claim-input-area">
                <h4>Verify Claim</h4>
                <p style="font-size:0.9rem; color:#666; margin-bottom:10px;">Please describe any unique markings to verify ownership.</p>
                <form action="claim_action.php" method="POST">
                    <input type="hidden" name="item_id" id="qvFormID">
                    <textarea name="proof" required placeholder="Verification details..."></textarea>
                    <button type="submit" name="submit_claim" class="btn-claim">Submit Claim</button>
                </form>
            </div>
        </div>
        
        <div class="qv-actions">
            <button class="btn-quick-view" onclick="closeModal('quickViewModal')">Close</button>
            <button class="btn-claim" id="qvMainClaimBtn" style="width:auto; padding: 12px 30px;" onclick="toggleQvClaim()">Claim This Item</button>
        </div>
    </div>
</div>

<div class="modal-overlay-custom" id="simpleClaimModal">
    <div class="modal-box" style="max-width: 450px; padding: 30px;">
        <h3 style="margin-top:0;">Submit Claim</h3>
        <p style="color:#666; font-size:0.95rem; line-height:1.5;">Please provide proof of ownership.</p>
        <form action="claim_action.php" method="POST">
            <input type="hidden" name="item_id" id="simpleClaimID">
            <textarea name="proof" style="width:100%; border:1px solid #ddd; padding:10px; border-radius:8px; min-height:100px; margin:15px 0;" required placeholder="Describe unique details..."></textarea>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="btn-quick-view" onclick="closeModal('simpleClaimModal')">Cancel</button>
                <button type="submit" name="submit_claim" class="btn-claim" style="width:auto;">Submit</button>
            </div>
        </form>
    </div>
</div>

<script>
    // 1. FILTER FUNCTION
    function fetchItems() {
        var query = $("#liveSearch").val();
        var type = $("#typeFilter").val();
        var category = $("#categoryFilter").val();
        $("#loadingSpinner").show();
        $.ajax({
            url: "search_handler.php",
            method: "POST",
            data: { query: query, type: type, category: category },
            success: function(data) {
                $("#itemsGrid").html(data);
                $("#loadingSpinner").hide();
            }
        });
    }

    $("#liveSearch").on("keyup", fetchItems);
    $("#typeFilter").on("change", fetchItems);
    $("#categoryFilter").on("change", fetchItems);

    // 2. QUICK VIEW FUNCTION
    function openQuickView(btn) {
        // Get data from attributes
        const data = btn.dataset;
        
        // Populate Modal
        document.getElementById('qvTitle').innerText = data.name;
        document.getElementById('qvDate').innerText = data.date;
        document.getElementById('qvLocation').innerText = data.loc;
        document.getElementById('qvType').innerText = data.type;
        document.getElementById('qvID').innerText = '#' + data.id;
        document.getElementById('qvDesc').innerText = data.desc;
        document.getElementById('qvImage').src = data.img;
        document.getElementById('qvFormID').value = data.id;
        
        // Badge Logic
        const badge = document.getElementById('qvBadge');
        badge.className = 'status-badge ' + (data.type === 'Found' ? 'found' : 'lost');
        badge.innerText = data.type;
        
        // Claim Button Text
        const claimBtn = document.getElementById('qvMainClaimBtn');
        claimBtn.innerText = (data.type === 'Found') ? 'Is this yours?' : 'I found this!';
        
        // Reset Claim Section
        document.getElementById('qvClaimSection').style.display = 'none';
        
        // Show Modal
        document.getElementById('quickViewModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    // 3. CLAIM FUNCTIONS
    function openClaimModal(id) {
        document.getElementById('simpleClaimID').value = id;
        document.getElementById('simpleClaimModal').style.display = 'flex';
    }

    function toggleQvClaim() {
        $('#qvClaimSection').slideToggle();
        const content = document.querySelector('.qv-content');
        setTimeout(() => { content.scrollTop = content.scrollHeight; }, 300);
    }

    // 4. CLOSING MODALS
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    window.onclick = function(e) {
        if (e.target.classList.contains('modal-overlay-custom')) {
            e.target.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }

    // 5. NEW: AUTO SEARCH FROM URL (Fix for redirection)
    $(document).ready(function() {
        // Parse URL params
        const urlParams = new URLSearchParams(window.location.search);
        const q = urlParams.get('q');
        const cat = urlParams.get('cat');

        // Pre-fill inputs
        if (q) $("#liveSearch").val(q);
        if (cat) $("#categoryFilter").val(cat);

        // Trigger search immediately if params exist
        if (q || cat) {
            fetchItems();
        }
    });
</script>

<?php include 'footer.php'; ?>