<?php
require_once "models/connection.php";

class PostModel
{

    static public function getColumsData($table, $database)
    {
        return Connection::connect()->query("SELECT COLUMN_NAME AS item FROM information_schema.columns WHERE table_schema = '$database' AND table_name = '$table'")
            ->fetchAll(PDO::FETCH_OBJ);
    }

    static public function postData($table, $data)
    {

        $columns = "(";
        $params = "(";

        foreach ($data as $key => $value) {
            $columns .= $key . ",";
            $params .= ":" . $key . ",";
        }

        $columns = substr($columns, 0, -1);
        $params = substr($params, 0, -1);

        $columns .= ")";
        $params .= ")";

        $stmt = Connection::connect()->prepare("INSERT INTO $table $columns VALUES $params");

        foreach ($data as $key => $value) {
            $stmt->bindParam(":" . $key, $data[$key], PDO::PARAM_STR);
        }

        if ($stmt->execute()) {
            return "El proceso se ha hecho con éxito";
        } else {

            return Connection::connect()->errorInfo();
        }
    }
}
