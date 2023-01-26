<?php


$request = $_SERVER["REQUEST_URI"];

switch ($request) {
    case '/':
        require "home.php";
        break;
    case '/configurar':
        require "views/configurar.php";
        break;
    case '/tesouraria':
        require "views/tesouraria.php";
        break;
    case '/wait':
        require "views/wait.php";
        break;
    case '/view':
        require "views/view.php";
        break;
    case '/whatsapp':
        require "views/whatsapp.php";
        break;
    
    
    //403 Forbidden
    case '/files':
        http_response_code(403);
        require "views/403.php";
        break;
    case '/ajax':
        http_response_code(403);
        require "views/403.php";
        break;
    case '/css':
            http_response_code(403);
            require "views/403.php";
            break;
    case '/js':
        http_response_code(403);
        require "views/403.php";
        break;
    case '/views':
        http_response_code(403);
        require "views/403.php";
        break;
    default:
        http_response_code(404);
        require "views/404.php";
        break;
}


?>