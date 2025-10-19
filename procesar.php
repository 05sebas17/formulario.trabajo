<?php
// Mostrar errores en pantalla
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>🔍 Depuración activa...</h3>";

// Datos de conexión
$servername = "127.0.0.1";
$username = "root";
$password = "";
$database = "lasalle_db";
$port = 3307;

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database, $port);

// Verificar conexión
if ($conn->connect_error) {
    die("<p>❌ Error de conexión: " . $conn->connect_error . "</p>");
}
echo "<p>✅ Conexión exitosa a la base de datos</p>";

// Verificar si se recibieron datos del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    echo "<p>📦 Datos recibidos:</p>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    $nombre = $_POST['nombre'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $mensaje = $_POST['mensaje'] ?? '';

    if ($nombre && $correo && $mensaje) {
        $sql = "INSERT INTO mensajes (nombre, correo, mensaje) VALUES ('$nombre', '$correo', '$mensaje')";
        if ($conn->query($sql) === TRUE) {
            echo "<p>✅ Datos guardados correctamente.</p>";
        } else {
            echo "<p>❌ Error al guardar: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>⚠️ Faltan campos del formulario.</p>";
    }
} else {
    echo "<p>⚠️ No se recibió POST.</p>";
}

$conn->close();
?>
