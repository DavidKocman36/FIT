<?php
include 'utils.php' 
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
        <link href="css/pavouk.css" rel="stylesheet">
        <link href="css/tymy.css" rel="stylesheet">
      
        <title>PUCIS | Turnaj detail</title>
        <style>
            main h1
            {
                text-align: left;
                
            }
            p
            {
                margin: 0;
                padding: 1em;
            }
            
            #name_trn{
                margin-bottom: 30px;
            }

            .data #user{
                display:flex;
                flex-direction: row;
                justify-content: space-between;
                gap: 120px;
            }

            
            #descr{
                
            }
            #popis{
                diplay: flex;
                flex-direction: column;
                max-width: 500px;
            }
            #creator_usr img{
                max-width: 150px;
            }
            #creator_usr{
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            #creator_usr h2{
                margin: 0;
            }
            .data a
            {
                padding: 0.75em;
                font-size: 0.9em;
                background: #fba405;
                color: #fff;
                font-weight: bold;
                width: fit-content;
                height: fit-content;
                text-decoration: none;
                grid-column: 3;
                grid-row: 1/-1;
                margin-left: auto;
                margin:auto;
                border-radius: 15px;
            }
            #user span{
                margin-top: 10px;
            }

            /*.theme{
                width: 400px;
            }
            .bracket{
                width: 400px;
            }*/
            @media screen and (max-width: 680px) {
                .data #user {
                    flex-direction: column;
                    gap: 30px;
                }
            }
        </style>
    </head>
<body>
    <?php 
        print_notifications();
        print_sidenav(); 
        /**
         * Author: David Kocman, xkocma08
         */
    ?>
    <main>
    <h1 id="name_trn"></h1>
    <div class="data">
        <h2>Informace</h2>
        <div id="user">
            <div id="descr">
                <label for="team">Popis turnaje</label>
                <textarea id="popis" readonly></textarea>
            </div>
            <div id="creator_usr">
            </div>
            
        </div>
        <div id="teams">
            <h2 id="teams_name"></h2>
            <div class="team-list creator">
                <i class="fa-solid fa-chevron-left"></i>
                <div class="container"></div>
                <i class="fa-solid fa-chevron-right"></i>
            </div>
        </div>
        <h1>Zápasy</h1>
        <h2 id="error"></h2>
    </div>
    <div id="pavouk">
        <div class="row">
        </div>
    </div>
    
<div class="theme theme-dark-trendy">
	<div class="bracket  disable-image">
		
	</div>
</div>
    </main>
    <script>
        loadData();
        get_tourn();
        /**
         * Author: Tomáš Souček, xsouce15
         */

        function getremainingTeamsCount(teams)
        {
            let winners = [];
            for(let i = 0; i < teams.length; i++)
            {
                if(teams[i].result1 > teams[i].result2)
                {
                    winners.push(teams[i].id_team1);
                }
                else
                {
                    winners.push(teams[i].id_team2);
                }
            }

            return winners;
        }

        function createNewMatchups(winners)
        {
            var jsonString = JSON.stringify(winners);
            $.ajax({
                type: "POST",
                url: "model/create-matchup.php",
                data: {data : jsonString}, 
                cache: false,

                success: function(data){
                    console.log(data);
                }
            });
        }

        function loadData()
        {
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                let response = JSON.parse(this.responseText);
                
                let content = '';
                let teamsCount = response.length; 
                console.log(teamsCount);
                let i = 0;
                let printThisAmount = Math.ceil(teamsCount)/2; //přiřadit počet zápasů (1 zápas = 2 týmy) na začátku UDĚLAT TO DYNAMICKY TODO
                let keepPrinting = true;
                

                while(keepPrinting)
                {
                    if(printThisAmount == 1 && i == 0)
                    {
                        printThisAmount++;
                    }
                    content += '<div class="column '+i+'">';
                    for(let j = 0; j < printThisAmount; j++)
                    {
                        if(j >= response.length)
                        {
                            break;
                        }
                        content += '<div class="match winner-';
                        if(response[j].result1 > response[j].result2)
                        {
                            content += 'top';
                        }
                        if(response[j].result1 < response[j].result2)
                        {
                            content += 'bottom';
                        }
                        content += '">';
                            content += '<div class="match-top team">';
                                content += '<span class="name">'+response[j].team1_name+'</span>';
                                content += '<span class="score">'+response[j].result1+'</span>';
                            content += '</div>';

                            content += '<div class="match-bottom team">';
                                content += '<span class="name">'+response[j].team2_name+'</span>';
                                content += '<span class="score">'+response[j].result2+'</span>';
                            content += '</div>';

                            content += '<div class="match-lines">';
                                content += '<div class="line one"></div>';
                                content += '<div class="line two"></div>';
                            content += '</div>';

                            content += '<div class="match-lines alt">';
                                content += '<div class="line one"></div>';
                            content += '</div>';
                        content += '</div>';
                    }
                        
                    content += '</div>';


                    console.log("printAmount: "+printThisAmount);
                    


                    response.splice(0,Math.ceil(printThisAmount)); //ze všech načetlých výsledků odstraní ty, které již byli vyprintovány
                    printThisAmount = printThisAmount / 2;
                    console.log(response.length);
                    if(response.length <= 0 || i == 10)
                    {
                        keepPrinting = false;
                    }
           
                    i++;
                    
          

                    //let winners = getremainingTeamsCount(response);
                    //console.log(winners);
                    //createNewMatchups(winners);

                }
                document.querySelector('.bracket').innerHTML = content;
                
                
            }
            xhttp.open("GET", "model/get-tournaments.php?matchup");
            xhttp.send();  
        }
        /**
         * Author: David Kocman, xkocma08
         */

        function get_tourn(){
            // 0 - user
            // 1 - turnaj
            // 2 - creator
            // 3 a vice - týmy
            const xhttp3 = new XMLHttpRequest();
            xhttp3.onload = function() {
                console.log(this.responseText);
                var obj = JSON.parse(this.responseText);

                

                if(obj[0].user_id == obj[1].creator){
                    document.querySelector(".data").style.display = "none";
                }
                else{
                    document.querySelector(".data").style.display = "visible";
                }

                /*if(obj[3].joined == 1){
                    document.getElementById("user").innerHTML += '<a href="turnaje.php" onclick="leave()">Odhlásit se</a>';
                }
                else{
                    document.getElementById("user").innerHTML += '<a onclick="join()">Přihlásit se</a>';
                }*/

                //tourn
                document.getElementById("name_trn").innerHTML = obj[1].name; 
                document.getElementById("popis").innerHTML = obj[1].description; 

                //creator 
                let creator = document.getElementById("creator_usr");
                var cont = '';

                cont += '<h2>Pořadatel</h2>';
                cont += '<img src="'+ obj[2].picture +'" alt="pfp">';
                cont += '<span>'+ obj[2].name +' '+ obj[2].surname +'</span>';
                creator.innerHTML = cont;


                //teams
                if(obj[1].type == 0){
                    document.getElementById("teams_name").innerHTML = 'Zúčastněné týmy'; 
                }
                else{
                    document.getElementById("teams_name").innerHTML = 'Zúčastnění hráči'; 
                }
                var div = document.querySelector(".creator .container");
                var inner = '';

                for(let i = 4; i < obj.length;i++){

                    inner += '<div class="teams-tile">';
                        inner += '<img src="'+ obj[i].logo +'" alt="logo">';
                        inner += '<h3>'+ obj[i].name +'</h3>';
                    inner += '</div>';
                    
                }
                div.innerHTML = inner;

                if(obj[1].matchup_generated == 0){
                    document.getElementById("error").innerHTML = 'Zatím ještě nebyla vytvořena soupiska!';
                }
            }
            xhttp3.open("POST", "model/get-tournaments.php?get-tourn");
            xhttp3.send();
        }

        /**
         * Author: Tomáš Souček, xsouce15
         */

        let tilesCreator = document.querySelectorAll('.creator .teams-tile');
        let arrowLeftCreator = document.querySelector('.creator  .fa-chevron-left');
        let arrowRightCreator = document.querySelector('.creator  .fa-chevron-right');

        let tilesMember = document.querySelectorAll('.member .teams-tile');
        let arrowLeftMember = document.querySelector('.member  .fa-chevron-left');
        let arrowRightMember = document.querySelector('.member  .fa-chevron-right');

        let containerWidth = document.querySelector('.container').offsetWidth;


        //init position
        for(let i = 0; i < tilesCreator.length; i++)
        {
            tilesCreator[i].style.left = '0';
        }

        //disable initial state arrows
        if(tilesCreator[0].offsetLeft > 1)
        {
            arrowLeftCreator.style.color = '#ccc';
        }

        if(tilesCreator[(tilesCreator.length-1)].offsetLeft < (containerWidth-200))
        {
            arrowRightCreator.style.color = '#ccc';
        }
        

        arrowLeftCreator.addEventListener('click', function(){

            if(arrowLeftCreator.style.color == 'rgb(204, 204, 204)')
            {
                return;
            }
            
           
            for(let i = 0; i < tilesCreator.length; i++)
            {
                let currPos = parseInt(tilesCreator[i].style.left,10);
                tilesCreator[i].style.left = (currPos+100) + 'px';
            }

            setTimeout(function(){
                if(tilesCreator[0].offsetLeft > 1)
                {
                    arrowLeftCreator.style.color = '#ccc';
                }
                else
                {
                    arrowLeftCreator.style.color = '#555'; 
                }

                if(tilesCreator[(tilesCreator.length-1)].offsetLeft < (containerWidth-200))
                {
                    arrowRightCreator.style.color = '#ccc';
                }
                else
                {
                    arrowRightCreator.style.color = '#555'; 
                }
            },500);
        });

        arrowRightCreator.addEventListener('click', function(){

            if(arrowRightCreator.style.color == 'rgb(204, 204, 204)')
            {
                return;
            }

            for(let i = 0; i < tilesCreator.length; i++)
            {
                let currPos = parseInt(tilesCreator[i].style.left,10);
                currPos = currPos * (-1);
                tilesCreator[i].style.left = '-' + (currPos+100) + 'px';
                console.log("posouvam");
            }

            setTimeout(function(){
                if(tilesCreator[0].offsetLeft > 1)
                {
                    arrowLeftCreator.style.color = '#ccc';
                }
                else
                {
                    arrowLeftCreator.style.color = '#555'; 
                }

                if(tilesCreator[(tilesCreator.length-1)].offsetLeft < (containerWidth-200))
                {
                    arrowRightCreator.style.color = '#ccc';
                }
                else
                {
                    arrowRightCreator.style.color = '#555'; 
                }
            },500);
        });





        //init position
        for(let i = 0; i < tilesMember.length; i++)
        {
            tilesMember[i].style.left = '0';
        }

        //disable initial state arrows
        if(tilesMember[0].offsetLeft > 1)
        {
            arrowLeftMember.style.color = '#ccc';
        }

        if(tilesMember[(tilesMember.length-1)].offsetLeft < (containerWidth-200))
        {
            arrowRightMember.style.color = '#ccc';
        }
        

        arrowLeftMember.addEventListener('click', function(){

            if(arrowLeftMember.style.color == 'rgb(204, 204, 204)')
            {
                return;
            }
            
           
            for(let i = 0; i < tilesMember.length; i++)
            {
                let currPos = parseInt(tilesMember[i].style.left,10);
                tilesMember[i].style.left = (currPos+100) + 'px';
            }

            setTimeout(function(){
                if(tilesMember[0].offsetLeft > 1)
                {
                    arrowLeftMember.style.color = '#ccc';
                }
                else
                {
                    arrowLeftMember.style.color = '#555'; 
                }

                if(tilesMember[(tilesMember.length-1)].offsetLeft < (containerWidth-200))
                {
                    arrowRightMember.style.color = '#ccc';
                }
                else
                {
                    arrowRightMember.style.color = '#555'; 
                }
            },500);
        });

        arrowRightMember.addEventListener('click', function(){

            if(arrowRightMember.style.color == 'rgb(204, 204, 204)')
            {
                return;
            }

            for(let i = 0; i < tilesMember.length; i++)
            {
                let currPos = parseInt(tilesMember[i].style.left,10);
                currPos = currPos * (-1);
                tilesMember[i].style.left = '-' + (currPos+100) + 'px';
                console.log("posouvam");
            }

            setTimeout(function(){
                if(tilesMember[0].offsetLeft > 1)
                {
                    arrowLeftMember.style.color = '#ccc';
                }
                else
                {
                    arrowLeftMember.style.color = '#555'; 
                }

                if(tilesMember[(tilesMember.length-1)].offsetLeft < (containerWidth-200))
                {
                    arrowRightMember.style.color = '#ccc';
                }
                else
                {
                    arrowRightMember.style.color = '#555'; 
                }
            },500);
        });
    </script>
    <?php print_footer(); ?>
</body>
</html>