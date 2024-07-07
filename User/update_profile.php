<?php
session_start();
require_once '../functions.php'; // Adjust the path based on your file structure

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $name = $_POST['name'];
    $username = $_POST['username'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];
    $postal_code = $_POST['postal_code'];

    // Update user's profile
    $success = updateUserProfile($_SESSION['user']['id'], $name, $username, $gender, $age, $contact_number, $address, $postal_code);

    if ($success) {
        // Update session with new user details
        $_SESSION['user'] = getUserById($_SESSION['user']['id']);
        $_SESSION['success_message'] = "Profile updated successfully.";
        header("Location: profile_details.php");
        exit;
    } else {
        $_SESSION['error_message'] = "Failed to update profile. Please try again.";
        header("Location: edit_profile.php");
        exit;
    }
}
?>