<?php



use Firebase\JWT\JWT;

class PostController


{

    static public function getColumnsData($table, $database)
    {
        $response = PostModel::getColumsData($table, $database);
        return $response;
    }

    static public function postData($table, $data)
    {

        $response = PostModel::postData($table, $data);
        $return = new PostController();
        $return->fncResponse($response, "postData", null);
    }
    /* Peticion POST para registrar usuario */

    public function postRegister($table, $data)
    {
        if (isset($data["password"]) && $data["password"] != null) {
            $crypt = crypt($data["password"], '$2a$07$azybxcags23425sdg23sdfhsd$');
            $data["password"] = $crypt;

            $response = PostModel::postData($table, $data);
            $return = new PostController();
            $return->fncResponse($response, "postData", null);
        }
    }

    /* Login para ingresar como usuario*/
    public function postLogin($table, $data)
    {
        $response = GetModel::getFilterData($table, "email", $data["email"], null, null, null, null, "*");

        if (!empty($response)) {
            $crypt = crypt($data["password"], '$2a$07$azybxcags23425sdg23sdfhsd$');
            $pass1 = $response[0]->password;
            $limite = strlen($pass1);
            $pass2 = substr($crypt, 0, $limite);



            if ($pass1 == $pass2) {

                $time = time();
                $key = "e45emM1213rhnqz92NnVvz";

                $token = array(
                    "iat" => $time,
                    "exp" => $time + (60 * 60 * 24),
                    "data" => [
                        "id" => $response[0]->id_usuario,
                        "email" => $response[0]->email

                    ]
                );

                $jwt = JWT::encode($token, $key, "HS256");

                $data = array(
                    'token_user' => $jwt,
                    "token_exp_user" => $token["exp"]
                );

                $update = Putmodel::putData($table, $data, $response[0]->id_usuario, "id_usuario");

                if ($update == "The process was successful") {
                    $response[0]->token_user = $jwt;


                    $return = new PostController();
                    $return->fncResponse($response, 'postLogin', null);
                }
            } else {
                $response = null;
                $return = new PostController();
                $return->fncResponse($response, "postLogin", "Wrong password");
            }
        } else {
            $response = null;
            $return = new PostController();
            $return->fncResponse($response, "postLogin", "Wrong email");
        }
    }

    /*Crear los primeros datos del curriculum*/
    public function postDatosContacto($table, $data)
    {
        $response = GetModel::getFilterData($table, "id_usuario_curriculum", $data["id_usuario_curriculum"], null, null, null, null, "*");
        if (isset($data["id_usuario_curriculum"]) && $data["id_usuario_curriculum"] != null) {

            $response = PostModel::postData($table, $data);
            $return = new PostController();
            $return->fncResponse($response, "postDataContacto", null);
        }
    }
       /* Login para ingresar como usuario*/
    public function postDatosProfesion($table, $data)
    {
        $response = GetModel::getFilterData($table, "regPuesto", $data["regPuesto"], null, null, null, null, "*");
        if (isset($data["regPuesto"]) && $data["regPuesto"] != null) {

            $response = PostModel::postData($table, $data);
            $return = new PostController();
            $return->fncResponse($response, "postDataProfesion", null);
        }
    }


    /*Respuestas del controlador */

    public function fncResponse($response, $method, $error)
    {
        if (!empty($response)) {
            $json = array(
                'status' => 200,
                'results' => $response
            );
        } else {

            if ($error != null) {
                $json = array(
                    'status' => 400,
                    'results' => $error
                );
            } else {
                $json = array(
                    'status' => 404,
                    'results' => "Not found",
                    'method'  => $method
                );
            }
        }
        echo json_encode($json, http_response_code($json['status']));
        return;
    }
}