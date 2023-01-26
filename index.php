<?php


$request = $_SERVER["REQUEST_URI"];

switch ($request) {
    case '/':
        # home
        require "home.php";
        break;
    case '/configurar':
        # configurar
        require "configurar.php";
        break;
    case '/tesouraria':
        require "tesouraria.php";
        break;
    case '/wait':
        # code...
        require "wait.php";
        break;
    
    default:
        # code...
        http_response_code(404);
        require "404.php";
        break;
}


?>