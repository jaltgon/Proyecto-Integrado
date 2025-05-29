<?php
session_start();

$playbooksDir = __DIR__ . '/playbooks/';
$tempPlaybookPath = $playbooksDir . 'temp_playbook_to_validate.yml'; // Archivo temporal para validación
$mensaje_estado = '';
$tipo_mensaje = '';
$yaml = ''; // Inicializar $yaml para que esté disponible incluso si hay error

// Asegurarse de que la carpeta 'playbooks' existe
if (!is_dir($playbooksDir)) {
    mkdir($playbooksDir, 0777, true); // Asegúrate de que los permisos sean adecuados para el usuario de PHP
}

// Comprobar que tenemos los datos mínimos en la sesión
if (!isset($_SESSION['nombre_playbook'], $_SESSION['hosts'], $_SESSION['become'], $_SESSION['tareas'])) {
    $mensaje_estado = "<h2 class='error-message'>Faltan datos para generar el playbook. Por favor, reinicia el proceso de creación.</h2>";
    $tipo_mensaje = 'error';
} else {
    // --- SANITIZACIÓN Y CONSTRUCCIÓN DEL NOMBRE DE ARCHIVO ---
    $rawPlaybookName = $_SESSION['nombre_playbook'];
    $sanitizedName = str_replace(' ', '_', $rawPlaybookName);
    $sanitizedName = preg_replace('/[^a-zA-Z0-9_\-.]/', '', $sanitizedName);
    $sanitizedName = preg_replace('/_+/', '_', $sanitizedName);
    if (!preg_match('/\.(yml|yaml)$/i', $sanitizedName)) {
        $sanitizedName .= '.yml';
    }
    $nombreArchivo = $sanitizedName;
    $ruta = $playbooksDir . $nombreArchivo;

    // --- CONSTRUCCIÓN DEL CONTENIDO YAML DEL PLAYBOOK ---
    $yaml = "---\n";
    $yaml .= "- name: " . htmlspecialchars($_SESSION['nombre_playbook']) . "\n";
    $yaml .= "  hosts: " . htmlspecialchars($_SESSION['hosts']) . "\n";
    $yaml .= "  become: " . ($_SESSION['become'] === 'true' ? 'true' : 'false') . "\n";
    $yaml .= "  tasks:\n";

    foreach ($_SESSION['tareas'] as $tarea) {
        $nombreTarea = htmlspecialchars($tarea['nombre']);
        $moduloTarea = $tarea['modulo']; // No aplicar htmlspecialchars al nombre del módulo
        $parametrosRaw = $tarea['parametros_raw'] ?? ''; // Obtener los parámetros en crudo

        $yaml .= "    - name: " . $nombreTarea . "\n";
        $yaml .= "      " . $moduloTarea . ":\n";

        // Intentar parsear los parámetros en crudo
        // Primero como JSON (más estricto), luego intentar con un parser YAML básico
        $parsedParams = null;
        $paramsParseError = false;

        // Intentar JSON primero
        $jsonDecoded = json_decode($parametrosRaw, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $parsedParams = $jsonDecoded;
        } else {
            // Si no es JSON, intentar parsear como YAML simple
            // Esto es muy básico y solo funciona para YAML simple de clave: valor o diccionarios/listas planas
            $lines = explode("\n", $parametrosRaw);
            $tempParams = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || str_starts_with($line, '#')) continue; // Saltar líneas vacías o comentarios
                if (strpos($line, ':') !== false) {
                    list($key, $value) = explode(':', $line, 2);
                    $tempParams[trim($key)] = trim($value);
                } else {
                    // Si no es un par clave-valor, podría ser un elemento de lista, o YAML inválido.
                    // Para simplificar, si no es clave:valor, no lo parseamos más allá y lo tratamos como string si no hay otra opción.
                    // O se marca como error de parsing.
                    $paramsParseError = true; // No pudimos parsear como clave:valor simple
                    break;
                }
            }
            if (!$paramsParseError && !empty($tempParams)) {
                 $parsedParams = $tempParams;
            } else {
                // Si ni JSON ni YAML simple funcionó, asumimos que es texto plano que Ansible podría manejar,
                // o un error que se capturará en syntax-check.
                // Aquí podrías añadir una validación más robusta si PHP tuviera la extensión YAML.
                // Por ahora, lo pasamos como string si no se pudo parsear.
                $parsedParams = $parametrosRaw; // Mantener como string si no se pudo parsear
            }
        }


        // Generar las líneas de parámetros con la indentación correcta
        if (is_array($parsedParams)) {
            foreach ($parsedParams as $paramKey => $paramValue) {
                if (is_array($paramValue)) {
                    // Si el valor es un array, puede ser una lista o un diccionario anidado
                    $yaml .= "        " . htmlspecialchars($paramKey) . ":\n";
                    // Para valores complejos, usar una función auxiliar para la indentación
                    $yaml .= indentYaml(json_encode($paramValue), 10); // Indentación para valores anidados (10 espacios)
                } else {
                    // Si es un valor simple
                    // Añadir comillas si el valor contiene caracteres especiales o es un booleano/número que debe ser string
                    $formattedValue = $paramValue;
                    if (is_bool($paramValue)) {
                        $formattedValue = $paramValue ? 'true' : 'false';
                    } elseif (is_numeric($paramValue) && (string)$paramValue !== $paramValue) {
                        // Si es numérico y no es un string (ej. 0644 puede ser octal, mejor como string)
                        $formattedValue = "'" . $paramValue . "'";
                    } elseif (preg_match('/[[:space:]"\'\[\]\{\},#&*\!\|><%@`]/', $paramValue)) {
                        $formattedValue = "'" . str_replace("'", "''", $paramValue) . "'"; // Escapar comillas simples
                    }
                    $yaml .= "        " . htmlspecialchars($paramKey) . ": " . $formattedValue . "\n";
                }
            }
        } elseif (!empty($parametrosRaw)) {
            // Si no se pudo parsear como array/dict, y hay contenido, lo tratamos como una línea de string literal
            // Esto es una medida de respaldo si el usuario puso algo que no es JSON/YAML simple pero Ansible podría interpretar
            $yaml .= "        " . $parametrosRaw . "\n"; // Podría causar problemas si no es válido YAML
        } else {
            // No hay parámetros
            $yaml .= "        {}\n"; // Añadir diccionario vacío si no hay parámetros, útil para algunos módulos
        }
    }


    // --- INTEGRACIÓN DE LA VALIDACIÓN YAML/Ansible ---

    // Paso 1: Guardar el contenido en un archivo temporal para validación
    if (file_put_contents($tempPlaybookPath, $yaml) === false) {
        $mensaje_estado = "<h2 class='error-message'>❌ Error interno: No se pudo escribir el archivo temporal para validación.</h2>";
        $tipo_mensaje = "error";
    } else {
        // Paso 2: Validar la sintaxis YAML/Ansible usando ansible-playbook --syntax-check
        $command = "ansible-playbook --syntax-check " . escapeshellarg($tempPlaybookPath) . " 2>&1";
        $output = [];
        $return_var = 0;
        exec($command, $output, $return_var);

        // Paso 3: Analizar el resultado de la validación
        if ($return_var === 0) {
            // La sintaxis es correcta, guardar el archivo final
            if (file_put_contents($ruta, $yaml)) {
                $mensaje_estado = "<h2 class='success-message'>✅ Playbook guardado correctamente como <code>" . htmlspecialchars($nombreArchivo) . "</code></h2>";
                $tipo_mensaje = 'success';
            } else {
                $mensaje_estado = "<h2 class='error-message'>❌ Error al guardar el playbook final. Verifique los permisos del servidor.</h2>";
                $tipo_mensaje = 'error';
            }
        } else {
            // La sintaxis es incorrecta, mostrar el error de Ansible
            $errorMessage = implode("\n", $output);
            $mensaje_estado = "<h2 class='error-message'>❌ Error de sintaxis en el playbook generado:<br><pre>" . htmlspecialchars($errorMessage) . "</pre></h2>";
            $tipo_mensaje = 'error';
        }

        // Limpiar el archivo temporal
        if (file_exists($tempPlaybookPath)) {
            unlink($tempPlaybookPath);
        }
    }
}

// Limpiar sesión al final de la creación/intento de creación
// Esto es importante para que no se reintenten crear playbooks con los mismos datos viejos.
session_destroy();

/**
 * Función auxiliar para indentar contenido YAML/JSON.
 * @param string $content El contenido (JSON o YAML) a indentar.
 * @param int $indentSpaces Número de espacios para la indentación.
 * @return string Contenido indentado.
 */
function indentYaml($content, $indentSpaces) {
    $indent = str_repeat(' ', $indentSpaces);
    $lines = explode("\n", $content);
    $indentedLines = [];
    foreach ($lines as $line) {
        if (trim($line) === '') continue; // Ignorar líneas vacías
        $indentedLines[] = $indent . $line;
    }
    return implode("\n", $indentedLines);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Playbook Generado</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Resultado del Playbook</h1>

        <?php echo $mensaje_estado; ?>

        <?php if ($tipo_mensaje === 'success'): // Solo muestra el YAML si se guardó correctamente ?>
            <h2>Contenido del Playbook:</h2>
            <pre class="yaml-output"><?php echo htmlspecialchars($yaml); ?></pre>
        <?php endif; ?>

        <div class="button-group">
            <a href="index.php" class="button">Volver al inicio</a>
            <?php if ($tipo_mensaje === 'error'): // Si hubo un error, ofrecer rehacerlo ?>
                 <a href="crear_playbook.php" class="button secondary">Rehacer creación</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>