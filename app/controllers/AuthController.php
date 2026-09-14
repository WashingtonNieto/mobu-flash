<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../../config/Database.php';

class AuthController {
    private $db;
    private $usuarioModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuarioModel = new Usuario($this->db);
    }

    // Mostrar formulario de Login
    public function showLogin() {
        if (isset($_SESSION['usuario_id'])) {
            header("Location: index.php");
            exit();
        }
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        require_once __DIR__ . '/../views/auth/login.php';
    }

    // Procesar Inicio de Sesión
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (!empty($email) && !empty($password)) {
                $usuario = $this->usuarioModel->getByEmail($email);

                if ($usuario && password_verify($password, $usuario['password_hash'])) {
                    // Guardar sesión de usuario
                    $_SESSION['usuario_id'] = $usuario['id_usuario'];
                    $_SESSION['usuario_nombre'] = $usuario['nombre'];
                    header("Location: index.php");
                    exit();
                } else {
                    $_SESSION['error'] = "Credenciales incorrectas. Intenta de nuevo.";
                }
            } else {
                $_SESSION['error'] = "Por favor completa todos los campos.";
            }
        }
        header("Location: index.php?action=login");
        exit();
    }

    // Mostrar formulario de Registro
    public function showRegister() {
        if (isset($_SESSION['usuario_id'])) {
            header("Location: index.php");
            exit();
        }
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        require_once __DIR__ . '/../views/auth/register.php';
    }

    // Procesar Registro
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (!empty($nombre) && !empty($email) && !empty($password)) {
                if ($this->usuarioModel->emailExiste($email)) {
                    $_SESSION['error'] = "El correo electrónico ya está registrado.";
                    header("Location: index.php?action=register");
                    exit();
                }

                if ($this->usuarioModel->registrar($nombre, $email, $password)) {
                    // Iniciar sesión automáticamente tras registro
                    $usuario = $this->usuarioModel->getByEmail($email);
                    $_SESSION['usuario_id'] = $usuario['id_usuario'];
                    $_SESSION['usuario_nombre'] = $usuario['nombre'];
                    header("Location: index.php");
                    exit();
                } else {
                    $_SESSION['error'] = "Error al registrar el usuario.";
                }
            } else {
                $_SESSION['error'] = "Por favor completa todos los campos.";
            }
        }
        header("Location: index.php?action=register");
        exit();
    }

    // Cerrar Sesión
    public function logout() {
        session_start();
        session_destroy();
        header("Location: index.php?action=login");
        exit();
    }
}
?>