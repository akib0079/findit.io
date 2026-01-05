<?php 
session_start(); // MUST be the very first line
include 'header.php'; 
include 'db_connect.php'; 

// 1. SECURITY CHECK: Ensure User is Logged In
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to home with a message
    $_SESSION['status'] = "error";
    $_SESSION['message'] = "You must be logged in to report an item.";
    header("Location: index.php"); 
    exit();
}
?>

<style>
    /* Popup Background */
    .popup-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }
    
    /* Popup Box */
    .popup-content {
        background: white;
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        position: relative;
        animation: slideDown 0.3s ease-out;
    }

    .popup-content h3 { color: #28a745; margin-bottom: 10px; }
    .popup-content.error h3 { color: #dc3545; }
    
    .close-btn {
        background: #007bff; color: white; border: none;
        padding: 10px 20px; border-radius: 5px; cursor: pointer;
        margin-top: 15px;
    }

    @keyframes slideDown {
        from { transform: translateY(-50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    /* --- ADDED: Modern Input Styling for consistency --- */
    .modern-input {
        width: 100%;
        padding: 12px 16px;
        font-size: 16px;
        color: #333;
        background-color: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        outline: none;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }
    
    .modern-input:focus {
        border-color: #007bff;
        background-color: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.1);
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #2c3e50;
    }
</style>

<div class="hero-container" style="padding-top: 150px; color: black; max-width: 600px; margin: 0 auto; padding-bottom: 80px;">
    <h2>Report a Lost or Found Item</h2>
    
    <form action="insert.php" method="POST" enctype="multipart/form-data" style="text-align: left;">
       
       <div class="form-group" style="margin-bottom: 15px;">
            <label>Item Name:</label>
            <input type="text" name="ItemName" class="modern-input" required placeholder="e.g. Black Wallet">
        </div>
       
       <div class="form-group" style="margin-bottom: 15px;">
            <label>Category:</label>
            <select name="CategoryID" class="modern-input" required>
                <option value="">Select Category</option>
                <?php
                $sql = "SELECT ID, CategoryName FROM tbl_categories";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['ID'] . "'>" . $row['CategoryName'] . "</option>";
                    }
                }
                ?>
            </select>
        </div>
        
        <div class="form-group" style="margin-bottom: 15px;">
            <label>Type:</label>
            <select name="Type" class="modern-input" required>
                <option value="Lost">Lost</option>
                <option value="Found">Found</option>
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label for="date-field">Date:</label>
            <input type="date" id="date-field" name="DateLostFound" class="modern-input" required>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Location:</label>
            <input type="text" name="Location" class="modern-input" required placeholder="e.g. Library, Main Hall">
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Description:</label>
            <textarea name="Description" rows="4" class="modern-input" required placeholder="Describe the item..."></textarea>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Upload Image:</label>
            <input type="file" name="Image" class="modern-input" style="padding: 10px;" accept="image/*" required>
        </div>

        <button type="submit" name="submit" class="btn btn-outline" style="background-color: #007bff; color: white; width: 100%; border-radius: 12px; padding: 14px; font-weight: bold;">Submit Report</button>
    </form>
</div>

<div class="popup-overlay" id="statusPopup">
    <div class="popup-content" id="popupContent">
        <h3 id="popupTitle">Success!</h3>
        <p id="popupMessage">Operation completed successfully.</p>
        <button class="close-btn" onclick="closePopup()">OK</button>
    </div>
</div>

<script>
    // Function to close popup
    function closePopup() {
        document.getElementById('statusPopup').style.display = 'none';
    }

    // Check if PHP set a session message
    <?php
    if (isset($_SESSION['status'])) {
        $status = $_SESSION['status'];
        $message = $_SESSION['message'];
        
        // Show the popup using JS
        echo "document.getElementById('statusPopup').style.display = 'flex';";
        echo "document.getElementById('popupMessage').innerText = '$message';";
        
        if ($status == 'error') {
            echo "document.getElementById('popupTitle').innerText = 'Error';";
            echo "document.getElementById('popupTitle').style.color = '#dc3545';";
        } else {
             echo "document.getElementById('popupTitle').innerText = 'Success!';";
        }
        
        // Clear the session so it doesn't show again on refresh
        unset($_SESSION['status']);
        unset($_SESSION['message']);
    }
    ?>
</script>

<?php include 'footer.php'; ?>