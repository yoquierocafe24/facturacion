<?php
session_start();
include "../config/conexion.php";

 if (!isset($_SESSION['intentos'])) {
    $_SESSION['intentos'] = 0;
}

if ($_SESSION['intentos'] >= 3) {
    echo json_encode([
        "success" => false,
        "mensaje" => "Demasiados intentos. Intente más tarde."
    ]);
    exit();
}
$data = json_decode(file_get_contents("php://input"), true);

$usuario = $data['usuario'];
$password = $data['password'];

$sql = "SELECT u.*, r.nombre_rol 
        FROM usuarios u
        INNER JOIN roles r ON u.id_rol = r.id_rol
        WHERE u.usuario = ? AND u.estado = 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $user = $result->fetch_assoc();

    // 🔐 Verificar contraseña encriptada
    if (password_verify($password, $user['password'])) {

        $_SESSION['id_usuario'] = $user['id_usuario'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['rol'] = $user['nombre_rol'];

        echo json_encode([
            "success" => true,
            "rol" => $user['nombre_rol']
        ]);

    } else {
        echo json_encode(["success" => false, "mensaje" => "Contraseña incorrecta"]);
    }

} else {
    echo json_encode(["success" => false, "mensaje" => "Usuario no encontrado"]);
}
?>