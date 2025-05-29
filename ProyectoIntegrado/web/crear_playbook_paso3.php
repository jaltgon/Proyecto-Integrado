<?php
session_start();

// --- Capturar los datos del Paso 2 (hosts y become) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hosts'], $_POST['become'])) {
    $_SESSION['hosts'] = $_POST['hosts'];
    $_SESSION['become'] = $_POST['become'];
    // Limpiar mensajes de error de este paso para una nueva carga
    unset($_SESSION['message_paso3']);
    unset($_SESSION['message_type_paso3']);
}

// Asegurarse de que las variables de sesión de pasos anteriores estén definidas
// Si no están, redirigir al paso 1 o 2 para evitar un estado inconsistente
if (!isset($_SESSION['nombre_playbook'], $_SESSION['descripcion'])) {
    header('Location: crear_playbook.php'); // Redirigir al paso 1 si falta info básica
    exit();
}
if (!isset($_SESSION['hosts'], $_SESSION['become'])) {
    header('Location: crear_playbook_paso2.php'); // Redirigir al paso 2 si falta info de hosts/become
    exit();
}

// Inicializar $_SESSION['tareas'] si no existe o si se llega a esta página por primera vez
// Es importante resetear las tareas solo si se inicia un nuevo flujo de creación de tareas
// Si se refresca la página o se vuelve atrás en el navegador, queremos mantener las tareas ya añadidas.
// Para el flujo normal, la primera vez que se accede, se debería resetear si no es un POST de la misma página
if (!isset($_SESSION['tareas']) || (isset($_GET['reset_tasks']) && $_GET['reset_tasks'] == 'true')) {
    $_SESSION['tareas'] = [];
}


// Procesar el envío del formulario para añadir una tarea (cuando el usuario hace "Añadir tarea" en esta misma página)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_tarea'])) {
    $nombre_tarea = trim($_POST['nombre_tarea']);
    $modulo = trim($_POST['modulo']);
    $parametros_raw = trim($_POST['parametros_modulo']); // Capturamos el nuevo campo

    // Validar que los campos obligatorios de la tarea no estén vacíos
    if (empty($nombre_tarea) || empty($modulo)) {
        $_SESSION['message_paso3'] = "Nombre de la tarea y Módulo son obligatorios.";
        $_SESSION['message_type_paso3'] = "error";
    } else {
        $tarea = [
            'nombre' => $nombre_tarea,
            'modulo' => $modulo,
            'parametros_raw' => $parametros_raw // Guardamos la cadena tal cual
        ];
        $_SESSION['tareas'][] = $tarea;

        // Limpiar el mensaje de éxito si ya se añadió una tarea
        if (isset($_SESSION['message_paso3']) && $_SESSION['message_type_paso3'] === 'success') {
            unset($_SESSION['message_paso3']);
            unset($_SESSION['message_type_paso3']);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Paso 3: Añadir tareas</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Paso 3: Añadir tareas</h1>

        <?php
        // Mostrar mensajes de estado específicos de este paso
        if (isset($_SESSION['message_paso3']) && isset($_SESSION['message_type_paso3'])) {
            $messageClass = $_SESSION['message_type_paso3'] === 'success' ? 'success-message' : 'error-message';
            echo '<div class="' . $messageClass . '">' . $_SESSION['message_paso3'] . '</div>';
            unset($_SESSION['message_paso3']); // Limpiar el mensaje después de mostrarlo
            unset($_SESSION['message_type_paso3']);
        }
        ?>

        <form method="post" class="add-task-form">
            <label for="nombre_tarea">Nombre de la tarea:</label><br>
            <input type="text" id="nombre_tarea" name="nombre_tarea" required><br><br>

            <label for="modulo">Módulo (ej: ansible.builtin.apt, copy, file, ping):</label><br>
            <input type="text" id="modulo" name="modulo" required><br><br>

            <label for="parametros_modulo">Parámetros del Módulo (en formato YAML o JSON opcional):</label><br>
            <textarea id="parametros_modulo" name="parametros_modulo" rows="5" cols="60" class="full-width-textarea" placeholder="Ej:
name: apache2
state: present
    o:
src: /tmp/my_file.txt
dest: /var/www/html/my_file.txt
mode: '0644'"></textarea><br><br>
            <p class="small-text">
                Para módulos como <code>ansible.builtin.ping</code>, deja el campo de parámetros vacío.
            </p>
            <br>

            <button type="submit" class="button">Añadir tarea</button>
        </form>

        <?php if (!empty($_SESSION['tareas'])): ?>
            <h2>Tareas añadidas hasta ahora:</h2>
            <ul class="task-list">
                <?php foreach ($_SESSION['tareas'] as $index => $t): ?>
                    <li>
                        <strong><?= htmlspecialchars($t['nombre'] ?? 'Sin nombre') ?></strong><br>
                        Módulo: <code><?= htmlspecialchars($t['modulo'] ?? 'Sin módulo') ?></code><br>
                        <?php if (!empty($t['parametros_raw'])): ?>
                            Parámetros:<br><pre class="task-params"><?= htmlspecialchars($t['parametros_raw']) ?></pre>
                        <?php else: ?>
                            (Sin parámetros)
                        <?php endif; ?>
                        </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Todavía no has añadido ninguna tarea.</p>
        <?php endif; ?>

        <div class="button-group">
            <a href="crear_playbook_paso2.php" class="button secondary">Volver al paso anterior</a>
            <form action="crear_playbook_final.php" method="post" style="display:inline-block;">
                <button type="submit" class="button primary">Finalizar y guardar playbook</button>
            </form>
        </div>
    </div>
</body>
</html>