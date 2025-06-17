<?php
class Noticias{
    private $conn;
    private $table_name = "noticias";   
    public $id;
    public $titulo;
    public $conteudo;
    public $data;
    public $autor_id;
    public $categoria_id;
    public $imagem;
    
    public function __construct($db){
        $this -> conn = $db;
    }   
        public function registrar($titulo, $noticia, $data, $autor, $imagem){
            $query = "INSERT INTO " . $this->table_name . " (titulo, noticia, data, autor, imagem) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$titulo, $noticia, $data, $autor, $imagem]);
            return $stmt;
        }

        public function criar($titulo, $noticia, $data, $autor, $imagem){
            return $this->registrar($titulo, $noticia, $data, $autor, $imagem);
        }
        
        public function ler() {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$this->id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row) {
                $this->titulo = $row['titulo'];
                $this->conteudo = $row['noticia'];
                $this->data = $row['data'];
                $this->autor_id = $row['autor'];
                $this->categoria_id = $row['categoria'];
                $this->imagem = $row['imagem'];
                return true;
            }
            return false;
        }
        public function lerPorId($id){
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        public function deletar($id){
            $query = "DELETE FROM " . $this->table_name. " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt;
        }

        public function lerPorAutor($autor_id){
            $query = "SELECT * FROM " . $this->table_name . " WHERE autor = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$autor_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function lerPorCategoria($categoria_id){
            $query = "SELECT * FROM " . $this->table_name . " WHERE categoria = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$categoria_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function atualizar($id, $titulo, $noticia, $categoria, $imagem){
            $query = "UPDATE " . $this->table_name . " SET titulo = ?, noticia = ?, categoria = ?, imagem = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$titulo, $noticia, $categoria, $imagem, $id]);
            return $stmt;
        }
}
?>