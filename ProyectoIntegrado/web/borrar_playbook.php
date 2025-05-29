<?php
session_start();

// Directorio base donde se guardan los playbooks del usuario
$userPlaybooksDir = __DIR__ . '/playbooks/';

// Mensajes de estado
$messageType = '';
$message = '';

if (!isset($_GET['file']) || empty($_GET['file'])) {
    $messageType = 'error';
    $message = '❌ No se ha especificado ningún archivo de playbook para borrar.';
} else {
    $requestedFile = $_GET['file'];

    // Construye la ruta completa del archivo
    $fullPathToFile = realpath($userPlaybooksDir . $requestedFile);

    // **IMPORTANTE: Verificaciones de seguridad**
    // 1. Asegurarse de que realpath() no haya fallado
    // 2. Asegurarse de que el archivo existe y es un archivo regular
    // 3. Asegurarse de que el archivo está dentro del directorio de playbooks del usuario y no en 'ejemplos/'
    if ($fullPathToFile === false || !is_file($fullPathToFile) || !is_writable($fullPathToFile)) {
        $messageType = 'error';
        $message = '❌ Error: El playbook especificado no existe, no es un archivo o no se tienen permisos para borrarlo.';
    } elseif (strpos($fullPathToFile, realpath($userPlaybooksDir)) !== 0 || strpos($fullPathToFile, realpath($userPlaybooksDir . 'ejemplos/')) === 0) {
        // Esta condición evita que se borre fuera de la carpeta o dentro de la carpeta 'ejemplos'
        $messageType = 'error';
        $message = '❌ Acceso denegado: No se permite borrar playbooks fuera del directorio de usuario o los ejemplos.';
    } else {
        // Intentar borrar el archivo
        if (unlink($fullPathToFile)) {
            $messageType = 'success';
            $message = '✅ Playbook "<code>' . htmlspecialchars($requestedFile) . '</code>" borrado con éxito.';
        } else {
            $messageType = 'error';
            $message = '❌ Error al borrar el playbook "<code>' . htmlspecialchars($requestedFile) . '</code>". Permisos incorrectos o el archivo está bloqueado.';
        }
    }
}

// Redirigir al index.php con un mensaje
$_SESSION['message'] = $message;
$_SESSION['message_type'] = $messageType;
header('Location: index.php');
exit;
?>