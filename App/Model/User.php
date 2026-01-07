<?php 
include_once __DIR__ ."/../../Config/Database.php";
include_once __DIR__ ."/../Services/AuthService.php";
class User{
    private Database $db;
    public function __construct(Database $db){
        $this->db = $db;
    }
    private function searchAll($sql , $params){
        try{
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        }catch(Exception $e){
            throw new Exception("Erro " . $e->getMessage());
        }
    }
    private function searchUnique($sql , $params){
        try {
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error" . $e->getMessage());
        }
    }
    public function findById($id){
        return $this->searchUnique("SELECT * FROM users WHERE id = ?" , [$id]);
    }
    public function findByEmail($email){
        if(AuthService::isValidEmail($email) == true){
            return $this->searchUnique("SELECT * FROM users WHERE email = ?" , [$email]);
        }else{
            throw new Exception("Email inválido!");
        }
    }
    public function findByName($name){
        return $this->searchAll("SELECT * FROM users WHERE nome = ?" , [$name]);
    }
    private function existingUser($email){
        
        if(AuthService::isValidEmail($email) == true){
            return $this->searchUnique("SELECT * FROM users WHERE email = ?" , [$email]) != null ? true : false;
        }else{
            throw new Exception("Email inválido!");
        }   

    }

    public function createUser($name , $email , $password){
        try {
            if (trim($name) == "" || 
                !AuthService::isValidEmail($email) || 
                $this->existingUser($email) || 
                !AuthService::isValidPassword($password)) {
                return false; 
            }

                $password_hash = password_hash($password , PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (nome , email , senha_hash) VALUES (? , ?, ?)";
                $stmt = $this->db->getConnection()->prepare($sql);
                $stmt->execute([$name , $email , $password_hash]);
                return $stmt->rowCount() > 0;           
        } catch (Exception $e) {
            throw new Exception("Erro inesperado ao criar usuario" . $e->getMessage());
        }
    }
    public function deleteUser($id){
        try {
            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            throw new Exception("Erro ao deletar o usuário " . $e->getMessage());
        }
    }
    private function updateUser($sql , $params){
        try{
            $stmt= $this->db->getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount() > 0;

        }catch(Exception $e){
            throw new Exception("Erro ao atualizar usuário " . $e->getMessage());
        }

    }
    public function updateName($name , $id){
        return $this->updateUser("UPDATE users set nome = ? WHERE id = ?" , [$name , $id]);
    }
    public function updateEmail($email , $id){
        if(AuthService::isValidEmail($email) == true){
            return $this->updateUser("UPDATE users set email = ? WHERE id = ?" , [$email , $id]);
        }else{
            throw new Exception("Email Invalido!");
        }
    }
    public function updatePassword($password , $id){
        if(AuthService::isValidPassword($password) == true){
            $password_hash = password_hash($password , PASSWORD_DEFAULT);
            return $this->updateUser("UPDATE users set senha_hash = ? WHERE id = ?" , [$password_hash , $id]);

        }else{
            throw new Exception("Senha invalida!");
        }
    }

    public function listLastUsers($limit = 10){
        $limit = min((int)$limit , 100);
        $sql = "SELECT * FROM users ORDER BY id DESC LIMIT $limit";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listAllUsers(){
        $sql = "SELECT * FROM users ORDER BY data_criacao DESC";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>