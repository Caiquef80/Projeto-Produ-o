<?php 

header("Cache-Control: no-cache, no-store, must-revalidate"); //No cache significa que precisamos validar com o servidor se pode mostrar a versão antiga
//no-store e mais radical instrui o navegador a não armazenar nada em cache
//must-revalidate obriga o navegador a não usar conteudo expirado
header("Pragma: no-cache"); // Instruir o navegador a não usar cache sem autorização do servidor
header("Expires: 0");//aqui definimos uma data de expiração para nossa pagina, como foi colocado 0 essa data sempre esta expirada
include_once __DIR__ . '/../Core/Container.php';
include_once __DIR__ . '/../App/Controller/AuthController.php';
include_once __DIR__ . '/../App/Model/Session.php';

$controller = new AuthController(
    Container::getDatabase(),
    Container::getSession(),
    Container::getTokenService(),
    Container::getUser()
);

if(!isset($_SESSION['token'])){
    session_unset();
    session_destroy();
    Header('Location: ../App/View/Login.php');
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
            Header('Location: ../App/View/Login.php');
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
    <title>Pagina Inicial</title>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="nav">
                <h1>LogoTech</h1>
                <nav>
                    <ul>
                        <li><a href="../App/View/Profile.php">Meu perfil</a></li>
                        <li><a href="logout.php">Sair</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <script>window.onpageshow = function(event){
        //window.onpagesshow é um evento disparado sempre q uma pagina e inicializada / exibid
        //event.persisted me retorna um boolean se a pagina esta sendo exibida atraves de um cache de navegador
        //window.location.reload e para forçar a pagina a recarregar para que quando o usuario tentar voltar apos um logout ele nao conseguir.
        if(event.persisted){
            window.location.reload();
        }
    }</script>
</body>
</html>