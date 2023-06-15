<?php
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <title>PUCIS | Turnaje</title>
    <style>
        /**
        * Author: Tomáš Souček, xsouce15
        */
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

        .collapsible 
        {
            color: #555;
            cursor: pointer;
            padding: 18px;
            width: 100%;
            text-align: left;
            outline: none;
            font-size: 15px;
        }
        .collapsible.odd
        {
            background: #fff9ef;
        }

        .active, .collapsible:hover 
        {
            background-color: #feedce;
        }

        .content
        {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.2s ease-out;
            background-color: #f1f1f1;
            
        }
        .content-container
        {
            display:grid;
            grid-template-columns: auto auto 1fr;
            grid-template-rows: auto auto 1fr;
            padding: 1em;
        }
        .content span
        {
            padding: 0.5em 1em;
        }
        .content a
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
            margin-top: 15%;
            border-radius: 15px;
        }
        .name
        {
            position: relative;
        }
        .name i
        {
            position: absolute;
            right: 15px;
            top: 30%;
            font-size: 1.5em;
            color: #777;
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
        .tournament-container
        {
            border-radius: 15px;
            box-shadow: 0 0 10px #ccc;
        }

        {box-sizing: border-box;}

        .form-container {
            min-width: 100px;
            width:300px;
            padding: 10px;
            background-color: white;
            height: 400px;
            border-radius: 20px;
            display: none;
            border: 1px solid black;
            z-index: 9;

            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .teams-offer{
            margin-top:20px;
            padding: 20px;
            overflow:scroll;
            overflow-x:hidden;
            display: grid;
            gap: 10px;
            grid-template-comuns: 1fr;
            justify-content: center;
            max-height:300px;
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
        .dropdown-buttons{
            grid-column: 3/3;
            grid-row:2/2;
            display:flex;
            flex-direction: row;
            gap: 20px;
            justify-content: flex-end;
        }
        .dropdown-buttons a{
            margin:0;
            
        }

        @media screen and (max-width: 1100px){
            .content-container{
                grid-template-columns: auto auto auto;
                grid-template-rows: auto auto auto;
            }
        }

        @media screen and (max-width: 690px){
            .content-container{
                display:grid;
                grid-template-columns: auto auto;
                grid-template-rows: auto auto auto auto auto;
                padding: 1em;
                height: 400px;
            }
            .dropdown-buttons{
                grid-column: 1/ span 2;
                grid-row:4/4;
                justify-content: space-around;
            }

            .content-container a {
                margin:0;
                margin: auto;
                grid-column: 1/ span 2;
                grid-row:4/4;
            }
        }
        @media screen and (max-width: 480px){
            .content-container{
                display: flex;
                flex-flow: column;
                padding: 1em;
                height: 400px;
            }
            .dropdown-buttons{
                margin-top: 20px;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
            .content-container a {
                margin:0;
                margin: auto;
            }
        }
    </style>
</head>
<body>
    <?php 
        /**
         * Author: Tomáš Souček, xsouce15
         */
        print_notifications();
        print_sidenav();

    ?>

    <main>
        <a class="btn" onclick="create()"><i class="fa-solid fa-plus"></i>Vytvořit turnaj</a>
        <div class="searchbar">
            <input type="text" id="searchInput" onkeyup="filter()" placeholder="začněte vyhledávat">
            <i class="fa-sharp fa-solid fa-magnifying-glass"></i>
        </div>
		<?php
		if(isset($_REQUEST["sort"])){
			print("<h2>Současně vyfiltrované turnaje: ".$_REQUEST["sort"]."</h2>");
		}?>
        <h2>Mnou pořádané turnaje</h2>
        <div class="error"></div>
        <div class="tournament-container creator">
        </div>

        <h2>Turnaje, kterých se účastním</h2>
        <div class="error-added"></div>
        <div class="tournament-container added">
        </div>

        <h2>Ostatní turnaje</h2>
        <div class="error-others"></div>
        <div class="tournament-container others">
        </div>

        <?php/**
        * Author: David Kocman, xkocma08
        */?>
        <div class="form-container" id="myForm">
            <div class="head">
                <h2>Vyber Tým<h2>
                <a id="close" onclick="closeForm()"> <i class="fa-solid fa-xmark"></i></a>
            </div>
            <div class="teams-offer">
            </div>
        </div>

        

    </main>

    <?php print_footer(); ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
		/**
         * Author: David Kocman, xkocma08
         */
        get_creator();
        added();
        others();
        //creator
        function get_creator(){
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                
                let tournaments = JSON.parse(this.responseText);
                let container = document.querySelector(".creator");
                let content = '';

                const isEmpty = Object.keys(tournaments).length === 0;
                if(isEmpty){
                    document.querySelector(".error").innerHTML = '<p>Zatím jsi nevytvořil žádné turnaje!</p>';
                }
                else{
                    for(let i = 0; i < tournaments.length; i++)
                    {
                        content += '<div class="name">';
                            content += '<h3 class="collapsible ';
                            if(i % 2 == 0) { content += 'odd';}
                            content += '">'+ tournaments[i].name +'</h3>';
                            content += ' <i class="fa-solid fa-caret-down"></i>';
                        content += '</div>';

                        content += '<div class="content">';
                            content += '<div class="content-container">';
                                if(tournaments[i].type == 0){
                                    content += '<span>Týmový <input type="checkbox" checked onclick="return false;"></span>';
                                    content += '<span>Maximální velikost týmu: '+ tournaments[i].size +'</span>';
                                    content += '<span>Přihlášeno týmů: '+ tournaments[i].currentCount +'/'+tournaments[i].max_teams+'</span>';
                                }
                                else{
                                    content += '<span>Týmový <input type="checkbox" onclick="return false;"></span>';
                                    content += '<span>Přihlášeno hráčů: '+ tournaments[i].currentCount +'/'+tournaments[i].max_teams+'</span>';
                                }
                                if(tournaments[i].accepted == 0){
                                    content += '<span>Přijat: NE</span>';
                                }
                                else{
                                    content += '<span>Přijat: ANO</span>';
                                }
                                content += '<span>Sport: '+ tournaments[i].sport +'</span>';
                                content += '<span>Datum: '+ tournaments[i].date +'</span>';
                                content += '<a href="turnaj-sprava.php" onclick="detail(' + tournaments[i].turnament_id + ')">Detail turnaje</a>';
                            content += '</div>';
                        content += '</div>';
                    }
                    container.innerHTML = content;
                }
                
                addCollapsible();
            }
			<?php
			if(isset($_REQUEST["sort"]))
			{?>	
				xhttp.open("GET", "model/get-tournaments.php?creator&sort=<?php print($_REQUEST["sort"]);?>");
			<?PHP }
			else{ ?>
				xhttp.open("GET", "model/get-tournaments.php?creator");
			<?php }
			?>
            
            xhttp.send();
        }
        
        function others(){
            //others
            const xhttp2 = new XMLHttpRequest();
            xhttp2.onload = function() {

                let tournaments = JSON.parse(this.responseText);
                let container2 = document.querySelector(".others");
                container2.innerHTML = '';
                
                let content2 = '';

                const isEmpty = Object.keys(tournaments).length === 0;
                if(isEmpty){
                    document.querySelector(".error-others").innerHTML = '<p>Žádné další turnaje nejsou k dispozici!</p>';
                }
                else{
                    document.querySelector(".error-others").innerHTML = '';
                    for(let j = 0; j < tournaments.length; j++)
                    {
                        content2 = '';
                        content2 += '<div class="name">';
                            content2 += '<h3 class="collapsible ';
                            if(j % 2 == 0) { content2 += 'odd';}
                            content2 += '">'+ tournaments[j].name +'</h3>';
                            content2 += ' <i class="fa-solid fa-caret-down"></i>';
                        content2 += '</div>';

                        content2 += '<div class="content">';
                            content2 += '<div class="content-container">';
                                if(tournaments[j].type == 0){
                                    content2 += '<span>Týmový <input type="checkbox" checked onclick="return false;"></span>';
                                    content2 += '<span>Maximální velikost týmu: '+ tournaments[j].size +'</span>';
                                    content2 += '<span>Přihlášeno týmů: '+ tournaments[j].currentCount +'/'+tournaments[j].max_teams+'</span>';
                                }
                                else{
                                    content2 += '<span>Týmový <input type="checkbox" onclick="return false;"></span>';
                                    content2 += '<span>Přihlášeno hráčů: '+ tournaments[j].currentCount +'/'+tournaments[j].max_teams+'</span>';
                                }
                                content2 += '<span>Sport: '+ tournaments[j].sport +'</span>';
                                content2 += '<span>Datum: '+ tournaments[j].date +'</span>';
                                content2 += '<div class="dropdown-buttons">';
                                    content2 += '<a href="turnaj-detail.php" onclick="detail(' + tournaments[j].turnament_id + ')">Zobrazit turnaj</a>';
                                    content2 += '<a onclick="join(' + tournaments[j].turnament_id +','+ tournaments[j].type + ')">Přihlásit se</a>';
                                content2 += '</div>';
                            content2 += '</div>';
                        content2 += '</div>';
                        container2.innerHTML += content2;
                        addCollapsible();
                    }
                }
            }
			<?php
			if(isset($_REQUEST["sort"]))
			{?>	
				xhttp2.open("GET", "model/get-tournaments.php?others&sort=<?php print($_REQUEST["sort"]);?>");
			<?PHP }
			else{ ?>
				xhttp2.open("GET", "model/get-tournaments.php?others");
			<?php }
			?>
            xhttp2.send();
        }
        
        function added(){
            const xhttp5 = new XMLHttpRequest();
            xhttp5.onload = function() {
                let tournaments = JSON.parse(this.responseText);
                let container2 = document.querySelector(".added");
                container2.innerHTML = '';
                
                let content2 = '';

                const isEmpty = Object.keys(tournaments).length === 0;
                if(isEmpty){
                    document.querySelector(".error-added").innerHTML = '<p>Neúčastníš se žádných turnajů!</p>';
                }
                else{
                    document.querySelector(".error-added").innerHTML = '';
                    for(let j = 0; j < tournaments.length; j++)
                    {
                        content2 = '';
                        content2 += '<div class="name">';
                            content2 += '<h3 class="collapsible ';
                            if(j % 2 == 0) { content2 += 'odd';}
                            content2 += '">'+ tournaments[j].name +'</h3>';
                            content2 += ' <i class="fa-solid fa-caret-down"></i>';
                        content2 += '</div>';

                        content2 += '<div class="content">';
                            content2 += '<div class="content-container">';
                                if(tournaments[j].type == 0){
                                    content2 += '<span>Týmový <input type="checkbox" checked onclick="return false;"></span>';
                                    content2 += '<span>Maximální velikost týmu: '+ tournaments[j].size +'</span>';
                                    content2 += '<span>Přihlášeno týmů: '+ tournaments[j].currentCount +'/'+tournaments[j].max_teams+'</span>';
                                }
                                else{
                                    content2 += '<span>Týmový <input type="checkbox" onclick="return false;"></span>';
                                    content2 += '<span>Přihlášeno hráčů: '+ tournaments[j].currentCount +'/'+tournaments[j].max_teams+'</span>';
                                }
                                content2 += '<span>Sport: '+ tournaments[j].sport +'</span>';
                                content2 += '<span>Datum: '+ tournaments[j].date +'</span>';
                                if(tournaments[j].accepted == 0){
                                    if(tournaments[j].type == 0){
                                        content2 += '<span><b>Tým čeká na schválení</b></span>';
                                    }
                                    else{
                                        content2 += '<span><b>Hráč čeká na schválení</b></span>';
                                    }   
                                }
                                else{
                                    if(tournaments[j].type == 0){
                                        content2 += '<span><b>Tým schválen</b></span>';
                                    }
                                    else{
                                        content2 += '<span><b>Hráč schválen</b></span>';
                                    }  
                                }
                                content2 += '<div class="dropdown-buttons">';
                                    content2 += '<a href="turnaj-detail.php" onclick="detail(' + tournaments[j].turnament_id + ')">Zobrazit turnaj</a>';
                                    content2 += '<a onclick="leave('+ tournaments[j].turnament_id +')">Odhlásit se</a>';
                                content2 += '</div>';
                            content2 += '</div>';
                        content2 += '</div>';
                        container2.innerHTML += content2;
                        addCollapsible();
                    }
                }
            }
            <?php
			if(isset($_REQUEST["sort"]))
			{?>	
				xhttp5.open("GET", "model/get-tournaments.php?added&sort=<?php print($_REQUEST["sort"]);?>");
			<?PHP }
			else{ ?>
				xhttp5.open("GET", "model/get-tournaments.php?added");
			<?php }
			?>
            xhttp5.send();
        }
        

        function detail(id){
            var data = new FormData();
            data.append('detail', id);

            const xhttp3 = new XMLHttpRequest();
            xhttp3.onload = function() {
                
            }
            xhttp3.open("POST", "model/get-tournaments.php?detail");
            xhttp3.send(data);
        }

        function join(id, type){
            var data = new FormData();
            data.append('join', id);
            data.append('type', type);
            
            if(type==0){
                

                const xhttp = new XMLHttpRequest();
                xhttp.onload = function() {
                    
                    var obj = JSON.parse(this.responseText);
                    if(obj.hasOwnProperty('logged')){
                        if(obj.logged == "0"){
                            alert("Pro přihlášení se na turnaj se musíš přihlásit!");
                            return;
                        }
                    }
                    document.getElementById("myForm").style.display = "block";

                    var cont = document.querySelector(".teams-offer");
                    var inner = '';

                    for(let i = 0; i < obj.length;i++){

                        inner += '<div class="teams-tile" onclick="join_team('+ obj[i].team_id +','+ id +')">';
                            inner += '<img src="'+ obj[i].logo +'" alt="logo">';
                            inner += '<h3>'+ obj[i].name +'</h3>';
                        inner += '</div>';
                        
                    }
                    cont.innerHTML = inner;

                }
                xhttp.open("GET", "model/get-tournaments.php?teams");
                xhttp.send();
            }
            else{
                const xhttp4 = new XMLHttpRequest();
                xhttp4.onload = function() {
                    var obj = JSON.parse(this.responseText);
                    if(obj.hasOwnProperty('logged')){
                        if(obj.logged == "0"){
                            alert("Pro přihlášení se na turnaj se musíš přihlásit!");
                            return;
                        }
                    }

                    get_creator();
                    added();
                    others();
                }
                xhttp4.open("POST", "model/get-tournaments.php?join");
                xhttp4.send(data);
                
            }
        }
        function closeForm() {
            document.getElementById("myForm").style.display = "none";
        }
        function join_team(team_id, tourn_id){
            const xhttp4 = new XMLHttpRequest();
            var data = new FormData();
            data.append('join', team_id);
            data.append('type', 0);
            data.append('tourn_id', tourn_id);
            
            xhttp4.onload = function() {
                var obj = JSON.parse(this.responseText);

                if(obj.success == "1"){
                    document.getElementById("myForm").style.display = "none";
                }
                get_creator();
                added();
                others();
            }
            xhttp4.open("POST", "model/get-tournaments.php?join");
            xhttp4.send(data);  
        }

        function leave(turn_id){
            if (!confirm('Opravdu se chceš odhlásit z turnaje?')) {
                return;
            }
            console.log(turn_id);
            const xhttp4 = new XMLHttpRequest();
            var data = new FormData();
            data.append('leave', turn_id);
            console.log(turn_id);
            
            xhttp4.onload = function() {
                
                var obj = JSON.parse(this.responseText);

                if(obj.error == "left"){
                    alert("Nejsi kapitán týmu!");
                    return;
                }
                else{
                    get_creator();
                    added();
                    others();
                }
            }
            xhttp4.open("POST", "model/get-tournaments.php?leave");
            xhttp4.send(data);  
        }

        function create(){

            const xhttp3 = new XMLHttpRequest();
            xhttp3.onload = function() {
                var obj = JSON.parse(this.responseText);

                if(obj.good == "1"){
                    window.location.replace("turnaj-create.php");
                }
                else{
                    alert("Nejste přihlášen!");
                }
            }
            xhttp3.open("POST", "model/get-tournaments.php?create_tourn");
            xhttp3.send();
        }

    </script>
    <script>
        /**
         * Author: Tomáš Souček, xsouce15
         */
         function filter() {
            var input = document.getElementById("searchInput");
            var filter = input.value.toUpperCase();
           
            var container = document.querySelectorAll(".tournament-container");
            for(let j = 0; j < container.length; j++)
            {
                for (let i = 0; i < container[j].children.length; i += 2) { //potřeba iterovat ob jedno jelikož mě zajímají pouze h3 uvnitř div.name
                    var tournament = container[j].children[i].getElementsByTagName("h3")[0];
                    var txtValue = tournament.textContent || tournament.innerText;
         
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        
                        container[j].children[i].style.display = "";
                    } else {
                        container[j].children[i].style.display = "none";
                    }
                }
            }
           
        }

        function addCollapsible()
        {
            var coll = document.getElementsByClassName("collapsible");
            for (let i = 0; i < coll.length; i++) 
            {
                coll[i].removeEventListener("click", collapse); //aby se zamezilo více click handlerů na již existující elementy
                coll[i].addEventListener("click", collapse);
            }
        }
        function collapse()
        {   
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
        }
    </script>
     
</body>
</html>