<?php
session_start();
include 'db_connect.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['update_btn'])) {
    
    $itemID = $_POST['item_id'];
    $userID = $_SESSION['user_id']; 
    
    $name = $_POST['ItemName'];
    $cat  = $_POST['CategoryID'];
    $type = $_POST['Type'];
    $loc  = $_POST['Location'];
    $date = $_POST['DateLostFound'];
    $desc = $_POST['Description'];
    $imageName = $_POST['old_image']; 

    // Handle Image Upload
    if (!empty($_FILES['Image']['name'])) {
        $target_dir = "uploads/";
        $fileType = strtolower(pathinfo($_FILES["Image"]["name"], PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileType, $allowedTypes)) {
            $newFileName = uniqid() . "." . $fileType;
            $target_file = $target_dir . $newFileName;

            if (move_uploaded_file($_FILES["Image"]["tmp_name"], $target_file)) {
                $imageName = $newFileName;
            }
        }
    }

    // UPDATE SQL: Note that we are NOT changing 'Status' here.
    // It keeps whatever status it had before (e.g., if it was 'Approved', it stays 'Approved').
    $sql = "UPDATE tbl_items SET ItemName=?, CategoryID=?, Type=?, Location=?, DateLostFound=?, Description=?, Image=? WHERE ID=? AND UserID=?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sisssssii", $name, $cat, $type, $loc, $date, $desc, $imageName, $itemID, $userID);

        if ($stmt->execute()) {
            $_SESSION['status'] = "success";
            $_SESSION['message'] = "Item updated successfully!";
            header("Location: profile.php");
            exit();
        } else {
            echo "Error updating record: " . $stmt->error;
        }
        $stmt->close();
    }
}
$conn->close();
?>