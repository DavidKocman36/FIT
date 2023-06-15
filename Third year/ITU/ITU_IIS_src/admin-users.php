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
        <link rel="stylesheet" href="css/users-admin-style.css">
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
            <H1>Seznam uživatelů</H1>
            <section class="content">
                <div id="data"/>
            </section>

            <div class="form-container" id="myForm">
                <div class="head">
                    <h2 id="nadpis">Nové heslo pro<h2>
                    <a id="close" onclick="closeForm()"> <i class="fa-solid fa-xmark"></i></a>
                </div>
                <div class="forma">
                    <form>
                        <div>
                            <input type="text" id="passw" name="name" value=""/>
                        </div>
                        <input type="submit" id="submit" name="submit" value="OK"/>
                    </form>
                </div>
            </div>
        </div>
            
        </main>
        <?php print_footer();?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script type="text/javascript">

            get(false);

            function remove(id){
                if (!confirm('Opravdu chceš odstranit tohoto uživatele?')) {
                    return;
                }
                $.ajax({
                    url: 'model/users-model.php',
                    type: 'GET',
                    dataType: "json",
                    data: { delete: id},
                    async: false,
                    success: function(response) {     
                    }
                });
                get(false);
            }

            function get(async){
                var xmlHttp = new XMLHttpRequest();

                xmlHttp.open("GET", "model/users-model.php", async);
                xmlHttp.send(null);
                
                try{
                    var obj = JSON.parse(xmlHttp.responseText);
                }
                catch (e){
                    return;
                }
                
                document.getElementById("data").innerHTML = "";
                document.getElementById("data").innerHTML += "<ol>";
                for(let i = 0; i < obj.length; i++){
                    document.getElementById("data").innerHTML += "<li> <span>" + obj[i].name +" </span><span>"+ obj[i].surname + "</span><span>" + obj[i].username + "</span><span> " + obj[i].email + "</span><button type=\"submit\" onclick='remove(" + obj[i].user_id + ")' class=\"delete\"><i class=\"fa-sharp fa-solid fa-xmark\"></i></button> <button type=\"submit\" onclick='edit(" + obj[i].user_id +", \""+ obj[i].name +"\", \"" + obj[i].surname + "\")' class=\"edit\"><i class=\"fa-solid fa-pen-to-square\"></i></button></li>";
                    //join(' + tournaments[j].turnament_id +','+ tournaments[j].type + ')
                    
                }
                document.getElementById("data").innerHTML += "</ol>";
                obj = undefined;
            }

            function edit(id, name, surname){
                document.getElementById("myForm").style.display = "block";
                document.getElementById("nadpis").innerHTML = "Nové heslo pro " + name + " " + surname ;

                document.getElementById("submit").addEventListener("click", function(e){ 
                    if (!confirm('Opravdu chceš změnit heslo?')) {
                        return;
                    }
                    e.preventDefault();
                    var text = document.getElementById("passw").value;
                    const xhttp2 = new XMLHttpRequest();

                    var data = new FormData();
                    data.append('pass', id);
                    data.append('text', text);
                    
                    console.log(text);
                    xhttp2.onload = function() {
                        
                        console.log(this.responseText);
                        //let response = JSON.parse(this.responseText);
                        //console.log(response);
                        document.getElementById("passw").value = "";
                        
                    }

                    xhttp2.open("POST", "model/users-model.php");
                    xhttp2.send(data);

                    document.getElementById("myForm").style.display = "none";
                });
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

            function closeForm() {
                document.getElementById("myForm").style.display = "none";
            }
        </script>
    </body>
</html>