<?php
include_once '../controller/config.php';

$usertable = "CREATE TABLE Users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    registration_date DATETIME NOT NULL,
    last_login DATETIME,
    token VARCHAR(255) NOT NULL,
    verification_code VARCHAR(100),
    verification_expiry DATETIME,
    resetpassword_code VARCHAR(100),
    resetpassword_expiry DATETIME
);
";
$query = mysqli_query($conn,$usertable);
if($query){
    echo 'Table Created Successfully';
}else{
    echo 'Error Creating Table'.mysqli_error($conn);
}
?>