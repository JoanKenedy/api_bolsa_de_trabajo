<?php
$routesArray = explode("/", $_SERVER['REQUEST_URI']);
$routesArray = array_filter($routesArray);
/*Con esto traigo mi url $routesArray = $_SERVER['HTTP_HOST'];*/
if (count($routesArray) == 0) {
  $json = array(
    'status' => 404,
    "results" => "Not found"
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
      $response->getFilterData(explode("?", $routesArray[1])[0], $_GET["linkTo"], $_GET["equalTo"], $orderBy, $orderMode, $startAt, $endAt );


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

    foreach ($response as $key => $value) {
      array_push($columns, $value->item);
    }
    /*Quitamos el primer y ultimo indice del array */
    array_shift($columns);
    array_pop($columns);

    /* Recibimos los valores Post */
    if (isset($_POST)) {
      /* Validamos que las variables POST coincidan con los nombres  de la columnas de la  base de datos */
      $count = 0;

      foreach (array_keys($_POST) as $key => $value) {
        $count = array_search($value, $columns);
      }

      /* Validamos que las variables POST coincidan con la misma cantidad  de la columnas de la  base de datos */
      if ($count > 0) {

        if (isset($_GET["register"]) && $_GET["register"] == true) {
          $response = new PostController();
          $response->postRegister(explode("?", $routesArray[1])[0], $_POST);
        } else if (isset($_GET["login"]) && $_GET["login"] == true) {
          $response = new PostController();
          $response->postLogin(explode("?", $routesArray[1])[0], $_POST);
        } else if (isset($_GET["token"])) {


          /* Traemos al usuario de acurdo al token */

          $user = GetModel::getFilterData("usuarios", "token_user", $_GET["token"], null, null, null, null);
          if (!empty($user)) {

            $time = time();
            if ($user[0]->token_exp_user > $time) {
              /* Solicitamos respuesta del controlador pra crear datos en cualquier tabla */
              $response = new PostController();
              $response->postData(explode("?", $routesArray[1])[0], $_POST);
            } else {
              $json = array(
                'status' => 400,
                'results' => "Error: The token has expired",
              );

              echo json_encode($json, http_response_code($json['status']));
              return;
            }
          } else {
            $json = array(
              'status' => 400,
              'results' => "Error: The user is not authorized",
            );

            echo json_encode($json, http_response_code($json['status']));
            return;
          }
        } else {
          $json = array(
            'status' => 400,
            'results' => "Error: Authorization required",
          );

          echo json_encode($json, http_response_code($json['status']));
          return;
        }
      } else {
        $json = array(
          'status' => 400,
          'results' => "error"
        );

        echo json_encode($json, http_response_code($json['status']));
        return;
      }
    }
  }

  /* PETICIONES PUT */

  if (
    count($routesArray) == 1 && isset($_SERVER["REQUEST_METHOD"])
    && $_SERVER["REQUEST_METHOD"] == "PUT"
  ) {
    /* Preguntamos si viene el  id */
    if (isset($_GET["id"]) && isset($_GET["nameId"])) {
      /* Validamos que exista el id */
      $table = explode("?", $routesArray[1])[0];
      $linkTo = $_GET["nameId"];
      $equalTo = $_GET["id"];
      $orderBy = null;
      $orderMode = null;
      $startAt = null;
      $endAt = null;
     

      $response = PutController::getFilterData($table, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt);

      if ($response) {


        $data = array();
        parse_str(file_get_contents('php://input'), $data);

        $columns = array();
        /* Traemos el listado de columnas de la tabla a cambiar */
        $database = RoutesController::database();
        $response = PostController::getColumnsData(explode("?", $routesArray[1])[0], $database);

        foreach ($response as $key => $value) {
          array_push($columns, $value->item);
        }

        /*Quitamos el primer y ultimo indice del array */
        array_shift($columns);
        array_pop($columns);
        array_pop($columns);


        $count = 0;

        foreach (array_keys($data) as $key => $value) {
          $count = array_search($value, $columns);
        }

        if ($count > 0) {

          if (isset($_GET["token"])) {

              if($_GET['token'] == 'no'){

                
                     if(isset($_GET["except"])){
                      $num = 0;
                       foreach ($colums as $key => $value){
                        $num++;
                          if($value == $_GET["except"]){
                              /* Solicitamos respuesta del controlador para editar datos en cualquier tabla */
                      $response = new PutController();
                      $response->putData(explode("?", $routesArray[1])[0], $data, $_GET["id"], $_GET["nameId"]);
                       return;
                          }
                       }

                    if($num == count($colums)){
                          $json = array(
                  'status' => 400,
                  'results' => "The execption no existe ",
                );

                echo json_encode($json, http_response_code($json['status']));
                return;
                    }   

                       
                }else{
                      $json = array(
                  'status' => 400,
                  'results' => "There is no execption",
                );

                echo json_encode($json, http_response_code($json['status']));
                return;
                }
              }else{
            /* Traemos al usuario de acuerdo al token */

            $user = GetModel::getFilterData("usuarios", "token_user", $_GET["token"], null, null, null, null);
            if (!empty($user)) {

              $time = time();
              if ($user[0]->token_exp_user > $time) {
                /* Solicitamos respuesta del controlador pra crear datos en cualquier tabla */
                $response = new PutController();
                $response->putData(explode("?", $routesArray[1])[0], $data, $_GET["id"], $_GET["nameId"]);
              } else {
                $json = array(
                  'status' => 400,
                  'results' => "Error: The token has expired",
                );

                echo json_encode($json, http_response_code($json['status']));
                return;
              }
            } else {
              $json = array(
                'status' => 400,
                'results' => "Error: The user is not authorized",
              );

              echo json_encode($json, http_response_code($json['status']));
              return;
            }
          }
          } else {
            $json = array(
              'status' => 400,
              'results' => "Error: Authorization required",
            );

            echo json_encode($json, http_response_code($json['status']));
            return;
          }
        } else {
          $json = array(
            'status' => 400,
            'results' => "Los campos no coinciden con los de la base de datos"
          );

          echo json_encode($json, http_response_code($json['status']));
          return;
        }
      } else {

        $json = array(
          'status' => 400,
          'results' => "El id no se encontro en la base de datos"
        );

        echo json_encode($json, http_response_code($json['status']));
        return;
      }
    }
  }

  /* PETICIONES DELETE */

  if (
    count($routesArray) == 1 && isset($_SERVER["REQUEST_METHOD"])
    && $_SERVER["REQUEST_METHOD"] == "DELETE"
  ) {


    if (isset($_GET["id"]) && isset($_GET["nameId"])) {

      /* Validamos que exista el id */
      $table = explode("?", $routesArray[1])[0];
      $linkTo = $_GET["nameId"];
      $equalTo = $_GET["id"];
      $orderBy = null;
      $orderMode = null;
      $startAt = null;
      $endAt = null;

      $response = PutController::getFilterData($table, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt);

      if ($response) {
        if (isset($_GET["token"])) {


          /* Traemos al usuario de acurdo al token */

          $user = GetModel::getFilterData("usuarios", "token_user", $_GET["token"], null, null, null, null);
          if (!empty($user)) {

            $time = time();
            if ($user[0]->token_exp_user > $time) {
              /* Solicitamos respuesta del controlador pra crear datos en cualquier tabla */
              $response = new DeleteController();
              $response->deleteData(explode("?", $routesArray[1])[0], $_GET["id"], $_GET["nameId"]);
            } else {
              $json = array(
                'status' => 400,
                'results' => "Error: The token has expired",
              );

              echo json_encode($json, http_response_code($json['status']));
              return;
            }
          } else {
            $json = array(
              'status' => 400,
              'results' => "Error: The user is not authorized",
            );

            echo json_encode($json, http_response_code($json['status']));
            return;
          }
        } else {
          $json = array(
            'status' => 400,
            'results' => "Error: Authorization required",
          );

          echo json_encode($json, http_response_code($json['status']));
          return;
        }
      } else {
        $json = array(
          'status' => 400,
          'results' => "El id no se encontro en la base de datos"
        );

        echo json_encode($json, http_response_code($json['status']));
        return;
      }
    }
  }
}