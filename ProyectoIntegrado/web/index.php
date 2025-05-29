<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Gestor de Playbooks Ansible</title>
  <link rel="stylesheet" href="css/styles.css" />
</head>
<body>

<div class="container">
    <h1>Gestor de Playbooks Ansible</h1>

     <?php
    // Mostrar mensajes de éxito o error si existen en la sesión
    if (isset($_SESSION['message']) && isset($_SESSION['message_type'])) {
        $messageClass = $_SESSION['message_type'] === 'success' ? 'success-message' : 'error-message';
        echo '<div class="' . $messageClass . '">' . $_SESSION['message'] . '</div>';
        // Limpiar las variables de sesión para que el mensaje no se muestre de nuevo
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
    ?>

    <section class="general-info-section">
        <h2>¿Nuevo en Ansible?</h2>
        <p>
            Si necesitas una explicación básica sobre qué son los Playbooks y cómo se estructuran, haz clic en el siguiente botón:
        </p>
        <p class="explanation-link-container">
            <a href="explicacion_ansible.php" class="button info">Ver Explicación General de Playbooks</a>
        </p>
    </section>

    <section class="general-info-section">
        <h2>¿Qué es el Inventario?</h2>
        <p>
            Si necesitas una explicación detallada sobre qué es el archivo de inventario de Ansible, cómo se estructura y para qué sirve, haz clic en el siguiente botón:
        </p>
        <p class="explanation-link-container">
            <a href="explicacion_inventario.php" class="button info">Ver Explicación del Inventario</a>
        </p>
    </section>

    <h2>Crear nuevo playbook</h2>
    <form action="crear_playbook.php" method="post">
      <button type="submit">Iniciar creación</button>
    </form>

    <section class="inventory-section">
        <h2>Gestión del Inventario</h2>
        <p>
            Aquí puedes ver y editar el archivo de inventario de Ansible, donde defines los hosts y grupos con los que Ansible se comunicará.
        </p>
        <p class="inventory-link-container">
            <a href="gestion_inventario.php" class="button primary">Gestionar Inventario</a>
        </p>
    </section>


   <h2>Playbooks de Demostración (Solo Lectura)</h2>
<ul class="playbook-list">
<?php
$examplesDir = __DIR__ . '/playbooks/ejemplos';
if (is_dir($examplesDir)) {
    $files = array_diff(scandir($examplesDir), ['.', '..']);
    if (count($files) === 0) {
        echo "<li>No hay playbooks de demostración.</li>";
    } else {
        foreach ($files as $file) {
            // Filtra 'templates' y asegura que solo sean archivos .yml
            if ($file != 'templates' && pathinfo($file, PATHINFO_EXTENSION) == 'yml') {
                $playbookName = htmlspecialchars($file);
                echo "<li>$playbookName
                        <a href='ejecutar_playbook.php?file=ejemplos/$playbookName' class='button small'>Ejecutar</a>
                        <a href='ver_playbook_ejemplo.php?file=$playbookName' class='button small info'>Detalles</a>
                        <a href='clonar_playbook_ejemplo.php?file=ejemplos/$playbookName' class='button small clone'>Clonar</a>
                    </li>";
            }
        }
    }
} else {
    echo "<li>No existe la carpeta <code>playbooks/ejemplos</code>.</li>";
}
?>
</ul>

    <h2>Mis Playbooks Guardados</h2>
    <ul class="playbook-list">
    <?php
    $userPlaybooksDir = __DIR__ . '/playbooks';
    if (is_dir($userPlaybooksDir)) {
        $files = array_filter(array_diff(scandir($userPlaybooksDir), ['.', '..', 'ejemplos']), function($file) use ($userPlaybooksDir) {
            return is_file($userPlaybooksDir . '/' . $file);
        });

        if (count($files) === 0) {
            echo "<li>No hay playbooks guardados por el usuario.</li>";
        } else {
            foreach ($files as $file) {
                $playbookName = htmlspecialchars($file);
                echo "<li>$playbookName
                        <a href='ejecutar_playbook.php?file=$playbookName' class='button small'>Ejecutar</a>
                        <a href='ver_playbook.php?file=$playbookName' class='button small'>Ver</a>
                        <a href='editar_playbook.php?file=$playbookName' class='button small info'>Editar</a>  <a href='borrar_playbook.php?file=$playbookName' class='button small danger' onclick='return confirm(\"¿Estás seguro de que quieres borrar el playbook " . addslashes($playbookName) . "?\");'>Borrar</a> </li>";
            }
        }
    } else {
        echo "<li>No existe la carpeta <code>playbooks</code>.</li>";
    }
    ?>
    </ul>

</div>
</body>
</html>