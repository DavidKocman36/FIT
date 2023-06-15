<?php 
include 'utils.php' 
/**
 * Author: David Kocman, xkocma08
 */
?>
<!DOCTYPE html>
<html lang="cs">
    <head>
        <meta name="theme-color" content="#ffffff">
		<meta name="viewport" content="width=device-width, initial-scale=1"> 
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" > 
        <link rel="stylesheet" href="css/admin-main-style.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> 
        <title>PUCIS | Admin</title>
    </head>
    <body>
        <header>
        <nav class="topnav">
            <a href="admin-tournaments.php">Turnaje</a>
            <a href="admin-users.php">Uživatelé</a>
            <a><?php echo $_SESSION["username"];?></a>
            <a href="#" onclick="logout()">Odhlásit se</a>
        </nav>
        </header>
        <main>
            <div class="wrapper">
                <div id="stats"/>
            </div>
            
        </main>
        <?php print_footer();?>
        <script>
            let async = false;

            const xhttp2 = new XMLHttpRequest();
            xhttp2.onload = function() {
                console.log(this.responseText);
                let response = JSON.parse(this.responseText);

                document.getElementById("stats").innerHTML = "";
                document.getElementById("stats").innerHTML += "<div class=\"upc\"> <h2>Nastávajících zápasů: </h2><p>"+ response.turn_u +"</p></div>";
                document.getElementById("stats").innerHTML += "<div class=\"pass\"> <h2>Proběhlých zápasů: </h2><p>"+ response.turn_p +"</p></div>";
                document.getElementById("stats").innerHTML += "<div class=\"usr\"> <h2>Registrovaných uživatelů: </h2><p>"+ response.users +"</p></div>";
                document.getElementById("stats").innerHTML += "<div class=\"teams\"> <h2>Registrovaných týmů: </h2><p>"+ response.teams +"</p></div>";

            }

            xhttp2.open("GET", "model/admin-stats-model.php");
            xhttp2.send(null);

            function logout(){
                let async = false;
                    
                var data = new FormData();
                data.append('logout-submit', 'depart');
        
                const xhttp2 = new XMLHttpRequest();
                xhttp2.onload = function() {
                    
                    console.log(this.responseText);
                    let response = JSON.parse(this.responseText);
                    //console.log(response);
                    
                    window.location.replace("index.php");
                }

                xhttp2.open("POST", "model/login-backend.php");
                xhttp2.send(data);
            }
        </script>
    </body>
</html>