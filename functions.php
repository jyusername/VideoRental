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
function addVideo($title, $director, $release_year, $image, $price, $genre, $format, $copies) {
    global $conn;

    $stmt = $conn->prepare("INSERT INTO videos (title, director, release_year, image, price, genre, format, copies) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissssi", $title, $director, $release_year, $image, $price, $genre, $format, $copies);
    
    $result = $stmt->execute();

    $stmt->close();
    return $result;
}

// Get all videos from the database
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

    // Get current copies and rented status
    $video = getVideoById($id);

    if ($video && $video['copies'] >= $rental_copies) {
        // Update copies and rented status
        $new_copies = $video['copies'] - $rental_copies;
        $sql = "UPDATE videos SET copies = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $new_copies, $id);

        // Execute statement
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}


// Update video
// Update video in the database
function editVideo($id, $title, $director, $release_year, $image, $price, $genre, $format, $copies) {
    global $conn; // Access the global database connection

    // Prepare SQL statement
    $sql = "UPDATE videos 
            SET title = ?, director = ?, release_year = ?, image = ?, price = ?, genre = ?, format = ?, copies = ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssissssii", $title, $director, $release_year, $image, $price, $genre, $format, $copies, $id);
    
    // Execute statement
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Delete video
function deleteVideo($id) {
    global $conn; // Access the global database connection

    // Prepare SQL statement
    $sql = "DELETE FROM videos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    // Execute statement
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}
?>