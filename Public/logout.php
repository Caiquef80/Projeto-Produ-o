<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
include_once __DIR__ . "/../Core/Container.php";
include_once __DIR__ . "/../App/Controller/AuthController.php";

$controller = new AuthController(
    Container::getDatabase(),
    Container::getSession(),
    Container::getTokenService(),
    Container::getUser()
);

try {
    if(isset($_SESSION['token'])){
        $endSession = $controller->logout($_SESSION['token']);
    }
    session_unset();
    session_destroy();
    header('location:../App/View/Login.php');
   

} catch (Exception $e){
    throw new Exception("Erro ao encerrar sessão " . $e->getMessage());
}

?>