<?php
$host = "localhost";
$user = "root";
$password = "Alejandra20."; // sin contraseña
$db = "facturacion";

$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>