<?php
session_start();

$basePlaybooksDir = __DIR__ . '/playbooks/';

// Inicializa $fileName como cadena vacía para evitar warnings si no se define después
$fileName = '';

if (!isset($_GET['file']) || empty($_GET['file'])) {
    echo "<h2 class='error-message'>❌ No se ha especificado ningún archivo de playbook para ver.</h2>";
    echo "<a href='index.php'>Volver al inicio</a>";
    exit;
}

$requestedFile = $_GET['file'];
$fullPathToPlaybook = realpath($basePlaybooksDir . $requestedFile);

if ($fullPathToPlaybook === false || !is_file($fullPathToPlaybook) || !is_readable($fullPathToPlaybook)) {
    echo "<h2 class='error-message'>❌ El playbook '<code>" . htmlspecialchars($requestedFile) . "</code>' no existe o no se puede leer.</h2>";
    echo "<a href='index.php'>Volver al inicio</a>";
    exit;
}

if (strpos($fullPathToPlaybook, realpath($basePlaybooksDir)) !== 0) {
    echo "<h2 class='error-message'>❌ Acceso denegado: Intento de acceder a un archivo fuera del directorio de playbooks.</h2>";
    echo "<a href='index.php'>Volver al inicio</a>";
    exit;
}

// Ahora podemos leer el contenido del playbook de forma segura
$playbookContent = file_get_contents($fullPathToPlaybook);
$fileName = htmlspecialchars($requestedFile); // Para mostrar el nombre en la vista

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contenido del Playbook: <?php echo $fileName; ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Contenido del Playbook: <code><?php echo $fileName; ?></code></h1>
        <pre><?php echo htmlspecialchars($playbookContent); ?></pre>
        <a href="index.php">Volver al inicio</a>
    </div>
</body>
</html>