<?php

$conn = new mysqli('localhost', 'root', '', 'video_rental_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Initialize the videos array if it does not exist
if (!isset($_SESSION['videos'])) {
    $_SESSION['videos'] = [];
}

// Add a video with copies parameter
// Add a video with copies parameter
function addVideo($title, $director, $release_year, $image, $price, $genre, $format, $copies) {
    global $conn;

    $stmt = $conn->prepare("INSERT INTO videos (title, director, release_year, image, price, genre, format, copies) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissssi", $title, $director, $release_year, $image, $price, $genre, $format, $copies);
    
    $result = $stmt->execute();

    $stmt->close();
    return $result;
}

/// Get all videos from the database
function getVideos() {
    global $conn; // Access the global database connection

    $videos = [];
    $sql = "SELECT * FROM videos";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $videos[] = $row;
        }
    }

    return $videos;
}

// Update the session variable with updated videos
function updateSessionVideos() {
    $videos = getVideos();
    saveVideos($videos); // Save to session variable
}

// Get video by ID
function getVideoById($id) {
    global $conn; // Access the global database connection

    $sql = "SELECT * FROM videos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}




function saveVideos($videos) {
    $_SESSION['videos'] = $videos;
}

function updateVideo($id, $title, $director, $release_year, $image, $price, $genre, $format, $copies) {
    $videos = getVideos();
    foreach ($videos as &$video) {
        if ($video['id'] === $id) {
            $video['title'] = $title;
            $video['director'] = $director;
            $video['release_year'] = $release_year;
            if ($image !== null) {
                $video['image'] = $image; // Update image only if a new image is uploaded
            }
            $video['price'] = $price;
            $video['genre'] = $genre;
            $video['format'] = $format;
            $video['copies'] = $copies;
            break;
        }
    }
    saveVideos($videos);
}



// Rent video
function rentVideo($id, $rental_copies = 1) {
    global $conn; // Access the global database connection

    $video = getVideoById($id);

    if ($video && $video['copies'] >= $rental_copies) {
        $new_copies = $video['copies'] - $rental_copies;
        $sql = "UPDATE videos SET copies = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $new_copies, $id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}


// Update video in the database
function editVideo($id, $title, $director, $release_year, $image, $price, $genre, $format, $copies) {
    global $conn; // Access the global database connection

    $sql = "UPDATE videos 
            SET title = ?, director = ?, release_year = ?, image = ?, price = ?, genre = ?, format = ?, copies = ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssissssii", $title, $director, $release_year, $image, $price, $genre, $format, $copies, $id);
    
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Delete video
function deleteVideo($id) {
    global $conn; // Access the global database connection

    $sql = "DELETE FROM videos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

function generateReceipt($email, $rentalDetails) {
    $subject = "Rental Receipt";
    $message = "Thank you for your rental.\n\nDetails:\n";
    foreach ($rentalDetails as $key => $value) {
        $message .= ucfirst($key) . ": " . $value . "\n";
    }

    mail($email, $subject, $message);
}



// Validate registration inputs
function validateRegistration($name, $username, $password, $gender, $age, $contact_number, $address, $postal_code) {
    $errors = [];

    // Name validation
    if (empty($name)) {
        $errors['name'] = "Name is required.";
    }

    // Username validation
    if (empty($username)) {
        $errors['username'] = "Username is required.";
    } elseif (!preg_match("/^[a-zA-Z0-9_\-]+$/", $username)) {
        $errors['username'] = "Username can only contain letters, numbers, underscores, and dashes.";
    }

    // Password validation
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters.";
    }

    // Gender validation
    if (!in_array($gender, ['male', 'female'])) {
        $errors['gender'] = "Gender must be Male or Female.";
    }

    // Age validation
    if ($age < 12 || $age > 98) {
        $errors['age'] = "Age must be between 12 and 98.";
    }

    // Contact number validation
    if (!preg_match("/^[0-9]{11}$/", $contact_number)) {
        $errors['contact_number'] = "Contact number must be an 11-digit number.";
    }

    // Postal code validation
    if (!preg_match("/^[0-9]{4}$/", $postal_code)) {
        $errors['postal_code'] = "Postal code must be a 4-digit number.";
    }

    // Address validation
    if (empty($address)) {
        $errors['address'] = "Address is required.";
    }

    return $errors;
}

// Register user into database
function registerUser($name, $username, $password, $gender, $age, $contact_number, $address, $postal_code) {
    // Establish database connection
    $conn = new mysqli('localhost', 'root', '', 'video_rental_db');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO customers (name, username, password, gender, age, contact_number, address, postal_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    // Bind parameters
    $stmt->bind_param("ssssisss", $name, $username, $password, $gender, $age, $contact_number, $address, $postal_code);

    // Execute statement
    $success = $stmt->execute();

    // Close statement and connection
    $stmt->close();
    $conn->close();

    return $success;
}

function getUserByUsernameAndPassword($username, $password) {
    // Establish database connection
    $conn = new mysqli('localhost', 'root', '', 'video_rental_db');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement
    $stmt = $conn->prepare("SELECT * FROM customers WHERE username=? AND password=?");

    // Bind parameters
    $stmt->bind_param("ss", $username, $password);

    // Execute statement
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        // Fetch user data
        $user = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $user;
    } else {
        $stmt->close();
        $conn->close();
        return null;
    }
}

// Get user by ID
function getUserById($id) {
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

function updateUserProfile($id, $name, $username, $gender, $age, $contact_number, $address, $postal_code) {
    $conn = new mysqli('localhost', 'root', '', 'video_rental_db');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("UPDATE customers SET name=?, username=?, gender=?, age=?, contact_number=?, address=?, postal_code=? WHERE id=?");
    $stmt->bind_param("sssisssi", $name, $username, $gender, $age, $contact_number, $address, $postal_code, $id);

    $result = $stmt->execute();

    $stmt->close();
    $conn->close();

    return $result;
}
?>