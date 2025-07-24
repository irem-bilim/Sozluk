<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sözlük</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap" rel="stylesheet">    
    <link rel="stylesheet" href="css/style.css"> 
</head>
<body>
    <main>
        <header>
         <h1>Türkçe Sözlük</h1>
        </header>
        <div class="arama-kutusu">
            <input type="text" id="aramaInput" placeholder="Kelime ara...">
            <button id="aramaButon">Ara</button>
            <div id="onerilerAlani" class="oneriler-kutusu"></div>
        </div>
        <div id="sonucAlani">
        </div>
    </main>
    <button id="scrollToTopBtn" title="Başa Dön">↑</button> 
    <script src="js/script.js"></script>
</body>
</html>