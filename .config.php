<?php
// Configuración del acceso a la base de datos
// Observemos que este método es de lo más inseguro que encontramos,
// es tarea del estudiante securizarlo. 
$host = "localhost"; // Tambien se puede anotar la ip fija del servicor
$user = "usuario";   // Aqui el usuario autorizado del servidor
$pass = "secreto";   // La contraseña que usted usa como root en Ubuntu
$db = "pruebas";     // El nombre de la base de datos

// Establecer conexión
$conn = new mysqli($host, $user, $pass, $db);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
