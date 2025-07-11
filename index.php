<?php
session_start();

require "./requires/db.php";
require "./requires/common.php";
require "./requires/common_function.php";
$error = false;
$email = 
$email_error =
$password =
$password_error= '';
$checkUserExist = selectData('admin', $mysqli);
 $data = [
        'name'     => 'Admin',
        'email'    => "admin@gmail.com",
        'password' => md5("password")
    ]; 
if($checkUserExist->num_rows == 0){
    insertData('admin', $mysqli, $data);
}
if(isset($_POST['form_sub']) && $_POST['form_sub'] == 1){
    $email =  $mysqli->real_escape_string($_POST['email']);
    $password = $mysqli->real_escape_string($_POST['password']);
    
    //email
    if ($email === '') {
    $error = true;
    $email_error = "Please Enter Your Email.";}
   
    // Password
    if ($password == '') {
    $error = true;
    $password_error = "Please Enter Your Password.";}

    if(!$error){
    $user = selectData('admin', $mysqli,"*", "WHERE `email`='$email'");
    $old_user  = $user->fetch_assoc();
    if($user->num_rows > 0){
        $old_password = $old_user['password'];  
        if($old_password === md5($password)){
          $_SESSION['email'] = $old_user['email'];
          header("Location: $admin_base_url");
        }else{
          $password_error = "Password is wrong!";
        }
    }else{
      $email_error = "This email is not registered yet!";
    }
  }
}
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">

        <div class="card shadow rounded-4">
          <div class="card-body p-4">
            <h3 class="text-center mb-4">Admin Login</h3>

            <form method="POST">
              <div class="mb-3">

              <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= $email?>">
                <small class="text-danger"><?= $email_error ?></small>
              </div> 

              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" value="<?= $password?>">
                 <small class="text-danger"><?= $password_error ?></small>
              </div>

              <input type="hidden" value="1" name="form_sub">

              <div class="mb-3">
                <button type="submit" class="btn btn-primary w-100">Login</button>
              <div class="mb-3">
            </form>

          </div>
        </div>

      </div>
    </div>
  </div>

</body>
</html>
