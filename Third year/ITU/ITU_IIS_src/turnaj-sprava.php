<?php 
/**
 * Author: Tomáš Souček, xsouce15
 */
include 'utils.php' ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/fontawesome/css/all.css" rel="stylesheet">
        <link href="css/normalize.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
      
        <title>PUCIS | Turnaje</title>
        <style>
            .disabled::before
            {   
                content: " ";
                background: rgba(0, 0, 0, 0.3);
                width: 100%;
                height: 100%;
                display: block;
                z-index: 5;
                position: absolute;
                box-sizing: border-box;
                top: 0;
                left: 0;
                border-radius: 15px;
                cursor: not-allowed;
            }
            .tile.disabled
            {
                opacity: 0.5;
                user-select: none;
            }
            .tile
            {
                border: none;
                box-shadow: 0 0 5px #ccc;
                border-radius: 15px;
                padding: 1.5em;
                text-align: center;
                height: 100%;
                width: 100%;
            }
            .tile i
            {
                font-size: 4em;
                font-style: normal;
            }
            .tile h3
            {
                margin: 1em 0;
                font-size: 1.5em;
            }
            .tile p
            {
                color: #555;
                position: static;
                display: inline;
                opacity: 1;
                font-weight: 400;
                
            }
            .tile:last-of-type i
            {
                font-size: 3.2em;
            }
            .tile:hover
            {
                background: #eee;
            }
            h1
            {
                text-align: left;
                margin-bottom: 2em;
            }
            h2
            {
                font-size: 2em;
            }
            main a
            {
                text-decoration: none;
                color: #555;
            }
        </style>
    </head>
<body>
    <?php 
        print_notifications();
        print_sidenav(); 
    ?>

    <main>
        <h1>Správa turnaje</h1>
        <h2 id="nazev"></h2>
        <div class="flex">
            <a href="turnaj-edit.php">
                <div class="tile">
                    <i class="fa-sharp fa-solid fa-gears"></i>
                    <h3>Upravit nastavení</h3>
                    <p>Zde můžete upravit nastavení jako jsou název turnaje, počet hráčů, ...</p>
                </div>
            </a>

            <a href="turnaj-approve.php">
                <div class="tile">
                    <i class="fa-solid fa-thumbs-up"></i>
                    <h3>Schválit týmy/hráče</h3>
                    <p>Schvalování hráčů do turnaje</p>
                </div>
            </a>

            <a href="turnaj-detail.php">
                <div class="tile">
                    <i class="fa-solid fa-eye"></i>
                    <h3>Zobrazit pavouka</h3>
                    <p>Zobrazí soupisku a rozehraného pavouka</p>
                </div>
            </a>

            <a href="#" id="createMatchup" onclick="createMatchup(event)">
                <div class="tile">
                    <i class="fa-solid fa-shuffle"></i>
                    <h3>Vygenerovat soupisku</h3>
                    <p>Vygenerovat další kolo turnaje</p>
                </div>
            </a>

            <a href="turnaj-results.php" id="insertResults" onclick="insertResults(event)">
                <div class="tile">
                    <i>1&nbsp;:&nbsp;0</i>
                    <h3>Zadat výsledky</h3>
                    <p>Zadávání výsledků jednotlivých zápasů</p>
                </div>
            </a>
        </div>
    </main>
    <?php print_footer(); ?>
    <script>
        const xhttp2 = new XMLHttpRequest();
        xhttp2.onload = function() {
            var obj = JSON.parse(this.responseText);
            document.querySelector("main h2#nazev").innerHTML = obj[0].name;
        }
        xhttp2.open("GET", "model/edit-tourn-model.php");
        xhttp2.send();

        //kontrola permissons 
        const xhttp = new XMLHttpRequest();
        xhttp.onload = function() {
            var response = JSON.parse(this.responseText);
           console.log(response);
           if(response.createMatchup != 1)
           {    
                $('#createMatchup .tile').addClass('disabled');
           }
           if(response.insertResults != 1)
           {    
                $('#insertResults .tile').addClass('disabled');
           }
        }
        xhttp.open("GET", "model/get-tournaments.php?permissions");
        xhttp.send();


    </script>
    <script>
        function createMatchup(event)
        {            
            event.preventDefault();
            if(event.target.className.includes("disabled"))
            {
                return -1;
            }
            const xhttp2 = new XMLHttpRequest();
            xhttp2.onload = function() {
                console.log(this.responseText);
                let response = JSON.parse(this.responseText);
                if(response.success == 1)
                {
                    $('a#createMatchup').remove();
                    successAnimation();
                }
                else
                {
                    alert("error");
                }
            }
            xhttp2.open("GET", "model/create-matchup.php?initial");
            xhttp2.send();
        }

        function insertResults(event)
        {            
            if(event.target.className.includes("disabled"))
            {
                event.preventDefault();
                return -1;
            }
        }
    </script>
</body>
</html>