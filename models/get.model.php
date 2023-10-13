<?php



require_once "models/connection.php";

class GetModel {
    /* Peticiones sin filtro */
    
   static public function getData($table){
        $stmt = Connection::connect()->prepare("SELECT * FROM $table");
        $stmt-> execute();

        return $stmt -> fetchAll(PDO::FETCH_CLASS);
    }

    /* Peticiones con filtro */
    
    static public function getFilterData($table,$linkTo, $equalTo){
        $stmt = Connection::connect()->prepare("SELECT * FROM $table WHERE $linkTo = :$linkTo");
        $stmt -> bindParam(":".$linkTo, $equalTo, PDO::PARAM_STR);
        $stmt-> execute();

        return $stmt -> fetchAll(PDO::FETCH_CLASS);
    } 
}

?>