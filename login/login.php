<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'db.php'; // Update the path if needed

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT id, password, profile_completed FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];

            if ($user['profile_completed']) {
                header("Location: /dashboard/dashboard.html");
            } else {
                header("Location: ../profile/user_prof.html");
            }
            exit;
        } else {
            header("Location: ../login.html?error=Incorrect email or password");
            exit;
        }
    } catch (PDOException $e) {
        header("Location: ../login.html?error=Login failed: " . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: ../login.html?error=Invalid request method");
    exit;
}
?>
