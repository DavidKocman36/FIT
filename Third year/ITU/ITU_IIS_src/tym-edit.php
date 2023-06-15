<?php include 'utils.php' 
/**
 * Author: Tomáš Souček, xsouce15
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


        <title>PUCIS | Týmy</title>
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
            }
            @media screen and (max-width: 1100px) {
                .container
                {
                    grid-template-columns: 1fr;
                }
                .photo
                {
                    width: 100%;
                    order: -1;
                }
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
            form
            {
                font-weight: 500;
                font-size: 1.1em;
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
                bottom: -80px;
            }
            .fa-minus
            {
                position: absolute;
                right: -50px;
                top: 25%;
            }
            .players div
            {
                position: relative;
            }
            .fa-crown
            {
                position: absolute;
                right: 17px;
                top: 40%;
                font-size: 1.2em;
                opacity: 0.6;
            }
            form input#submit
            {
                margin-right: 1em;
            }
            #buttons
            {
                margin-top: 6em;
            }
            .btn
            {
                display: block;
                margin-top: 3em;
                text-align: center;
                cursor: pointer;
                background: #555;
            }
            .players-pending
            {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                display: none;
                flex-direction: column;
                background: rgba(0,0,0,0.7);
                align-items: center;
                justify-content: center;
                z-index: 12;
                color: white;
            }
            .child
            {
                position: relative;
            }
        </style>
    </head>
<body>
    <?php 
        print_notifications();
        print_sidenav(); 
    ?>

    <main>
        <a href="tymy.php" style="position:fixed;left:200px;top:50px;"><i class="fa-solid fa-chevron-left"></i> Zpět na seznam týmů</a>
        <?php
         if(isset($_GET["create"]))
         {
            echo "<h1>Vytváření týmu</h1>";
         } 
         else
         {
            echo "<h1></h1>";      
         }
         ?>
        
        <div class="container">
            <form>
                <div>
                    <label for="name">Název týmu</label>
                    <input type="text" id="name" name="name" placeholder="např. Andreas Rulezz">
                </div>
                <?php
                    if(isset($_GET["edit"]))
                    {
                        ?>
                            <div class="players">
                                <label for="players">Hráči</label>
                                <div>
                                </div>                            
                                
                                <i class="fa-solid fa-plus"></i>
                            </div>
                        <?php
                    }
                
            
                    if(isset($_GET["create"]))
                    {
                        echo '<input type="submit" id="submit" name="create" value="Vytvořit tým">';
                    }                     
                ?>
                <div class="players-pending">
                    <label for="players-pending"><i class="fa-solid fa-clock-rotate-left"></i> Čekající pozvánky</label>
                    <div>
                    </div>                            
                    
                </div>

                <div id="buttons">
                </div>
            </form>
           
            
            <div class="photo">
                <img src="img/team-logo.png" alt="logo týmu" title="logo týmu">
                <span id="changePicture">
                    <label for="file"><span class="edit-photo"><i class="fa-solid fa-pencil"></i></span></label>
                    <input type="file" id="file" style="display:none;" onchange="changePicture(event)">
                </span>
                <div>
                </div>
            </div>
           
        </div>
    </main>
    <?php print_footer(); ?>


    <script>
        //get current team id
        let params = window.location.search;
        const urlParams = new URLSearchParams(params);
        const id = urlParams.get('edit');

        var allPlayers = [];
        var currentPlayerIteration = 1; //nezbytné pro vkládání nových hráčů do týmu
        var canEdit = 0;
        var isMember = 0;

        function loadData()
        {
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                let results = JSON.parse(this.responseText);
                canEdit = results["canEdit"];

                //přesměrování na seznam týmů, pokud chtěl přes url přistoupit k neexistujícímu týmu
                if(this.status  != 200)
                {
                    window.location.href = "tymy.php";
                }

                for(let i = 0; i < results["players"].length; i++)
                {
                    if(results["players"][i]["user_id"] == <?php echo $_SESSION["user_id"]; ?>)
                    {
                        isMember = 1;
                    }
                }

                let container = document.querySelector(".players div");
                let content = '';
             
                
                $('#name').val(results["team"][0].name);
                $('.photo img')[0].src = results["team"][0].logo;

                allPlayers = [];
                for(let i = 0; i < results["allPlayers"].length; i++)
                {
                    allPlayers.push(results["allPlayers"][i]);
                }


                for(let i = 0; i < results["players"].length; i++)
                {
                    content += '<div>';
                    content += '<input type="text" id="p'+results["players"][i].user_id+'" name="p'+results["players"][i].user_id+'" placeholder="např. xAnreasX" value="'+ results["players"][i].username +'" disabled>';              
                   
                    if(i != 0 && canEdit == 1)
                    {
                        content += '<i class="fa-solid fa-minus"></i>';
                    }
                    else if(i == 0)
                    {
                        content += '<i class="fa-solid fa-crown"></i>';
                    }
                   
                    content += '</div>';
                    currentPlayerIteration++;
                }
                container.innerHTML = content;

                console.log(results);

                content = '';
                for(let i = 0; i < results["playersPending"].length; i++)
                {
                    content += '<div class="child">';
                    content += '<input type="text" id="p'+results["playersPending"][i].user_id+'" name="p'+results["playersPending"][i].user_id+'" value="'+ results["playersPending"][i].username +'" disabled>';              
                   
                    console.log("i: "+i+", edit: "+canEdit);
                    if(canEdit == 1)
                    {
                        console.log("why am i not here");
                        content += '<i class="fa-solid fa-minus"></i>';
                        console.log(content);
                    }
                   
                    content += '</div>';
                }
                console.log(content);
                $('.players-pending div').html(content);               

               
               
                if(canEdit == 1)
                { 
                    let content ='<input type="submit" id="submit" name="submit" value="Uložit změny"><a class="warning-btn" onclick="deleteTeam()" id="deleteTeam">Smazat tým</a>';
                   
                    $('#buttons').html(content);
                    $('.photo div').html('<a class="btn" id="pendingInvitations"><i class="fa-solid fa-clock-rotate-left"></i> Odeslané pozvánky</a>');
                    $('#pendingInvitations').click(function(){
                        $('.players-pending').css("display", "flex");
                    });
                    $('.players-pending').click(function(event){
                        console.log(event.target.className);
                        if(event.target.className == 'players-pending')
                        {
                            $('.players-pending').css("display", "none");
                        }
                       
                    });
                    $('h1').text('Editace týmu');
                }
                else
                {
                    $('#changePicture').remove();
                    if(isMember == 1 )
                    {
                        $('#buttons').html('<a class="warning-btn" onclick="leaveTeam(\'<?php echo $_SESSION["username"] ?>\')" id="deleteTeam">Opustit tým</a>');
                    }
                    $('input#name').prop("disabled", true);
                    $('.fa-plus').css("display", "none");   
                    $('h1').text('Detail týmu'); 
                } 

                confirmationCheck('.players .fa-minus');
                confirmationCheck('.players-pending .fa-minus');
            }
          
            xhttp.open("GET", "model/get-teams.php?edit="+id);
            xhttp.send();
        }

        function changePicture(event)
        {
            var image = document.querySelector('.photo img');
            image.src = URL.createObjectURL(event.target.files[0]);

        }

        function editTeam()
        {   
            //checkRights(canEdit);

            var data = new FormData();
            data.append('team_id', id);
            data.append('name', $('#name').val());     
            data.append('edit', '1');
            data.append('image', $('#file')[0].files[0]);

            let playersForm = $('form .players > div').find('input');
            for(let i = 0; i < playersForm.length;i++)
            {
                if(playersForm[i].value.trim() != '')
                {
                    data.append(playersForm[i].name, playersForm[i].value);
                }
               
            }
       

            const xhttp2 = new XMLHttpRequest();
            xhttp2.onload = function() {
                console.log(this.responseText);
                let response = JSON.parse(this.responseText);
                console.log(response);
                if(response.success == 1)
                {
                    loadData();
                    successAnimation();
                }
                else
                {
                    alert("uživateli již byla zaslána pozvánka");
                }
            }
            xhttp2.open("POST", "model/get-teams.php");
            xhttp2.send(data);
        }

        function createTeam()
        {           
            var data = new FormData();
            data.append('name', $('#name').val());       
            //TODO FOTKA    
            data.append('create', '1');

            const xhttp2 = new XMLHttpRequest();
            xhttp2.onload = function() {
                console.log('post se povedl');
                console.log(this.responseText);
                let response = JSON.parse(this.responseText);
                console.log(response);
                if(response.success == 1)
                {
                    window.location.href = "tym-edit.php?edit="+response.team_id;
                }
                else
                {
                    alert("error");
                }
            }
            xhttp2.open("POST", "model/get-teams.php");
            xhttp2.send(data);
        }

        function confirmationCheck(selector)
        {
            var minus = document.querySelectorAll(selector);
            for (let i = 0; i < minus.length; i++) 
            {
                minus[i].addEventListener("click", function() {
                    let username = minus[i].previousElementSibling.value;
                    if(confirm("opravdu chcete odstranit "+username+" z týmu?"))
                    {
                        if(selector == '.players .fa-minus')
                        {
                            removePlayer(username, "member");
                        }
                        else
                        {
                            removePlayer(username, "invite");
                        }
                        
                    }
                });
            }
        }

        function checkRights(canEdit)
        {
            if(canEdit != 1)
            {
                throw new Error("Tady nemáš žádnou moc! a ani pravomoce na editaci :(");
            }
        }

        function leaveTeam()
        {
            checkRights(isMember);
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {

                console.log(this.responseText);
                let response = JSON.parse(this.responseText);
                console.log(response);

                if(response.success == 1)
                {
                    window.location.href = "tymy.php";
                }
                else
                {
                    alert("error");
                }
            }
            xhttp.open("GET", "model/get-teams.php?leave&team_id="+id);
            xhttp.send();
        }

        function removePlayer(username, type)
        {          
            checkRights(canEdit);
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                let response = JSON.parse(this.responseText);

                if(response.success == 1)
                {
                    loadData();
                    successAnimation();
                }
                else
                {
                    alert("error");
                }
            }
            xhttp.open("GET", "model/get-teams.php?delete&username="+username+"&team_id="+id+"&type="+type);
            xhttp.send();
        }

        function deleteTeam()
        {   
            checkRights(canEdit);
            if(confirm("opravdu chcete odstranit tým?"))
            {
                var data = new FormData();
                data.append('team_id', id);       
                data.append('deleteTeam', '1');

                const xhttp = new XMLHttpRequest();
                xhttp.onload = function() {

                    let response = JSON.parse(this.responseText);

                    if(response.success == 1)
                    {
                        window.location.href = "tymy.php";
                    }
                    else
                    {
                        alert("error");
                    }
                }
                xhttp.open("POST", "model/get-teams.php");
                xhttp.send(data);
            } 
        }
        
        
        if(urlParams.get('create') == null)
        {
            loadData();
            let players = document.querySelector('form .players > div');
            document.querySelector('.fa-plus').addEventListener('click', function(){

                let content= '';
                content += '<input list="allPlayersList" name="p'+currentPlayerIteration+'"><datalist id="allPlayersList">';
                for(let i = 0; i < allPlayers.length; i++)
                {
                    content += '<option value="'+ allPlayers[i].username +'"></option>';
                }
                content += '</datalist> ';
                $('form .players > div').append(content);
                currentPlayerIteration++;

            });
        }
			
		
		 $(document).ready(function () {
		  $('select').selectize({
			  sortField: 'text'
		  });
  		});
       
       
        window.addEventListener('load', function () {
            
            document.querySelector('form').addEventListener('submit', function(evt){
                evt.preventDefault();
                if(urlParams.get('deleteTeam') != null)
                {
                    deleteTeam();
                }
                if(urlParams.get('create') != null)
                {       
                    createTeam();
                }
                else
                {
                    editTeam();
                }
            });
            

            
        });
        
    </script>
    <div class="cover"></div>
</body>
</html>