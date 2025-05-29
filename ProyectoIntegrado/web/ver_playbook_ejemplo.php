<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Playbook de Demostración</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <?php
        if (isset($_GET['file'])) {
            $playbookName = basename($_GET['file']); // Obtener solo el nombre del archivo
            $playbookPath = __DIR__ . '/playbooks/ejemplos/' . $playbookName;
            $explanationPath = __DIR__ . '/playbooks/explicaciones_ejemplos/' . str_replace('.yml', '_explicacion.php', $playbookName);

            // Asegurarse de que el archivo existe y es seguro
            if (file_exists($playbookPath) && strpos($playbookName, '..') === false) {

                // --- 1. MOSTRAR EL CÓDIGO YAML COMPLETO PRIMERO (COMO BLOQUE INDEPENDIENTE) ---
                echo "<h2>Contenido del Playbook: " . htmlspecialchars($playbookName) . "</h2>";
                echo '<pre class="yaml-output">' . htmlspecialchars(file_get_contents($playbookPath)) . '</pre>';


                // --- 2. LUEGO, INCLUIR LA EXPLICACIÓN DETALLADA ---
                if (file_exists($explanationPath)) {
                    // Si la explicación existe, la incluimos
                    include $explanationPath;
                } else {
                    // Si no hay explicación, lo indicamos
                    echo "<h2>Explicación detallada no encontrada para este playbook.</h2>";
                    echo "<p>El archivo de explicación (<code>" . htmlspecialchars(basename($explanationPath)) . "</code>) no existe.</p>";
                }
            } else {
                echo "<p class='error-message'>Playbook no encontrado o ruta inválida.</p>";
            }
        } else {
            echo "<p class='error-message'>Nombre de archivo no especificado.</p>";
        }
        ?>
        <p class="button-container">
            <a href="index.php" class="button">Volver al inicio</a>
        </p>
    </div>
</body>
</html>