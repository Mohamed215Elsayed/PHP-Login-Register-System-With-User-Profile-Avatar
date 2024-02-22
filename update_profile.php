<?php
include('connect.php');
session_start();
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>update profile</title>
  <link rel="stylesheet" href="css/style.css">

</head>

<body>
  <?php
  if (isset($_POST['update_profile'])) {
    $update_name = htmlspecialchars($_POST['update_name']);
    $update_email = htmlspecialchars($_POST['update_email']);
    // $updateQuery = "UPDATE `user_form` SET `name` = '$update_name', 'email' = '$update_email' WHERE 'id' = '$user_id'";
    // Assuming $conn is a PDO connection
    $updateQuery = "UPDATE user_form SET name = :update_name, email = :update_email WHERE id = :user_id";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bindParam(':update_name', $update_name);
    $updateStmt->bindParam(':update_email', $update_email);
    $updateStmt->bindParam(':user_id', $user_id);
    $updateStmt->execute();
    //Security--> Performance -->Readability and Maintainability.
    $old_pass = htmlspecialchars($_POST['old_pass']);
    $update_pass = htmlspecialchars($_POST['update_pass']);
    $hashedPw_update = password_hash($update_pass, PASSWORD_BCRYPT);
    $new_pass = htmlspecialchars($_POST['new_pass']);
    $hashedpw_newpw = password_hash($new_pass, PASSWORD_BCRYPT);
    $confirm_pass = htmlspecialchars($_POST['confirm_pass']);
    $hashedpw_confirm = password_hash($confirm_pass, PASSWORD_BCRYPT);
    if (!empty($hashedPw_update) || !empty($hashedpw_newpw) || !empty($hashedpw_confirm)) {
      if ($hashedPw_update != $old_pass) {
        $message[] = 'old password not matched!';
      } else if ($hashedpw_newpw != $hashedpw_confirm) {
        $message[] = 'confirm password not matched!';
      } else {
        $updateQuery = ("UPDATE `user_form`   SET password = :hashedpw_confirm WHERE id = :user_id") or die('query failed');
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':update_pass', $hashedpw_newpw);
        $updateStmt->bindParam(':user_id', $user_id);
        $updateStmt->execute();
        $message[] = 'password updated successfully!';
      }
    }
    $update_image = $_FILES['update_image']['name'];
    $update_image_size = $_FILES['update_image']['size'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_image_folder = 'uploaded_img/' . $update_image;
    if (!empty($update_image)) {
      if ($update_image_size > 2000000) {
        $message[] = 'image is too large';
      } else {
        $image_update_query = "UPDATE `user_form` SET  `image` = '$update_image' WHERE `id` = '$user_id'" or die('query failed');
        if ($image_update_query) {
          move_uploaded_file($update_image_tmp_name, $update_image_folder);
        }
        $message[] = 'image updated succssfully!';
      }
    }
  }
  ?>
  <?php
  $selectQuery = "SELECT * FROM `user_form` WHERE `id` = :user_id";
  $selectStmt = $conn->prepare($selectQuery);
  $selectStmt->bindParam(':user_id', $user_id);
  $selectStmt->execute();
  if ($selectStmt->rowCount() > 0) {
    $fetch = $selectStmt->fetch(PDO::FETCH_ASSOC);
    // Process the fetched data
  }
  ?>

  <div class="update-profile">
    <form action="" method="post" enctype="multipart/form-data">
      <?php
      if ($fetch['image'] == '') {
        echo '<img src="images/default-avatar.png">';
      }
      else {
        echo '<img src="uploaded_img/' . $fetch['image'] . '">';
      }
      if(isset($message)){
        foreach($message as $message){
            echo '<div class="message">'.$message.'</div>';
        }
      }
      ?>

      <div class="flex">
        <div class="inputBox">
          <span>username :</span>
          <input type="text" name="update_name" value="<?php echo $fetch['name']; ?>" class="box">
          <span>your email :</span>
          <input type="email" name="update_email" value="<?php echo $fetch['email']; ?>" class="box">
          <span>update your pic :</span>
          <input type="file" name="update_image" accept="image/jpg, image/jpeg, image/png" class="box">
        </div>
        <div class="inputBox">
          <input type="hidden" name="old_pass" value="<?php echo $fetch['password']; ?>">
          <span>old password :</span>
          <input type="password" name="update_pass" placeholder="enter previous password" class="box">
          <span>new password :</span>
          <input type="password" name="new_pass" placeholder="enter new password" class="box">
          <span>confirm password :</span>
          <input type="password" name="confirm_pass" placeholder="confirm new password" class="box">
        </div>
      </div>
      <input type="submit" value="update profile" name="update_profile" class="btn">
      <a href="home.php" class="delete-btn">go back</a>
    </form>
  </div>

</body>

</html>