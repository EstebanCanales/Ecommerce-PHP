<?php
session_start();
require_once '../includes/config.php';

// Obtener productos de la base de datos
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Función para obtener el total de artículos en el carrito
function getCartItemsCount() {
    return isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Tienda</title>
    <link href="/css/styles.css" rel="stylesheet">
</head>
<body class="min-h-screen flex flex-col bg-gray-100">
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6 md:justify-start md:space-x-10">
                <div class="flex justify-start lg:w-0 lg:flex-1">
                    <a href="index.php" class="text-xl font-bold text-gray-800">Mi Tienda</a>
                </div>
                <nav class="hidden md:flex space-x-10">
                    <a href="index.php" class="text-base font-medium text-gray-500 hover:text-gray-900">Productos</a>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="admin.php" class="text-base font-medium text-gray-500 hover:text-gray-900">Admin</a>
                    <?php endif; ?>
                    <a href="cart.php" class="text-base font-medium text-gray-500 hover:text-gray-900">Carrito</a>
                </nav>
                <div class="hidden md:flex items-center justify-end md:flex-1 lg:w-0">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="logout.php" class="ml-8 whitespace-nowrap inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            Cerrar sesión
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="whitespace-nowrap text-base font-medium text-gray-500 hover:text-gray-900">
                            Iniciar sesión
                        </a>
                        <a href="register.php" class="ml-8 whitespace-nowrap inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            Registrarse
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Nuestros Productos</h1>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($products as $product): ?>
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h2 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($product['name']); ?></h2>
                            <p class="text-gray-600">$<?php echo number_format($product['price'], 2); ?></p>
                            <form method="post" action="cart.php">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" name="add_to_cart" class="mt-4 w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition duration-300">
                                    Añadir al carrito
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-base text-gray-400">
                &copy; <?php echo date('Y'); ?> Mi Tienda. Todos los derechos reservados.
            </p>
        </div>
    </footer>
</body>
</html>
