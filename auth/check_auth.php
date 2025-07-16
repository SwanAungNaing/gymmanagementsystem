<?php
$user = json_decode($_COOKIE["user"], true);
if (!$user) {
    header("Location: http://localhost/gymmanagement/index.php?invalid=Please login first!");
    exit();
}
if (isset($_POST['logout'])) {
    setcookie("user", '', -1, "/");
    header('Location:../index.php');
}
    