<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extintores</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Saira:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=play_circle" />
    <link rel="stylesheet" href="../styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script src="https://kit.fontawesome.com/41fe01d854.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php $url="http://".$_SERVER['HTTP_HOST']."/login.php"?>
    <header>
        
            <div id="head_contain">
                
                    
                    <nav class="menu">
                        
                        <i id="icon_user" class="fa-solid fa-user" style="color:#f2de00;"></i>
                        <a href="<?php echo $url;?>">Acceder al sistema</a>
                        <i class="fa-solid fa-envelope" style="color:#f2de00;"></i>
                        <a href="">Contacto@lobosoft.com</a>
                        <i class="fa-solid fa-phone" style="color:#f2de00;"></i>
                        <a href="">333232-3232</a>
                    </nav>
                
            </div>
                    
        <div class="banner">
            <div class="menu_banner">
                <div class="logo"><img src="../img/logo.png" alt=""></div>
                <div id="menu">
                    <nav>
                        <a href="index.php">INICIO</a>
                        <a href=<?php echo $url;?>>SISTEMA</a>
                        <a href="">DEMO</a>
                        <a href="">COSTOS</a>
                    </nav>
                    <i class="fa-solid fa-bars" id="menu_button" style="color: blue;"></i>
                </div>
            </div>
        
        </div>
    </header>

    <main>
