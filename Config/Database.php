<?php 
require_once __DIR__ . '/env.php';
class Database {
    private string $host;
    private string $dbname;
    private string $user;
    private string $password;
    private ?PDO $conn = null;

    public function __construct(){
        $this->host = $_ENV['DB_HOST'];
        $this->dbname = $_ENV['DB_NAME'];
        $this->user = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASS'];
    }

    public function getConnection(){
        if($this->conn !== null){
            return $this->conn;
        }
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            $this->conn = new PDO($dsn , $this->user, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $e) {
            throw new Exception("Erro ao se conectar com o banco " . $e->getMessage());
        }
    }
}


?>