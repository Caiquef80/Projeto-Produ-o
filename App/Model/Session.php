<?php 
include_once __DIR__ ."/../../Config/Database.php";
include_once __DIR__ . "/../Services/TokenService.php";
include_once __DIR__ . "/LogAccess.php";
class Session{
    private Database $db;
    private LogAccess $logAccess;
    public function __construct(Database $db , LogAccess $logAccess){
        $this->db = $db;
        $this->logAccess = $logAccess;
    }

    public function createSession(int $id , string $token):bool{
        try {
            $this->removeExpiredSessions();
            $sql = "INSERT INTO sessions (user_id , token , data_expira) VALUES ( ?, ? , DATE_ADD(NOW(), INTERVAL 1 HOUR))";
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute([$id , $token]);
            $this->logAccess->lastLogin($id);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            throw new Exception("Erro ao criar sessão " . $e->getMessage());
        }
    }
    public function getSession(string $token): ?array{
        try{
            $sql = "SELECT * FROM sessions WHERE token = ? AND data_expira > NOW()";
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute([$token]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result === false ? null : $result;
        }catch(Exception $e){
            throw new Exception("Erro ao encontrar sessão " .$e->getMessage());
        }
    }

    public function endSession(string $token):bool{
        try {
            $sql = "DELETE FROM sessions WHERE token = ?" ;
            $stmt= $this->db->getConnection()->prepare($sql);
            $stmt->execute([$token]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            throw new Exception("Erro ao finalizar sessão" . $e->getMessage());
        }
    }

    public function tokenExists(string $token):bool{
        $sql = "SELECT 1 FROM sessions WHERE token = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$token]);
        return $stmt->fetchColumn() !== false;
    }

    public function refreshSession(string $token):bool{
        try {
            if($this->tokenExists($token)){
                $sql = "UPDATE sessions SET data_expira = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE token = ?";
                $stmt = $this->db->getConnection()->prepare($sql);
                $stmt->execute([$token]);
                return $stmt->rowCount() > 0;
            }else{
                return false;
            }
        } catch (Exception $e) {
            throw new Exception("Erro no token " . $e->getMessage());
        }
    }
    public function removeExpiredSessions():bool{
        try{
            $sql = "DELETE FROM sessions WHERE data_expira < NOW()";
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        }catch(Exception $e){ 
            throw new Exception("Erro ao remover Token ". $e->getMessage());
        }
    }
    public function logoutUser(int $user_id):bool{
        try {
            $sql = "DELETE FROM sessions WHERE user_id = ?";
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute([$user_id]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            throw new Exception("Erro ao deletar sessões de usuário " . $e->getMessage());
        }
    }
 
}


?>