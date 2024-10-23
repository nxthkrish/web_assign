<?php

$dataFile = 'users.json';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle form submission (add/update)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $age = filter_var($_POST['age'], FILTER_VALIDATE_INT);
    $place = htmlspecialchars($_POST['place']);
    $userIndex = isset($_POST['user_index']) ? (int)$_POST['user_index'] : -1;

    // Validate input fields
    if (!$age) {
        die("Invalid age input. Please enter a valid number.");
    }

    // Load existing users data
    if (file_exists($dataFile)) {
        $jsonData = file_get_contents($dataFile);
        $users = json_decode($jsonData, true);

        // Check for JSON decoding errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            die("Error decoding JSON: " . json_last_error_msg());
        }
    } else {
        $users = [];
    }

    if ($userIndex >= 0) {
        // Update existing user
        if (isset($users[$userIndex])) {
            $users[$userIndex]['name'] = $name;
            $users[$userIndex]['email'] = $email;
            $users[$userIndex]['age'] = $age;
            $users[$userIndex]['place'] = $place;
        } else {
            die("User not found.");
        }
    } else {
        // Add new user
        $users[] = ['name' => $name, 'email' => $email, 'age' => $age, 'place' => $place];
    }

    // Save updated users list
    if (!file_put_contents($dataFile, json_encode($users))) {
        die("Error writing to file.");
    }
}

// Handle delete request
if (isset($_GET['delete'])) {
    $userIndex = (int)$_GET['delete'];

    // Load existing users data
    if (file_exists($dataFile)) {
        $jsonData = file_get_contents($dataFile);
        $users = json_decode($jsonData, true);

        // Check for JSON decoding errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            die("Error decoding JSON: " . json_last_error_msg());
        }

        // Remove user if exists
        if (isset($users[$userIndex])) {
            unset($users[$userIndex]);
            $users = array_values($users); // Reindex array after deletion
            if (!file_put_contents($dataFile, json_encode($users))) {
                die("Error writing to file.");
            }
        } else {
            die("User not found.");
        }
    }
}

// Fetch users for display
$usersData = '';
if (file_exists($dataFile)) {
    $jsonData = file_get_contents($dataFile);
    $users = json_decode($jsonData, true);

    // Check for JSON decoding errors
    if (json_last_error() === JSON_ERROR_NONE) {
        $usersData .= "<h3>Users List:</h3><div class='users-list'>";
        foreach ($users as $index => $user) {
            $usersData .= "<div class='user'><strong>Name:</strong> " . $user['name'] . "<br>";
            $usersData .= "<strong>Email:</strong> " . $user['email'] . "<br>";
            $usersData .= "<strong>Age:</strong> " . $user['age'] . "<br>";
            $usersData .= "<strong>Place:</strong> " . $user['place'] . "<br>";
            $usersData .= "<a href='?delete=$index' onclick='return confirm(\"Are you sure?\");'>Delete</a> | ";
            $usersData .= "<a href='?edit=$index'>Edit</a><br><br></div>";
        }
        $usersData .= "</div>";
    } else {
        $usersData .= "Error decoding JSON data.";
    }
}

// Check if editing
$editName = '';
$editEmail = '';
$editAge = '';
$editPlace = '';
$editIndex = -1;
if (isset($_GET['edit'])) {
    $editIndex = (int)$_GET['edit'];

    // Load the user to edit
    if (file_exists($dataFile)) {
        $jsonData = file_get_contents($dataFile);
        $users = json_decode($jsonData, true);

        // Check for JSON decoding errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            die("Error decoding JSON: " . json_last_error_msg());
        }

        if (isset($users[$editIndex])) {
            $editName = $users[$editIndex]['name'];
            $editEmail = $users[$editIndex]['email'];
            $editAge = $users[$editIndex]['age'];
            $editPlace = $users[$editIndex]['place'];
        } else {
            die("User not found.");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP JSON CRUD with Age and Place</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .form-container { margin: 20px; }
        .users-list { margin-top: 20px; }
        .user { margin-bottom: 15px; }
        #userData { display: none; } /* Initially hidden */
    </style>
</head>
<body>

<div class="form-container">
    <form method="POST">
        <input type="hidden" name="user_index" value="<?php echo $editIndex; ?>">
        Name: <input type="text" name="name" value="<?php echo $editName; ?>" required><br>
        Email: <input type="email" name="email" value="<?php echo $editEmail; ?>" required><br>
        Age: <input type="number" name="age" value="<?php echo $editAge; ?>" required><br>
        Place: <input type="text" name="place" value="<?php echo $editPlace; ?>" required><br>
        <input type="submit" value="<?php echo $editIndex >= 0 ? 'Update' : 'Submit'; ?>">
    </form>

    <button id="toggleButton">Display Data</button>

    <div id="userData">
        <?php echo $usersData; ?>
    </div>
</div>

<script>
    document.getElementById("toggleButton").addEventListener("click", function() {
        var userDataDiv = document.getElementById("userData");
        if (userDataDiv.style.display === "none" || userDataDiv.style.display === "") {
            userDataDiv.style.display = "block";  
            this.textContent = "Hide Data";  
        } else {
            userDataDiv.style.display = "none";  
            this.textContent = "Display Data";  
        }
    });
</script>

</body>
</html>
