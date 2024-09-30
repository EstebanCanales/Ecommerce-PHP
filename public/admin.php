<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Asi se hacen bien la sql querys 
$sql = "SELECT COUNT(*) as total_orders, SUM(total_amount) as total_revenue FROM orders";
$result = $conn->query($sql);
$stats = $result->fetch_assoc();

$sql = "SELECT COUNT(*) as total_products FROM products";
$result = $conn->query($sql);
$product_stats = $result->fetch_assoc();

$sql = "SELECT COUNT(*) as total_users FROM users WHERE role = 'user'";
$result = $conn->query($sql);
$user_stats = $result->fetch_assoc();

$sql = "SELECT o.id, o.total_amount, o.created_at, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5";
$result = $conn->query($sql);
$recent_orders = $result->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Silk road</title>
    <link href="/css/styles.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <a href="index.php" class="text-xl font-bold text-gray-800">Silk road</a>
                    <a href="admin.php" class="ml-4 text-gray-500 hover:text-gray-700">Admin</a>
                </div>
                <a href="logout.php" class="text-red-600 hover:text-red-800">Cerrar sesión</a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Panel de Administración</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Total de Órdenes
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        <?php echo $stats['total_orders']; ?>
                    </dd>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Ingresos Totales
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        $<?php echo number_format($stats['total_revenue'], 2); ?>
                    </dd>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Total de Productos
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        <?php echo $product_stats['total_products']; ?>
                    </dd>
                </div>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
            <div class="px-4 py-5 sm:px-6">
                <h2 class="text-lg leading-6 font-medium text-gray-900">
                    Órdenes Recientes
                </h2>
            </div>
            <div class="border-t border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Usuario
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($recent_orders as $order): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo $order['id']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo htmlspecialchars($order['username']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                $<?php echo number_format($order['total_amount'], 2); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $order['created_at']; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h2 class="text-lg leading-6 font-medium text-gray-900">
                    Añadir Nuevo Producto
                </h2>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                <form action="add_product.php" method="POST" class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nombre del Producto</label>
                        <input type="text" name="name" id="name" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                        <textarea name="description" id="description" rows="3" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                    </div>
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">Precio</label>
                        <input type="number" name="price" id="price" step="0.01" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700">URL de la Imagen</label>
                        <input type="url" name="image" id="image" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Añadir Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
