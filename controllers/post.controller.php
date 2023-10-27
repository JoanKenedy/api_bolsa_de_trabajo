<?php
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
        $return-> fncResponse($response, "postData", null);
    }
    /* Peticion POST para registrar usuario */

    public function postRegister($table, $data){
      if(isset($data["password"]) && $data["password"] != null){
       $crypt = crypt($data["password"], '$2a$07$azybxcags23425sdg23sdfhsd$');
        $data["password"] = $crypt;

         $response = PostModel::postData($table, $data);
        $return = new PostController();
        $return-> fncResponse($response, "postData", null);
      }
    }

    /* Login para ingresar como usuario*/
    public function postLogin($table, $data){
       $response = GetModel::getFilterData($table, "email", $data["email"], null, null, null, null);
       
       if(!empty($response)){
          $crypt = crypt($data["password"], '$2a$07$azybxcags23425sdg23sdfhsd$'); 
                  $data["password"] = $crypt;
         echo '<pre>'; print_r($response); echo '</pre>';
          echo '<pre>'; print_r($data); echo '</pre>';
         
        if($response[0]->password == $data["password"]){
            
           $return = new PostController();
           $return -> fncResponse($response, 'postLogin', null);
        }else{
           $response = null;
          $return = new PostController();
          $return -> fncResponse($response, "postLogin", "Wrong password");
        }
            
       }else{
          $response = null;
          $return = new PostController();
          $return -> fncResponse($response, "postLogin", "Wrong email");
       }
    }


    /*Respuestas del controlador */

    public function fncResponse($response, $method, $error)
    {
        if (!empty($response)) {

           /* if(isset($response[0]->password)){
                unset($response[0]->password);
            } */
            $json = array(
                'status' => 200,
                'result' => $response
            );
        } else {

            if($error != null){
                $json = array(
                'status' => 400,
                'result' => $error
            );
            }else{
                $json = array(
                'status' => 404,
                'result' => "Not found",
                'method'  => $method
            );
            }
          
        }
        echo json_encode($json, http_response_code($json['status']));
        return;
    }
}