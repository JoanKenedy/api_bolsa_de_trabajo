<?php

class DeleteController
{

    public function deleteData($table, $id, $nameId)
    {
        $response = DeleteModel::deleteData($table, $id, $nameId);
        $return = new DeleteController();
        $return->fncResponse($response, 'deleteData');
    }

    /* Respuestas de controlador */

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
