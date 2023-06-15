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
    <title>PUCIS | Výpis týmů</title>
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
	
	.team-detail
	{
		display: flex;
  		justify-content: center;
  		align-items: center;
		margin-left: 5%;
	}
	
	.members{
		margin-left: 2%;
	}
</style>
		
	
		<?php 
        print_notifications();
        print_sidenav();
    ?>

    <main>
        <div class="center" id="small-form-container">
			<h1>Detaily týmů</h1>
			<h2 style="text-align: center">Vyberte tým, který chcete zobrazit</h2>
			<form id="select-form" action="" method="GET">
				<select id="get-team-id" name="teams" id="teams">
					
				</select>
				<input type="submit" id="submit" name="submit" value="Zobrazit tým" onclick="change_team()">
			</form>
		</div>
        <div class="teams"></div>
    </main>
    <?php 
	print_footer(); ?>
    <script>
		var data = new FormData();
            data.append('get-select', 'arrived');
		const xhttp2 = new XMLHttpRequest();
            xhttp2.onload = function() {
				let select = document.getElementById("get-team-id");
                let team = JSON.parse(this.responseText);
                
				for(let i = 0; i < team.length; i++ ){
					select.innerHTML += '<option value="'+team[i].team_id+'">'+team[i].name+'</option>';
				}
            }
            xhttp2.open("POST", "model/get-team-detail.php");
            xhttp2.send(data);
		
		
        function loadData()
        {		
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                let team = JSON.parse(this.responseText);
                let container = document.querySelector(".teams");
                let content = '';

                content +=  '<div class="flex photo-container">';
                    content +=  '<div class="photo">';
                        content +=  '<img src="' + team[0].logo + '" alt="profilový obrázek" title="profilový obrázek">';
                    content +=  '</div>';
                            
                    content +=  '<div class="team-detail">';
                        content +=  '<h2 class="team-h2">'+ team[0].name +'</h2>';
                    content +=  '</div>';
                content +=  '</div>';
                
                content += '<div class="members">'
                content += '<h4>Členové týmu:</h4>';
			
				
                $.ajax({  
                    type: 'POST',
                    url: 'model/get-team-detail.php', 
                    async: false,
                    data: { current_team: team[0].team_id,
                            members: 'arrived'},
                    success: function (data){
                        var members_list = JSON.parse(data);
                        
                        for(let i = 0; i < members_list.length; i++ ){
                            content += '<p>'+members_list[i].name+' '+members_list[i].surname+'</p>';
                        }
                        
                    },
                    error: function(data) {
                        alert("Nastal error!");
                    }
                });

                var matches = 0, won = 0, draw = 0;
                if(team.length > 2){

                    matches = team.length - 2;

                    for(var i = 2; i < team.length; i++){
                        if(team[i].result1 > team[i].result2){
                            won += 1;
                        }
                        else if(team[i].result1 == team[i].result2){
                            draw += 1;
                        }
                    }
                }

				content += '</div>'
                content +=  '<div class="stats-wrapper">';
                    content += '<h2>Statistiky</h2>';
                    content +=  '<div class="stats">';
                        content += '<span> <b>Zúčastněných turnajů: ' + team[1].cnt + '</b> </span>';
                        content += '<span> Odehraných zápasů: ' + matches + ' </span>';
                        content += '<span> Vyhraných zápasů: ' + won + ' </span>';
                        content += '<span> Remíz: ' + draw + ' </span>';
                    content +=  '</div>';
                content +=  '</div>';
                container.innerHTML = content;
            }
            xhttp.open("GET", "model/get-team-detail.php");
            xhttp.send();
        }
		
		function change_team(){
                var team_id = $("#get-team-id").val();
           
                $.ajax({  
                    type: 'POST',
                    url: 'model/get-team-detail.php', 
                    async: false,
                    data: { Team_id: team_id},
                    success: function (data){
                        var res = JSON.parse(data);
						if(res.success == "ok"){
							loadData();
						}
                    },
                    error: function(data) {
                        alert("Nastal error!");
                    }
                });
                 
            }
		
        //loadData();
        window.addEventListener('load', function () {
            document.querySelector('#select-form').addEventListener('submit', function(evt){
                evt.preventDefault();
            });
        });

    </script>
</body>
</html>