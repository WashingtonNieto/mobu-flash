<?php
require_once __DIR__ . '/../models/Carpeta.php';
require_once __DIR__ . '/../../config/Database.php';

class CarpetaController {
    private $db;
    private $carpetaModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $database = new Database();
        $this->db = $database->getConnection();
        $this->carpetaModel = new Carpeta($this->db);
    }

    // Guardar nueva carpeta (una sola declaración de store)
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre_carpeta'] ?? '');
            $descripcion = trim($_POST['descripcion_carpeta'] ?? '');
            $color = $_POST['color_carpeta'] ?? '#4F46E5';
            
            // Asigna el ID real del usuario conectado (o 1 por defecto)
            $id_usuario = $_SESSION['usuario_id'] ?? 1;

            if (!empty($nombre)) {
                $this->carpetaModel->create($id_usuario, $nombre, $descripcion, $color);
            }
        }
        header("Location: index.php");
        exit();
    }

    // Actualizar carpeta existente
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_carpeta = intval($_POST['id_carpeta'] ?? 0);
            $nombre = trim($_POST['nombre_carpeta'] ?? '');
            $descripcion = trim($_POST['descripcion_carpeta'] ?? '');
            $color = $_POST['color_carpeta'] ?? '#4F46E5';

            if ($id_carpeta > 0 && !empty($nombre)) {
                $this->carpetaModel->update($id_carpeta, $nombre, $descripcion, $color);
                header("Location: index.php?folder=" . $id_carpeta);
                exit();
            }
        }
        header("Location: index.php");
        exit();
    }

    // Eliminar carpeta (en cascada)
    public function destroy() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['id'])) {
            $id_carpeta = intval($_POST['id_carpeta'] ?? $_GET['id'] ?? 0);

            if ($id_carpeta > 0) {
                $this->carpetaModel->delete($id_carpeta);
            }
        }
        header("Location: index.php");
        exit();
    }
}
?>