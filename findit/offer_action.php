<?php
session_start();
include 'db_connect.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['submit_offer'])) {
    
    // 1. Permission Check
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'restaurant') {
        header("Location: index.php");
        exit();
    }

    // 2. Gather Data (Mapping to tbl_food columns)
    $resID = $_SESSION['user_id']; // Mapped to 'res_ID'
    $name = $_POST['Name'];        // Mapped to 'Name'
    $desc = $_POST['Desc'];        // Mapped to 'Desc'
    $loc  = $_POST['location'];    // Mapped to 'location'
    $time = $_POST['Time'];        // Mapped to 'Time'
    $date = $_POST['event_Date'];  // Mapped to 'event_Date'
    
    // New Columns
    $origPrice = $_POST['OriginalPrice'];
    $discPrice = $_POST['DiscountedPrice'];
    $status = 'Active';

    // 3. Handle Image
    $target_dir = "uploads/";
    $imageName = "default.jpg";

    if (!empty($_FILES['Image']['name'])) {
        $fileType = strtolower(pathinfo($_FILES["Image"]["name"], PATHINFO_EXTENSION));
        $newFileName = uniqid() . "_food." . $fileType;
        $targetPath = $target_dir . $newFileName;
        
        if (move_uploaded_file($_FILES["Image"]["tmp_name"], $targetPath)) {
            $imageName = $newFileName;
        }
    }

    // 4. Insert into tbl_food
    $sql = "INSERT INTO tbl_food (res_ID, Name, `Desc`, Image, location, `Time`, event_Date, OriginalPrice, DiscountedPrice, Status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // i = int, s = string, d = decimal
        $stmt->bind_param("issssssdds", $resID, $name, $desc, $imageName, $loc, $time, $date, $origPrice, $discPrice, $status);

        if ($stmt->execute()) {
            $_SESSION['status'] = "success";
            $_SESSION['message'] = "Food Offer Posted Successfully!";
            header("Location: profile.php");
        } else {
            $_SESSION['status'] = "error";
            $_SESSION['message'] = "Database Error: " . $stmt->error;
            header("Location: post_offer.php");
        }
        $stmt->close();
    } else {
        $_SESSION['status'] = "error";
        $_SESSION['message'] = "SQL Error: " . $conn->error;
        header("Location: post_offer.php");
    }
}
$conn->close();
?>