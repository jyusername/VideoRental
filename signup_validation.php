<?php
session_start();
require_once 'functions.php'; // Adjust the path based on your file structure

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data and sanitize if necessary
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password']; // Plain text password from form input
    $gender = strtolower(trim($_POST['gender']));
    $age = $_POST['age'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];
    $postal_code = $_POST['postal_code'];

    // Validation (your existing validation logic here)
    $errors = validateRegistration($name, $username, $password, $gender, $age, $contact_number, $address, $postal_code);

    if (empty($errors)) {
        // Store user data in the database (password is stored as plain text)
        $success = registerUser($name, $username, $password, $gender, $age, $contact_number, $address, $postal_code);

        if ($success) {
            // Redirect to login page after successful registration
            header("Location: login.php");
            exit;
        } else {
            // Handle database error if registration fails
            $_SESSION['errors'] = ["Database error. Please try again later."];
            header("Location: signup.php");
            exit;
        }
    } else {
        // Redirect back to signup page with errors
        $_SESSION['errors'] = $errors;
        header("Location: signup.php");
        exit;
    }
}
?>
