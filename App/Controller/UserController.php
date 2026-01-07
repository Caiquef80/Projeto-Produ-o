<?php 
include_once __DIR__ ."/../Model/Session.php";
include_once __DIR__ . "/../Services/AuthService.php";
include_once __DIR__ . "/../Services/TokenService.php";
include_once __DIR__ . "/../../Config/Database.php";
include_once __DIR__ . "/../Model/User.php";
include_once __DIR__ . "/../../Core/Container.php";
class UserController{
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

    public function register(array $data):array{
        try {
            $cleanName = AuthService::sanitizeString($data['name']); 
            $cleanEmail = AuthService::sanitizeEmail($data['email']);
            $normalizeEmail = AuthService::normalizeEmail($cleanEmail);
            if(!AuthService::isValidEmail($normalizeEmail) || !AuthService::isValidPassword($data['password'])){
                return ['success' => false , 'message' => "Email ou senha invalidos."];
            }
            $this->user->createUser($cleanName , $normalizeEmail, $data['password']);
            return ['success' => true , 'message' => "Usuário registrado com sucesso."];
        } catch (Exception $e) {
            return['success' => false , 'message' => "Erro ao registrar usuário " . $e->getMessage()];
        }
    }

    public function updateName(string $name , int $id):array{
        try {
            $cleanName = AuthService::sanitizeString($name);
            if(empty($cleanName)){
                return ['success' => false , 'message' => 'Nome invalido'];
            }
            $this->user->updateName($cleanName , $id);
            return ['success' => true , 'message' => 'Nome atualizado com sucesso.'];
        } catch (Exception $e) {
            return ['success' => false , 'message' => 'Erro ao atualizar o nome do usuário ' . $e->getMessage()];
        }
        }

    public function updateEmail(string $email , int $id):array{
        try {
            $cleanEmail = AuthService::sanitizeEmail($email);
            $normalizeEmail = AuthService::normalizeEmail($cleanEmail);
            if(empty($normalizeEmail)){
                return ['success' => false , 'message' => 'Email não preenchido.'];
            }
            if(!AuthService::isValidEmail($normalizeEmail)){
                return['success' => false , 'message' => 'Email invalido.'];
            }
            $this->user->updateEmail($normalizeEmail,$id);
            return [
                'success' => true , 
                'message' => 'Email atualizado com sucesso.'
            ];       
        } catch (Exception $e) {
           return['success' => false , 'message' => 'Erro ao atualizar o email do usuário ' . $e->getMessage()];
        }
    }

    public function updatePassword(string $password , int $id):array{
        try {
            if(empty($password)){
                return['success' => false , 'message' => 'Senha não foi preenchida'];
            }
            $this->user->updatePassword($password , $id);
            return [
                'success' => true,
                'message' => 'Senha atualizada com sucesso.'
            ];
        } catch (Exception $e) {
            return['success' => false , 'message' => "Erro ao atualizar senha do usuário " . $e->getMessage()];
        }
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $controller = new UserController(Container::getDatabase() , Container::getSession() , Container::getTokenService() , Container::getUser());
    $mensagem = $controller->register($_POST);

    include "../View/Register.php";
}
?>