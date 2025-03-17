<?php
session_start();

include 'functions.php';
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
<?php toonNavbar(); ?>
<!--  -->
    <div class="info-container">
        <a href="orderconfirmed.php" class="info-button">bestellingsstatus</a>
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
            setTimeout(showSlides, 3000);
        }
        showSlides();
    </script>

    <?php toonFooter(); ?>
</body>
</html>
