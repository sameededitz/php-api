
<?php
include_once 'config.php';
include_once 'prepare-crud.php';

$crud = new Prepare_crud('localhost', 'root', '', 'api-test');

class userAuth
{
    public $crud;

    public function __construct()
    {
        $this->crud = new Prepare_crud('localhost', 'root', '', 'api-test');
    }

    public function userRegister($data)
    {
        $required_fields = array('username', 'phone', 'password');
        foreach ($required_fields as $field) {
            if (!isset($data[$field])) {
                return "Missing Required Field: $field";
            }
        }

        if (strlen($data['username']) > 10) {
            return "Username must be less than 10 characters";
        }

        if (strlen($data['password']) < 8) {
            return "Password must be greater than 8 characters";
        }

        $existinguser = $this->crud->select('users', 'user_id', null, "phone = ?", array($data['phone']));
        if (is_array($existinguser) && !empty($existinguser['user_id'])) {
            return "Phone number already exists";
        }

        $passwordhash = password_hash($data['password'], PASSWORD_DEFAULT);
        unset($data['password']);
        $data['password'] = $passwordhash;
        $data['registration_date'] = date('Y-m-d H:i:s');

        $saveuser = $this->crud->insert('users', $data);
        if ($saveuser) {
            return true;
        } else {
            return $saveuser;
        }
    }

    public function userLogin($data)
    {
        $required_fields = array('username', 'password');
        foreach ($required_fields as $field) {
            if (!isset($data[$field])) {
                return "Missing Required Field: $field";
            }
        }

        $checkuser = $this->crud->select('users', 'user_id,password,token', null, "username = ? OR phone = ?", array($data['username'], $data['username']));
        if (is_array($checkuser) && !empty($checkuser['user_id'])) {
            if (password_verify($data['password'], $checkuser['password'])) {
                // last login
                $user_id = $checkuser['user_id'];
                $last_login = date('Y-m-d H:i:s');
                $updateLogin = $this->crud->update('users', array("last_login" => $last_login), "`user_id`=$user_id");

                if ($checkuser['token'] == null) {
                    $user_id = $checkuser['user_id'];
                    $token = bin2hex(random_bytes(4));

                    $updatetoken = $this->crud->update('users', array("token" => $token), "user_id = $user_id");
                    if ($updatetoken) {
                        return $token;
                    } else {
                        return $updatetoken;
                    }
                } else {
                    return $checkuser['token'];
                }
            }
        } else {
            return "User Does not Found";
        }
    }
}
?>
