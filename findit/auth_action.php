<?php
session_start();
include 'db_connect.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $action = $_POST['action'];

    // ==========================================
    // 1. REGISTER LOGIC
    // ==========================================
    if ($action == "register") {
        $fullName = trim($_POST['full_name']);
        $email    = trim($_POST['email']);
        $phone    = trim($_POST['contact_no']);
        $rawPass  = $_POST['password'];
        $role     = $_POST['role']; // 'user' or 'restaurant'

        // A. Check if Email Already Exists
        $checkSql = "SELECT ID FROM tbl_users WHERE email = ?";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['status'] = "error";
            $_SESSION['message'] = "Email already registered. Please login.";
            header("Location: index.php");
            exit();
        }
        $stmt->close();

        // B. Hash the Password
        $hashedPass = password_hash($rawPass, PASSWORD_DEFAULT);

        // C. Insert into tbl_users
        $sql = "INSERT INTO tbl_users (full_name, email, password, contact_no, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $fullName, $email, $hashedPass, $phone, $role);

        if ($stmt->execute()) {
            
            // --- RESTAURANT SPECIFIC LOGIC ---
            // If the user registered as a Restaurant, create a profile entry for them.
            if ($role == 'restaurant') {
                $newUserID = $stmt->insert_id; // Get the ID of the new user
                
                // Create a placeholder restaurant profile. 
                // Note: We use CategoryID '1' as a default. Ensure you have a category with ID 1 in tbl_categories.
                $resSql = "INSERT INTO tbl_restaurant (user_id, ResName, CategoryID, Type, Description, Location, Cover_Image) 
                           VALUES (?, ?, 1, 'General', 'Welcome! Please update your description.', 'Update Location', 'default_cover.jpg')";
                
                if ($resStmt = $conn->prepare($resSql)) {
                    $resStmt->bind_param("is", $newUserID, $fullName);
                    $resStmt->execute();
                    $resStmt->close();
                }
            }
            // ---------------------------------

            $_SESSION['status'] = "success";
            $_SESSION['message'] = "Account created successfully! Please login.";
        } else {
            $_SESSION['status'] = "error";
            $_SESSION['message'] = "Database Error: " . $stmt->error;
        }
        $stmt->close();
        header("Location: index.php");
        exit();
    }

    // ==========================================
    // 2. LOGIN LOGIC
    // ==========================================
    elseif ($action == "login") {
        $email = trim($_POST['email']);
        $rawPass = $_POST['password'];

        // Select the Role as well
        $sql = "SELECT ID, full_name, password, role FROM tbl_users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Verify Password
            if (password_verify($rawPass, $row['password'])) {
                
                // --- SUCCESSFUL LOGIN ---
                $_SESSION['user_id'] = $row['ID'];
                $_SESSION['user_name'] = $row['full_name'];
                $_SESSION['role'] = $row['role']; // <--- CRITICAL: Saves role for the Header check

                $_SESSION['status'] = "success";
                $_SESSION['message'] = "Welcome back, " . $row['full_name'] . "!";
                
                // Optional: Redirect Restaurants directly to their profile
                if ($row['role'] === 'restaurant') {
                    header("Location: profile.php");
                } else {
                    header("Location: index.php");
                }
                exit();

            } else {
                $_SESSION['status'] = "error";
                $_SESSION['message'] = "Incorrect password.";
            }
        } else {
            $_SESSION['status'] = "error";
            $_SESSION['message'] = "No account found with this email.";
        }
        $stmt->close();
        header("Location: index.php");
        exit();
    }
}
$conn->close();
?>