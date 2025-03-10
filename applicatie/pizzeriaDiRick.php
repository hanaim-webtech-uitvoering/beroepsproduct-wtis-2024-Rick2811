<?php
    // Start PHP-script (optioneel voor toekomstige uitbreiding)
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frontend Pagina</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="navbar">
        <button onclick="window.location.href='pizzeriaDiRick.php'">Home</button>
        <button onclick="window.location.href='Menu.php'">Menu</button>
        <button onclick="alert('Bestelling plaatsen...')">Bestelling Plaatsen</button>
        <button onclick="window.location.href='inloggen.php'">Inloggen</button>
    </div>
    
    <h1 class="welcome-text">Welkom bij Pizza di Rick - De keuze is reuze!</h1>
    
    <div class="slideshow-container">
        <div class="slide"><img src="images/pizza1.jpg" alt="Pizza 1"></div>
        <div class="slide"><img src="images/pizza2.jpg" alt="Pizza 2"></div>
        <div class="slide"><img src="images/pizza3.jpg" alt="Pizza 3"></div>
    </div>

    <script>
        let slideIndex = 0;
        function showSlides() {
            let slides = document.querySelectorAll(".slide");
            slides.forEach(slide => slide.style.display = "none");
            slideIndex++;
            if (slideIndex > slides.length) { slideIndex = 1; }
            slides[slideIndex - 1].style.display = "block";
            setTimeout(showSlides, 3000); // Wissel elke 3 seconden
        }
        showSlides();
    </script>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2025 Pizzeria di Rick | <a href="privacystatement.php">Privacyverklaring</a></p>
    </footer>
</body>
</html>
