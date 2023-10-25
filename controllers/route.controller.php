<?php

class RoutesController{
    public function index (){
        include "routes/route.php";
    }

   static public function database(){
        return "bolsa_de_trabajo";
    }
}
?>