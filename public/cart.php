<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Manejar la acción de añadir al carrito
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }
}

// Procesar la compra
if (isset($_POST['checkout'])) {
    $total_amount = 0;
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $sql = "SELECT price FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $total_amount += $product['price'] * $quantity;
    }

    // Crear la orden
    $sql = "INSERT INTO orders (user_id, total_amount) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("id", $_SESSION['user_id'], $total_amount);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Insertar los items de la orden
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $sql = "SELECT price FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $product['price']);
        $stmt->execute();
    }

    // Limpiar el carrito
    $_SESSION['cart'] = [];
    $message = "¡Compra realizada con éxito! Número de orden: " . $order_id;
}

// Obtener los productos en el carrito
$cart_items = [];
$total = 0;
foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $product['quantity'] = $quantity;
    $product['subtotal'] = $product['price'] * $quantity;
    $cart_items[] = $product;
    $total += $product['subtotal'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito - Mi Tienda</title>
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
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="admin.php" class="text-base font-medium text-gray-500 hover:text-gray-900">Admin</a>
                    <?php endif; ?>
                    <a href="cart.php" class="text-base font-medium text-gray-500 hover:text-gray-900">Carrito</a>
                </nav>
                <div class="hidden md:flex items-center justify-end md:flex-1 lg:w-0">
                    <a href="logout.php" class="ml-8 whitespace-nowrap inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Cerrar sesión
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Tu Carrito</h1>
            <?php if (isset($message)): ?>
                <p class="mb-4 text-green-600"><?php echo $message; ?></p>
            <?php endif; ?>
            <?php if (empty($cart_items)): ?>
                <p class="text-gray-600">Tu carrito está vacío.</p>
            <?php else: ?>
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full" src="<?php echo htmlspecialchars($item['image']); ?>" alt="">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($item['name']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">$<?php echo number_format($item['price'], 2); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo $item['quantity']; ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">$<?php echo number_format($item['subtotal'], 2); ?></div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-right font-bold">Total:</td>
                                <td class="px-6 py-4 whitespace-nowrap font-bold">$<?php echo number_format($total, 2); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <form method="post" action="" class="mt-8">
                    <button type="submit" name="checkout" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Realizar Compra
                    </button>
                </form>
            <?php endif; ?>
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
