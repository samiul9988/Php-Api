<?php

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read the incoming JSON data
    $json_data = file_get_contents('php://input');
    
    // Decode the JSON data
    $data = json_decode($json_data, true);
    
    // Check if required fields are present and valid
    if (isset($data['product_id']) && isset($data['user_id']) && isset($data['review_text'])) {
        // Validate non-empty fields
        if (!empty($data['product_id']) && !empty($data['user_id']) && !empty($data['review_text'])) {
            // Validate numerical IDs
            if (is_numeric($data['product_id']) && is_numeric($data['user_id'])) {
                // Sanitize input data
                $product_id = intval($data['product_id']);
                $user_id = intval($data['user_id']);
                $review_text = htmlspecialchars($data['review_text']);

                // Connect to MySQL database 
                $host = "localhost";
                $username = "root";
                $password = "";
                $database = "review";

                $conn = new mysqli($host, $username, $password, $database);

                // Check the database connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Insert data into the database
                $sql = "INSERT INTO product_reviews (product_id, user_id, review_text) VALUES ('$product_id', '$user_id', '$review_text')";

                if ($conn->query($sql) === TRUE) {
                    echo json_encode(array('message' => 'Review submitted successfully'));
                } else {
                    echo json_encode(array('error' => 'Error submitting review: ' . $conn->error));
                }

                // Close the database connection
                $conn->close();
            } else {
                echo json_encode(array('error' => 'Invalid numerical IDs'));
            }
        } else {
            echo json_encode(array('error' => 'Fields cannot be empty'));
        }
    } else {
        echo json_encode(array('error' => 'Missing required fields'));
    }
} else {
    // Respond with an error for non-POST requests
    echo json_encode(array('error' => 'Invalid request method'));
}

?>
