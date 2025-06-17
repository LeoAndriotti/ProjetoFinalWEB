<?php
class Usuario{
    private $conn;
    private $table_name = "usuarios";   
    
    public function __construct($db){
        $this -> conn = $db;
    }   
        public function registrar($nome, $sexo, $fone, $email, $senha){
            $query = "INSERT INTO " . $this->table_name . " (nome, sexo, fone, email, senha) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $hashed_password = password_hash($senha, PASSWORD_BCRYPT);
            $stmt->execute([$nome, $sexo, $fone, $email, $hashed_password]);
            return $stmt;
        }

        public function login($email, $senha){
            $query = "SELECT * FROM " . $this->table_name . " WHERE email = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if($usuario && password_verify($senha, $usuario['senha'])){
                return $usuario;
            } 
            return false;
        }

        public function criar($nome, $sexo, $fone, $email, $senha){
            return $this->registrar($nome, $sexo, $fone, $email, $senha);
        }
        
        public function ler(){
            $query = "SELECT * FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        public function lerPorId($id){
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        public function atualizar($id, $nome, $sexo, $fone, $email){
            $query = "UPDATE " . $this->table_name . " SET nome = ?, sexo = ?, fone = ?, email = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$nome, $sexo, $fone, $email, $id]);
            return $stmt; 
        }
        public function deletar($id){
            $query = "DELETE FROM " . $this->table_name. " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt;
        }

        public function buscarPorEmail($email) {
            $query = "SELECT * FROM usuarios WHERE email = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
}
?>