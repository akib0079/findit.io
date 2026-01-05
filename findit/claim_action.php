<?php
// claim_action.php
session_start();
include 'db_connect.php';

// ENABLE ERROR REPORTING (Helps fix 500 Errors)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['submit_claim'])) {
    
    // 1. Security Check
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['status'] = "error";
        $_SESSION['message'] = "You must be logged in to claim an item.";
        header("Location: index.php");
        exit();
    }

    $userID = $_SESSION['user_id'];
    $itemID = $_POST['item_id'];
    $proof  = $_POST['proof'];
    $status = 'Pending';
    $remark = 'Waiting for admin review';

    // 2. Prevent Duplicate Claims
    $checkSql = "SELECT ID FROM tbl_claims WHERE ItemID = ? AND UserID = ?";
    if ($stmtCheck = $conn->prepare($checkSql)) {
        $stmtCheck->bind_param("ii", $itemID, $userID);
        $stmtCheck->execute();
        $stmtCheck->store_result();
        
        if ($stmtCheck->num_rows > 0) {
            $_SESSION['status'] = "error";
            $_SESSION['message'] = "You have already claimed this item.";
            $stmtCheck->close();
            header("Location: browse.php");
            exit();
        }
        $stmtCheck->close();
    }

    // 3. Insert Claim
    $sql = "INSERT INTO tbl_claims (ItemID, UserID, VerificationProof, ClaimStatus, AdminRemark) VALUES (?, ?, ?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        // iisss -> Integer, Integer, String, String, String
        $stmt->bind_param("iisss", $itemID, $userID, $proof, $status, $remark);

        // ... inside claim_action.php ...
        if ($stmt->execute()) {
            $_SESSION['status'] = "success";
            // This message does NOT contain "login" or "password", so it will open the General Popup
            $_SESSION['message'] = "Claim submitted successfully! The admin will notify you shortly.";
        }
        else {
            // Log the specific database error
            $_SESSION['status'] = "error";
            $_SESSION['message'] = "Database Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // Log if preparation failed
        $_SESSION['status'] = "error";
        $_SESSION['message'] = "SQL Prepare Error: " . $conn->error;
    }
    
    header("Location: browse.php");
    exit();
}
?>