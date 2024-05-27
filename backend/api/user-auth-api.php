<?php
include_once '../controller/config.php';
include_once '../controller/prepare-crud.php';
include_once '../controller/user-crud.php';

$crud = new Prepare_crud('localhost', 'root', '', 'api-test');
$user = new userAuth();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if (isset($_GET['action']) && $_GET['action'] == 'register') {
    header('Access-Control-Allow-Methods:POST');
    header('Access-Control-Allow-Headers:Access-Control-Allow-Headers,Access-Control-Allow-Methods,Access-Control-Allow-Origin,Content-Type');

    $getdata = json_decode(file_get_contents("php://input"), true);

    $data = array(
        'username' => $getdata['name'],
        'phone' => $getdata['phone'],
        'password' => $getdata['password']
    );

    $registeruser = $user->userRegister($data);
    if ($registeruser === true) {
        echo json_encode(array('status' => 'true', 'message' => 'User Registered Successfully'), JSON_PRETTY_PRINT || JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(array('status' => 'false', 'message' => $registeruser), JSON_PRETTY_PRINT || JSON_UNESCAPED_UNICODE);
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'login') {
    header('Access-Control-Allow-Methods:POST');
    header('Access-Control-Allow-Headers:Access-Control-Allow-Headers,Access-Control-Allow-Methods,Access-Control-Allow-Origin,Content-Type');

    $getdata = json_decode(file_get_contents("php://input"), true);

    $data = array(
        'username' => $getdata['name'],
        'password' => $getdata['password']
    );

    $loginuser = $user->userLogin($data);
    if ($loginuser == true && !empty($loginuser)) {
        echo json_encode(array('status' => 'true', 'message' => 'User Login Successfully', 'Token' => $loginuser), JSON_PRETTY_PRINT || JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(array('status' => 'false', 'message' => $loginuser), JSON_PRETTY_PRINT || JSON_UNESCAPED_UNICODE);
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'getuser') {
    header('Access-Control-Allow-Methods:GET');
    header('Access-Control-Allow-Headers:Access-Control-Allow-Headers,Access-Control-Allow-Methods,Access-Control-Allow-Origin,Content-Type');

    $getdata = json_decode(file_get_contents("php://input"), true);

    if (empty($getdata['token'])) {
        echo json_encode(array('status' => 'false', 'message' => 'Token is required'), JSON_PRETTY_PRINT || JSON_UNESCAPED_UNICODE);
        return;
    }

    $data = array(
        'token' => $getdata['token']
    );
    $token = $data['token'];

    $userdata = $crud->select('users', '*', null, "`token`= ?", array($token));
    if ($userdata == true && is_array($userdata)) {
        echo json_encode(array('status' => 'true', 'message' => 'User Data Retrieved Successfully', 'data' => $userdata), JSON_PRETTY_PRINT || JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(array('status' => 'false', 'message' => 'Invalid Token', 'Error' => $userdata), JSON_PRETTY_PRINT || JSON_UNESCAPED_UNICODE);
    }
}
