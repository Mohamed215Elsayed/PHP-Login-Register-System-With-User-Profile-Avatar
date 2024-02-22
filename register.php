<?php
include('connect.php');
ob_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>register</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php
    if (isset($_POST['submit'])) {
        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $pass = htmlspecialchars($_POST['password']);
        $cpass = htmlspecialchars($_POST['cpassword']);
        $image = $_FILES['image']['name'];
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = 'uploaded_img/' . $image;
        $errors = [];
        if ($pass == $cpass) {
            $selectQuery = "SELECT * FROM `user_form`";
            $selectDone = $conn->query($selectQuery);
            // print_r($selectDone);
            $data = $selectDone->fetchAll(PDO::FETCH_ASSOC);
            // print_r($data);
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]['email'] == $email) {
                    $errors['email_error'] = "Email already exists";
                };
                if ($data[$i]['name'] == $name) {
                    $errors['name_error'] = "Username already exists";
                }
            }
            // if (empty($errors)) {//===
            if (count($errors) == 0) {
                // $pw = md5($pw);
                $hashedPw = password_hash($pass, PASSWORD_BCRYPT);
                $insertQuery = "INSERT INTO `user_form` (`name`,`email`, `password`,`image`) VALUES ('$name','$email','$hashedPw','$image')";
                $insertDone = $conn->query($insertQuery);
                move_uploaded_file($image_tmp_name, $image_folder);
                if ($insertDone) {
                    header('location:login.php');
                }
            }
        }
        else {
            $errors['password_error'] = "Password not Match";
        }
        if($image_size > 2000000){
            $message[] = 'image size is too large!';
        }
    }

    ?>
    <?php
    if (isset($message)) {
        foreach ($message as $message) {
            echo '<div class="message">' . $message . '</div>';
        }
    }
    ?>
    <div class="form-container">
        <form action="" method="post" enctype="multipart/form-data">
            <h3>register now</h3>
            <input type="text" name="name" placeholder="enter username" class="box" required value="<?php if (isset($name)) {echo $name;} ?>">
            <P class="error"><?php if (isset($errors['name_error'])) echo $errors['name_error']; ?></P>
            <input type="email" name="email" placeholder="enter email" class="box" required value="<?php if (isset($email)){ echo $email;}; ?>">
            <P class="error"><?php if (isset($errors['email_error'])) echo $errors['email_error']; ?></P>
            <input type="password" name="password" placeholder="enter password" class="box" required>
            <input type="password" name="cpassword" placeholder="confirm password" class="box" required>
            <P class="error"><?php if (isset($errors['password_error'])) echo $errors['password_error']; ?></P>
            <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
            <input type="submit" name="submit" value="register now" class="btn">
            <p>already have an account? <a href="login.php">login now</a></p>
        </form>
    </div>

</body>

</html>
<?php
ob_end_flush();
?>