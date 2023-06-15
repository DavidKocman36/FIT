<?php include 'utils.php' 
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
        <link rel="stylesheet" href="css/admin-tourn-style.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> 
        <title>PUCIS | Admin</title>
    </head>
    <body>
        <header>
        <nav class="topnav">
            <a href="admin-tournaments.php">Turnaje</a>
            <a href="admin-users.php">Uživatelé</a>
            <a href="admin-main.php"><?php echo $_SESSION["username"];?></a>
            <a href="#" onclick="logout()">Odhlásit se</a>
        </nav>
        </header>
        <main>
            <div class="page">
                <section class="acc">
                    <h1>Schválené turnaje</h1>
                    <div id="acc-content"/>
                </section>
                <section class="not-acc">
                    <h1>Neschválené turnaje</h1>
                    <div id="not-acc-content"/>
                </section>

                <div class="wrapper">
                    <div class="popup">
                        <div id="detail"/>
                    </div>
                </div>
            </div>
            
        </main>
        <?php print_footer();?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script type="text/javascript">
            get(false, "acc-content", "acc");
            get(false, "not-acc-content", "not-acc");

            function get(async, elem, funct){
                $.ajax({
                    url: 'model/admin-tournament-model.php',
                    type: 'GET',
                    data: { get: funct},
                    async: async,
                    success: function(result) {

                        try{
                            var obj = JSON.parse(result);
                        }
                        catch (e){
                            return;
                        }

                        var team;
                        document.getElementById(elem).innerHTML = "";
                        document.getElementById(elem).innerHTML += "<ol>";
                        if(funct == "not-acc"){
                            for(let i = 0; i < obj.length; i++){
                                if(obj[i].type == 0){
                                    team = "tým";
                                }
                                else{
                                    team = "jednotlivci";
                                }
                                document.getElementById(elem).innerHTML += "<li onclick=\"detail("+ obj[i].turnament_id + ")\"><span class=\"name\">" + obj[i].name +" </span><span class=\"sport\">"+ obj[i].sport + "</span><span class=\"date\">" + obj[i].date + "</span><span class=\"type\"> " + team + "</span><div class=\"buttons\"><button type=\"submit\" onclick='remove(" + obj[i].turnament_id + ")' class=\"delete\"><i class=\"fa-sharp fa-solid fa-xmark\"></i></button><button type=\"submit\" onclick='accept(" + obj[i].turnament_id + ")' class=\"accept\"><i class=\"fa-solid fa-check\"></i></button></div></li>";
                            }
                        }
                        else{
                            for(let i = 0; i < obj.length; i++){
                                if(obj[i].type == 0){
                                    team = "tým";
                                }
                                else{
                                    team = "jednotlivci";
                                }
                                document.getElementById(elem).innerHTML += "<li onclick=\"detail("+ obj[i].turnament_id + ")\"> <span class=\"name\">" + obj[i].name +" </span><span class=\"sport\">"+ obj[i].sport + "</span><span class=\"date\">" + obj[i].date + "</span><span class=\"type\"> " + team + "</span><button type=\"submit\" onclick='remove(" + obj[i].turnament_id + ")' class=\"delete\"><i class=\"fa-sharp fa-solid fa-xmark\"></i></button></li>";
                            }
                        }
                        
                        document.getElementById(elem).innerHTML += "</ol>";
                        obj = undefined;
                    }
                });
            }

            function remove(id){
                if (!confirm('Opravdu chceš odstranit tento turnaj?')) {
                    return;
                }

                $.ajax({
                    url: 'model/admin-tournament-model.php',
                    type: 'GET',
                    dataType: "json",
                    data: { delete: id},
                    async: false,
                    success: function(response) {     
                    }
                });
                get(false, "acc-content", "acc");
                get(false, "not-acc-content", "not-acc");
            }

            function accept(id){
                
                if (!confirm('Opravdu chceš schválit tento turnaj?')) {
                    return;
                }

                $.ajax({
                    url: 'model/admin-tournament-model.php',
                    type: 'GET',
                    dataType: "json",
                    data: { accept: id},
                    async: false,
                    success: function(response) {     
                    }
                });
                get(false, "acc-content", "acc");
                get(false, "not-acc-content", "not-acc");
            }

            function detail(id){
                let async = false;
                var elem="detail";

                var data = new FormData();
                data.append('detail_pop', id);

                const xhttp2 = new XMLHttpRequest();
                xhttp2.onload = function() {
                    
                    let response = JSON.parse(this.responseText);

                    document.getElementById(elem).innerHTML = "";
                    document.getElementById(elem).innerHTML += "<h1>"+response[0].name+"</h1>";
                    document.getElementById(elem).innerHTML += "<h2>Sport: </h2><span>"+response[0].sport+"</span>";
                    document.getElementById(elem).innerHTML += "<h2>Datum: </h2><span>"+response[0].date+"</span>";
                    document.getElementById(elem).innerHTML += "<h2>Typ: </h2><span>"+response[0].type+"</span>";
                    document.getElementById(elem).innerHTML += "<div class=\"numbers\"><h2>Min. týmů: </h2><span>"+response[0].min_teams+"</span><h2>Max. týmů: </h2><span> "+response[0].max_teams+"</span><h2>Poč. týmů: </h2><span> "+response[0].squads+"</span></div>";
                    document.getElementById(elem).innerHTML += "<h2>Popis: </h2><span>"+response[0].description+"</span>";
                    
                    
                }

                xhttp2.open("POST", "model/admin-tournament-model.php");
                xhttp2.send(data);
            }

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