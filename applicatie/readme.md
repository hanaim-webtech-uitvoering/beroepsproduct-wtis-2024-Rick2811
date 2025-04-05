# 🔐 Beveiligingsdocumentatie – Pizzeria Sole Machina (WTIS)

Deze documentatie behandelt vijf beveiligingsrisico's uit de OWASP Top 10 voor de PHP-webapplicatie Pizzeria Sole Machina.  
Per risico vind je een risicotabel, een uitleg over de mogelijke gevolgen en een samenvatting van de genomen beveiligingsmaatregelen, inclusief voorbeeldcode.

---

## ⚠️ R1: SQL Injection

### Risicotabel

| Onderdeel       | Waarde                                                |
|------------------|--------------------------------------------------------|
| Risico           | SQL-injectie via formulier of logincomponent           |
| Aanvalstechniek  | SQL-broncode injectie                                  |
| Kans             | Hoog                                                   |
| Gevolg           | Volledige toegang tot database, uitlezen of wijzigen   |

### Gevolgen bij doorbraak
Een aanvaller kan gegevens zoals gebruikersnamen, wachtwoorden en bestellingen uit de database uitlezen of manipuleren.

### Beveiligingsmaatregelen
- Gebruik van `prepare()` en `execute()` voor veilige queries
- Nooit gebruikersinput direct in SQL-query’s
- Extra invoercontrole met `is_numeric()` en `htmlspecialchars()`

### Codevoorbeeld
```php
$stmt = $pdo->prepare("SELECT * FROM [User] WHERE username = :username");
$stmt->execute(['username' => $_POST['username']]);
```
# 🔓 R2: Broken Authentication

## Risicotabel

| Onderdeel         | Waarde                                                      |
|-------------------|-------------------------------------------------------------|
| Risico            | R2: Broken Authentication – onveilige sessies of inlog      |
| Aanvalstechniek   | Sessiemisbruik, zwakke wachtwoorden                          |
| Kans              | Middelmatig                                                 |
| Gevolg            | Onbevoegde toegang tot klant- of personeelspagina’s         |

---

## Gevolgen bij een doorbraak

Als een aanvaller erin slaagt om de authenticatie te omzeilen, kan deze inloggen als medewerker of klant. Hierdoor kan hij:
- Bestellingen van anderen inzien of wijzigen
- Bestelstatussen manipuleren
- Klantgegevens stelen
- De privacy van gebruikers ernstig schenden

---

## Beveiligingsmaatregelen in de applicatie

- ✅ Gebruik van `password_hash()` voor veilige opslag van wachtwoorden in de database.
- ✅ Gebruik van `password_verify()` voor het controleren van wachtwoorden tijdens het inloggen.
- ✅ Sessiebeheer via `$_SESSION`, met controle op inlogstatus en gebruikersrol.
- ✅ Beveiligde toegang tot gevoelige pagina’s (bijv. personeelspagina's of orderbeheer).

---

## Codevoorbeelden

**Wachtwoord hashing bij registratie:**
```php
$password = password_hash($_POST['wachtwoord'], PASSWORD_DEFAULT);

Wachtwoord controleren bij inloggen:

if (password_verify($_POST['wachtwoord'], $gebruiker['password'])) {
    $_SESSION['ingelogd'] = true;
    $_SESSION['gebruiker'] = $gebruiker['username'];
    $_SESSION['rol'] = $gebruiker['role'];
}

Toegang tot personeelspagina beveiligen:
if (!isset($_SESSION['ingelogd']) || $_SESSION['rol'] !== 'Personnel') {
    header('Location: inloggen.php');
    exit();
}
```
# 💉 R3: Cross-Site Scripting (XSS)

## Risicotabel

| Onderdeel         | Waarde                                                      |
|-------------------|-------------------------------------------------------------|
| Risico            | R3: Cross-Site Scripting (XSS)                              |
| Aanvalstechniek   | Injectie van scripts via formulierinvoer of URL’s          |
| Kans              | Middelmatig                                                 |
| Gevolg            | Diefstal van sessiegegevens, manipulatie van UI            |

---

## Gevolgen bij een doorbraak

Bij een succesvolle XSS-aanval kan een aanvaller JavaScript-code uitvoeren in de browser van een andere gebruiker. Hierdoor kan hij:
- Sessie-informatie (zoals cookies) stelen
- De interface manipuleren (bijv. valse knoppen of velden)
- Gebruikers omleiden naar phishing-websites
- De reputatie van de website beschadigen

---

## Beveiligingsmaatregelen in de applicatie

- ✅ Gebruik van `htmlspecialchars()` op alle gebruikersinvoer voordat het wordt weergegeven
- ✅ Nooit ruwe gebruikersdata direct in de HTML output zetten
- ✅ Formulieren valideren en beperken tot verwachte types en lengtes
- ✅ Geen inline of dynamische JavaScript op basis van invoer

---

## Codevoorbeelden

**Output van gebruikersdata (bijv. klantnaam):**
```php
<p><?= htmlspecialchars($bestelling['client_name']) ?></p>
Output van status in een tabel:
<td><?= htmlspecialchars($statusTekst[$bestelling['status']] ?? 'Onbekend') ?></td>
```
# 🕵️‍♂️ R4: Insecure Direct Object References (IDOR)

## Risicotabel

| Onderdeel         | Waarde                                                              |
|-------------------|---------------------------------------------------------------------|
| Risico            | R4: Insecure Direct Object References (IDOR)                        |
| Aanvalstechniek   | Handmatig aanpassen van IDs in URL’s (bijv. `?order_id=2`)          |
| Kans              | Hoog                                                                |
| Gevolg            | Onbevoegde toegang tot gegevens van andere gebruikers               |

---

## Gevolgen bij een doorbraak

Een kwaadwillende gebruiker kan in de URL een ander `order_id` invullen, waardoor hij toegang kan krijgen tot bestellingen van andere klanten.  
Dit betekent:
- Overtreding van de privacywet (AVG)
- Vertrouwelijke informatie wordt zichtbaar (adres, naam, bestelling)
- Mogelijkheid tot manipulatie van gegevens (status aanpassen)

---

## Beveiligingsmaatregelen in de applicatie

- ✅ Elke bestelling wordt opgehaald met zowel `order_id` als `client_username`
- ✅ Klanten kunnen alleen hun eigen bestellingen zien
- ✅ Sessiebeveiliging voorkomt dat niet-ingelogde gebruikers überhaupt toegang krijgen

---

## Codevoorbeelden

**Bestelling ophalen met controle op gebruiker:**
```php
$stmt = $pdo->prepare("
    SELECT * FROM Pizza_Order 
    WHERE order_id = :order_id AND client_username = :username
");
$stmt->execute([
    'order_id' => $_GET['order_id'],
    'username' => $_SESSION['gebruiker']
]);


Sessiebeveiliging bovenaan de pagina:
if (!isset($_SESSION['ingelogd']) || $_SESSION['rol'] !== 'Client') {
    header('Location: inloggen.php');
    exit();
}

```
# 🔓 R5: Broken Access Control

## Risicotabel

| Onderdeel         | Waarde                                                                 |
|-------------------|------------------------------------------------------------------------|
| Risico            | R5: Broken Access Control                                              |
| Aanvalstechniek   | Omzeilen van toegangscontroles door URL-manipulatie of force browsing  |
| Kans              | Hoog                                                                   |
| Gevolg            | Ongeautoriseerde toegang tot gevoelige gegevens of functionaliteiten   |

---

## Gevolgen bij een doorbraak

Bij een succesvolle aanval door gebroken toegangscontrole kan een aanvaller:

- Toegang krijgen tot gegevens van andere gebruikers
- Gevoelige informatie wijzigen of verwijderen
- Administratieve functies uitvoeren zonder de juiste rechten
- De integriteit en vertrouwelijkheid van het systeem compromitteren

---

## Beveiligingsmaatregelen in de applicatie

- ✅ **Role-Based Access Control (RBAC):** Implementatie van RBAC om ervoor te zorgen dat gebruikers alleen toegang hebben tot functies die overeenkomen met hun rol.
- ✅ **Controle op serverniveau:** Toegangscontroles worden uitsluitend op de server afgedwongen, niet op de client.
- ✅ **Sessiebeheer:** Sessies worden gevalideerd op authenticiteit en autorisatie bij elke gevoelige actie.
- ✅ **Geen vertrouwelijke informatie in URL's:** Vermijden van het plaatsen van gevoelige gegevens in URL's om manipulatie te voorkomen.

---

## Codevoorbeelden

**Voorbeeld van rolgebaseerde toegangscontrole:**

```php
// Controleer of de gebruiker een 'Admin' rol heeft
if ($_SESSION['rol'] !== 'Admin') {
    // Log de ongeautoriseerde toegangspoging
    error_log("Ongeautoriseerde toegangspoging door gebruiker: " . $_SESSION['gebruikersnaam']);
    // Stuur de gebruiker naar de toegang geweigerd pagina
    header('Location: toegang_geweigerd.php');
    exit();
}
Voorbeeld van sessiebeheer bij gevoelige acties:
// Start de sessie
session_start();

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['ingelogd']) || $_SESSION['ingelogd'] !== true) {
    // Stuur de gebruiker naar de inlogpagina
    header('Location: inloggen.php');
    exit();
}

// Controleer of de CSRF-token geldig is
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    // Ongeldige CSRF-token
    die("Ongeldige aanvraag.");
}
```


## 👤 Testgebruikers

| Rol         | Gebruikersnaam | Wachtwoord     |
|--------------|----------------|----------------|
| Klant        | klant          | ikwilpizza     |
| Personeel    | owner          | wachtwoord     |
