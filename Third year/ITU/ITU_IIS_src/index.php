<?php 
/**
 * Author: Tomáš Souček, xsouce15
 */
include 'utils.php';
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
    <title>PUCIS | Putovní cibule systém</title>
</head>
<body>
    <header>
    </header>
	
	
    <main>
        <?php 
            print_notifications();
            print_sidenav(); 
        ?>
        
        </div>
        <h1>PUCIS</h1>
        <p class="subheading">Jedinečný PUtovní CIbule Systém je tu právě pro Tebe! Tak neváhej a přidej se do komunity více než 3&nbsp;000 studentů</p>

        <h2 id="name"></h2>
        <h3 id="subname"></h3>

        <div id="pavouk">
            <div class="watch">
            </div>
        </div>

        <h2>Nejsledovanější aktivity</h2>
        <div class="flex">
            
                <div class="tile">
					<a href="turnaje.php?sort=kulečník">
                    	<img src="img/kulecnik_placeholder.jpg" alt="" title="">
                    	<p>Kulečník</p>
					</a>
                </div>
         
            
                <div class="tile">
					<a href="turnaje.php?sort=šachy">
                    	<img src="img/chess_placeholder.jpg" alt="" title="">
                    	<p>Šachy</p>
					</a>
                </div>
           
           
                <div class="tile">
                    <a href="turnaje.php?sort=fotbálek">
                    	<img src="img/fotbalek_placeholder.jpg" alt="" title="">
                    	<p>Stolní fotbálek</p>
                	</a>
                </div>
           
        </div>
    </main>
    <?php print_footer(); ?>
    <script>
        /**
         * Author: David Kocman, xkocma08
         */
        const xhttp = new XMLHttpRequest();
        xhttp.onload = function() {
            console.log(this.responseText);
            let pokus = this.responseText.substring(293);
            console.log(pokus);
            var obj;
            try{
                //obj = JSON.parse(pokus);
                obj = JSON.parse(this.response);
            }
            catch(e){
                
            }

            if(obj.length == 0){
                document.getElementById("name").innerHTML = 'Žádný nastávající či právě probíhající zápas! :(';
                document.getElementById("subname").innerHTML = 'Co takhle nějaký vytvořit ;)';
                return;
            }
            
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();

            today = dd+ '. ' + mm + '. ' + yyyy;

            var ind = Math.floor(Math.random() * obj.length);
            console.log(ind);

            if(obj[ind].date == today){
                document.getElementById("name").innerHTML = 'Právě probíhá:';
                document.getElementById("subname").innerHTML = ''+ obj[ind].name +'';
            }
            else{
                document.getElementById("name").innerHTML = 'Nastávající zápas:';
                document.getElementById("subname").innerHTML = ''+ obj[ind].name +' ' + obj[ind].date + ' ';
            }

            document.querySelector(".watch").innerHTML = '<a class="btn" href="turnaj-detail.php" onclick="detail('+ obj[ind].turnament_id +')">Sleduj nyní!</a>'
            //let container = document.querySelector("main");
            //let content = '';
        }
        xhttp.open("GET", "model/main-page-model.php", true);
        xhttp.setRequestHeader("Content-Type", "text/plain");
        xhttp.send();

        function detail(id){
            var data = new FormData();
            data.append('id', id);
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                console.log(this.responseText);

            }
            xhttp.open("POST", "model/main-page-model.php?detail", true);
             //xhttp.setRequestHeader("Content-Type", "text/plain");
            xhttp.send(data);
        }
        /**
         * Author: Tomáš Souček, xsouce15
         */

        function myFunction() {
            var x = document.querySelector("nav");
            if (x.className === "topnav") {
                x.className += " responsive";
            } else {
                x.className = "topnav";
            }
        }

    </script>
</body>
</html>