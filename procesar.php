<?php
// Configuración de errores (desactivar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de la base de datos
$servername = "127.0.0.1";
$username = "root";
$password = "";
$database = "lasalle_db";
$port = 3307;

// Función para sanitizar datos
function sanitizarInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Función para validar email
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Función para validar teléfono
function validarTelefono($telefono) {
    $telefono_limpio = preg_replace('/\D/', '', $telefono);
    return strlen($telefono_limpio) >= 10;
}

// Conectar a la base de datos
$conn = new mysqli($servername, $username, $password, $database, $port);

// Verificar conexión
if ($conn->connect_error) {
    die("<div style='color: red; padding: 20px;'>❌ Error de conexión: " . $conn->connect_error . "</div>");
}

echo "<div style='padding: 20px; font-family: Arial, sans-serif;'>";
echo "<h2>🔍 Procesamiento del Formulario</h2>";
echo "<p style='color: green;'>✅ Conexión exitosa a la base de datos</p>";

// Verificar si es una petición POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    echo "<h3>📥 Datos recibidos:</h3>";
    echo "<pre style='background: #f4f4f4; padding: 15px; border-radius: 5px;'>";
    print_r($_POST);
    echo "</pre>";

    // Recibir y sanitizar datos
    $nombre   = sanitizarInput($_POST['nombre'] ?? '');
    $correo   = sanitizarInput($_POST['correo'] ?? '');
    $telefono = sanitizarInput($_POST['telefono'] ?? '');
    $programa = sanitizarInput($_POST['programa'] ?? '');
    $mensaje  = sanitizarInput($_POST['mensaje'] ?? '');

    // Array para almacenar errores
    $errores = [];

    // Validaciones
    if (empty($nombre)) {
        $errores[] = "El nombre es obligatorio";
    } elseif (strlen($nombre) < 3) {
        $errores[] = "El nombre debe tener al menos 3 caracteres";
    }

    if (empty($correo)) {
        $errores[] = "El correo electrónico es obligatorio";
    } elseif (!validarEmail($correo)) {
        $errores[] = "El correo electrónico no es válido";
    }

    if (empty($telefono)) {
        $errores[] = "El teléfono es obligatorio";
    } elseif (!validarTelefono($telefono)) {
        $errores[] = "El teléfono debe tener al menos 10 dígitos";
    }

    if (empty($programa)) {
        $errores[] = "Debe seleccionar un programa o carrera";
    }

    if (empty($mensaje)) {
        $errores[] = "El mensaje es obligatorio";
    } elseif (strlen($mensaje) < 10) {
        $errores[] = "El mensaje debe tener al menos 10 caracteres";
    }

    // Mostrar errores si existen
    if (!empty($errores)) {
        echo "<div style='background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
        echo "<h3>❌ Errores de validación:</h3>";
        echo "<ul>";
        foreach ($errores as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
        echo "<p><a href='javascript:history.back()' style='color: #1976d2;'>← Volver al formulario</a></p>";
        echo "</div>";
    } else {
        // INSERT sin la columna de términos
        $stmt = $conn->prepare(
            "INSERT INTO mensajes (nombre, correo, telefono, programa, mensaje, fecha_registro)
             VALUES (?, ?, ?, ?, ?, NOW())"
        );
        
        if ($stmt === false) {
            echo "<div style='background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px;'>";
            echo "<h3>❌ Error al preparar la consulta:</h3>";
            echo "<p>" . $conn->error . "</p>";
            echo "</div>";
        } else {
            // Vincular parámetros (5 strings)
            $stmt->bind_param("sssss", $nombre, $correo, $telefono, $programa, $mensaje);
            
            // Ejecutar la consulta
            if ($stmt->execute()) {
                echo "<div style='background: #e8f5e9; color: #2e7d32; padding: 20px; border-radius: 5px; margin: 15px 0;'>";
                echo "<h3>✅ ¡Mensaje enviado correctamente!</h3>";
                echo "<p>Gracias <strong>$nombre</strong>, tu mensaje ha sido guardado exitosamente.</p>";
                echo "<p>Nos pondremos en contacto contigo a través de <strong>$correo</strong> o al teléfono <strong>$telefono</strong>.</p>";
                echo "<p>ID del registro: " . $stmt->insert_id . "</p>";
                echo "<hr style='margin: 20px 0;'>";
                echo "<h4>📋 Resumen de tu solicitud:</h4>";
                echo "<ul>";
                echo "<li><strong>Nombre:</strong> $nombre</li>";
                echo "<li><strong>Correo:</strong> $correo</li>";
                echo "<li><strong>Teléfono:</strong> $telefono</li>";
                echo "<li><strong>Programa:</strong> $programa</li>";
                echo "<li><strong>Mensaje:</strong> " . substr($mensaje, 0, 100) . "...</li>";
                echo "</ul>";
                echo "<p style='margin-top: 20px;'><a href='index.html' style='background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>← Volver al inicio</a></p>";
                echo "</div>";
            } else {
                echo "<div style='background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px;'>";
                echo "<h3>❌ Error al guardar los datos:</h3>";
                echo "<p>" . $stmt->error . "</p>";
                echo "<p><a href='javascript:history.back()' style='color: #1976d2;'>← Volver al formulario</a></p>";
                echo "</div>";
            }
            
            $stmt->close();
        }
    }
    
} else {
    echo "<div style='background: #fff3e0; color: #e65100; padding: 15px; border-radius: 5px;'>";
    echo "<h3>⚠️ Acceso no permitido</h3>";
    echo "<p>Este archivo solo acepta peticiones POST desde el formulario.</p>";
    echo "<p><a href='index.html' style='color: #1976d2;'>← Ir al formulario</a></p>";
    echo "</div>";
}

echo "</div>";

$conn->close();
?>
