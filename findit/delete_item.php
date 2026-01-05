<?php
session_start();
include 'db_connect.php';

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $itemID = $_GET['id'];
    $userID = $_SESSION['user_id'];

    // Secure Delete: Ensure the item belongs to the logged-in user
    $sql = "DELETE FROM tbl_items WHERE ID = ? AND UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $itemID, $userID);

    if ($stmt->execute()) {
        header("Location: profile.php");
    } else {
        echo "Error deleting item.";
    }
} else {
    header("Location: index.php");
}
?>