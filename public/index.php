<?php


// se hace la modificacion del index de 
// forma temporal


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/FlashcardController.php';
require_once __DIR__ . '/../app/controllers/CarpetaController.php';

$action = $_GET['action'] ?? '';

// Rutas Públicas (Auth)
if ($action === 'login') {
    (new AuthController())->showLogin();
    exit();
} elseif ($action === 'do_login') {
    (new AuthController())->login();
    exit();
} elseif ($action === 'register') {
    (new AuthController())->showRegister();
    exit();
} elseif ($action === 'do_register') {
    (new AuthController())->register();
    exit();
} elseif ($action === 'logout') {
    (new AuthController())->logout();
    exit();
}

// Control de Sesión Obligatorio
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php?action=login");
    exit();
}

// Rutas Privadas CRUD (Carpetas & Flashcards)
switch ($action) {
    // CRUD Carpetas
    case 'store_folder':
        (new CarpetaController())->store();
        break;

    case 'update_folder':
        (new CarpetaController())->update();
        break;

    case 'delete_folder':
        (new CarpetaController())->destroy();
        break;

    // CRUD Flashcards
    case 'store_card':
        (new FlashcardController())->store();
        break;

    case 'update_card':
        (new FlashcardController())->update();
        break;

    case 'delete_card':
        (new FlashcardController())->destroy();
        break;

    default:
        (new FlashcardController())->index();
        break;
}
?>