<?php
session_start();

$examplePlaybooksDir = __DIR__ . '/playbooks/ejemplos/';
$userPlaybooksDir = __DIR__ . '/playbooks/';

// Ahora esperamos el nombre del archivo vía GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['file'])) {
    $exampleFileName = basename($_GET['file']); // Asegurarse de que sea solo el nombre del archivo

    // Ruta completa al playbook de ejemplo
    $sourcePath = $examplePlaybooksDir . $exampleFileName;

    // Verificar que el archivo de ejemplo existe y es un archivo
    if (!file_exists($sourcePath) || !is_file($sourcePath)) {
        $_SESSION['message'] = "El playbook de ejemplo '" . htmlspecialchars($exampleFileName) . "' no fue encontrado.";
        $_SESSION['message_type'] = "error";
        header('Location: index.php');
        exit();
    }

    // Leer el contenido del playbook de ejemplo
    $playbookContent = file_get_contents($sourcePath);
    if ($playbookContent === false) {
        $_SESSION['message'] = "Error al leer el contenido del playbook de ejemplo.";
        $_SESSION['message_type'] = "error";
        header('Location: index.php');
        exit();
    }

    // Generar un nuevo nombre para el playbook clonado
    $info = pathinfo($exampleFileName);
    $baseName = $info['filename'];
    $extension = isset($info['extension']) ? '.' . $info['extension'] : '.yml';
    
    // Añadir timestamp para asegurar un nombre único
    $clonedFileName = $baseName . '_clonado_' . date('YmdHis') . $extension; 

    // Asegurarse de que el directorio de playbooks del usuario existe
    if (!is_dir($userPlaybooksDir)) {
        mkdir($userPlaybooksDir, 0755, true);
    }

    $destinationPath = $userPlaybooksDir . $clonedFileName;

    // Guardar el contenido en el nuevo archivo clonado
    if (file_put_contents($destinationPath, $playbookContent) !== false) {
        $_SESSION['message'] = "Playbook de ejemplo clonado como '" . htmlspecialchars($clonedFileName) . "' y listo para editar.";
        $_SESSION['message_type'] = "success";
        
        // Redirigir de vuelta a la página principal con un mensaje de éxito
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['message'] = "Error al clonar el playbook de ejemplo. Verifique los permisos de escritura en la carpeta 'playbooks/'.";
        $_SESSION['message_type'] = "error";
        header('Location: index.php');
        exit();
    }

} else {
    // Si no se recibió un GET o el nombre del archivo no está seteado
    $_SESSION['message'] = "Solicitud inválida para clonar playbook.";
    $_SESSION['message_type'] = "error";
    header('Location: index.php');
    exit();
}
?>