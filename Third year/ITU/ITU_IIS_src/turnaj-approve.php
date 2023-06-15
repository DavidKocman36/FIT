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
    <link href="css/tymy.css" rel="stylesheet">
    <title>PUCIS | Turnaje schválení týmů</title>
    <style>
        .fa-plus
        {
            background: none;
            padding: 0;
            padding-right: 2em;
        }
        main
        {
            margin-top: 3.5%;
            color: #555;
        }

        h3
        {
            margin:0;
        }
        
        .searchbar
        {
            position: relative;
            margin-top: 2em;
        }
        .searchbar i
        {
            position: absolute;
            right: 2%;
            top: 40%;
            font-weight: bold;
            transition: 0.3s ease-in-out;
        }
        .teams-tile 
        {
            display: flex;
            flex-direction: column;
            align-items: center;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 0 10px #ccc;
        }
        h1
        {
            text-align: left;
        }
        .teams-tile img{
            max-width:100px;
        }
        button{
            width: 50px;
            border: none;
            background-color: transparent;
        }
        .buttons i{
            font-size: 20px;
            position: relative;
        }
        .buttons{
            margin-bottom:20px;
            display: flex;
            flex-direction: row;
            gap: 20px;
        }

        {box-sizing: border-box;}

        .form-container {
            min-width: 100px;
            width:300px;
            padding: 10px;
            background-color: white;
            height: 200px;
            border-radius: 20px;
            display: none;
            border: 1px solid black;
            z-index: 9;
            box-shadow: 0 0 3px black;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .size{
            margin-top:20px;
            padding: 20px;
            overflow:hidden;
            display: grid;
            gap: 10px;
            grid-template-comuns: 1fr;
            justify-content: center;
            max-height:150px;
        }
        .form-container h2{
            margin:0;
            font-size:20px;
        }
        .head{
            display:flex;
            flex-direction:row;
            justify-content:space-between;
        }
    </style>
</head>
<body>
    

    <?php 
        print_notifications();
        print_sidenav(); 
    ?>

    <main>
        <h1 >Schvalování týmů/hráčů</h1>
        <div class="searchbar">
            <input type="text" placeholder="začněte vyhledávat">
            <i class="fa-sharp fa-solid fa-magnifying-glass"></i>
        </div>

        <h2 id="max_size"></h2>

        <h2 id="waiting">Týmy čekající na schválení</h2>

        <div class="team-list creator">
            <i class="fa-solid fa-chevron-left"></i>
            <div class="container">    
            </div>
            <i class="fa-solid fa-chevron-right"></i>
        </div>   

        <div class="form-container" id="myForm">
            <div class="head">
                <a id="close" onclick="closeForm()"> <i class="fa-solid fa-xmark"></i></a>
            </div>
            <div class="size">
                <h3 id="size-text"></h3>
                <div class="text"></div>
            </div>
        </div>
        
        <h2 id="count"> </h2>

        <div class="team-list accepted">
            <i class="fa-solid fa-chevron-left"></i>
            <div class="container">    
            </div>
            <i class="fa-solid fa-chevron-right"></i>
        </div>  
            

    </main>

    <?php print_footer(); ?>
    <script>
        var coll = document.getElementsByClassName("collapsible");

        for (let i = 0; i < coll.length; i++) {
        coll[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var content = this.parentElement.nextElementSibling;

            if(this.nextElementSibling.style.transform == "")
            {
                this.nextElementSibling.style.transform = "rotate(180deg)";
            }
            else
            {
                this.nextElementSibling.style.transform = "";
            }
            if (content.style.maxHeight)
            {
                content.style.maxHeight = null;
            } 
            else 
            {
                content.style.maxHeight = content.scrollHeight + "px";
            } 
        });
        }
    </script>
     <script>

        get();
        get_added();
        get_size();

        function get_size(){
            //var data = new FormData();
            //data.append('id', id);

            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                var res = JSON.parse(this.responseText);
                
                if(res[0].type == "0"){
                    document.getElementById("waiting").innerHTML = 'Týmy čekající na schválení';
                    document.getElementById("max_size").innerHTML = 'Maximální velikost týmu: '+ res[0].size +'';
                }
                else{
                    document.getElementById("waiting").innerHTML = 'Hráči čekající na schválení';
                }
            }
            xhttp.open("POST", "model/turnaj-approve-model.php?size");
            xhttp.send();
        }

        function get(){
            
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                var obj = JSON.parse(this.responseText);

                var cont = document.querySelector(".creator .container");
                var inner = '';

                for(let i = 0; i < obj.length;i++){

                    inner += '<div class="teams-tile">';
                        inner += '<div class="buttons">'
                            inner += '<button type="submit" class="accept" onclick="accept_or_decline(' + obj[i].tm_trn_id + ' ,1)"><i class="fa-solid fa-check\"></i></button>';
                            inner += '<button type="submit" class="decline" onclick="accept_or_decline(' + obj[i].tm_trn_id + ',0)"><i class="fa-solid fa-xmark"></i></button>';
                        inner += '</div>';
                        inner += '<div onclick="detail('+ obj[i].team_id +')">'
                            inner += '<img src="'+ obj[i].logo +'" alt="logo">';
                            inner += '<h3>'+ obj[i].name +'</h3>';
                        inner += '</div>';
                    inner += '</div>';
                    
                }
                cont.innerHTML = inner;

            }
            xhttp.open("GET", "model/turnaj-approve-model.php?create");
            xhttp.send();
        }

        function get_added(){
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                
                var obj = JSON.parse(this.responseText);

                var div = document.querySelector(".accepted .container");
                var inner = '';

                for(let i = 1; i < obj.length;i++){

                    inner += '<div class="teams-tile" onclick="detail('+ obj[i].team_id +')">';
                        inner += '<img src="'+ obj[i].logo +'" alt="logo">';
                        inner += '<h3>'+ obj[i].name +'</h3>';
                    inner += '</div>';
                    
                }
                div.innerHTML = inner;

                cnt = obj.length - 1;
                if(obj[0].type == 0){
                    document.getElementById("count").innerHTML = 'Schválené týmy: '+cnt+' z '+obj[0].max_teams+'';
                }
                else{
                    document.getElementById("count").innerHTML = 'Schválení hráči: '+cnt+' z '+obj[0].max_teams+'';
                }
                

            }
            xhttp.open("GET", "model/turnaj-approve-model.php?added");
            xhttp.send();
        }

        function accept_or_decline(id, flag){
            var data = new FormData();
            data.append('acc_or_dec', flag);
            data.append('id', id);

            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                console.log(this.responseText);
                var res = JSON.parse(this.responseText);
                if(res.update == "error"){
                    alert("Už je plno!");
                    return;
                }

                if(res.update == "done"){
                    alert("Přijat!");
                }
                else if(res.delete == "done"){
                    alert("Odmítnut!")
                }
            }
            xhttp.open("POST", "model/turnaj-approve-model.php");
            xhttp.send(data);
            get();
            get_added();
        }

        function detail(id){
            var data = new FormData();
            data.append('id', id);

            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                var res = JSON.parse(this.responseText);
                if(res[0].type == 0){
                    document.getElementById("myForm").style.display = "block";
                    document.getElementById("size-text").innerHTML = 'Velikost týmu: ' + res[1].cnt + '';
                    if(res[1].cnt < res[0].size){
                        document.querySelector(".text").innerHTML = '<p style="color: green">Velikost týmu je vyhovující, ale pod limitem!</p>';
                    }
                    else if(res[1].cnt == res[0].size){
                        document.querySelector(".text").innerHTML = '<p style="color: green">Velikost týmu je vyhovující!</p>';
                    }
                    else{
                        document.querySelector(".text").innerHTML = '<p style="color: red">Moc velký tým!</p>';
                    }
                    
                }
                
                //popup
            }
            xhttp.open("POST", "model/turnaj-approve-model.php?detail");
            xhttp.send(data);
        }

        function closeForm(){
            document.getElementById("myForm").style.display = "none";
        }

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
</body>
</html>