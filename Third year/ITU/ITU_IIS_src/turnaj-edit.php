<?php 
/**
 * Author: David Kocman, xkocma08
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
                margin-bottom: 0.5em;
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
                margin-top:30px;
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
            .team{
                display:flex;
                flex-direction: column;
            }
            #single-form #check.invisible{
                display:none;
            }
            .buttons{
                display: flex;
                flex-direction: row;
                align-items:center
                grid-row: 4/4;
                grid-column: 1/ span 2;
            }
            .buttons input{
                max-width:200px;
            }
            .buttons #delete{
                width: 100%;
                margin: auto;
                background: #fb5005;
                color: #333;
                font-weight: 700;
                border: none;
                padding: 1em;
                cursor: pointer;
                border-radius: 15px;
                margin-top: 3em;
            }
            .buttons #delete:hover {
                background: #c33c00;
            }

            @media screen and (max-width: 680px) {
                form.active {
                    display: flex;
                    flex-direction: column;
                }

                .buttons {
                    flex-direction: column;
                }

                .buttons #delete {
                    min-width: 150px;
                }

                form #submit {
                    min-width: 150px;
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
        <h1>Editace turnaje</h1>
        <form class="container" id="team-form">
            <div>
                <label for="name">Název turnaje</label>
                <input id="name-team">
            </div>
            <div>
                <label for="name">Sport</label>
                <select id="sport-team" name="sport">
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
                <label for="birth">Datum turnaje</label>
                <input type="date" id="birth-team" >
            </div>

            <div>
                <label id="label-pocet" for="players">Počet týmů</label>
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
                <label id="label-t" for="players">Velikost týmu</label>
                <select id="size-team" name="team">
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                </select>
                <div id="checkbox-team">
                    <span>Týmový <input id="check" type="checkbox"></span>
                </div>
            </div>

            <div>
                <label for="team">Popis turnaje</label>
                <textarea id="popis-team"></textarea>
            </div>
            

            <div class="buttons">
                <input type="submit" id="delete" name="submit" value="Smazat turnaj">
                <input type="submit" id="submit" name="submit" value="Uložit změny">
            </div>
        </form>
    </main>
    <?php print_footer(); 
    
    ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        var checkboxes = document.querySelectorAll("input[type=checkbox]");
        let enabledSettings = [];

        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                enabledSettings = Array.from(checkboxes).filter(i => i.checked).map(i => i.value);
                
                if(enabledSettings == 'on'){
                    document.getElementById("size-team").style.display = "block";
                    document.getElementById("label-t").style.display = "block";
                    document.getElementById("label-pocet").innerHTML = "Počet týmů";
                }
                else{
                    document.getElementById("size-team").style.display = "none";
                    document.getElementById("label-t").style.display = "none";
                    document.getElementById("label-pocet").innerHTML = "Počet hráčů";
                }
                enabledSettings = [];
            })
        });

        get();
        function get(){
            const xhttp2 = new XMLHttpRequest();
            xhttp2.onload = function() {
                console.log(this.responseText);
                var obj = JSON.parse(this.responseText);
                
                if(obj[0].type == 1){

                    document.querySelector('form#team-form').classList.add('active');

                    document.getElementById("name-team").value = obj[0].name;
                    document.getElementById("sport-team").value = obj[0].sport;
                    document.getElementById("teams-team").value = obj[0].max_teams;
                    document.getElementById("birth-team").value = obj[0].date;
                    document.getElementById("popis-team").value = obj[0].description;
                    document.getElementById("size-team").style.display = "none";
                    document.getElementById("label-t").style.display = "none";
                    document.getElementById("label-pocet").innerHTML = "Počet hráčů";
                    
                    document.getElementById("check").checked = false;
                }
                else if(obj[0].type == 0){
                    document.querySelector('form#team-form').classList.add('active');

                    document.getElementById("name-team").value = obj[0].name;
                    document.getElementById("sport-team").value = obj[0].sport;
                    document.getElementById("teams-team").value = obj[0].max_teams;
                    document.getElementById("birth-team").value = obj[0].date;
                    document.getElementById("popis-team").value = obj[0].description;
                    document.getElementById("size-team").style.display = "block";
                    document.getElementById("label-t").style.display = "block";
                    document.getElementById("label-pocet").innerHTML = "Počet týmů";
                    document.getElementById("size-team").value = obj[0].size;
                    
                    document.getElementById("check").checked = true;
                    
                }
            
            }
            xhttp2.open("GET", "model/edit-tourn-model.php", false);
            xhttp2.send();
        }

        document.getElementById("submit").addEventListener('click', function(event){
            var name, sport, date, descr, num, size, acc, type, max;
            event.preventDefault();
            
            if (document.getElementById('check').checked) {
                type = 0;
            }
            else{
                type = 1
            }
            
            name = document.getElementById("name-team").value;
            sport = document.getElementById("sport-team").value;
            date = document.getElementById("birth-team").value;
            num = document.getElementById("teams-team").value;
            descr = document.getElementById("popis-team").value;

            if(name.trim().length == 0){
                alert("Chybí název!");
                return;
            }

            if(document.getElementById("size-team").style.display != "none"){
                size = document.getElementById("size-team").value;
            }
            else{
                size=1;
            }

            var data = new FormData();
            data.append('name', name);
            data.append('sport', sport);
            data.append('date', date);
            data.append('descr', descr);
            data.append('num', num);
            data.append('type', type);
            data.append('size', size);
            data.append('update', 1);
            

            const xhttp2 = new XMLHttpRequest();
            xhttp2.onload = function() {
                
                alert("Údaje úspěšně změněny!");
            }
            xhttp2.open("POST", "model/edit-tourn-model.php");
            xhttp2.send(data);
            get();
        });
        
        document.getElementById("delete").addEventListener('click', function(event){
            event.preventDefault();
            if (!confirm('Opravdu chceš smazat tento turnaj?')) {
                return;
            }

            var data = new FormData();
            data.append('delete', '');

            const xhttp3 = new XMLHttpRequest();
            xhttp3.onload = function() {
                window.location.replace("turnaje.php");
                alert("Turnaj smazán!");
            }
            xhttp3.open("POST", "model/edit-tourn-model.php", false);
            xhttp3.send(data);
        });
    </script>
</body>
</html>