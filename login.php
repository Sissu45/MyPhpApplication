<?php

$servername = "localhost"; 
$username = "root";        
$password = "";            
$dbname = "myapplication"; 

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

   
    if (empty($input_username) || empty($input_password)) {
        $error = "Both fields are required.";
    } else {
        
        $sql = "SELECT * FROM users WHERE username = '$input_username'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            
            if (password_verify($input_password, $user['password'])) {
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: movie_dashboard.php"); 
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "No such user found.";
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>

        <?php if (isset($error)) { echo "<div class='error'>$error</div>"; } ?>

        <form method="POST" action="login.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>

        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
