<?php
session_start();
include 'db_connect.php';

// Enable error reporting for development
ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- SECURITY CHECK: Stop here if user is not logged in ---
if (!isset($_SESSION['user_id'])) {
    $_SESSION['status'] = "error";
    $_SESSION['message'] = "Session expired. Please login to report items.";
    header("Location: index.php"); // Redirect to home/login, NOT report.php
    exit();
}

// Check if form was submitted
if (isset($_POST['submit'])) {

    // --- STEP 1: GATHER & PREPARE DATA ---
    $userID = $_SESSION['user_id']; // <--- UPDATED: Now links to the actual user
    $uploadFolder = "uploads/";
    
    // Inputs from the form
    $categoryID = $_POST['CategoryID'];
    $itemName   = $_POST['ItemName'];
    $type       = $_POST['Type'];
    $desc       = $_POST['Description'];
    $date       = $_POST['DateLostFound'];
    $location   = $_POST['Location'];
    $status     = "Pending";

    // --- STEP 2: VALIDATION CHECKS ---
    
    // Check 1: Does the upload folder exist?
    if (!is_dir($uploadFolder)) {
        redirectWithError("System Error: 'uploads' folder is missing.");
    }

    // Check 2: Is the file an actual image?
    $fileType = strtolower(pathinfo($_FILES["Image"]["name"], PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (!in_array($fileType, $allowedTypes)) {
        redirectWithError("Only JPG, JPEG, PNG & GIF files are allowed.");
    }

    // --- STEP 3: HANDLE IMAGE UPLOAD ---
    
    // Generate unique name
    $newFileName = uniqid() . "." . $fileType;
    $targetPath  = $uploadFolder . $newFileName;

    // Try to move the file
    if (!move_uploaded_file($_FILES["Image"]["tmp_name"], $targetPath)) {
        redirectWithError("Failed to save image. Check folder permissions.");
    }

    // --- STEP 4: SAVE TO DATABASE ---
    
    $sql = "INSERT INTO tbl_items (UserID, CategoryID, ItemName, Type, Description, DateLostFound, Location, Image, Status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        redirectWithError("Database Error: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("iisssssss", $userID, $categoryID, $itemName, $type, $desc, $date, $location, $newFileName, $status);

    if ($stmt->execute()) {
        // Success!
        $_SESSION['status'] = "success";
        $_SESSION['message'] = "Item reported successfully!";
        header("Location: report.php");
        exit();
    } else {
        // SQL Execution Error
        redirectWithError("Database Insert Failed: " . $stmt->error);
    }

    $stmt->close();
}

$conn->close();

// --- HELPER FUNCTION ---
function redirectWithError($message) {
    $_SESSION['status'] = "error";
    $_SESSION['message'] = $message;
    header("Location: report.php");
    exit(); 
}
?>