<?php
session_start();
require_once '../includes/config.php';

// Verificar si el usuario es admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image = $_POST['image'];

    $sql = "INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssds", $name, $description, $price, $image);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Producto añadido con éxito.";
    } else {
        $_SESSION['error'] = "Error al añadir el producto: " . $conn->error;
    }

    header("Location: admin.php");
    exit();
}
