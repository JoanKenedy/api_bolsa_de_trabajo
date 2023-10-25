<?php

$routesArray = explode("/", $_SERVER['REQUEST_URI']);
$routesArray = array_filter($routesArray);
/*Con esto traigo mi url $routesArray = $_SERVER['HTTP_HOST'];*/
if (count($routesArray) == 0) {
  $json = array(
    'status' => 404,
    "result" => "Not found"
  );

  echo json_encode($json, http_response_code($json["status"]));
  return;
} else {

  /* PETICIONES GET */

  if (
    count($routesArray) == 1 && isset($_SERVER["REQUEST_METHOD"])
    && $_SERVER["REQUEST_METHOD"] == "GET"
  ) {

    /* Peticion GET con filtro */


    if (isset($_GET["linkTo"]) && isset($_GET["equalTo"]) && !isset($_GET["rel"]) && !isset($_GET["type"])) {

      /* Preguntamos si vienen variables de orden */

      if (isset($_GET["orderBy"]) && isset($_GET["orderMode"])) {
        $orderBy = $_GET["orderBy"];
        $orderMode = $_GET["orderMode"];
      } else {
        $orderBy = null;
        $orderMode = null;
      }

      /* Preguntamos si vienen variables de limite */

      if (isset($_GET["startAt"]) && isset($_GET["endAt"])) {
        $startAt = $_GET["startAt"];
        $endAt = $_GET["endAt"];
      } else {
        $startAt = null;
        $endAt = null;
      }
      $response = new GetController();
      $response->getFilterData(explode("?", $routesArray[1])[0], $_GET["linkTo"], $_GET["equalTo"], $orderBy, $orderMode, $startAt, $endAt);


      /* Peticion GET entre tablas relacionadas sin filtro */
    } else if (
      isset($_GET["rel"]) && isset($_GET["type"]) && explode("?", $routesArray[1])[0] == "relations"
      && !isset($_GET["linkTo"]) && !isset($_GET["equalTo"])
    ) {
      if (isset($_GET["orderBy"]) && isset($_GET["orderMode"])) {
        $orderBy = $_GET["orderBy"];
        $orderMode = $_GET["orderMode"];
      } else {
        $orderBy = null;
        $orderMode = null;
      }

      /* Preguntamos si vienen variables de limite */

      if (isset($_GET["startAt"]) && isset($_GET["endAt"])) {
        $startAt = $_GET["startAt"];
        $endAt = $_GET["endAt"];
      } else {
        $startAt = null;
        $endAt = null;
      }

      $response = new GetController();
      $response->getRelData($_GET["rel"], $_GET["type"], $orderBy, $orderMode, $startAt, $endAt);
      /* Peticion GET entre tablas relacionadas con filtro */
    } else if (
      isset($_GET["rel"]) && isset($_GET["type"]) && explode("?", $routesArray[1])[0] == "relations" &&
      isset($_GET["linkTo"]) && isset($_GET["equalTo"])
    ) {
      if (isset($_GET["orderBy"]) && isset($_GET["orderMode"])) {
        $orderBy = $_GET["orderBy"];
        $orderMode = $_GET["orderMode"];
      } else {
        $orderBy = null;
        $orderMode = null;
      }

      /* Preguntamos si vienen variables de limite */

      if (isset($_GET["startAt"]) && isset($_GET["endAt"])) {
        $startAt = $_GET["startAt"];
        $endAt = $_GET["endAt"];
      } else {
        $startAt = null;
        $endAt = null;
      }

      $response = new GetController();
      $response->getRelFilterData($_GET["rel"], $_GET["type"], $_GET["linkTo"], $_GET["equalTo"], $orderBy, $orderMode, $startAt, $endAt);
    } else if (isset($_GET["linkTo"]) && isset($_GET["search"])) {

      if (isset($_GET["orderBy"]) && isset($_GET["orderMode"])) {
        $orderBy = $_GET["orderBy"];
        $orderMode = $_GET["orderMode"];
      } else {
        $orderBy = null;
        $orderMode = null;
      }

      /* Preguntamos si vienen variables de limite */

      if (isset($_GET["startAt"]) && isset($_GET["endAt"])) {
        $startAt = $_GET["startAt"];
        $endAt = $_GET["endAt"];
      } else {
        $startAt = null;
        $endAt = null;
      }

      /* Peticion GET para el buscador */
      $response = new GetController();
      $response->getSearchData(explode("?", $routesArray[1])[0], $_GET["linkTo"], $_GET["search"], $orderBy, $orderMode, $startAt, $endAt);

      /* Peticion GET sin filtro */
    } else {
      /* Preguntamos si vienen variables de orden */

      if (isset($_GET["orderBy"]) && isset($_GET["orderMode"])) {
        $orderBy = $_GET["orderBy"];
        $orderMode = $_GET["orderMode"];
      } else {
        $orderBy = null;
        $orderMode = null;
      }
      /* Preguntamos si vienen variables de limite */

      if (isset($_GET["startAt"]) && isset($_GET["endAt"])) {
        $startAt = $_GET["startAt"];
        $endAt = $_GET["endAt"];
      } else {
        $startAt = null;
        $endAt = null;
      }

      $response = new GetController();
      $response->getData(explode('?', $routesArray[1])[0], $orderBy, $orderMode, $startAt, $endAt);
    }
  }

  /* PETICIONES POST */

  if (
    count($routesArray) == 1 && isset($_SERVER["REQUEST_METHOD"])
    && $_SERVER["REQUEST_METHOD"] == "POST"
  ) {

    $columns = array();
    /* Traemos el listado de columnas de la tabla a cambiar */
    $database = RoutesController::database();
    $response = PostController::getColumnsData(explode("?", $routesArray[1])[0], $database);

    foreach($response as $key => $value){
      array_push($columns, $value->item);
    }
    /*Quitamos el primer y ultimo indice del array */
    array_shift($columns);
    array_pop($columns);

     /* Recibimos los valores Post */
     if(isset($_POST)){
      /* Validamos que las variables POST coincidan con los nombres  de la columnas de la  base de datos */
      $count = 0;

      foreach($columns as $key => $value){
             if(array_keys($_POST)[$key] == $value){
                 $count++;
             }else{
                $json = array (
                    "status" => 400,
                    "result" => "No coinciden las columnas con las de la base de datos"
                );

                echo json_encode($json, http_response_code($json["status"]));
                return;
             }
      }

    /* Validamos que las variables POST coincidan con la misma cantidad  de la columnas de la  base de datos */
       if($count == count($columns)){
           echo "Coincide";
          


            /* Solicitamos respuesta del controlador pra crear datos en cualquier tabla */
         $response = new PostController();
         $response -> postData(explode("?", $routesArray[1])[0], $_POST);
       }

   
     }
  }

  /* PETICIONES PUT */

  if (
    count($routesArray) == 1 && isset($_SERVER["REQUEST_METHOD"])
    && $_SERVER["REQUEST_METHOD"] == "PUT"
  ) {
    $json = array(
      'status' => 200,
      'result' => "PUT"
    );

    echo json_encode($json, http_response_code($json['status']));
    return;
  }

  /* PETICIONES DELETE */

  if (
    count($routesArray) == 1 && isset($_SERVER["REQUEST_METHOD"])
    && $_SERVER["REQUEST_METHOD"] == "DELETE"
  ) {
    $json = array(
      'status' => 200,
      'result' => "DELETE"
    );

    echo json_encode($json, http_response_code($json['status']));
    return;
  }
}