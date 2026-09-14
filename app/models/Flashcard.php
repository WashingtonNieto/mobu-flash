<?php
class Flashcard {
    private $conn;
    private $table = "flashcards";

    public function __construct($db) {
        $this->conn = $db;
    }

    // [READ] Obtener todas las tarjetas de una carpeta específica
    public function getByFolder($id_carpeta) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE id_carpeta = :id_carpeta 
                  ORDER BY FIELD(nivel_dificultad, 'dificil', 'medio', 'facil'), fecha_creacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_carpeta', $id_carpeta, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // [READ] Obtener una tarjeta individual por su ID
    public function getById($id_flashcard) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_flashcard = :id_flashcard LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_flashcard', $id_flashcard, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // [CREATE] Crear una nueva flashcard
    public function create($id_carpeta, $pregunta, $respuesta, $nivel_dificultad) {
        $query = "INSERT INTO " . $this->table . " (id_carpeta, pregunta, respuesta, nivel_dificultad) 
                  VALUES (:id_carpeta, :pregunta, :respuesta, :nivel_dificultad)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_carpeta', $id_carpeta, PDO::PARAM_INT);
        $stmt->bindParam(':pregunta', $pregunta);
        $stmt->bindParam(':respuesta', $respuesta);
        $stmt->bindParam(':nivel_dificultad', $nivel_dificultad);

        return $stmt->execute();
    }

    // [UPDATE] Actualizar contenido de una flashcard
    public function update($id_flashcard, $pregunta, $respuesta, $nivel_dificultad, $estado_dominio = 'por_repasar') {
        $query = "UPDATE " . $this->table . " 
                  SET pregunta = :pregunta, 
                      respuesta = :respuesta, 
                      nivel_dificultad = :nivel_dificultad,
                      estado_dominio = :estado_dominio
                  WHERE id_flashcard = :id_flashcard";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_flashcard', $id_flashcard, PDO::PARAM_INT);
        $stmt->bindParam(':pregunta', $pregunta);
        $stmt->bindParam(':respuesta', $respuesta);
        $stmt->bindParam(':nivel_dificultad', $nivel_dificultad);
        $stmt->bindParam(':estado_dominio', $estado_dominio);

        return $stmt->execute();
    }

    // [DELETE] Eliminar una flashcard
    public function delete($id_flashcard) {
        $query = "DELETE FROM " . $this->table . " WHERE id_flashcard = :id_flashcard";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_flashcard', $id_flashcard, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>