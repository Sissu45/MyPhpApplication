<?php

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "myapplication"; 


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) 
{
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    
    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
       
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
        
        if ($conn->query($sql) === TRUE) {
            $success = "Registration successful! You can now log in.";
            header("Location: login.php"); 
            exit();
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
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
    <title>Registration Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2>

       
        <?php if (isset($error)) { echo "<div class='error'>$error</div>"; } ?>
        <?php if (isset($success)) { echo "<div class='success'>$success</div>"; } ?>

        <form method="POST" action="register.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Register</button>
        </form>

        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>