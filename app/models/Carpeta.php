<?php
class Carpeta {
    private $conn;
    private $table = "carpetas";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todas las carpetas con el conteo de flashcards
    public function getAllWithCardCount($id_usuario = 1) {
        $query = "SELECT 
                    c.id_carpeta, 
                    c.nombre, 
                    c.descripcion, 
                    c.color_identificador, 
                    COUNT(f.id_flashcard) AS total_tarjetas
                  FROM " . $this->table . " c
                  LEFT JOIN flashcards f ON c.id_carpeta = f.id_carpeta
                  WHERE c.id_usuario = :id_usuario
                  GROUP BY c.id_carpeta, c.nombre, c.descripcion, c.color_identificador
                  ORDER BY c.fecha_creacion DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener una carpeta específica por ID
    public function getById($id_carpeta) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_carpeta = :id_carpeta LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_carpeta', $id_carpeta, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear una nueva carpeta
    public function create($id_usuario, $nombre, $descripcion, $color) {
        $query = "INSERT INTO " . $this->table . " (id_usuario, nombre, descripcion, color_identificador) 
                  VALUES (:id_usuario, :nombre, :descripcion, :color)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':color', $color);

        return $stmt->execute();
    }

    // Actualizar datos de una carpeta existente
    public function update($id_carpeta, $nombre, $descripcion, $color) {
        $query = "UPDATE " . $this->table . " 
                  SET nombre = :nombre, descripcion = :descripcion, color_identificador = :color 
                  WHERE id_carpeta = :id_carpeta";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_carpeta', $id_carpeta, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':color', $color);

        return $stmt->execute();
    }

    // Eliminar carpeta (la BBDD ejecuta ON DELETE CASCADE en las flashcards)
    public function delete($id_carpeta) {
        $query = "DELETE FROM " . $this->table . " WHERE id_carpeta = :id_carpeta";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_carpeta', $id_carpeta, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>