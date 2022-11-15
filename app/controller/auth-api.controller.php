<?php
require_once './app/model/auth-api.model.php';
require_once './app/view/api.view.php';
require_once './app/helpers/auth-api.helper.php';


function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}


class AuthApiController {
    private $authModel;
    private $view;
    private $authHelper;

    private $data;

    public function __construct() {
        $this->authModel = new userModel();
        $this->view = new ApiView();
        $this->authHelper = new AuthApiHelper();
        
        // lee el body del request
        $this->data = file_get_contents("php://input");
    }

    private function getData() {
        return json_decode($this->data);
    }



    public function getToken($params = null) {
        $basic = $this->authHelper->getAuthHeader();
        
        if(empty($basic)){
            return $this->view->response('No autorizado', 401);
        }
        
        $basic = explode(" ",$basic); 
        if($basic[0]!="Basic"){
            return $this->view->response('La autenticación debe ser Basic', 401);
        }

        $userpass = base64_decode($basic[1]);
        $userpass = explode(":", $userpass);

        $user = $userpass[0];
        $pass = $userpass[1];

        $userDb = $this->authModel->getUser($user);
        if($userDb && password_verify($pass, $userDb->password)){
            $header = array(
                'alg' => 'HS256',
                'typ' => 'JWT'
            );
            $payload = array(
                'id' => 1,
                'name' => $user,
                'exp' => time()+3600
            );
            $header = base64url_encode(json_encode($header));
            $payload = base64url_encode(json_encode($payload));
            $signature = hash_hmac('SHA256', "$header.$payload", "Clave1234", true);
            $signature = base64url_encode($signature);
            $token = "$header.$payload.$signature";
            return $this->view->response($token, 200);
        }else
            return $this->view->response('No autorizado', 401);
    }

}
