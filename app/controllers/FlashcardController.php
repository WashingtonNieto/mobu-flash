<?php
require_once __DIR__ . '/../models/Flashcard.php';
require_once __DIR__ . '/../models/Carpeta.php';
require_once __DIR__ . '/../../config/Database.php';

class FlashcardController {
    private $db;
    private $flashcardModel;
    private $carpetaModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->flashcardModel = new Flashcard($this->db);
        $this->carpetaModel = new Carpeta($this->db);
    }

    // [READ & INDEX] Mostrar carpetas y las tarjetas de la carpeta seleccionada
    public function index() {
        $id_usuario = $_SESSION['usuario_id'] ?? 1;
        
        $carpetas = $this->carpetaModel->getAllWithCardCount($id_usuario);

        $id_carpeta = isset($_GET['folder']) 
            ? intval($_GET['folder']) 
            : (!empty($carpetas) ? $carpetas[0]['id_carpeta'] : 0);

        $flashcards = ($id_carpeta > 0) ? $this->flashcardModel->getByFolder($id_carpeta) : [];
        
        // ❌ LÍNEA 38 CON ERROR:
        // require_once __DIR__ . '/../app/views/flashcards/index.php';

        // ✅ REEMPLAZA LA LÍNEA 38 POR ESTA:
        require_once __DIR__ . '/../views/flashcards/index.php';
    }

    // [CREATE] Guardar nueva tarjeta
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_carpeta = intval($_POST['id_carpeta'] ?? 0);
            $pregunta = trim($_POST['pregunta'] ?? '');
            $respuesta = trim($_POST['respuesta'] ?? '');
            $dificultad = $_POST['dificultad'] ?? 'medio';

            if ($id_carpeta > 0 && !empty($pregunta) && !empty($respuesta)) {
                $this->flashcardModel->create($id_carpeta, $pregunta, $respuesta, $dificultad);
            }
            header("Location: index.php?folder=" . $id_carpeta);
            exit();
        }
    }

    // [UPDATE] Actualizar tarjeta existente
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_flashcard = intval($_POST['id_flashcard'] ?? 0);
            $id_carpeta = intval($_POST['id_carpeta'] ?? 0);
            $pregunta = trim($_POST['pregunta'] ?? '');
            $respuesta = trim($_POST['respuesta'] ?? '');
            $dificultad = $_POST['dificultad'] ?? 'medio';

            if ($id_flashcard > 0 && !empty($pregunta) && !empty($respuesta)) {
                $this->flashcardModel->update($id_flashcard, $pregunta, $respuesta, $dificultad);
            }
            header("Location: index.php?folder=" . $id_carpeta);
            exit();
        }
    }

    // [DELETE] Eliminar tarjeta
    public function destroy() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_flashcard = intval($_POST['id_flashcard'] ?? 0);
            $id_carpeta = intval($_POST['id_carpeta'] ?? 0);

            if ($id_flashcard > 0) {
                $this->flashcardModel->delete($id_flashcard);
            }
            header("Location: index.php?folder=" . $id_carpeta);
            exit();
        }
    }
}
?>