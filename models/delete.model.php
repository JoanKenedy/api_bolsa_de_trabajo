<?php
require_once 'connection.php';

class DeleteModel
{
    static  public function deleteData($table, $id, $nameId)
    {

        $stmt = Connection::connect()->prepare("DELETE FROM $table WHERE  $nameId = :$nameId ");
        $stmt->bindParam(":" . $nameId, $id, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "El proceso se ha realizado con éxito";
        } else {
            return Connection::connect()->errorInfo();
        }
    }
}