<?php
class PostController
{

     static public function getColumnsData($table, $database){
        $response = PostModel::getColumsData($table, $database);
        return $response;
     }

    static public function postData($table, $data){
          
        $response = PostModel::postData($table, $data);
        $return = new PostController();
        $return -> fncResponse($response, "postData");
    }



     /*Respuestas del controlador */

    public function fncResponse($response, $method)
    {
        if (!empty($response)) {
            $json = array(
                'status' => 200,
                'result' => $response
            );
        } else {
            $json = array(
                'status' => 404,
                'result' => "Not found",
                'method'  => $method
            );
        }
        echo json_encode($json, http_response_code($json['status']));
        return;
    }
}

?>