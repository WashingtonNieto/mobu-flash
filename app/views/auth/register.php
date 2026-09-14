<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobu Flash - Registro</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body style="display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: var(--bg-main);">

    <div class="modal" style="width: 100%; max-width: 400px; box-shadow: var(--shadow-lg);">
        <div class="brand" style="justify-content: center; margin-bottom: 20px;">
            <div class="brand-icon">M</div>
            <div class="brand-title">Mobu Flash</div>
        </div>

        <h2 style="text-align: center; font-size: 20px; margin-bottom: 20px;">Crear Cuenta</h2>

        <?php if (!empty($error)): ?>
            <div style="background-color: #FEE2E2; color: var(--danger); padding: 10px; border-radius: var(--radius-md); font-size: 13px; margin-bottom: 16px; text-align: center;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="index.php?action=do_register" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre Completo</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ej: Juan Pérez" required>
            </div>
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" placeholder="tu@email.com" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Crear Cuenta</button>
        </form>

        <p style="text-align: center; margin-top: 20px; font-size: 13px; color: var(--text-muted);">
            ¿Ya tienes una cuenta? <a href="index.php?action=login" style="color: var(--primary); font-weight: 600; text-decoration: none;">Inicia sesión</a>
        </p>
    </div>

</body>
</html>