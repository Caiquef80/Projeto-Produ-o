<?php 

header("Cache-Control: no-cache, no-store, must-revalidate"); //No cache significa que precisamos validar com o servidor se pode mostrar a versão antiga
//no-store e mais radical instrui o navegador a não armazenar nada em cache
//must-revalidate obriga o navegador a não usar conteudo expirado
header("Pragma: no-cache"); // Instruir o navegador a não usar cache sem autorização do servidor
header("Expires: 0");//aqui definimos uma data de expiração para nossa pagina, como foi colocado 0 essa data sempre esta expirada
include_once __DIR__ . '/../../Core/Container.php';
include_once __DIR__ . '/../Controller/AuthController.php';
include_once __DIR__ . '/../Model/Session.php';

$controller = new AuthController(
    Container::getDatabase(),
    Container::getSession(),
    Container::getTokenService(),
    Container::getUser()
);

if(!isset($_SESSION['token'])){
    session_unset();
    session_destroy();
    Header('Location: Login.php');
    exit();
}
$session = Container::getSession();
$getSession = $session->getSession($_SESSION['token']);
$inatividade = 1800;

if(!isset($_SESSION['last_activity'])){
    $_SESSION['last_activity'] = time();
}   
try {
    if($getSession){
        $timestampSession = strtotime($getSession['criado']);
        if(time() - $_SESSION['last_activity'] > $inatividade){
            $controller->logout($_SESSION['token']);
            session_unset();
            session_destroy();
            Header('Location: Login.php');
            exit();
        }else{
            $_SESSION['last_activity'] = time();
        }
        if($timestampSession - time() >= 3600){
            $controller->refresh($_SESSION['id'] , $_SESSION['token']);
                
        }
    } 
}catch(Exception $e) {
    throw new Exception("Token não encontrado " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../../Public/css/style.css">

</head>
<body>
    <div class="container">
        <header>
            <div class="header">
                <div class="header-content">
                    <h1>LogTech</h1>
                     <div class="nav">
                    <nav>
                        <ul>
                            <li><a href="">Inicio</a></li>
                            <li><a href="">Suporte</a></li>
                            <li><a href="../../Public/logout.php">Sair</a></li>
                        </ul>
                    </nav>
                </div>
                </div>
            </div>
        </header>
        <div class="main">
            <div class="main-header">
                <h2>Seja bem vindo, <?php echo $_SESSION['name']?></h2>
                <div class="main-content">
                    <h3>Seus acessos como administrador são:</h3>
                    <ul>
                        <li>Listar todos os usuários</li>
                        <li>Deletar usuários</li>
                        <li>Alterar informações</li>
                    </ul>
                    <button id="btn-list">Listar usuários</button>
                </div>
            </div>
        </div>
    </div>
    <script >
        window.onpageshow = function(event){
            if(event.persisted){
                window.location.reload();
            }
        }
    </script>
</body>
</html>