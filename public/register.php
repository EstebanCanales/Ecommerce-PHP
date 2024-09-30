<?php
session_start();
require_once '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Usuario registrado con éxito. Por favor, inicia sesión.";
        header("Location: login.php");
        exit();
    } else {
        $error = "Error al registrar el usuario: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Silk road</title>
    <link href="/css/styles.css" rel="stylesheet">
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Registrarse
            </h2>
        </div>
        <form class="mt-8 space-y-6" action="" method="POST">
            <input type="hidden" name="remember" value="true">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="username" class="sr-only">Nombre de usuario</label>
                    <input id="username" name="username" type="text" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Nombre de usuario">
                </div>
                <div>
                    <label for="password" class="sr-only">Contraseña</label>
                    <input id="password" name="password" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Contraseña">
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Registrarse
                </button>
            </div>
        </form>
        <?php
        if (isset($error)) {
            echo "<p class='mt-2 text-center text-sm text-red-600'>$error</p>";
        }
        ?>
        <div class="text-sm text-center">
            <a href="login.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                ¿Ya tienes una cuenta? Inicia sesión
            </a>
        </div>
    </div>
</body>
</html>
