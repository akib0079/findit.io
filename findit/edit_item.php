<?php
include 'header.php';
include 'db_connect.php';

// 1. SECURITY: Check Login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// 2. GET ITEM DETAILS
if (isset($_GET['id'])) {
    $itemID = $_GET['id'];
    $userID = $_SESSION['user_id'];

    // Fetch item ONLY if it belongs to this user
    $sql = "SELECT * FROM tbl_items WHERE ID = ? AND UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $itemID, $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();

    // If item doesn't exist or doesn't belong to user, kick them out
    if (!$item) {
        header("Location: profile.php");
        exit();
    }
} else {
    header("Location: profile.php");
    exit();
}
?>

<style>
    .edit-container {
        max-width: 600px;
        margin: 120px auto 60px; /* Centered with top spacing */
        padding: 40px;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid #f0f0f0;
    }
    
    .section-title {
        text-align: center;
        font-size: 1.8rem;
        font-weight: 800;
        color: #333;
        margin-bottom: 30px;
    }

    .form-group { margin-bottom: 20px; }
    
    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #444;
    }

    /* Modern Input Style */
    .modern-input {
        width: 100%;
        padding: 12px 16px;
        font-size: 1rem;
        border: 2px solid #e1e1e1;
        border-radius: 12px;
        outline: none;
        transition: all 0.2s ease;
        background: #f9f9f9;
        font-family: inherit;
        box-sizing: border-box; /* Fixes width issues */
    }

    .modern-input:focus {
        border-color: #007bff;
        background: #fff;
        box-shadow: 0 4px 12px rgba(0,123,255,0.1);
    }

    /* Image Preview */
    .current-img-box {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 12px;
        border: 1px dashed #ccc;
    }

    /* Buttons */
    .btn-row { display: flex; gap: 15px; margin-top: 30px; }
    
    .btn-primary {
        flex: 1;
        padding: 14px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        cursor: pointer;
        font-size: 1rem;
        transition: 0.2s;
    }
    .btn-primary:hover { background: #0069d9; }

    .btn-secondary {
        flex: 1;
        padding: 14px;
        background: #e9ecef;
        color: #444;
        text-align: center;
        text-decoration: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        transition: 0.2s;
    }
    .btn-secondary:hover { background: #dee2e6; color: #222; }
</style>

<div class="edit-container">
    <h2 class="section-title">Edit Item Details</h2>

    <form action="edit_action.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="item_id" value="<?php echo $item['ID']; ?>">
        <input type="hidden" name="old_image" value="<?php echo $item['Image']; ?>">

        <div class="form-group">
            <label>Item Name</label>
            <input type="text" name="ItemName" class="modern-input" value="<?php echo htmlspecialchars($item['ItemName']); ?>" required>
        </div>

        <div class="form-group">
            <label>Category</label>
            <select name="CategoryID" class="modern-input" required>
                <?php
                $catSql = "SELECT ID, CategoryName FROM tbl_categories";
                $catResult = $conn->query($catSql);
                while($cat = $catResult->fetch_assoc()) {
                    $selected = ($cat['ID'] == $item['CategoryID']) ? 'selected' : '';
                    echo "<option value='" . $cat['ID'] . "' $selected>" . $cat['CategoryName'] . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label>Type</label>
            <select name="Type" class="modern-input" required>
                <option value="Lost" <?php echo ($item['Type'] == 'Lost') ? 'selected' : ''; ?>>Lost</option>
                <option value="Found" <?php echo ($item['Type'] == 'Found') ? 'selected' : ''; ?>>Found</option>
            </select>
        </div>

        <div class="form-group">
            <label>Location</label>
            <input type="text" name="Location" class="modern-input" value="<?php echo htmlspecialchars($item['Location']); ?>" required>
        </div>

        <div class="form-group">
            <label>Date</label>
            <input type="date" name="DateLostFound" class="modern-input" value="<?php echo $item['DateLostFound']; ?>" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="Description" class="modern-input" rows="4" required><?php echo htmlspecialchars($item['Description']); ?></textarea>
        </div>

        <div class="form-group">
            <label>Item Image</label>
            <div class="current-img-box">
                <img src="uploads/<?php echo $item['Image']; ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                <div style="flex-grow:1;">
                    <span style="display:block; font-size:0.85rem; color:#666; margin-bottom:5px;">Change Image (Optional):</span>
                    <input type="file" name="Image" class="modern-input" style="padding: 8px; font-size: 0.9rem;" accept="image/*">
                </div>
            </div>
        </div>

        <div class="btn-row">
            <a href="profile.php" class="btn-secondary">Cancel</a>
            <button type="submit" name="update_btn" class="btn-primary">Save Changes</button>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>