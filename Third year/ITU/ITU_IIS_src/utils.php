<?php
/**
 * Author: Tomáš Souček, xsouce15
 */
session_start();
include_once 'db_operations.php';

function print_notifications()
{
  if(!isset($_SESSION["user_id"]))
  {
    return -1;
  }
   //status = 1 (accepted), status = 0 (pending), status = -1 (declined)
    $details = get_data("SELECT team_invite.user_id, team_invite.team_id, team_invite.status, team.name FROM team_invite LEFT JOIN team ON team.team_id = team_invite.team_id WHERE team_invite.user_id = ".$_SESSION["user_id"]." AND team_invite.status = 0");

    $count = count($details);
    ?>
    <span class="notification">
        <div>
            <i class="fa-solid fa-bell"></i>
            <?php 
              if(!empty($details) && $count > 0)
              {
                echo '<span class="active">'.$count.'</span>';
              }
            ?>     
        </div> 
    </span>
    <div class="notification-popup-container">
      <?php
      for($i = 0; $i < $count; $i++)
      {
        echo '<div class="notification-popup">Došla ti pozvánka do týmu <a style="font-weight: bold; text-decoration: underline;" href="tym-edit.php?edit='.$details[$i]["team_id"].'">'.$details[$i]["name"].'</a>. Jak na ni zareaguješ? <span><a class="true" href="#" id="1" team="'.$details[$i]["team_id"].'">Přijmout</a>        <a class="false" href="#" id="-1" team='.$details[$i]["team_id"].'>Odmítnout</a></span>      </div>';
      }
    ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.1.slim.js" integrity="sha256-tXm+sa1uzsbFnbXt8GJqsgi2Tw+m4BLGDof6eUPjbtk=" crossorigin="anonymous"></script>
    <script>
    $(document).ready(function () {
            document.querySelector('.fa-bell').addEventListener('click', function(){
           document.querySelector('.notification-popup-container').classList.toggle("visible");
           document.querySelector('.notification span').style.display = "none";
      });
      /*$('.fa-bell').click(function(){
        $('.notification-popup-container').toggleClass('visible');
        $('.notification span').css("display", "none");
      });*/

      //odstranění řádku z popup okna po přijmutí nebo odmítnutí pozvánky
      $('.notification-popup span a.true').click(function(event){
        
        var data = new FormData();
        data.append('status', event.target.id);     
        data.append('team_id', $(this).attr('team'));    
        var parent = $(this).parent().parent();

        const xhttp = new XMLHttpRequest();
        xhttp.onload = function() {
            let response = JSON.parse(this.responseText);
            if(response.success == 1)
            {
              parent[0].remove();
            }
            else
            {
              alert("error");
            }
        }
        xhttp.open("POST", "model/invitations-model.php");
        xhttp.send(data);
      });
      
      $('.notification-popup span a.false').click(function(event){
        
        var data = new FormData();
        data.append('status', event.target.id);     
        data.append('team_id', $(this).attr('team'));    
        var parent = $(this).parent().parent();

        const xhttp = new XMLHttpRequest();
        xhttp.onload = function() {
            console.log(this.responseText);
            let response = JSON.parse(this.responseText);
            if(response.success == 1)
            {
              parent[0].remove();
            }
            else
            {
              alert("error");
            }
        }
        xhttp.open("POST", "model/invitations-model.php");
        xhttp.send(data);
      });
      /*let decisions = document.querySelectorAll('.notification-popup span a');
      for(let i = 0; i < decisions.length; i++)
      {
        decisions[i].addEventListener('click', function(event){
        var data = new FormData();
        data.append('status', event.target.id);     
        data.append('team_id', event.target.getAttribute('team'));    
        var parent = event.target.parentElement.parentElement;

        console.log(data);

        const xhttp = new XMLHttpRequest();
        xhttp.onload = function() {
            console.log(this.responseText);
            let response = JSON.parse(this.responseText);
            if(response.success == 1)
            {
              parent.remove();
            }
            else
            {
              alert("error");
            }
        }
        xhttp.open("POST", "model/invitations-model.php");
        xhttp.send(data);
      });
      }*/
    });
      
      

      

    </script>
    <?php
}
/**
 * Author: Adam Cologna, xcolog00
 */
function print_sidenav()
{	
	if(isset($_SESSION["user_id"])){
    $sql = "SELECT picture FROM users WHERE user_id = ".$_SESSION["user_id"];
    $results = get_data($sql);
	}
  echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>';
    ?>
     
    <style>
      .menu_wrapper h1{
          color: #333;
      }
      .menu_wrapper{
          width: 100%;
          height: 100%;
          background: rgba(0, 0, 0, 0.6);
          display: none;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          position: absolute;
          bottom: 0;
          top: 0;
          right: 0;
          left: 0;
          font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
          transition: 0.5;
      }
      .menu_wrapper.visible{
          display: flex;
          position: fixed;
          z-index: 10;
          
      }
      .menu_wrapper button{
          position: relative;
          width: 25px;
          border: none;
          background-color: transparent;
          left: 120px;
          top: 10px;
          cursor: pointer;
      }
      body.noscroll{
          overflow-y: hidden;
      }
      .dropdown_menu{
          margin: 0;
          width: 300px;
          height: 380px;
          display: flex;
          flex-direction: column;
          align-items: center;
          border: 1px solid black;
          border-radius: 20px;
          text-align: center;
          margin: auto;
          margin-top: 80px;
          background-color: white;
      }
      #login-username{
          margin: 15px;
          padding: 10px;
          border-radius: 20px;
          border: 1px solid black;
          width: 80%;
      }
      #login-password{
          margin: 15px;
          padding: 10px;
          border-radius: 20px;
          border: 1px solid black;
          width: 80%;
      }
      #login-submit{
          margin: 15px;
          background-color: #fba405;
          box-shadow: 0 0 10px 2px #fba405;
          transition: 0.5s;
          border: 0px;
          border-radius: 20px;
          padding: 10px;
          width: 80%;
          cursor: pointer;
      }
      #logout-submit{
        margin: 15px;
          background-color: #fba405;
          box-shadow: 0 0 10px 2px #fba405;
          transition: 0.5s;
          border: 0px;
          border-radius: 20px;
          padding: 10px;
          width: 80%;
          cursor: pointer;
      }
      .menu_wrapper input, select{
          box-sizing: border-box;
      }
      #login-submit:hover{
          background-color: #db8e03;
          box-shadow: 0 0 10px 2px #db8e03;
      }
      #nazev{
          font-size: 40px;
          margin-top: auto;
          margin-bottom: 10px;
      }
      .logo_wrapper{
          display: flex;
          gap: 20px;
          margin-top: 20px;
          justify-content: center;
      }
      #logo{
          max-width: 50px;
      }
      #registr{
          font-size: 12px;
          color: gray;
      }
      .wrapper a{
        color: gray;  
      }
      .checkbox{
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 5px;
      }
      #login-remember_me{
          margin: 0;
          width: 11px;
          cursor: pointer;
      }
      #remember_me{
          font-size: 12px;
          color: gray;
      }
      .side-nav .hamburger-items
      {
        gap: 1.5em;
        display: flex;
        flex-direction: column;
      }
      .hamburger 
      {
        display: none;
        cursor: pointer;
      }
      @media screen and (max-width: 1100px) {
        .hamburger-items
        {
          display: none;
        }
        .hamburger 
        {
          display: initial;
        }
        .side-nav
        {
          flex-direction: row;
          height: initial;
          padding: 1em;
          width: 100%;
          justify-content: space-between;
          box-shadow: 0 0 5px 0px #ccc;
        }
        .side-nav .hamburger-items
        {
          display:none;
          position: absolute;
          flex-direction: row;
          top: 100%;
          background: white !important;
          width: calc(100% + 0.5em);
          left: -0.5em;
          gap: 0.5em;
          padding: 1em;
          justify-content: space-between;
        }
        .side-nav img
        {
          display: none;
        }
        main
        {
          padding-left: 0;
          margin-top: 14em !important;
        }
      }
    </style>
    <aside class="side-nav">
        <h3><a href="index.php"><span style="color: #fba405">PUC</span>IS</a></h3>
        <?php if(isset($_SESSION["user_id"])){ ?>
            <img src="<?php echo $results[0]["picture"] ?>" alt="profilová fotka" class="profile-photo">
        <?php ;} ?>
        <div class="hamburger-items">
            <a id="homepage" href="index.php"><span class="icon"><i class="fa-solid fa-tv"></i></span></a>
            <?php 
            if(isset($_SESSION["username"])){?>
              <a id="tym" href="tymy.php"><span class="icon"><i class="fa-solid fa-people-group"></i></span></a>
            <?php ;}
            else{ ?>
              <a id="tym" href="tym-detail.php"><span class="icon"><i class="fa-solid fa-people-group"></i></span></a>
            <?php ;} ?>
                <a id="turnaj" href="turnaje.php"><span class="icon"><i class="fa-solid fa-trophy"></i></span></a>
                <?php 
            if(isset($_SESSION["username"])){?>
              <a id="profil" href="profil.php"><span class="icon"><i class="fa-solid fa-user"></i></span></a>
            <?php ;}
            else{ ?>
              <a id="profily_uzivatelu" href="users.php"><span class="icon"><i class="fa-solid fa-user"></i></span></a>
            <?php ;} ?>
            <?php if(!isset($_SESSION["username"])){ ?>
              <a id="login-button"><span class="icon"><i class="fa-solid fa-right-to-bracket"></i></span></a> <?php ;} 
            else{?>
              <a id="login-button"><span class="icon"><i class="fa-solid fa-right-from-bracket"></i></span></a> <?php ;
            } ?>
        </div>
        <span class="hamburger"><i class="fa-solid fa-bars"></i></span>
    </aside>
    <div class="menu_wrapper">
      <section class="dropdown_menu">
              	<button id="close-login"><i class="fa-solid fa-xmark"></i></button>
              	<section class="logo_wrapper">
                  	<h1 id="nazev">PUCIS</h1>
                 	<img id="logo" src="img/onion_logo.jpg" alt="logo">
          </section>
              <?php if(!isset($_SESSION["username"])){ ?>
                  <input id="login-username" type="text" name="name" placeholder="Přezdívka" required>
                  <input id="login-password" type="password" name="password" placeholder="Heslo" required>
                  <input id="login-submit" onclick="login()" type="submit" value="Přihlásit se" name="login-submit">
                  <span  id="registr">Nemáte účet? <a href="registrace.php">Zaregistrujte se.</a></span>
                   <?php ;}
              else{ ?>
                  <p>Kliknutím na tlačítko se odhlásíte</p>
                  <input id="logout-submit" onclick="logout()" type="submit"  value="Odhlásit se" name="logout-submit">
              <?php ;} ?>
                <script>
                  function login(){
                      let async = false;
                      
                      var data = new FormData();
                      var username = document.getElementById("login-username").value;
                      var pswd = document.getElementById("login-password").value;
                      data.append('login-submit', 'arrived');
                      data.append('login-username', username);
                      data.append('login-password', pswd);
              
                      const xhttp2 = new XMLHttpRequest();
                      xhttp2.onload = function() {
                          
                          console.log(this.responseText);
                          let response = JSON.parse(this.responseText);
                          
                          console.log(window.location);
                          if(response.admin==0 && window.location != "index.php"){
                              window.location.replace("index.php");
                          }
                          else if(response.admin==1){
                              window.location.replace("admin-main.php");
                          }
                          else if(response.chyba=="user"){
                              alert("Špatné jméno nebo heslo!");
                          }   
                      }

                      xhttp2.open("POST", "model/login-backend.php");
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
                          
                          if(window.location == "admin-main.php"){
                            window.location.replace("index.php");
                          }
                          if(window.location != "index.php"){
                            window.location.replace("index.php");
                          }
                          
                      }

                      xhttp2.open("POST", "model/login-backend.php");
                      xhttp2.send(data);
                  }
                  document.getElementById("login-button").addEventListener("click", function(){
                      console.log("klikl");
                      document.querySelector(".menu_wrapper").classList.add("visible");
                      document.querySelector("body").classList.add("noscroll");
                  })
                  document.getElementById("close-login").addEventListener("click", function(){
                      document.querySelector(".menu_wrapper").classList.remove("visible");
                      document.querySelector("body").classList.remove("noscroll");
                  })

                  $('.side-nav .hamburger').on('click', function(){
                    if( $('.side-nav .hamburger-items').css("display") == "flex")
                    {
                      $('.side-nav .hamburger-items').css("display", "");
                    }
                    else
                    {
                      $('.side-nav .hamburger-items').css("display", "flex");
                    }
                   
                  });
                </script>
            </section>
        </div>
    <?php
}

function print_footer()
{
    ?>
      <script src="script.js"></script>
      <footer>
        <p>© 2022 Putovní cibule, Adam Cologna && David Kocman && Tomáš Souček</p>
      </footer>
      <noscript>Váš prohlížeč nemá aktivní javascript. Pro správné fungování systému je třeba jej zapnout!</noscript>
    <?php
}

function nice_array_print($array)
{
  ?>
    <pre>
        <?php print_r($array); ?>
    </pre>
  <?php
}

?>
