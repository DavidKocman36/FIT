<?php 
include 'utils.php' 
/**
 * Author: David Kocman, xkocma08
 */
?>
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
            .photo img
            {
                border-radius: 50%;
                width: 100%;
                height: 100%;
                object-fit: cover;
                box-shadow: 0 0 5px rgba(0,0,0, 0.3);
            }
            .photo
            {
                position: relative;
                width: 300px;
                height: 300px;
                margin-left: auto;
            }
            .container
            {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 2em;
                margin-bottom: 2em;
            }
            .edit-photo
            {
                font-size: 1.5em;
                bottom: 5%;
                right: 5%;

            }
            main h1
            {
                text-align: left;
                margin-bottom: 2em;
            }
            form.container
            {
                font-weight: 500;
                font-size: 1.1em;
                display: none;
            }
            .players
            {
                position: relative;
                margin: 2em 0;
            }
            .fa-plus
            {
                position: absolute;
                right: 0;
                bottom: -50%;
            }
            .fa-minus
            {
                position: absolute;
                right: -8%;
                top: 25%;
            }
            .players div
            {
                position: relative;
            }

            input[type=checkbox] {
                position: relative;
                cursor: pointer;
            }
            input[type=checkbox]:before {
                content: "";
                display: block;
                position: absolute;
                width: 30px;
                height: 30px;
                top: -10px;
                left: 0;
                background-color:#e9e9e9;
                border-radius: 5px;
            }
            input[type=checkbox]:checked:before {
                content: "";
                display: block;
                position: absolute;
                width: 30px;
                height: 30px;
                top: -10px;
                left: 0;
                background-color:#559f00;
            }
            input[type=checkbox]:checked:after {
                content: "";
                display: block;
                width: 10px;
                height: 20px;
                border: solid white;
                border-width: 0 2px 2px 0;
                -webkit-transform: rotate(45deg);
                -ms-transform: rotate(45deg);
                transform: rotate(45deg);
                position: absolute;
                top: -8px;
                left: 8px;
            }
            .checkbox
            {
               
                font-size: 1.5em;
            }
            .form-nav
            {
                width: 100%;
                display: flex;
                justify-content: space-around;
              
            }
            .form-nav a
            {
                padding: 1em;
                text-decoration: none;
                color: #333;
                font-weight: 700;
                background: #eee;
                width: 100%;
                text-align: center;
                position: relative;
                z-index: 2;
            }
            form
            {
                box-shadow: 0 0 5px #ddd;
                padding: 2em;
            }
            form.active
            {
                display: grid;
            }
            .form-nav a.active
            {
                background: #fba405;
                transform-style: preserve-3d;
            }
            .form-nav a.active::after
            {
                content: '';
                width: 20px;
                height: 20px;
                transform: rotate(45deg) translateZ(-1px);;
                display: block;
                background: #fba405;
                position: absolute;
                left: calc(50% - 10px);
                bottom: -10px;
            }
            .form-nav a.active:hover::after
            {
                background: #db8e03;
            }
            .form-nav a:hover
            {
                background: #db8e03;
            }
            @media screen and (max-width: 680px){
                form.active {
                    display: flex;
                    flex-direction: column;
                }
            }
        </style>
    </head>
<body>
    <?php 
        print_notifications();
        print_sidenav(); 
    ?>

    <main>
        <?php
        //
        //  0 -> týmový  
        //  1 -> jednotlivci
        //
        ?>
        <h1>Vytváření turnaje</h1>
        <div class="form-nav">
            <a href="#" class="active" id="single">Turnaje po jednom</a>
            <a href="#" id="team">Týmové turnaje</a>
        </div>
        <form class="container active" id="single-form">
            <div>
                <label for="name">Název turnaje</label>
                <input type="text" id="name-single" name="name" required placeholder="např. Krvelačné šachy"/>
            </div>
            <div>
                <label for="name">Sport</label>
                <select id="sport-single" name="sport">
                    <option value="šachy">šachy</option>
                    <option value="fotbálek">fotbálek</option>
                    <option value="pivní štafeta">pivní štafeta</option>
                    <option value="kulečník">kulečník</option>
                    <option value="šipky">šipky</option>
                    <option value="stolní hry">stolní hry</option>
                    <option value="jiné (v popisku)">jiné (v popisku)</option>
                </select>
            </div>
            <div>
                <label for="players">Počet hráčů</label>
                <select id="players-single" name="players">
                    <option value="2">2</option>
                    <option value="4">4</option>
                    <option value="6">6</option>
                    <option value="8">8</option>
                    <option value="10">10</option>
                    <option value="12">12</option>
                    <option value="14">14</option>
                    <option value="16">16</option>
                </select>
            </div>
            <div>
                <label for="birth">Datum turnaje</label>
                <input type="date" id="birth-single" required name="birth" value="<?php echo date('Y-m-d'); ?>"/>
            </div>
            <div>
                <label for="team">Popis turnaje</label>
                <textarea id="popis-single" placeholder="Zadejte popis turnaje"></textarea>
            </div>

            <input type="submit" id="submit" name="submit" value="Založit"/>
        </form>

        <form class="container" id="team-form">
            <div>
                <label for="name">Název turnaje</label>
                <input type="text" id="name-team" name="name" required placeholder="např. Krvelačné šachy"/>
            </div>
            <div>
                <label for="name">Sport</label>
                <select id="sport-team" name="sport">
                    <option value="šachy">šachy</option>
                    <option value="lolko">fotbálek</option>
                    <option value="pivní štafeta">pivní štafeta</option>
                    <option value="kulečník">kulečník</option>
                    <option value="šipky">šipky</option>
                    <option value="stolní hry">stolní hry</option>
                    <option value="jiné (v popisku)">jiné (v popisku)</option>
                </select>
            </div>
            <div>
                <label for="birth">Datum turnaje</label>
                <input type="date" id="birth-team" required name="birth" value="<?php echo date('Y-m-d'); ?>"/>
            </div>

            <div>
                <label for="players">Počet týmů</label>
                <select id="teams-team" name="players">
                    <option value="2">2</option>
                    <option value="4">4</option>
                    <option value="6">6</option>
                    <option value="8">8</option>
                    <option value="10">10</option>
                    <option value="12">12</option>
                    <option value="14">14</option>
                    <option value="16">16</option>
                </select>
            </div>

            <div>
                <label for="players">Velikost týmu</label>
                <select id="size-team" name="team">
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                </select>
            </div>

            <div>
                <label for="team">Popis turnaje</label>
                <textarea id="popis-team" placeholder="Zadejte popis turnaje"></textarea>
            </div>

            <input type="submit" id="submit" name="submit" value="Založit"/>
        </form>
    </main>
    <?php print_footer(); ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        var link_arr = document.querySelectorAll('.form-nav a');
        for (let i = 0; i < link_arr.length; i++)
        {
            link_arr[i].addEventListener('click', function(){
                for (let j = 0; j < link_arr.length; j++)
                {
                    link_arr[j].classList.remove('active');
                }
                link_arr[i].classList.add('active');
                if(link_arr[i].id == 'team')
                {
                    document.querySelector('form#team-form').classList.add('active');
                    document.querySelector('form#single-form').classList.remove('active');
                }
                else
                {
                    document.querySelector('form#single-form').classList.add('active');
                    document.querySelector('form#team-form').classList.remove('active');
                }
            });
        }

        //function
        var single = document.querySelector('form#single-form');
        var team = document.querySelector('form#team-form');
        var sub = document.querySelectorAll('[id=submit]');

        for(let i = 0; i < sub.length; i++){

            sub[i].addEventListener('click', function(event){
                event.preventDefault();
                var name, sport, date, descr, num, size, acc, type, max;
                if(single.classList.contains("active")){
                    name = document.getElementById("name-single").value;
                    if(name.trim().length == 0){
                        alert("Chybí název!");
                        return;
                    }
                    sport = document.getElementById("sport-single").value;
                    date = document.getElementById("birth-single").value;
                    max = document.getElementById("players-single").value;
                    descr = document.getElementById("popis-single").value;
                    acc = 0;
                    type = 1;
                    size = 1;

                    var data = new FormData();
                    data.append('name', name);
                    data.append('sport', sport);
                    data.append('date', date);
                    data.append('descr', descr);
                    data.append('num', num);
                    data.append('type', type);
                    data.append('acc', acc);
                    data.append('max', max);
                    data.append('size', size);

                    const xhttp2 = new XMLHttpRequest();
                    xhttp2.onload = function() {
                        alert("Turnaj úspěšně vytvořen!");
                        window.location.replace("turnaje.php");
                    }
                    xhttp2.open("POST", "model/create-tourn-model.php", false);
                    xhttp2.send(data);
                }
                else{
                    name = document.getElementById("name-team").value;
                    if(name.trim().length == 0){
                        alert("Chybí název!");
                        return;
                    }
                    sport = document.getElementById("sport-team").value;
                    date = document.getElementById("birth-team").value;
                    max = document.getElementById("teams-team").value;
                    descr = document.getElementById("popis-team").value;
                    size = document.getElementById("size-team").value;
                    acc = 0;
                    type = 0;

                    console.log(max);
                    console.log(size);

                    var data = new FormData();
                    data.append('name', name);
                    data.append('sport', sport);
                    data.append('date', date);
                    data.append('descr', descr);
                    data.append('num', num);
                    data.append('type', type);
                    data.append('acc', acc);
                    data.append('max', max);
                    data.append('size', size);

                    const xhttp2 = new XMLHttpRequest();
                    xhttp2.onload = function() {
                        alert("Turnaj úspěšně vytvořen!");
                        window.location.replace("turnaje.php");
                    }
                    xhttp2.open("POST", "model/create-tourn-model.php", false);
                    xhttp2.send(data);
                }
            });

        }
        

    </script>
</body>
</html>