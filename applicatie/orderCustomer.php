<?php
session_start();

include 'functions.php';

$ingelogd = isset($_SESSION['ingelogd']) && $_SESSION['ingelogd'] === true;
$gebruikersnaam = $ingelogd ? $_SESSION['gebruiker'] : "Gast";
$adres = $ingelogd && isset($_SESSION['adres']) ? $_SESSION['adres'] : "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cart_data'])) {
        $_SESSION['cart'] = json_decode($_POST['cart_data'], true);
    }

    if (isset($_POST['remove_item'])) {
        $index = $_POST['remove_item'];
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }

    if (isset($_POST['decrease_quantity'])) {
        $index = $_POST['decrease_quantity'];
        if ($_SESSION['cart'][$index]['quantity'] > 1) {
            $_SESSION['cart'][$index]['quantity']--;
        } else {
            unset($_SESSION['cart'][$index]);
        }
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }

    if (isset($_POST['increase_quantity'])) {
        $index = $_POST['increase_quantity'];
        $_SESSION['cart'][$index]['quantity']++;
    }
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

toonHeader('Bestelling Overzicht');
?>

<div class="container">
    <h2>Jouw Bestelling</h2>

    <?php if (empty($_SESSION['cart'])): ?>
        <p>Je winkelwagen is leeg. <a href="menu.php">Ga naar het menu</a></p>
    <?php else: ?>
        <table>
            <tr>
                <th>Product</th>
                <th>Aantal</th>
                <th>Prijs per stuk (€)</th>
                <th>Totaal (€)</th>
                <th>Extra Ingrediënten</th>
            </tr>
            <?php 
            $totaalprijs = 0;
            foreach ($_SESSION['cart'] as $index => $item) : 
                $totaalprijs += $item['price'] * $item['quantity'];
            ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <button type="submit" name="decrease_quantity" value="<?= $index ?>">➖</button>
                        </form>
                        <?= $item['quantity'] ?>
                        <form method="post" style="display:inline;">
                            <button type="submit" name="increase_quantity" value="<?= $index ?>">➕</button>
                        </form>
                    </td>
                    <td><?= number_format($item['price'], 2, ',', '.') ?> €</td>
                    <td><?= number_format($item['price'] * $item['quantity'], 2, ',', '.') ?> €</td>
                    <td><?= !empty($item['extra']) ? htmlspecialchars($item['extra']) : 'Geen extra' ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <p><strong>Totaalprijs:</strong> €<?= number_format($totaalprijs, 2, ',', '.') ?></p>

        <form action="orderconfirmed.php" method="post">
            <label for="naam">Naam:</label>
            <input type="text" name="naam" required value="<?= $ingelogd ? htmlspecialchars($gebruikersnaam) : '' ?>">
            
            <label for="adres">Adres:</label>
            <input type="text" name="adres" required value="<?= htmlspecialchars($adres) ?>" <?= $ingelogd ? 'readonly' : '' ?>>
            
            <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                <input type="hidden" name="producten[<?= $index ?>][name]" value="<?= htmlspecialchars($item['name']) ?>">
                <input type="hidden" name="producten[<?= $index ?>][quantity]" value="<?= $item['quantity'] ?>">
                <input type="hidden" name="producten[<?= $index ?>][price]" value="<?= $item['price'] ?>">
                <input type="hidden" name="producten[<?= $index ?>][extra]" value="<?= htmlspecialchars($item['extra']) ?>">
            <?php endforeach; ?>

            <button type="submit">✅ Bestelling Plaatsen</button>
        </form>
    <?php endif; ?>
</div>

<?php toonFooter(); ?>
</body>
</html>
