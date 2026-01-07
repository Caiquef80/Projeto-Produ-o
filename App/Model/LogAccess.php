<?php
include_once __DIR__ . "/../../Config/Database.php";

class LogAccess{
    private Database $db;
    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function lastLogin(int $id): bool{
    try {
        if($this->hasLastLoginRecord($id)){
            $this->updateLastLogin($id);
            return true;
        }else{
            $sql = "INSERT INTO log_acesso (user_id) VALUES (?)";
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->rowCount() > 0;
        }
        
    } catch (Exception $e) {
        throw new Exception("Erro ao registrar acesso do usuário"  .$e->getMessage());
    }
    }

    private function hasLastLoginRecord(int $id): bool{
        $sql = "SELECT * FROM log_acesso WHERE user_id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$id]);
        return (bool) $stmt->fetch();
        
    }

    private function updateLastLogin(int $id): bool{
        try {
            $sql = "UPDATE log_acesso SET ultimo_login = NOW() WHERE user_id = ?";
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            throw new Exception("Não foi possivel atualizar o ultimo login " . $e->getMessage());
        }
    }
}


?>