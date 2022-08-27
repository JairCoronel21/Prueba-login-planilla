<?php

        require 'database/conexion.php';
        require 'funciones/funcs.php';


        if(isset($_GET["id"]) and isset($_GET["val"])) {
          
            $idUsuario = $_GET["id"];
            $token = $_GET["token"];


            $mensaje = validaIdToken($idUsuario, $token);
            

            

        }





















?>