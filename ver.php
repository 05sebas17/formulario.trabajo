<?php
// Mostrar errores para depurar (puedes quitar estas 2 líneas al final)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL); ini_set('display_errors', 1);

$host = '127.0.0.1';   // importante: no usar 'localhost'
$user = 'root';
$pass = '';            // XAMPP por defecto
$db   = 'lasalle_db';
$port = 3307;          // tu MySQL corre en 3307

$conn = new mysqli($host, $user, $pass, $db, $port);
$conn->set_charset('utf8mb4');

$result = $conn->query("SELECT id, nombre, correo, mensaje FROM mensajes ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Mensajes Recibidos</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <h2>Mensajes recibidos</h2>
  <table border="1" cellpadding="8" style="margin:auto; max-width: 900px;">
    <tr>
      <th>ID</th>
      <th>Nombre</th>
      <th>Correo</th>
      <th>Mensaje</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
      <tr>
        <td><?= (int)$row['id'] ?></td>
        <td><?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($row['correo'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= nl2br(htmlspecialchars($row['mensaje'], ENT_QUOTES, 'UTF-8')) ?></td>
      </tr>
    <?php } ?>
  </table>
  <br />
  <a href="contacto.html">Volver al formulario</a>
</body>
</html>
