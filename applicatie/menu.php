<?php
session_start();

// Database configuratie
$DB_HOST = "database_server";
$DB_NAME = "pizzeria";
$DB_USER = "sa";
$DB_PASS = "abc123!@#";

// Verbinding maken met de database
try {
    $pdo = new PDO("sqlsrv:server=$DB_HOST;Database=$DB_NAME;Encrypt=no;TrustServerCertificate=yes", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Databaseverbinding mislukt: " . $e->getMessage());
}

// Haal producttypes op
$queryTypes = $pdo->query("SELECT name FROM ProductType");
$productTypes = $queryTypes->fetchAll(PDO::FETCH_ASSOC);

// Haal ingrediënten op
$queryIngredients = $pdo->query("SELECT name FROM Ingredient");
$ingredients = $queryIngredients->fetchAll(PDO::FETCH_ASSOC);

// Haal producten op (gesorteerd per type)
$queryProducts = $pdo->query("SELECT p.name, p.price, pt.name as type 
                               FROM Product p
                               JOIN ProductType pt ON p.type_id = pt.name
                               ORDER BY pt.name ASC");
$products = $queryProducts->fetchAll(PDO::FETCH_ASSOC);

// Haal Product-Ingredient relaties op
$queryProductIngredients = $pdo->query("SELECT pi.product_name, pi.ingredient_name 
                                        FROM Product_Ingredient pi");
$productIngredients = $queryProductIngredients->fetchAll(PDO::FETCH_ASSOC);

// Groepeer ingrediënten per product
$ingredientsPerProduct = [];
foreach ($productIngredients as $pi) {
    $ingredientsPerProduct[$pi['product_name']][] = $pi['ingredient_name'];
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu | Pizzeria di Rick</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="navbar">
        <button onclick="window.location.href='pizzeriaDiRick.php'">Home</button>
        <button onclick="window.location.href='Menu.php'">Menu</button>
        <button onclick="alert('Bestelling plaatsen...')">Bestelling Plaatsen</button>
        <button onclick="window.location.href='inloggen.php'">Inloggen</button>
    </div>

    <div class="container">
        <!-- Producten met ingrediënten en extra opties -->
        <div class="section">
            <h2>Producten</h2>
            <table>
                <tr>
                    <th>Naam</th>
                    <th>Prijs (€)</th>
                    <th>Type</th>
                    <th>Eventuele extra ingrediënten</th>
                    <th>Extra Ingrediënten</th>
                    <th>Toevoegen</th>
                </tr>
                <?php 
                $lastType = "";
                foreach ($products as $product) : 
                    if ($product['type'] !== $lastType) {
                        echo "<tr><td colspan='6' class='product-group'><strong>{$product['type']}</strong></td></tr>";
                        $lastType = $product['type'];
                    }
                ?>
                <tr>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= number_format($product['price'], 2, ',', '.') ?> €</td>
                    <td><?= htmlspecialchars($product['type']) ?></td>
                    <td>
                        <?= isset($ingredientsPerProduct[$product['name']]) 
                            ? implode(', ', $ingredientsPerProduct[$product['name']]) 
                            : "Geen ingrediënten" ?>
                    </td>
                    <td>
                        <?php if ($product['type'] == 'Pizza' || $product['type'] == 'Maaltijd'): ?>
                            <select class="extra-ingredients" data-name="<?= htmlspecialchars($product['name']) ?>">
                                <option value="">-- Kies extra --</option>
                                <?php 
                                if (isset($ingredientsPerProduct[$product['name']])) {
                                    foreach ($ingredientsPerProduct[$product['name']] as $ingredient) {
                                        echo "<option value='" . htmlspecialchars($ingredient) . "'>" . htmlspecialchars($ingredient) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        <?php else: ?>
                            <span>Geen extra ingrediënten</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="add-to-cart" data-name="<?= htmlspecialchars($product['name']) ?>" 
                                data-price="<?= $product['price'] ?>">➕</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <!-- Winkelwagen -->
        <div class="section">
            <h2>Winkelwagen</h2>
            <ul id="cart-list"></ul>
            <p><strong>Totaalprijs:</strong> €<span id="total-price">0.00</span></p>

            <!-- Formulier voor afrekenen -->
            <form id="checkout-form" action="orderCustomer.php" method="post">
                <input type="hidden" name="cart-data" id="cart-data">
                <button type="submit" id="checkout-button" style="display: none;">Afrekenen</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2025 Pizzeria di Rick | <a href="privacystatement.php">Privacyverklaring</a></p>
    </footer>

    <script>
        let cart = [];

        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                let productName = this.getAttribute('data-name');
                let productPrice = parseFloat(this.getAttribute('data-price'));
                let extraIngredientSelect = document.querySelector(`.extra-ingredients[data-name='${productName}']`);
                let extraIngredient = extraIngredientSelect ? extraIngredientSelect.value : "";

                let existingProduct = cart.find(item => item.name === productName && item.extra === extraIngredient);
                if (existingProduct) {
                    existingProduct.quantity += 1;
                } else {
                    cart.push({ name: productName, price: productPrice, quantity: 1, extra: extraIngredient });
                }

                updateCart();
            });
        });

        function updateCart() {
            let cartList = document.getElementById('cart-list');
            let totalPriceElement = document.getElementById('total-price');
            let checkoutButton = document.getElementById('checkout-button');
            let cartDataInput = document.getElementById('cart-data');
            
            cartList.innerHTML = ""; 
            let totalPrice = 0;

            cart.forEach(item => {
                totalPrice += item.price * item.quantity;
                let extraText = item.extra ? ` (Extra: ${item.extra})` : "";

                let listItem = document.createElement('li');
                listItem.innerHTML = `${item.name}${extraText} - €${item.price.toFixed(2)} x ${item.quantity}
                    <button class="remove-item" data-name="${item.name}" data-extra="${item.extra}">❌</button>`;
                cartList.appendChild(listItem);
            });

            totalPriceElement.textContent = totalPrice.toFixed(2);

            // Toon/verberg afreken-knop
            checkoutButton.style.display = cart.length > 0 ? "block" : "none";

            // Zet de winkelwagen data in het verborgen inputveld als JSON
            cartDataInput.value = JSON.stringify(cart);
        }
    </script>

</body>
</html>
