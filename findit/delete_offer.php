<?php
session_start();
include 'db_connect.php';

// 1. SECURITY: Check if user is logged in AND is a restaurant
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'restaurant') {
    $_SESSION['status'] = "error";
    $_SESSION['message'] = "Unauthorized access.";
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $offerID = $_GET['id'];
    $userID = $_SESSION['user_id'];

    // 2. DELETE QUERY (With Ownership Check)
    // We check 'AND res_ID = ?' to ensure they can't delete someone else's offer
    $sql = "DELETE FROM tbl_food WHERE ID = ? AND res_ID = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $offerID, $userID);
        
        if ($stmt->execute()) {
            // Check if any row was actually deleted
            if ($stmt->affected_rows > 0) {
                $_SESSION['status'] = "success";
                $_SESSION['message'] = "Offer deleted successfully.";
            } else {
                $_SESSION['status'] = "error";
                $_SESSION['message'] = "Error: Offer not found or you don't have permission to delete it.";
            }
        } else {
            $_SESSION['status'] = "error";
            $_SESSION['message'] = "Database error during deletion.";
        }
        $stmt->close();
    }
}

header("Location: profile.php");
exit();
?>