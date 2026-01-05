<?php
include 'header.php';
include 'db_connect.php';

// 1. SECURITY CHECK: Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$userID = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

// 2. FETCH USER DETAILS & ROLE
$userSql = "SELECT email, contact_no, role FROM tbl_users WHERE ID = ?";
$stmt = $conn->prepare($userSql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$userRole = $userData['role']; // 'user' or 'restaurant'
?>

<style>
    /* Global Profile Styles */
    .profile-header {
        background: white; padding: 40px; border-bottom: 1px solid #eaeaea;
        margin-bottom: 40px; text-align: center; margin-top: 100px;
    }
    .profile-avatar {
        width: 80px; height: 80px; background: #007bff; color: white;
        border-radius: 50%; display: inline-flex; align-items: center;
        justify-content: center; font-size: 2rem; font-weight: bold; margin-bottom: 15px;
    }
    .profile-info h1 { margin: 0; font-size: 1.8rem; color: #333; }
    .profile-info p { color: #666; margin: 5px 0; }
    .role-tag {
        background: #e7f1ff; color: #007bff; padding: 4px 12px;
        border-radius: 20px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;
    }
    
    /* Dashboard Layout */
    .dashboard-container { max-width: 1200px; margin: 0 auto; padding: 0 20px 60px; min-height: 60vh; }
    .items-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px; }
    
    /* Card Styles */
    .item-card { background: white; border-radius: 12px; overflow: hidden; border: 1px solid #eee; display: flex; flex-direction: column; }
    .card-image { width: 100%; height: 180px; object-fit: cover; }
    .card-content { padding: 15px; flex-grow: 1; display: flex; flex-direction: column; }
    
    /* Food Specific Styles */
    .price-tag { font-size: 1.1rem; font-weight: 700; color: #28a745; }
    .original-price { text-decoration: line-through; color: #999; font-size: 0.9rem; margin-right: 8px; }
    
    /* Status Badges */
    .claim-badge { display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 0.8rem; font-weight: 600; margin-bottom: 10px; }
    .status-Active { background: #d4edda; color: #155724; }
    .status-Pending { background: #fff3cd; color: #856404; }
    .status-Approved { background: #cce5ff; color: #004085; }
    .status-Rejected { background: #f8d7da; color: #721c24; }
    .status-Lost { background: #f8d7da; color: #721c24; }
    .status-Found { background: #d4edda; color: #155724; }
</style>

<div class="profile-header">
    <div class="profile-avatar">
        <?php echo strtoupper(substr($userName, 0, 1)); ?>
    </div>
    <div class="profile-info">
        <h1><?php echo htmlspecialchars($userName); ?></h1>
        <p><?php echo htmlspecialchars($userData['email']); ?></p>
        <span class="role-tag"><?php echo htmlspecialchars($userRole); ?></span>
        
        <div style="margin-top: 15px;">
            <a href="logout.php" style="color: #dc3545; font-weight: 600; text-decoration: none; border: 1px solid #dc3545; padding: 5px 15px; border-radius: 20px; font-size: 0.9rem;">
                Logout
            </a>
        </div>
    </div>
</div>

<div class="dashboard-container">

    <?php if ($userRole === 'restaurant'): ?>
        
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
            <h3 style="margin:0; color: #333; border-left: 4px solid #28a745; padding-left: 10px;">My Food Offers</h3>
            <a href="post_offer.php" style="background:#28a745; color:white; padding:8px 16px; border-radius:6px; text-decoration:none; font-weight:600;">+ Post New Offer</a>
        </div>

        <div class="items-grid">
            <?php
            // Query for Food Offers
            $foodSql = "SELECT * FROM tbl_food WHERE res_ID = ? ORDER BY event_Date DESC";
            $stmt = $conn->prepare($foodSql);
            $stmt->bind_param("i", $userID);
            $stmt->execute();
            $foodResult = $stmt->get_result();

            if ($foodResult->num_rows > 0) {
                while($food = $foodResult->fetch_assoc()) {
                    $foodID = $food['ID'];
                    $name = htmlspecialchars($food['Name']);
                    $img = "uploads/" . htmlspecialchars($food['Image']);
                    $price = htmlspecialchars($food['DiscountedPrice']);
                    $orig = htmlspecialchars($food['OriginalPrice']);
                    $status = htmlspecialchars($food['Status']);
                    $date = date("M d, Y", strtotime($food['event_Date']));
                    
                    if (empty($food['Image']) || !file_exists($img)) { $img = "https://via.placeholder.com/300x200?text=Food+Offer"; }
            ?>
                <div class="item-card">
                    <img src="<?php echo $img; ?>" class="card-image">
                    <div class="card-content">
                        <div style="display:flex; justify-content:space-between; align-items:start;">
                            <h4 style="margin: 0 0 5px 0;"><?php echo $name; ?></h4>
                            <span class="claim-badge status-<?php echo $status; ?>"><?php echo $status; ?></span>
                        </div>
                        
                        <p style="margin: 5px 0;">
                            <span class="original-price">Tk <?php echo $orig; ?></span>
                            <span class="price-tag">Tk <?php echo $price; ?></span>
                        </p>
                        
                        <p style="color: #666; font-size: 0.85rem;">Valid until: <?php echo $date; ?></p>
                        
                        <a href="delete_offer.php?id=<?php echo $foodID; ?>" 
                           onclick="return confirm('Are you sure you want to remove this offer?');" 
                           style="display:block; text-align:center; margin-top:auto; padding:8px; background:#ffebeb; color:#dc3545; border-radius:6px; text-decoration:none; font-weight:600; border:1px solid #fadbd8; transition:0.2s;">
                           Delete Offer
                        </a>
                    </div>
                </div>
            <?php
                }
            } else {
                echo "<p style='color:#777; grid-column: 1/-1;'>You haven't posted any food offers yet.</p>";
            }
            ?>
        </div>

    <?php else: ?>

        <h3 style="margin-bottom: 20px; color: #333; border-left: 4px solid #007bff; padding-left: 10px;">My Reported Items</h3>
        <div class="items-grid" style="margin-bottom: 60px;">
            <?php
            $sql = "SELECT * FROM tbl_items WHERE UserID = ? ORDER BY DateLostFound DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $userID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $name = htmlspecialchars($row['ItemName']);
                    $imagePath = "uploads/" . htmlspecialchars($row['Image']);
                    $status = htmlspecialchars($row['Status']);
                    $id = $row['ID'];

                    if (empty($row['Image']) || !file_exists($imagePath)) { $imagePath = "https://via.placeholder.com/300x200?text=No+Image"; }
            ?>
                <div class="item-card">
                    <img src="<?php echo $imagePath; ?>" class="card-image">
                    <div class="card-content">
                        <h4 style="margin: 0 0 5px 0;"><?php echo $name; ?></h4>
                        <p style="color: #777; font-size: 0.9rem; margin-bottom: 10px;">Post Status: <strong><?php echo $status; ?></strong></p>
                        <div style="display: flex; gap: 10px; margin-top: auto;">
                            <a href="edit_item.php?id=<?php echo $id; ?>" style="flex: 1; text-align: center; padding: 8px; background: #ffc107; color: #333; text-decoration: none; border-radius: 6px; font-weight: 600;">Edit</a>
                            <a href="delete_item.php?id=<?php echo $id; ?>" onclick="return confirm('Delete this item permanently?');" style="flex: 1; text-align: center; padding: 8px; background: #dc3545; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">Delete</a>
                        </div>
                    </div>
                </div>
            <?php
                }
            } else {
                echo "<p style='color:#777; grid-column: 1/-1;'>You haven't reported any items yet.</p>";
            }
            ?>
        </div>

        <h3 style="margin-bottom: 20px; color: #333; border-left: 4px solid #28a745; padding-left: 10px;">My Claimed Items</h3>
        <div class="items-grid">
            <?php
            // Join tbl_claims with tbl_items to get item details
            $claimSql = "SELECT c.ID as ClaimID, c.ClaimStatus, c.AdminRemark, i.ItemName, i.Image
                        FROM tbl_claims c
                        JOIN tbl_items i ON c.ItemID = i.ID
                        WHERE c.UserID = ? ORDER BY c.ID DESC";
            $stmt2 = $conn->prepare($claimSql);
            $stmt2->bind_param("i", $userID);
            $stmt2->execute();
            $claimResult = $stmt2->get_result();

            if ($claimResult->num_rows > 0) {
                while($claim = $claimResult->fetch_assoc()) {
                    $itemName = htmlspecialchars($claim['ItemName']);
                    $claimStatus = htmlspecialchars($claim['ClaimStatus']);
                    $remark = htmlspecialchars($claim['AdminRemark']);
                    $claimImg = "uploads/" . htmlspecialchars($claim['Image']);
                    
                    if (empty($claim['Image']) || !file_exists($claimImg)) { $claimImg = "https://via.placeholder.com/300x200?text=No+Image"; }
            ?>
                <div class="item-card">
                    <img src="<?php echo $claimImg; ?>" class="card-image">
                    <div class="card-content">
                        <h4 style="margin: 0 0 5px 0;"><?php echo $itemName; ?></h4>
                        <div style="margin-bottom: 10px;">
                            <span class="claim-badge status-<?php echo $claimStatus; ?>"><?php echo $claimStatus; ?></span>
                        </div>
                        <?php if(!empty($remark)): ?>
                            <p style="font-size: 0.85rem; background: #f8f9fa; padding: 8px; border-radius: 4px; color: #555;"><strong>Admin Note:</strong><br> <?php echo $remark; ?></p>
                        <?php else: ?>
                            <p style="font-size: 0.85rem; color: #999;">No admin remarks yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php
                }
            } else {
                echo "<p style='color:#777; grid-column: 1/-1;'>You haven't claimed any items yet.</p>";
            }
            ?>
        </div>

    <?php endif; ?>

</div>

<?php include 'footer.php'; ?>