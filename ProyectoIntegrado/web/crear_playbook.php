<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Crear Playbook - Paso 1</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <h1>Paso 1: Información general del Playbook</h1>
  <form action="crear_playbook_paso2.php" method="post">
    <label>Nombre del playbook (sin extensión):</label><br>
    <input type="text" name="nombre_playbook" required><br><br>

    <label>Descripción del playbook:</label><br>
    <input type="text" name="descripcion" required><br><br>

    <button type="submit">Siguiente</button>
  </form>
  <a href="index.php" class="button">Volver al inicio</a>
</body>
</html>