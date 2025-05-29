<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $_SESSION['nombre_playbook'] = $_POST['nombre_playbook'] ?? '';
    $_SESSION['descripcion'] = $_POST['descripcion'] ?? '';

}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Paso 2: Configuración general</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <h1>Paso 2: Hosts y privilegios</h1>
  <form action="crear_playbook_paso3.php" method="post">
    <label>Hosts:</label><br>
    <input type="text" name="hosts" value="localhost" required><br><br>

    <label>¿Usar privilegios de superusuario (become)?</label><br>
    <select name="become">
      <option value="true">Sí</option>
      <option value="false">No</option>
    </select><br><br>

    <button type="submit">Siguiente</button>
  </form>
  <a href="crear_playbook.php" class="button secondary">Volver al paso anterior</a>
</body>
</html>
