<?php 
include_once  __DIR__ . "/../../Config/Database.php";
include_once  __DIR__ .  "/../Model/Session.php";
require_once __DIR__ . "/../../Config/env.php";
class TokenService{
    private Database $db;
    private  Session $session;
    private static string $secretToken;
    public function __construct(Database $db , Session $session){
        $this->db = $db;
        $this->session = $session;
        self::$secretToken = $_ENV['APP_SECRET'];
    }


    public  function generateToken():array{
        try{
            $bytes = random_bytes(64);
            $token = bin2hex($bytes);
            $tokenHash = hash_hmac('sha256' , $token , self::$secretToken);
            return [
                "token" => $token,
                "hash" => $tokenHash
            ];

        }catch(Exception $e){
            throw new Exception ("Erro ao gerar Token " . $e->getMessage());
        }
    }
    public function refreshToken($user_id , $token) : array{
        $newToken = $this->generateToken();
        $this->session->endSession($token);
        $this->session->createSession($user_id , $newToken['hash']);
        return $newToken;
    }

    public function isValidToken(string $token):bool{
    try {
        return $this->session->getSession($token) !== null;
        
    }catch (Exception $e) {
        throw new Exception("Erro ao validar token" . $e->getMessage());
    }
    }

}




?>
