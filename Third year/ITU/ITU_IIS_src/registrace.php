<?php 
include 'utils.php';
/**
 * Author: Adam Cologna, xcolog00
 * Author: David Kocman, xkocma08
 */
?>
<!DOCTYPE html>
<html lang="cs">
    <head>
        <meta name="theme-color" content="#ffffff">
		<meta name="viewport" content="width=device-width, initial-scale=1"> 
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" > 
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> 
        <link rel="stylesheet" href="css/registrace_style.css">
        <title>PUCIS | Registrace</title>
    </head>
    <body>
        <main>
            <section class="content">
                <h1>Registrace</h1>
                <section class="register">
                    <form id="reg_form">
                        <div class="name">
                            <label>Jméno *</label>
                            <input id="reg-name" type="text" name="name" required value="<?php if(isset($_POST["name"])){print($_POST["name"]);}?>">
                        </div>
                        <div class="surname">
                            <label>Příjmení *</label>
                            <input id="reg-surname" type="text" name="surname" required value="<?php if(isset($_POST["surname"])){print($_POST["surname"]);}?>">
                        </div>
                        <div class="email">
                            <label>Email *</label>
                            <input id="reg-email" type="email" name="mail" required value="<?php if(isset($_POST["mail"])){print($_POST["mail"]);}?>">
                        </div>
                        <div class="username">
                            <label>Přezdívka *</label>
                            <input id="reg-username" type="text" name="username" required value="<?php if(isset($_POST["username"])){print($_POST["username"]);}?>">
                        </div>
                        <div class="date">
                            <label>Datum narození *</label>
                            <input id="reg-date" type="date" name="date" required value="<?php if(isset($_POST["date"])){print($_POST["date"]);}?>">
                        </div>
                        <div class="phone">
                            <label>Telefon *</label>
                            <input id="reg-phone" type="tel" name="phone" pattern="[0-9]{9}" required value="<?php if(isset($_POST["phone"])){print($_POST["phone"]);}?>">
                        </div>
                        <div class="school">
                            <div class="sch">
                                <label>Škola</label>
                                <input id="reg-school" type="text" name="school" value="">
                            </div>
                            <div class="fac">
                                <label>Fakulta</label>
                                <input id="reg-faculty" type="text" name="faculty" value="">
                            </div>
                            <div class="year">
                                <label>Ročník</label>
                                <input id="reg-year" type="number" name="year"  value="1">
                            </div>
                        </div>
                        <div class="password">
                            <label>Heslo *</label>
                            <input id="reg-pass" type="password" name="pass" required value="<?php if(isset($_POST["pass"])){print($_POST["pass"]);}?>">
                        </div>
                        <div class="passwordag">
                            <label>Heslo znovu *</label>
                            <input id="reg-passag" type="password" name="passag" required value="<?php if(isset($_POST["passag"])){print($_POST["passag"]);}?>">
                        </div>
                        <div class="submit">
                            <input id="reg-submit" name="registrovat_se" type="submit" value="Zaregistrovat se">
                        </div>
                    </form>
                </section>
            </section>
            

        </main>
        <?php print_footer(); ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-md5/2.5.0/js/md5.min.js"></script>
        <script type="text/javascript">
            $("#reg_form").submit(function(e) {
                e.preventDefault();

                var name = $("#reg-name").val();
                var surname = $("#reg-surname").val();
                var username = $("#reg-username").val();
                var email = $("#reg-email").val();
                var date = $("#reg-date").val();
                var phone = $("#reg-phone").val();
                var school = $("#reg-school").val();
                var faculty = $("#reg-faculty").val();
                var year = $("#reg-year").val();
                var password = md5($("#reg-pass").val());
                var passwordag = md5($("#reg-passag").val());
                
                if(password != passwordag){
                    alert("Hesla se neshodují!");
                    return;
                }

            
                $.ajax({  
                    type: 'POST',
                    url: 'model/registration-model.php', 
                    async: false,
                    data: { Name: name, 
                            Surname: surname,
                            Username: username,
                            Email: email,
                            Date: date,
                            Phone: phone,
                            School: school,
                            Faculty: faculty,
                            Year: year, 
                            Password: password
                        },
                    success: function (data){
                        console.log(data);
                        var res = JSON.parse(data);
                        if(res.fail == "email"){
                            alert("Uživatel s tímto emailem už existuje!");
                            return;
                        }
                        else if(res.fail == "phone"){
                            alert("Uživatel s tímto telefonním číslem už existuje!");
                            return;
                        }
                        else if(res.fail == "username"){
                            alert("Uživatel s touto přezdívkou už existuje!");
                            return;
                        }
                        else if(res.success == "inserted"){
                            alert("Úspěšně zaregistrován!");
                            window.location.replace("index.php");
                        }  
                    },
                    error: function(data) {
                        alert("Nastal error!");
                    }
                });
            });
            function register(){ 
            }   
        </script>
    </body>
</html>