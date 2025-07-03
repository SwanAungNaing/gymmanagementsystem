<?php

$host = "localhost";
$username = "root";
$password = "";

$mysqli = new mysqli($host, $username, $password);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

createDatabase($mysqli);
function createDatabase($mysqli)
{
    $sql = "CREATE DATABASE IF NOT EXISTS `gymmanagement` 
        DEFAULT CHARACTER SET utf8mb4
        COLLATE utf8mb4_general_ci";
    $mysqli->query($sql);
}

selectDatabase($mysqli);
function selectDatabase($mysqli)
{
    if ($mysqli->select_db("gymmanagement")) {
        return true;
    }
    return false;
}
createTable($mysqli);
function createTable($mysqli)
{
    // create admin
    $admin_sql = "CREATE TABLE IF NOT EXISTS `admin`
                (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name  VARCHAR(100) NOT NULL,
                email VARCHAR(50) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
                )";
    $mysqli->query($admin_sql);

    // create trainer
    $trainer_sql = "CREATE TABLE IF NOT EXISTS `trainers`
                (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name  VARCHAR(100) NOT NULL,
                email VARCHAR(50) NOT NULL,
                phone VARCHAR(20) NOT NULL,
                address VARCHAR(255) NOT NULL,
                gender ENUM('male', 'female', 'others'),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
                )";
    $mysqli->query($trainer_sql);

    // create certificate
    $certificate_sql = "CREATE TABLE IF NOT EXISTS `certificates`
                (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name  VARCHAR(100) NOT NULL,
                trainer_id INT,
                img_path VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (trainer_id) REFERENCES trainers(id) ON DELETE CASCADE
                )";
    $mysqli->query($certificate_sql);

    // create service
    $service_sql = "CREATE TABLE IF NOT EXISTS `services`
                (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name  VARCHAR(100) NOT NULL,
                start_time TIME,
                end_time TIME,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";
    $mysqli->query($service_sql);

    // create class
    $class_sql = "CREATE TABLE IF NOT EXISTS `classes`
                (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                batch_name VARCHAR(50),
                trainer_id INT,
                service_id INT,
                start_date DATE,
                end_date DATE,
                price VARCHAR(50),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
                FOREIGN KEY (trainer_id) REFERENCES trainers(id) ON DELETE CASCADE,
                FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
                )";
    $mysqli->query($class_sql);

    // create member
    $member_sql = "CREATE TABLE IF NOT EXISTS `members`
                (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100),
                phone VARCHAR(20),
                address VARCHAR(255),
                gender ENUM('male', 'female', 'others'),
                original_weight VARCHAR(20),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
                )";
    $mysqli->query($member_sql);

    // create class_member
    $class_member_sql = "CREATE TABLE IF NOT EXISTS `class_members`
                (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                class_id INT,
                member_id INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
                FOREIGN KEY (member_id) REFERENCES members(id)ON DELETE CASCADE
                )";
    $mysqli->query($class_member_sql);

    // create class_payment
    $class_payment_sql = "CREATE TABLE IF NOT EXISTS `class_payment`
                (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                class_member_id INT,
                total_amount VARCHAR(50),
                order_date DATE,
                status ENUM('paid', 'pending'),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (class_member_id) REFERENCES class_members(id) ON DELETE CASCADE 
                )";
    $mysqli->query($class_payment_sql);

    // create attendance
    $attendance_sql = "CREATE TABLE IF NOT EXISTS `attendance`
                (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                class_member_id INT,
                date DATE,
                status ENUM('present', 'absent'),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (class_member_id) REFERENCES class_members(id) ON DELETE CASCADE 
                )";
    $mysqli->query($attendance_sql);

    // create member_weight
    $member_weight_sql = "CREATE TABLE IF NOT EXISTS `member_weight`
                (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                member_id INT,
                date DATE,
                curr_weight VARCHAR(20) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE 
                )";
    $mysqli->query($member_weight_sql);

    // create brand_name
    $brand_name_sql = "CREATE TABLE IF NOT EXISTS `brand_name`
                (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
                )";
    $mysqli->query($brand_name_sql);

    // create equipment_type
    $equipment_type_sql = "CREATE TABLE IF NOT EXISTS `equipment_type`
                (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                type_name VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
                )";
    $mysqli->query($equipment_type_sql);

    // create equipment
    $equipment_sql = "CREATE TABLE IF NOT EXISTS `equipments`
                (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                brand_name_id INT,
                equipment_type_id INT,
                price VARCHAR(50),
                quantity INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (brand_name_id) REFERENCES brand_name(id) ON DELETE CASCADE,
                FOREIGN KEY (equipment_type_id) REFERENCES equipment_type(id) ON DELETE CASCADE 
                )";
    $mysqli->query($equipment_sql);

    // create esale_order
    $esale_order_sql = "CREATE TABLE IF NOT EXISTS `esale_order`
                (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                equipment_id INT,
                member_id INT,
                quantity INT,
                total_amount VARCHAR(50),
                order_date DATETIME,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (equipment_id) REFERENCES equipments(id) ON DELETE CASCADE,
                FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE 
                )";
    $mysqli->query($esale_order_sql);
}
