<?php
require_once '.config.php';


// php hace automáticamente que los datos enviados mediante el formulario, se almacenen en 
// $data, asi: $data = $_POST['telefono'], esto pasa internamente sin intervención del usuario
// Función para limpiar datos
function limpiar($data) {             // $data es una variable mutable. Contiene un string
    $data = trim($data);              // limpia de espacios vacíos al principio y final de $data
    $data = stripslashes($data);      // Elimina barras invertidas de escape en contenido de $data
    $data = htmlspecialchars($data);  // Carácteres especiales se convierten a entidades html
    return $data;                     // La función recibe un string de $data. Si no hat return, la
    }                                 // función deja de hacer su trabajo y no hay sanitización.

// VARIABLES PARA EL FORMULARIO
$id = $nombre = $apellido = $telefono = "";
$operacion = "crear";
$mensaje = "";

// PROCESAR OPERACIONES CRUD
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Limpiar datos de entrada
    $nombre = limpiar($_POST["nombre"] ?? "");
    $apellido = limpiar($_POST["apellido"] ?? "");
    $telefono = limpiar($_POST["telefono"] ?? "");
    $id = $_POST["id"] ?? "";

    // Determinar qué operación se está realizando
    if (isset($_POST["buscar"])) {
        // OPERACIÓN DE BÚSQUEDA
        if(!empty($telefono)) {
            $stmt = $conn->prepare("SELECT * FROM usuarios WHERE telefono = ?");
            $stmt->bind_param("s", $telefono);
            $stmt->execute();
            $resultado = $stmt->get_result();
            
            if($resultado->num_rows > 0) {
                $usuario = $resultado->fetch_assoc();
                $id = $usuario["id"];
                $nombre = $usuario["nombre"];
                $apellido = $usuario["apellido"];
                $telefono = $usuario["telefono"];
                $operacion = "editar";
                $mensaje = "Usuario encontrado!";
            } else {
                $mensaje = "No se encontró usuario con ese teléfono";
                $nombre = $apellido = "";
                $operacion = "crear";
            }
            $stmt->close();
        } else {
            $mensaje = "Ingrese un teléfono para buscar";
        }
    } 
    elseif (isset($_POST["guardar"])) {
        // OPERACIÓN DE GUARDAR/ACTUALIZAR
        if (empty($nombre) || empty($apellido) || empty($telefono)) {
            $mensaje = "Todos los campos son requeridos para guardar";
        } else {
            if (empty($id)) {
                // Crear nuevo registro
                $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, telefono) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $nombre, $apellido, $telefono);
            } else {
                // Actualizar registro existente
                $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, apellido=?, telefono=? WHERE id=?");
                $stmt->bind_param("sssi", $nombre, $apellido, $telefono, $id);
            }
            
            if ($stmt->execute()) {
                $mensaje = "Registro guardado correctamente!";
                $id = $nombre = $apellido = $telefono = "";
                $operacion = "crear";
            } else {
                $mensaje = "Error al guardar: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    elseif (isset($_POST["eliminar"])) {
        // OPERACIÓN DE ELIMINAR
        if (!empty($id)) {
            $stmt = $conn->prepare("DELETE FROM usuarios WHERE id=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $mensaje = "Registro eliminado correctamente!";
                $id = $nombre = $apellido = $telefono = "";
                $operacion = "crear";
            } else {
                $mensaje = "Error al eliminar: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    elseif (isset($_POST["nuevo"])) {
        // OPERACIÓN NUEVO
        $id = $nombre = $apellido = $telefono = "";
        $operacion = "crear";
    }
}


?>
