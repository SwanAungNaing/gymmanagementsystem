<?php
require './requires/common.php';
session_start();
if (session_destroy()) {
    $url = $base_url . "index.php";
    header("Location: $url");
}
