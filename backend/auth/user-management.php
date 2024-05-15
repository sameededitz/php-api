<?php
include_once '../controller/config.php';
include_once '../controller/prepare-crud.php';
include_once '../controller/user-crud.php';

$crud = new Prepare_crud('localhost', 'root', '', 'api-test');
$user = new userAuth();

if (isset($_GET['action']) && $_GET['action'] == 'register') {
    $data = array(
        'username' => 'sameed',
        'phone' => '03068517336',
        'password' => 'sameed222',
    );

    $registeruser = $user->userRegister($data);
    if ($registeruser == true && !is_array($registeruser)) {
        echo "User Registered";
    } else {
        print_r($registeruser);
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'login') {
    $data = array(
        'username' => 'sameed',
        'password' => 'sameed222',
    );
    $loginuser = $user->userLogin($data);
    if ($loginuser == true && !empty($loginuser)) {
        echo "User Login Successfully. Token is $loginuser";
    } else {
        echo($loginuser);
    }
}
