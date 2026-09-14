<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobu Flash - Mis Flashcards</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-icon">M</div>
            <div class="brand-title">Mobu Flash</div>
        </div>

        <div class="section-title">Mis Materias / Áreas</div>
        <ul class="folder-list">
            <?php if (!empty($carpetas)): ?>
                <?php foreach ($carpetas as $f): ?>
                    <li class="folder-item <?php echo ($f['id_carpeta'] == $id_carpeta) ? 'active' : ''; ?>" 
                        onclick="window.location.href='index.php?folder=<?php echo $f['id_carpeta']; ?>'">
                        <span style="display: flex; align-items: center; gap: 8px;">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background-color: <?php echo htmlspecialchars($f['color_identificador']); ?>;"></span>
                            <?php echo htmlspecialchars($f['nombre']); ?>
                        </span>
                        <span class="folder-badge"><?php echo $f['total_tarjetas']; ?></span>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="font-size: 12px; color: var(--text-muted); padding: 8px 0;">No tienes materias creadas.</p>
            <?php endif; ?>
        </ul>

        <button class="btn-add-folder" onclick="openFolderModal()">+ Nueva Carpeta</button>

        <!-- USUARIO Y CERRAR SESIÓN -->
        <div style="margin-top: auto; padding-top: 16px; border-top: 1px solid var(--border-color);">
            <p style="font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 8px;">
                👤 <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario'); ?>
            </p>
            <a href="index.php?action=logout" style="color: var(--danger); font-size: 12px; text-decoration: none; font-weight: 600;">🚪 Cerrar Sesión</a>
        </div>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content">
        <header class="header">
            <div class="header-title">
                <?php 
                    $carpeta_activa = array_filter($carpetas, function($c) use ($id_carpeta) {
                        return $c['id_carpeta'] == $id_carpeta;
                    });
                    $carpeta_actual = reset($carpeta_activa);
                ?>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <h1><?php echo $carpeta_actual ? htmlspecialchars($carpeta_actual['nombre']) : 'Selecciona una Materia'; ?></h1>
                    
                    <?php if ($carpeta_actual): ?>
                        <button class="btn-secondary" style="padding: 4px 10px; font-size: 12px; cursor: pointer;" 
                                onclick="openEditFolderModal(
                                    '<?php echo $carpeta_actual['id_carpeta']; ?>', 
                                    '<?php echo htmlspecialchars($carpeta_actual['nombre'], ENT_QUOTES); ?>', 
                                    '<?php echo htmlspecialchars($carpeta_actual['descripcion'] ?? '', ENT_QUOTES); ?>', 
                                    '<?php echo $carpeta_actual['color_identificador']; ?>'
                                )">✏️ Editar Materia</button>

                        <form action="index.php?action=delete_folder" method="POST" style="display: inline;" 
                            onsubmit="return confirm('⚠️ ¿Estás seguro de eliminar esta materia? Se borrarán automáticamente TODAS las flashcards dentro de ella.');">
                            <input type="hidden" name="id_carpeta" value="<?php echo $carpeta_actual['id_carpeta']; ?>">
                            <button type="submit" class="btn-secondary" style="padding: 4px 10px; font-size: 12px; color: var(--danger); cursor: pointer;">🗑️ Eliminar Materia</button>
                        </form>
                    <?php endif; ?>
                </div>
                <p><?php echo $carpeta_actual ? htmlspecialchars($carpeta_actual['descripcion'] ?? 'Sin descripción') : ''; ?></p>
            </div>

            <!-- Botón visible siempre -->
            <?php if ($id_carpeta > 0): ?>
                <button class="btn-primary" onclick="openCardModal()">+ Crear Flashcard</button>
            <?php else: ?>
                <button class="btn-primary" onclick="alert('Primero debes crear una materia usando el botón \'+ Nueva Carpeta\' en el panel lateral.'); openFolderModal();">+ Crear Flashcard</button>
            <?php endif; ?>
        </header>

        <!-- BARRA DE PROGRESO -->
        <section class="progress-card">
            <div class="progress-header">
                <span>Progreso del Módulo</span>
                <span><?php echo count($flashcards); ?> Tarjetas registradas</span>
            </div>
            <div class="progress-bar-bg">
                <div class="progress-bar-fill" style="width: <?php echo count($flashcards) > 0 ? '70%' : '0%'; ?>;"></div>
            </div>
        </section>

        <!-- REJILLA DE FLASHCARDS (READ / UPDATE / DELETE) -->
        <section class="card-grid">
            <?php if (!empty($flashcards)): ?>
                <?php foreach ($flashcards as $card): ?>
                    <div class="flashcard-container" onclick="flipCard(this)">
                        <div class="flashcard">
                            
                            <!-- FRENTE DE LA TARJETA -->
                            <div class="card-face card-front">
                                <span class="card-tag tag-<?php echo htmlspecialchars($card['nivel_dificultad']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($card['nivel_dificultad'])); ?>
                                </span>
                                <div class="card-body"><?php echo htmlspecialchars($card['pregunta']); ?></div>
                                <div class="card-footer">
                                    <span>Haz clic para voltear</span>
                                    <span>👁 Front</span>
                                </div>
                            </div>

                            <!-- REVERSO DE LA TARJETA CON BOTONES DE ACCIÓN -->
                            <div class="card-face card-back">
                                <span class="card-tag tag-<?php echo htmlspecialchars($card['nivel_dificultad']); ?>">Respuesta</span>
                                <div class="card-body"><?php echo htmlspecialchars($card['respuesta']); ?></div>
                                
                                <div class="card-footer">
                                    <div onclick="event.stopPropagation();" style="display: flex; gap: 8px;">
                                        <!-- Botón Editar Tarjeta -->
                                        <button type="button" 
                                                style="border:none; background:none; cursor:pointer; font-size: 14px;" 
                                                title="Editar Tarjeta"
                                                onclick="event.stopPropagation(); openEditCardModal('<?php echo $card['id_flashcard']; ?>', '<?php echo htmlspecialchars($card['pregunta'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($card['respuesta'], ENT_QUOTES); ?>', '<?php echo $card['nivel_dificultad']; ?>')">
                                            ✏️
                                        </button>
                                        
                                        <!-- Botón Eliminar Tarjeta -->
                                        <form action="index.php?action=delete_card" method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar esta tarjeta?');">
                                            <input type="hidden" name="id_flashcard" value="<?php echo $card['id_flashcard']; ?>">
                                            <input type="hidden" name="id_carpeta" value="<?php echo $id_carpeta; ?>">
                                            <button type="submit" style="border:none; background:none; cursor:pointer; font-size: 14px;" title="Eliminar Tarjeta">🗑️</button>
                                        </form>
                                    </div>
                                    <span>✓ <?php echo htmlspecialchars($card['estado_dominio']); ?></span>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: var(--text-muted); grid-column: 1 / -1; text-align: center; padding: 40px 0;">
                    No hay flashcards registradas en este módulo. Haz clic en <strong>"+ Crear Flashcard"</strong> para agregar una.
                </p>
            <?php endif; ?>
        </section>
    </main>

    <!-- MODAL 1: CREAR TARJETA -->
    <div class="modal-backdrop" id="cardModal">
        <div class="modal">
            <h2 class="modal-title">Crear Nueva Flashcard</h2>
            <form action="index.php?action=store_card" method="POST">
                <input type="hidden" name="id_carpeta" value="<?php echo $id_carpeta; ?>">
                
                <div class="form-group">
                    <label for="pregunta">Pregunta / Concepto</label>
                    <textarea id="pregunta" name="pregunta" rows="3" placeholder="Ej: ¿Qué es una clase abstracta?" required></textarea>
                </div>
                <div class="form-group">
                    <label for="respuesta">Respuesta</label>
                    <textarea id="respuesta" name="respuesta" rows="3" placeholder="Ej: Una clase que no se puede instanciar directamente..." required></textarea>
                </div>
                <div class="form-group">
                    <label for="dificultad">Dificultad</label>
                    <select id="dificultad" name="dificultad">
                        <option value="facil">Fácil</option>
                        <option value="medio" selected>Medio</option>
                        <option value="dificil">Difícil</option>
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('cardModal')">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar Tarjeta</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: EDITAR TARJETA -->
    <div class="modal-backdrop" id="editCardModal">
        <div class="modal">
            <h2 class="modal-title">Editar Flashcard</h2>
            <form action="index.php?action=update_card" method="POST">
                <input type="hidden" id="edit_id_flashcard" name="id_flashcard">
                <input type="hidden" name="id_carpeta" value="<?php echo $id_carpeta; ?>">
                
                <div class="form-group">
                    <label for="edit_pregunta">Pregunta / Concepto</label>
                    <textarea id="edit_pregunta" name="pregunta" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="edit_respuesta">Respuesta</label>
                    <textarea id="edit_respuesta" name="respuesta" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="edit_card_dificultad">Dificultad</label>
                    <select id="edit_card_dificultad" name="dificultad">
                        <option value="facil">Fácil</option>
                        <option value="medio">Medio</option>
                        <option value="dificil">Difícil</option>
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('editCardModal')">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: CREAR MATERIA -->
    <div class="modal-backdrop" id="folderModal">
        <div class="modal">
            <h2 class="modal-title">Nueva Materia / Área</h2>
            <form action="index.php?action=store_folder" method="POST">
                <div class="form-group">
                    <label for="nombre_carpeta">Nombre de la Materia</label>
                    <input type="text" id="nombre_carpeta" name="nombre_carpeta" required>
                </div>
                <div class="form-group">
                    <label for="descripcion_carpeta">Descripción (Opcional)</label>
                    <textarea id="descripcion_carpeta" name="descripcion_carpeta" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label for="color_carpeta">Color Identificador</label>
                    <input type="color" id="color_carpeta" name="color_carpeta" value="#4F46E5" style="height: 40px; padding: 2px; cursor: pointer;">
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('folderModal')">Cancelar</button>
                    <button type="submit" class="btn-primary">Crear Materia</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 4: EDITAR MATERIA -->
    <div class="modal-backdrop" id="editFolderModal">
        <div class="modal">
            <h2 class="modal-title">Editar Materia</h2>
            <form action="index.php?action=update_folder" method="POST">
                <input type="hidden" id="edit_id_carpeta" name="id_carpeta">
                
                <div class="form-group">
                    <label for="edit_nombre_carpeta">Nombre de la Materia</label>
                    <input type="text" id="edit_nombre_carpeta" name="nombre_carpeta" required>
                </div>
                <div class="form-group">
                    <label for="edit_descripcion_carpeta">Descripción</label>
                    <textarea id="edit_descripcion_carpeta" name="descripcion_carpeta" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label for="edit_color_carpeta">Color Identificador</label>
                    <input type="color" id="edit_color_carpeta" name="color_carpeta" style="height: 40px; padding: 2px; cursor: pointer;">
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('editFolderModal')">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JS INTERACTIVO -->
    <script>
        function flipCard(container) {
            const card = container.querySelector('.flashcard');
            card.classList.toggle('flipped');
        }

        function openCardModal() {
            document.getElementById('cardModal').classList.add('active');
        }

        function openFolderModal() {
            document.getElementById('folderModal').classList.add('active');
        }

        function openEditFolderModal(id, nombre, descripcion, color) {
            document.getElementById('edit_id_carpeta').value = id;
            document.getElementById('edit_nombre_carpeta').value = nombre;
            document.getElementById('edit_descripcion_carpeta').value = descripcion;
            document.getElementById('edit_color_carpeta').value = color;
            document.getElementById('editFolderModal').classList.add('active');
        }

        function openEditCardModal(id, pregunta, respuesta, dificultad) {
            document.getElementById('edit_id_flashcard').value = id;
            document.getElementById('edit_pregunta').value = pregunta;
            document.getElementById('edit_respuesta').value = respuesta;
            document.getElementById('edit_card_dificultad').value = dificultad;
            document.getElementById('editCardModal').classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }
    </script>
</body>
</html>