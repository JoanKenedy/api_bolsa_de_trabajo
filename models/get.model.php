<?php



require_once "models/connection.php";

class GetModel {
    /* Peticiones sin filtro */
    
   static public function getData($table){
        $stmt = Connection::connect()->prepare("SELECT * FROM $table");
        $stmt-> execute();

        return $stmt -> fetchAll(PDO::FETCH_CLASS);
    }
}

?>