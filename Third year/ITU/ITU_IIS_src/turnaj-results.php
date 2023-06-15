<?php 
/**
 * Author: Adam Cologna, xcolog00
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
            .flex
            {
                align-items: center;
                justify-content: space-between;
                margin-top: 4em;
            }
            h3
            {
                margin-bottom: 0;
            }
        </style>
    </head>
<body>
    <?php 
        print_notifications();
        print_sidenav(); 
    ?>

    <main>
        <h1>Zadávání výsledků zápasů</h1>
        <form class="container" id="single-form">
            <div class="data">
            </div>

            <div class="buttons">
                <input type="submit" id="submit" name="submit" value="Uložit změny">
            </div>
        </form>
    </main>
    <?php print_footer(); ?>
    <script>
       function loadData()
        {
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                console.log(this.responseText);
                let results = JSON.parse(this.responseText);
                console.log(results);
                if(results.length == 0)
                {
                    createMatchup();
                    return 1;
                }

                let container = document.querySelector(".container .data");
                let content = '';

                for(let i = 0; i < results.length; i++)
                {
                    content += '<div class="flex">';
                        content += "<div>";
                            content += '<h3>'+results[i].team1_name+'</h3>';
                            content += '<input type="number" name="match'+results[i].match_id+'&team'+results[i].id_team1+'" value="'+results[i].result1+'">';
                        content += '</div>';
                        content += '<span>VS</span>';
                        content += '<div>';
                            content += '<h3>'+results[i].team2_name+'</h3>';
                            content += '<input type="number" name="match'+results[i].match_id+'&team'+results[i].id_team2+'" value="'+results[i].result2+'">';
                        content += '</div>';
                    content += '</div>';
                }

                container.innerHTML = content;

                document.querySelector('form').addEventListener('submit', function(evt){
                    evt.preventDefault();
                    pushData();

                });
            }
            xhttp.open("GET", "model/get-tournaments.php?matchup&finished=0");
            xhttp.send();
        }

        function pushData()
        {           
            $.ajax({
                type: 'post',
                url: 'model/get-tournaments.php?results',
                data: $('form').serialize(),
                success: function (response) {
                    if(JSON.parse(response).success == 1)
                    {
                        loadData();
                    }
                    else
                    {
                        alert("error");
                    }
                }
            })
        }

        loadData();
        
         function createMatchup()
         {
             $.ajax({
                type: 'get',
                url: 'model/create-matchup.php?ongoing',
                success: function (response) {
                    console.log(response);
                    if(JSON.parse(response).success == 1)
                    {
                        //successAnimation();
                     
                        window.location.href = "turnaj-sprava.php";
                        //loadData();
                    }
                    else
                    {
                        alert("error");
                    }
                }
            })
             
         }
        
    </script>
    
</body>
</html>