<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['first'] ?? '');
    $last = trim($_POST['last'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $confirmEmail = trim($_POST['confirm-email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm-password'] ?? '';

    // Validation
    if ($email !== $confirmEmail || $password !== $confirmPassword) {
        header("Location: landing.html?error=Email or password do not match");
        exit;
    }

    if (!$first || !$last || !$email || !$password) {
        header("Location: landing.html?error=All fields are required");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            header("Location: landing.html?error=Email already registered");
            exit;
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$first, $last, $email, $hashed]);

        header("Location: landing.html?success=Account created. Please login.");
        exit;
    } catch (PDOException $e) {
        header("Location: landing.html?error=Signup failed");
        exit;
    }
}
