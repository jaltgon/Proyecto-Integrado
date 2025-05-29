<?php
session_start();

$inventoryFile = __DIR__ . '/inventory/hosts.ini';
$message = '';
$message_type = '';

// Asegurarse de que la carpeta 'inventory' existe
if (!is_dir(__DIR__ . '/inventory')) {
    mkdir(__DIR__ . '/inventory', 0755, true);
}

// Si el archivo inventory no existe, crearlo con un contenido básico
if (!file_exists($inventoryFile)) {
    $defaultContent = "[local]\nlocalhost ansible_connection=local\n";
    file_put_contents($inventoryFile, $defaultContent);
}

// Procesar el envío del formulario para guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inventory_content'])) {
    $newContent = $_POST['inventory_content'];

    // Intentar guardar el contenido
    if (file_put_contents($inventoryFile, $newContent) !== false) {
        $message = "Inventario actualizado exitosamente.";
        $message_type = "success";
    } else {
        $message = "Error al guardar el inventario. Verifique los permisos del archivo.";
        $message_type = "error";
    }
}

// Leer el contenido actual del inventario para mostrarlo
$currentContent = '';
if (file_exists($inventoryFile)) {
    $currentContent = file_get_contents($inventoryFile);
} else {
    $message = "Error: El archivo de inventario no se pudo leer o no existe.";
    $message_type = "error";
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Inventario Ansible</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Gestión de Inventario Ansible</h1>

        <?php if ($message): ?>
            <div class="<?php echo $message_type; ?>-message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="gestion_inventario.php" method="post">
            <label for="inventory_content">Contenido de <code>inventory/hosts.ini</code>:</label>
            <textarea id="inventory_content" name="inventory_content" rows="20" cols="80" class="full-width-textarea"><?php echo htmlspecialchars($currentContent); ?></textarea>
            <div class="button-group">
                <button type="submit" class="button">Guardar Inventario</button>
                <a href="index.php" class="button secondary">Volver al inicio</a>
            </div>
        </form>

        <p class="small-text">
            <strong>Nota de seguridad:</strong> El servidor web (PHP) necesita permisos de escritura en la carpeta <code>inventory/</code> y en el archivo <code>hosts.ini</code>. Para Docker, asegúrate de que el volumen esté configurado con los permisos adecuados.
        </p>

    </div>
</body>
</html>