<?php
session_start();

// Directorio base donde se guardan los playbooks
$basePlaybooksDir = __DIR__ . '/playbooks/';
// Define la ruta al inventario desde el principio
$inventoryPath = escapeshellarg(__DIR__ . '/inventory/hosts.ini'); // ¡DEFINIDA AQUÍ!

// Inicializa $fileName como cadena vacía para evitar warnings si no se define después
$fileName = '';

// --- VERIFICACIÓN Y SANITIZACIÓN DE LA RUTA ---
if (!isset($_GET['file']) || empty($_GET['file'])) {
    // Si no se proporciona un archivo, salimos con un error
    echo "<h2 class='error-message'>❌ No se ha especificado ningún archivo de playbook para ejecutar.</h2>";
    echo "<a href='index.php'>Volver al inicio</a>";
    exit;
}

$requestedFile = $_GET['file'];

// **CAMBIO CLAVE AQUÍ:** Usar realpath() y verificar seguridad
$fullPathToPlaybook = realpath($basePlaybooksDir . $requestedFile);

// Verificar si el archivo realmente existe dentro del directorio de playbooks
// y si es un archivo regular y legible.
if ($fullPathToPlaybook === false || !is_file($fullPathToPlaybook) || !is_readable($fullPathToPlaybook)) {
    echo "<h2 class='error-message'>❌ El playbook '<code>" . htmlspecialchars($requestedFile) . "</code>' no existe o no se puede leer.</h2>";
    echo "<a href='index.php'>Volver al inicio</a>";
    exit;
}

// Asegurarse de que el archivo está dentro de nuestro directorio base 'playbooks'
// Esto es una capa de seguridad crítica.
if (strpos($fullPathToPlaybook, realpath($basePlaybooksDir)) !== 0) {
    echo "<h2 class='error-message'>❌ Acceso denegado: Intento de acceder a un archivo fuera del directorio de playbooks.</h2>";
    echo "<a href='index.php'>Volver al inicio</a>";
    exit;
}

// Ahora que estamos seguros de la ruta, la preparamos para el comando shell
$playbookFilePathForShell = escapeshellarg($fullPathToPlaybook);

// Asignamos $fileName para la parte de visualización, para evitar los warnings
$fileName = htmlspecialchars($requestedFile); // ¡DEFINIDA AQUÍ!

// --- RESTO DEL CÓDIGO (EJECUTAR PLAYBOOK) ---

// El comando Ansible ahora usará $playbookFilePathForShell
$command = "ansible-playbook -i " . $inventoryPath . " " . $playbookFilePathForShell;

$output = "No se ha podido ejecutar el comando.";
$returnVar = 1;

exec($command . ' 2>&1', $outputArray, $returnVar);
$output = implode("\n", $outputArray);

$successMessage = "";
$errorMessage = "";
$type = ''; // Inicializar $type también

if ($returnVar === 0) {
    $successMessage = "✅ Playbook '<code>" . $fileName . "</code>' ejecutado con éxito.";
    $type = 'success';
} else {
    $errorMessage = "❌ Error al ejecutar el playbook '<code>" . $fileName . "</code>'.";
    $type = 'error';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultado de Ejecución</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Resultado de la Ejecución del Playbook: <code><?php echo $fileName; ?></code></h1>
        <?php if ($type === 'success'): ?>
            <div class="success-message"><?php echo $successMessage; ?></div>
        <?php elseif ($type === 'error'): ?>
            <div class="error-message"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <h3>Salida de Ansible:</h3>
        <pre><?php echo htmlspecialchars($output); ?></pre>

        <a href="index.php">Volver al inicio</a>
    </div>
</body>
</html>