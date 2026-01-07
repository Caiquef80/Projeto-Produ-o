<?php 
session_start();
include_once __DIR__ . "/../Model/Session.php";
include_once __DIR__ . "/../Services/AuthService.php";
include_once __DIR__ . "/../Services/TokenService.php";
include_once __DIR__ . "/../../Config/Database.php";
include_once __DIR__ . "/../Model/User.php";
include_once __DIR__ . "/../../Core/Container.php";
class AuthController{
        private Database $db;
        private Session $session;
        private TokenService $token;
        private User $user;

    public function __construct(Database $db , Session $session , TokenService $token  , User $user){
        $this->db = $db;
        $this->session = $session;
        $this->token = $token;
        $this->user = $user;
    }
        

    public function login(string $email , string $password): array{
        try {
            $cleanEmail = AuthService::sanitizeEmail($email);
            $normalizeEmail = AuthService::normalizeEmail($cleanEmail);
            $user = $this->user->findByEmail($normalizeEmail);
            if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
                return ["success" => false, "message" => "Email inválido"];
            }

            if(!$user){
                return ["success" => false , "message" => "Usuario não encontrado"];
            }
            if(!AuthService::passwordVerify($password , $user['senha_hash'])){
                return ["success" => false , "message" => "Email ou senha incorretos"];
            }
            $token = $this->token->generateToken();
            $_SESSION['token'] = $token['hash'];
            $this->session->createSession($user['id'] , $token['hash']);
            return["success" => true ,'message' => 'Login realizado com sucesso!' ,  "token" => $token['token']];
            
        }catch (Exception $e) {
            return['success' => false , 'message' => 'Erro ao tentar fazer login. ' . $e->getMessage()];
    }
    }

    public function logout(string $token):array{
        try {
            $this->session->endSession($token);
            return ['success' => true , 'message' =>  "Logout realizado com sucesso!"];
        }catch (Exception $e) {
            return ['success' => false , 'message' => "Erro ao tentar fazer logout. " . $e->getMessage()];
        }

    }
    public function refresh(int $user_id , string $Oldtoken): array{
        try {
            $newToken = $this->token->refreshToken($user_id , $Oldtoken);
            if(!$newToken){
                return["success" => false , 'message' => "Token invalido ou expirado."];
            }
            return["success" => true ,'message' => 'Novo token gerado com sucesso!', 'token' =>$newToken];
        }catch (Exception $e) {
            return["success" => false, 'message' => "Erro ao tentar renovar token na sessão. " . $e->getMessage()]; 
        }
        
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $authController = new AuthController(
        Container::getDatabase(),
        Container::getSession(),
        Container::getTokenService(),
        Container::getUser());
    $mensagem = $authController->login($_POST['email'] , $_POST['password']);
    $getUser = Container::getUser();
    $user = $getUser->findByEmail($_POST['email']);
    $_SESSION['id'] = $user['id'];
    $_SESSION['name'] = $user['nome'];
    if($mensagem['success']){
        if($user['tipo'] == 'user'){
            Header('Location: ../View/dashboard_user.php');
            exit();
        }elseif($user['tipo'] == 'admin'){
            Header('Location: ../View/dashboard_admin.php');
            exit();
        }   
    }else{
        include '../View/Login.php';
    }

}

?>