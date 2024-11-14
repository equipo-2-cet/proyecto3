<?php
session_start();

$host = 'localhost';
$dbname = 'tienda_peluches';
$username = 'root';
$password = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Error al conectar a la base de datos: ' . $e->getMessage();
}

$mensajeCompra = "";

// Agregar al carrito
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comprar'])) {
    $peluche = $_POST['peluche'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];

    $_SESSION['carrito'][] = [
        'peluche' => $peluche,
        'descripcion' => $descripcion,
        'precio' => $precio
    ];
}

// Eliminar un producto del carrito
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar'])) {
    $indice = $_POST['indice'];
    if (isset($_SESSION['carrito'][$indice])) {
        unset($_SESSION['carrito'][$indice]);
        $_SESSION['carrito'] = array_values($_SESSION['carrito']);
    }
}

// Confirmar la compra y vaciar el carrito
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirmar_compra'])) {
    $nombre_cliente = $_POST['nombre_cliente'];
    $carrito = $_SESSION['carrito'] ?? [];

    foreach ($carrito as $item) {
        $sql = "INSERT INTO compras (nombre_cliente, peluche, descripcion, precio) VALUES (:nombre_cliente, :peluche, :descripcion, :precio)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre_cliente', $nombre_cliente);
        $stmt->bindParam(':peluche', $item['peluche']);
        $stmt->bindParam(':descripcion', $item['descripcion']);
        $stmt->bindParam(':precio', $item['precio']);
        $stmt->execute();
    }

    $mensajeCompra = "¡Gracias por tu compra, $nombre_cliente!";
    unset($_SESSION['carrito']);
}

$carrito = $_SESSION['carrito'] ?? [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Peluches</title>
    <style>
        /* Estilos Generales */
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        header { background-color: #FF1493; color: white; padding: 10px; text-align: center; }
        .mensaje-compra { background-color: #32CD32; color: white; padding: 10px; text-align: center; margin-top: 10px; display: <?php echo empty($mensajeCompra) ? 'none' : 'block'; ?>; }
        .peluches { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; margin: 20px; }
        .peluche { border: 1px solid #ddd; padding: 10px; text-align: center; width: 200px; }
        .peluche img { max-width: 100%; height: auto; }
        .comprar-button, .ver-carrito-button, .confirmar-compra { background-color: #FF1493; border: none; color: white; padding: 10px; cursor: pointer; }
        .ver-carrito-button { padding: 10px 20px; font-size: 16px; border-radius: 5px; }
        
        /* Modal (Carrito de Compras) */
        .carrito-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; }
        .carrito { background-color: white; border-radius: 8px; padding: 20px; width: 90%; max-width: 400px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3); }
        .carrito h2 { margin-top: 0; }
        .carrito-item { border-bottom: 1px solid #ddd; padding: 10px 0; display: flex; justify-content: space-between; }
        .carrito-item button { background-color: red; color: white; border: none; padding: 5px; cursor: pointer; border-radius: 4px; }
        .cerrar-carrito { background-color: #FF1493; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; margin-top: 10px; }

    </style>
</head>
<body>
    <header>
        <h1>Bienvenido a la Tienda de Peluches</h1>
        <button class="ver-carrito-button" onclick="mostrarCarrito()">🛒 Ver Carrito</button>
    </header>

    <div class="mensaje-compra"><?php echo $mensajeCompra; ?></div>

    <!-- Lista de peluches -->
    <section class="peluches">
        <div class="peluche">
            <img src="imgpro/produc1.png" alt="Peluche 1">
            <h3>Peluche Panda Escandalosos</h3>
            <p><strong>Descripción:</strong> Peluche del personaje de panda de la caricatura escandalosos.</p>
            <p><strong>Precio:</strong> $49.99</p>
            <form action="" method="POST">
                <input type="hidden" name="peluche" value="Peluche Oso Amoroso">
                <input type="hidden" name="descripcion" value="Peluche suave y tierno.">
                <input type="hidden" name="precio" value="15.99">
                <button type="submit" name="comprar" class="comprar-button">Comprar</button>
            </form>
        </div>

        <!-- Otro peluche -->
        <div class="peluche">
            <img src="imgpro/produc2.png" alt="Peluche 2">
            <h3>Peluche Kuromi</h3>
            <p><strong>Descripción:</strong> Peluche de Kuromi.</p>
            <p><strong>Precio:</strong> $39.99</p>
            <form action="" method="POST">
                <input type="hidden" name="peluche" value="Peluche Conejo Alegre">
                <input type="hidden" name="descripcion" value="Peluche suave, perfecto para abrazos.">
                <input type="hidden" name="precio" value="12.99">
                <button type="submit" name="comprar" class="comprar-button">Comprar</button>
            </form>
        </div>
        <!-- Peluche 3 -->
        <div class="peluche">
            <img src="imgpro/produc3.png" alt="Peluche 3" class="peluche-img">
            <h3>Peluche Pikachu</h3>
            <p><strong>Descripción:</strong> Peluche de pikachu del anime pokemon.</p>
            <p><strong>Precio:</strong> $99.99</p>
            <form action="" method="POST">
                <input type="hidden" name="peluche" value="Peluche Gato Felíz">
                <input type="hidden" name="descripcion" value="Un peluche muy suave y tierno.">
                <input type="hidden" name="precio" value="10.99">
                <button type="submit" name="comprar" class="comprar-button">Comprar</button>
            </form>
        </div>

        <!-- Peluche 4 -->
        <div class="peluche">
            <img src="imgpro/produc4.png" alt="Peluche 4" class="peluche-img">
            <h3>Peluche Capibara</h3>
            <p><strong>Descripción:</strong> Peluche de capibara.</p>
            <p><strong>Precio:</strong> $109.99</p>
            <form action="" method="POST">
                <input type="hidden" name="peluche" value="Peluche Gato Dormilón">
                <input type="hidden" name="descripcion" value="Gato mullido y tranquilo">
                <input type="hidden" name="precio" value="14.99">
                <button type="submit" name="comprar" class="comprar-button">Comprar</button>
            </form>
        </div>

        <!-- Peluche 5 -->
        <div class="peluche">
            <img src="imgpro/produc5.png" alt="Peluche 5" class="peluche-img">
            <h3>Peluche Pou sad</h3>
            <p><strong>Descripción:</strong>Peluche adorable de un pou triste.</p>
            <p><strong>Precio:</strong> $50.50</p>
            <form action="" method="POST">
                <input type="hidden" name="peluche" value="Peluche Panda Risueño">
                <input type="hidden" name="descripcion" value="Panda adorable y esponjoso">
                <input type="hidden" name="precio" value="20.50">
                <button type="submit" name="comprar" class="comprar-button">Comprar</button>
            </form>
        </div>

        <!-- Peluche 6 -->
        <div class="peluche">
            <img src="imgpro/produc6.png" alt="Peluche 6" class="peluche-img">
            <h3>Cinnamonrol</h3>
            <p><strong>Descripción:</strong> Peluche de cinnamonrol de los amigos de hello kitty.</p>
            <p><strong>Precio:</strong> $50.00</p>
            <form action="" method="POST">
                <input type="hidden" name="peluche" value="Peluche León Valiente">
                <input type="hidden" name="descripcion" value="León pequeño y valiente">
                <input type="hidden" name="precio" value="22.00">
                <button type="submit" name="comprar" class="comprar-button">Comprar</button>
            </form>
        </div>

        <!-- Peluche 7 -->
        <div class="peluche">
            <img src="imgpro/produc7.png" alt="Peluche 7" class="peluche-img">
            <h3>Peluche Pokeball</h3>
            <p><strong>Descripción:</strong> Peluche de una pokeball para atraparlos a todos.</p>
            <p><strong>Precio:</strong> $70.75</p>
            <form action="" method="POST">
                <input type="hidden" name="peluche" value="Peluche Perro Juguetón">
                <input type="hidden" name="descripcion" value="Perro alegre y suave">
                <input type="hidden" name="precio" value="13.75">
                <button type="submit" name="comprar" class="comprar-button">Comprar</button>
            </form>
        </div>

        <!-- Peluche 8 -->
        <div class="peluche">
            <img src="imgpro/produc8.png" alt="Peluche 8" class="peluche-img">
            <h3>Peluche Hello Kitty </h3>
            <p><strong>Descripción:</strong> Peluche de el personaje de la gatita hello kitty.</p>
            <p><strong>Precio:</strong> $60.00</p>
            <form action="" method="POST">
                <input type="hidden" name="peluche" value="Peluche Elefante Dulce">
                <input type="hidden" name="descripcion" value="Elefante adorable y pequeño">
                <input type="hidden" name="precio" value="16.00">
                <button type="submit" name="comprar" class="comprar-button">Comprar</button>
            </form>
        </div>

        <!-- Peluche 9 -->
        <div class="peluche">
            <img src="imgpro/produc9.png" alt="Peluche 9" class="peluche-img">
            <h3>Peluche Bonnie Fnaf</h3>
            <p><strong>Descripción:</strong> Peluche del conejo aterrador Bonnie del juego FNAF.</p>
            <p><strong>Precio:</strong> $150.50</p>
            <form action="" method="POST">
                <input type="hidden" name="peluche" value="Peluche Pingüino Agradable">
                <input type="hidden" name="descripcion" value="Pingüino pequeño y amistoso">
                <input type="hidden" name="precio" value="11.50">
                <button type="submit" name="comprar" class="comprar-button">Comprar</button>
            </form>
        </div>

        <!-- Peluche 10 -->
        <div class="peluche">
            <img src="imgpro/produc10.jpg" alt="Peluche 10" class="peluche-img">
            <h3>Peluche Gato Tiburon Kawai</h3>
            <p><strong>Descripción:</strong>Peluche de un adorable gato con traje de tiburon.</p>
            <p><strong>Precio:</strong> $75.00</p>
            <form action="" method="POST">
                <input type="hidden" name="peluche" value="Peluche Unicornio Brillante">
                <input type="hidden" name="descripcion" value="Unicornio mágico y brillante">
                <input type="hidden" name="precio" value="25.00">
                <button type="submit" name="comprar" class="comprar-button">Comprar</button>
            </form>
        </div>
         <!-- Peluche 11 -->
         <div class="peluche">
            <img src="imgpro/produc11.jpg" alt="Peluche 11" class="peluche-img">
            <h3>Peluche Oso Gigante</h3>
            <p><strong>Descripción:</strong> Un grande pero adorable ositolisto para que lo abraces.</p>
            <p><strong>Precio:</strong> $75.00</p>
            <form action="" method="POST">
                <input type="hidden" name="peluche" value="Peluche Unicornio Brillante">
                <input type="hidden" name="descripcion" value="Unicornio mágico y brillante">
                <input type="hidden" name="precio" value="25.00">
                <button type="submit" name="comprar" class="comprar-button">Comprar</button>
            </form>
        </div>
         <!-- Peluche 12 -->
         <div class="peluche">
            <img src="imgpro/produc12.png" alt="Peluche 12" class="peluche-img">
            <h3>Peluche de Crafty Napp</h3>
            <p><strong>Descripción:</strong> Peluche del sonriente unicornio Crafty Napp del juego poppy playtime.</p>
            <p><strong>Precio:</strong> $25.00</p>
            <form action="" method="POST">
                <input type="hidden" name="peluche" value="Peluche Unicornio Brillante">
                <input type="hidden" name="descripcion" value="Unicornio mágico y brillante">
                <input type="hidden" name="precio" value="25.00">
                <button type="submit" name="comprar" class="comprar-button">Comprar</button>
            </form>
        </div>
    </section>

    <!-- Modal Carrito de Compras -->
    <div class="carrito-overlay" id="carrito-overlay">
        <div class="carrito">
            <h2>Carrito de Compras</h2>
            <?php if (!empty($carrito)) : ?>
                <?php foreach ($carrito as $indice => $item) : ?>
                    <div class="carrito-item">
                        <div>
                            <strong><?php echo $item['peluche']; ?></strong><br>
                            <?php echo $item['descripcion']; ?><br>
                            <strong>Precio:</strong> $<?php echo $item['precio']; ?>
                        </div>
                        <form action="" method="POST" style="display:inline;">
                            <input type="hidden" name="indice" value="<?php echo $indice; ?>">
                            <button type="submit" name="eliminar">X</button>
                        </form>
                    </div>
                <?php endforeach; ?>
                <form action="" method="POST">
                    <label for="nombre_cliente">Nombre del Cliente:</label>
                    <input type="text" id="nombre_cliente" name="nombre_cliente" required>
                    <button type="submit" name="confirmar_compra" class="confirmar-compra">Confirmar Compra</button>
                </form>
            <?php else : ?>
                <p>El carrito está vacío.</p>
            <?php endif; ?>
            <button class="cerrar-carrito" onclick="cerrarCarrito()">Cerrar Carrito</button>
        </div>
    </div>

    <script>
        function mostrarCarrito() {
            document.getElementById('carrito-overlay').style.display = 'flex';
        }
        function cerrarCarrito() {
            document.getElementById('carrito-overlay').style.display = 'none';
        }
    </script>
</body>
</html>
