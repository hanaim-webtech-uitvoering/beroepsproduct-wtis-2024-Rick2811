<?php
session_start();
include 'functions.php';

$pdo = connectToDatabase();

$queryTypes = $pdo->query("SELECT name FROM ProductType");
$productTypes = $queryTypes->fetchAll(PDO::FETCH_ASSOC);

$queryProducts = $pdo->query("SELECT p.name, p.price, pt.name as type, p.type_id
                               FROM Product p
                               JOIN ProductType pt ON p.type_id = pt.name
                               ORDER BY pt.name ASC");
$products = $queryProducts->fetchAll(PDO::FETCH_ASSOC);

$queryProductIngredients = $pdo->query("SELECT pi.product_name, pi.ingredient_name 
                                        FROM Product_Ingredient pi");
$productIngredients = $queryProductIngredients->fetchAll(PDO::FETCH_ASSOC);

$ingredientsPerProduct = [];
foreach ($productIngredients as $pi) {
    $ingredientsPerProduct[$pi['product_name']][] = $pi['ingredient_name'];
}

toonHeader('Menu');
?>

<div class="container">
    <div class="section">
        <h2>Producten</h2>
        <table>
            <tr>
                <th>Naam</th>
                <th>Prijs (€)</th>
                <th>Type</th>
                <th>Ingrediënten</th>
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
                    <?php if (isset($ingredientsPerProduct[$product['name']])): ?>
                        <select class="extra-ingredients" data-name="<?= htmlspecialchars($product['name']) ?>">
                            <option value="">-- Kies extra --</option>
                            <?php 
                            foreach ($ingredientsPerProduct[$product['name']] as $ingredient) {
                                echo "<option value='" . htmlspecialchars($ingredient) . "'>" . htmlspecialchars($ingredient) . "</option>";
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

    <div class="section">
        <h2>Winkelwagen</h2>
        <ul id="cart-list"></ul>
        <p><strong>Totaalprijs:</strong> €<span id="total-price">0.00</span></p>

        <form id="checkout-form" action="orderCustomer.php" method="post">
            <input type="hidden" name="cart_data" id="cart_data">
            <button type="submit" id="checkout-button" style="display: none;">Afrekenen</button>
        </form>
    </div>
</div>

<script>
    let cart = [];

    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            let productName = this.getAttribute('data-name');
            let productPrice = parseFloat(this.getAttribute('data-price'));
            let extraIngredientSelect = document.querySelector(`.extra-ingredients[data-name='${productName}']`);
            let extraIngredient = extraIngredientSelect && extraIngredientSelect.value !== "" ? extraIngredientSelect.value : "Geen extra";

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
        let cartDataInput = document.getElementById('cart_data');
        
        cartList.innerHTML = ""; 
        let totalPrice = 0;

        cart.forEach(item => {
            totalPrice += item.price * item.quantity;
            let extraText = item.extra !== "Geen extra" ? ` (Extra: ${item.extra})` : "";

            let listItem = document.createElement('li');
            listItem.innerHTML = `${item.name}${extraText} - €${item.price.toFixed(2)} x ${item.quantity}
                <button class="remove-item" data-name="${item.name}" data-extra="${item.extra}">❌</button>`;
            cartList.appendChild(listItem);
        });

        totalPriceElement.textContent = totalPrice.toFixed(2);
        checkoutButton.style.display = cart.length > 0 ? "block" : "none";
        cartDataInput.value = JSON.stringify(cart);
    }
</script>

<?php toonFooter(); ?>
</body>
</html>
