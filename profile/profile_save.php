<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once '../login/db.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: /login.html?error=Session expired");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    var_dump($_POST);
exit;

    $first = $_POST['first_name'] ?? '';
    $last = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $nickname = $_POST['nickname'] ?? '';
    $level = $_POST['level'] ?? '';
    $role = $_POST['user_role'] ?? '';

    $installations = isset($_POST['installations']) ? implode(', ', $_POST['installations']) : '';

}

    try {
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, nickname = ?, level = ?, user_role = ? WHERE id = ?");
       
        $stmt->execute([$first, $last, $email, $nickname, $level, $role, $user_id]);
        
        // ✅ Re-check profile completion after update
        $stmt = $pdo->prepare("SELECT profile_completed FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $status = $stmt->fetchColumn();
    }
if ($status) {
    header("Location: ../dashboard/dashboard.html");
} else {
    header("Location: ../profile/user_prof.html?error=incomplete");
}
exit;
