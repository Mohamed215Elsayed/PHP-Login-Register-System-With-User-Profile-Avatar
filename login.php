<?php

include('connect.php');
session_start();
ob_start(); // Start output buffering
if(isset($_POST['submit'])){
    $email = htmlspecialchars($_POST['email']);
    $pass = htmlspecialchars($_POST['password']);
    $selectQuery = "SELECT * FROM `user_form` WHERE `email` = '$email'"; 
    $selectDone = $conn->query($selectQuery);
    $data = $selectDone->fetchAll(PDO::FETCH_ASSOC);
    $loginError = [];
    if($data){
        $originalPw = password_verify($pass, $data[0]['password']);
        if ($originalPw) {
            $_SESSION['user_id'] = $data[0]['id'];
            header('location: home.php');
            exit();
        } else {
            $loginError['pwerror'] = "Incorrect Password";
        }
    } 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <form action="" method="post" enctype="multipart/form-data">
            <h3>login now</h3>
            <?php
            if (isset($loginError['pwerror'])) {
                echo '<div class="message">' . $loginError['pwerror'] . '</div>';
            }
            ?>
            <input type="email" name="email" placeholder="enter email" class="box" required>
            <input type="password" name="password" placeholder="enter password" class="box" required>
            <input type="submit" name="submit" value="login now" class="btn">
            <p>don't have an account? <a href="register.php">register now</a></p>
        </form>
    </div>
</body>
</html>
<?php 
ob_end_flush(); // Flush the output
?>