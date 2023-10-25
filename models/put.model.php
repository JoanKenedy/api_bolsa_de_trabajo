<?php

require_once "connection.php";

class PutModel
{

    static public function putData($table, $data, $id, $nameId)
    {
        $stmt = Connection::connect()->prepare("UPDATE $table SET nombre = :nombre, logo_empresa = :logo_empresa, apellido = :apellido, email = :email, telefono_contacto = :telefono_contacto, telefono_empresa = :telefono_empresa, name_empresa = :name_empresa, num_trabajadores = :num_trabajadores WHERE $nameId = :$nameId ");

        $stmt->bindParam(":nombre", $data["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(":logo_empresa", $data["logo_empresa"], PDO::PARAM_STR);
        $stmt->bindParam(":apellido", $data["apellido"], PDO::PARAM_STR);
        $stmt->bindParam(":email", $data["email"], PDO::PARAM_STR);
        $stmt->bindParam(":telefono_contacto", $data["telefono_contacto"], PDO::PARAM_STR);
        $stmt->bindParam(":telefono_empresa", $data["telefono_empresa"], PDO::PARAM_STR);
        $stmt->bindParam(":name_empresa", $data["name_empresa"], PDO::PARAM_STR);
        $stmt->bindParam(":num_trabajadores", $data["num_trabajadores"], PDO::PARAM_STR);
        $stmt->bindParam(":" . $nameId, $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "The process was successful";
        } else {
            echo Connection::connect()->errorInfo();
        }
    }
}
