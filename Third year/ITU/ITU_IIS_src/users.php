<?php include 'utils.php' 
/**
 * Author: Adam Cologna, xcolog00
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
    <link href="css/profile.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <title>PUCIS | Profily uživatelů</title>
</head>
<body>
<style>
	.center{
		margin: 0 auto;
  		width: 60%;
		text-align: center;
	}
	
	select{
		width: 50%;
		margin: auto;
		grid-column: 1 / span 2;
		text-align: center;
    	text-align-last: center;
    	-moz-text-align-last: center;
	}
	
</style>	
	
		<?php 
        print_notifications();
        print_sidenav();
    ?>

    <main>
    <div class="center" id="small-form-container">
			<h1>Profily uživatelů</h1>
			<h2 style="text-align: center">Vyberte uživatele, kterého chcete zobrazit</h2>
			<form id="select-form" action="" method="GET">
				<select id="get-username" name="users" id="users">
					
				</select>
				<input type="submit" id="submit" name="submit" value="Zobrazit uživatele" onclick="change_user()">
			</form>
		</div>

        <div class="user">
        </div>
    </main>
    <?php 
	print_footer(); ?>
    <script>
		var data = new FormData();
        data.append('get-select', 'arrived');
		const xhttp2 = new XMLHttpRequest();
        xhttp2.onload = function() {
            let select = document.getElementById("get-username");
            let user = JSON.parse(this.responseText);
            
            for(let i = 0; i < user.length; i++ ){
                select.innerHTML += '<option value="'+user[i].username+'">'+user[i].name+' '+user[i].surname+'</option>';
            }
        }
        xhttp2.open("POST", "model/get-users.php");
        xhttp2.send(data);
		
		
        function loadData()
        {		
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                console.log(this.responseText);
				//let select = document.getElementById("get-username");
                let user = JSON.parse(this.responseText);
                let container = document.querySelector(".user");
                let content = '';

                var matches = 0, won = 0, draw = 0;
                if(user.length > 2){

                    matches = user.length - 2;

                    for(var i = 2; i < user.length; i++){
                        if(user[i].result1 > user[i].result2){
                            won += 1;
                        }
                        else if(user[i].result1 == user[i].result2){
                            draw += 1;
                        }
                    }
                }

                content +=  '<div class="flex photo-container">';
                    content +=  '<div class="photo">';
                        content +=  '<img src="' + user[0].picture + '" alt="profilový obrázek" title="profilový obrázek">';
                    content +=  '</div>';
                            
                    content +=  '<div>';
                        content +=  '<h2>'+ user[0].name +' '+ user[0].surname +'</h2>';
                        content +=  '<p>'+ user[0].username +'</p>';
                    content +=  '</div>';
                content +=  '</div>';

                content +=  '<form>';
                    content +=  '<div>';
                        content +=  '<label for="name">Jméno</label>';
                        content +=  '<input type="text" id="name1" name="name" readonly placeholder="např. David" value="'+user[0].name+'">';
                    content +=  '</div>';
                    content +=  '<div>';
                        content +=  '<label for="surname">Přijmení</label>';
                        content +=  '<input type="text" id="surname" name="surname" readonly placeholder="např. Bednář" value="'+user[0].surname+'">';
                    content +=  '</div>';
                    content +=  '<div>';
                        content +=  '<label for="email">E-mail</label>';
                        content +=  '<input type="mail" id="email" name="email" readonly placeholder="bednarda@seznam.cz" value="'+user[0].email+'">';
                    content +=  '</div>';
                    content +=  '<div>';
                        content +=  '<label for="phone">Telefon</label>';
                        content +=  '<input type="tel" id="phone" name="phone" readonly placeholder="+420776969420" value="'+user[0].phone+'">';
                    content +=  '</div>';
                    content +=  '<div>';
                        content +=  '<label for="birth">Datum narození</label>';
                        content +=  '<input type="date" id="birth" name="birth" readonly value="'+user[0].birthdate+'">';
                    content +=  '</div>';
                    content +=  '<div>';
                        content +=  '<label for="school">Škola</label>';
                        content +=  '<input type="text" id="school" name="school" readonly value="'+user[0].school+'">';
                    content +=  '</div>';
                    content +=  '<div>';
                    content +=  '<label for="school">Ročník</label>';
                        content +=  '<input type="number" id="school" name="school" value="'+user[0].year+'">';
                    content +=  '</div>';
                    content +=  '<div>';
                        content +=  '<label for="school">Fakulta</label>';
                        content +=  '<input type="text" id="school" name="school" value="'+user[0].faculty+'">';
                    content +=  '</div>';

                    content +=  '<div class="stats-wrapper">';
                        content += '<h2>Statistiky</h2>';
                        content +=  '<div class="stats">';
                            content += '<span> <b>Zúčastněných turnajů: ' + user[1].cnt + '</b> </span>';
                            content += '<span> Odehraných zápasů: ' + matches + ' </span>';
                            content += '<span> Vyhraných zápasů: ' + won + ' </span>';
                            content += '<span> Remíz: ' + draw + ' </span>';
                        content +=  '</div>';
                    content +=  '</div>';

                content +=  '</form>';
			

                container.innerHTML = content;
            }
            xhttp.open("GET", "model/get-users.php");
            xhttp.send();
        }
		
		function change_user(){
                var username = $("#get-username").val();
           
                $.ajax({  
                    type: 'POST',
                    url: 'model/get-users.php', 
                    async: false,
                    data: { Username: username},
                    success: function (data){
                        console.log(data);
                        var res = JSON.parse(data);
						console.log(res.username);
						if(res.success == "ok"){
							loadData();
							//console.log("we good");
						}
                    },
                    error: function(data) {
                        alert("Nastal error!");
                    }
                });
                 
            }
		
		 $(document).ready(function () {
		  $('select').selectize({
			  sortField: 'text'
		  });
  		});
		
        //loadData();
        window.addEventListener('load', function () {
            document.querySelector('#select-form').addEventListener('submit', function(evt){
                evt.preventDefault();
            });
        });

    </script>
</body>
</html>