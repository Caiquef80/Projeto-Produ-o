<?php 
include_once __DIR__ . "/../../Config/Database.php";
class AuthService {
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }
 
    public static function isValidPassword(string $password): bool{
        return (strlen($password) >= 8 && strlen($password) <= 64 
        && preg_match('/[A-Z]/', $password) 
        && preg_match('/[0-9]/', $password));
    }
    public static function isValidEmail(string $email):bool{
    return (
        filter_var($email, FILTER_VALIDATE_EMAIL) &&
        !empty(trim($email))
        
    );
    }

    public static function hashPassword(string $password):string{
        return password_hash($password , PASSWORD_DEFAULT);
    }
    public static function passwordVerify(string $password , string $hash):bool{
        return password_verify($password , $hash);
    }

    public static function normalizeEmail(string $email):string{
        return strtolower(trim($email));
    }
    public static function sanitizeEmail(string $email): string{
        return filter_var($email , FILTER_SANITIZE_EMAIL);
    }
    public static function sanitizeString(string $string):string{
        return htmlspecialchars(trim($string) , ENT_QUOTES , 'UTF-8');
    }
}

?>
