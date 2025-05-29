<?php
session_start();

$userPlaybooksDir = __DIR__ . '/playbooks/';
$messageType = '';
$message = '';

// --- Lógica para Cargar el Playbook (sin cambios) ---
if (!isset($_GET['file']) || empty($_GET['file'])) {
    $messageType = 'error';
    $message = '❌ No se ha especificado ningún archivo de playbook para editar.';
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $messageType;
    header('Location: index.php');
    exit;
}

$requestedFile = $_GET['file'];
$fullPathToPlaybook = realpath($userPlaybooksDir . $requestedFile);

// Verificaciones de seguridad para la carga (sin cambios)
if ($fullPathToPlaybook === false || !is_file($fullPathToPlaybook) || !is_readable($fullPathToPlaybook)) {
    $messageType = 'error';
    $message = '❌ Error: El playbook especificado no existe o no se puede leer.';
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $messageType;
    header('Location: index.php');
    exit;
} elseif (strpos($fullPathToPlaybook, realpath($userPlaybooksDir)) !== 0 || strpos($fullPathToPlaybook, realpath($userPlaybooksDir . 'ejemplos/')) === 0) {
    $messageType = 'error';
    $message = '❌ Acceso denegado: No se permite editar playbooks de demostración o fuera del directorio de usuario.';
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $messageType;
    header('Location: index.php');
    exit;
}

// Si las verificaciones pasan, leer el contenido del playbook
$playbookContent = file_get_contents($fullPathToPlaybook);
$fileName = htmlspecialchars($requestedFile); // Nombre actual del archivo que se está editando

// --- Lógica para Guardar el Playbook (cuando se envía el formulario) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['playbook_content'])) {
    $newContent = $_POST['playbook_content'];
    $action = $_POST['action'] ?? ''; // Obtener la acción: 'overwrite' o 'save_as_new'

    $targetFileName = $fileName; // Por defecto, es el mismo archivo
    $targetFilePath = $fullPathToPlaybook; // Por defecto, la misma ruta

    if ($action === 'save_as_new') {
        $newFileName = trim($_POST['new_file_name'] ?? '');
        if (empty($newFileName)) {
            $messageType = 'error';
            $message = '❌ Error: El nombre para el nuevo archivo no puede estar vacío al guardar como nuevo.';
            // No redirigimos aquí para que el usuario pueda corregir el nombre en la misma página
        } else {
            // --- INICIO DE CAMBIO ---
            // 1. Reemplazar espacios por guiones bajos
            $newFileName = str_replace(' ', '_', $newFileName);
            // 2. Eliminar caracteres no alfanuméricos, guiones y puntos (excepto la extensión final)
            $newFileName = preg_replace('/[^a-zA-Z0-9_\-.]/', '', $newFileName);
            // 3. Asegurar que no haya múltiples guiones bajos seguidos
            $newFileName = preg_replace('/_+/', '_', $newFileName);
            // --- FIN DE CAMBIO ---

            // Asegurar que el nuevo nombre tenga la extensión .yml o .yaml
            if (!preg_match('/\.(yml|yaml)$/i', $newFileName)) {
                $newFileName .= '.yml';
            }
            $targetFileName = htmlspecialchars($newFileName);
            $targetFilePath = $userPlaybooksDir . $newFileName;

            // Verificar si el nuevo archivo ya existe para evitar sobrescribir accidentalmente
            if (file_exists($targetFilePath)) {
                $messageType = 'error';
                $message = '❌ Error: El archivo "<code>' . $targetFileName . '</code>" ya existe. Por favor, elige un nombre diferente.';
                // No redirigimos aquí
            }
        }
    }

    // Si no hubo errores en la validación del "Guardar como Nuevo"
    if ($messageType === '') {
        // Verificar permisos de escritura en la ruta de destino
        // Para "Guardar como Nuevo", la carpeta debe ser escribible
        // Para "Sobreescribir", el archivo debe ser escribible
        if (($action === 'overwrite' && is_writable($targetFilePath)) || ($action === 'save_as_new' && is_dir(dirname($targetFilePath)) && is_writable(dirname($targetFilePath)))) {
            if (file_put_contents($targetFilePath, $newContent) !== false) {
                $messageType = 'success';
                $message = '✅ Playbook "<code>' . $targetFileName . '</code>" guardado con éxito.';
            } else {
                $messageType = 'error';
                $message = '❌ Error al guardar el playbook "<code>' . $targetFileName . '</code>".';
            }
        } else {
            $messageType = 'error';
            $message = '❌ Error de permisos: El archivo/directorio no es escribible. Verifica los permisos.';
        }
    }

    // Redirigir solo si hubo un mensaje de éxito o un error que no requiere quedarse en la página
    if ($messageType === 'success' || ($messageType === 'error' && $action === 'overwrite')) {
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $messageType;
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Playbook: <?php echo $fileName; ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Editar Playbook: <code><?php echo $fileName; ?></code></h1>

        <?php
        // Mostrar mensajes de éxito o error si se intentó guardar desde esta página
        if ($messageType && $message) {
            $messageClass = $messageType === 'success' ? 'success-message' : 'error-message';
            echo '<div class="' . $messageClass . '">' . $message . '</div>';
        }
        ?>

        <form action="editar_playbook.php?file=<?php echo urlencode($requestedFile); ?>" method="post">
            <textarea name="playbook_content" rows="25" class="code-editor"><?php echo htmlspecialchars($playbookContent); ?></textarea>

            <div class="form-actions">
                <button type="submit" name="action" value="overwrite" class="button">Sobreescribir Playbook</button>

                <div class="save-as-new">
                    <input type="text" name="new_file_name" placeholder="Nuevo nombre de archivo (ej: mi_nuevo_playbook.yml)">
                    <button type="submit" name="action" value="save_as_new" class="button info">Guardar como Nuevo</button>
                </div>
            </div>
        </form>

        <a href="index.php" class="button">Volver al inicio</a>
    </div>
</body>
</html>