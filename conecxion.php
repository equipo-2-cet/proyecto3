<?php
$servername = "localhost"; // o el nombre del servidor si es remoto
$username = "root"; // tu nombre de usuario de MySQL
$password = ""; // tu contraseña de MySQL
$dbname = "tienda_peluches"; // el nombre de la base de datos que creamos

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>