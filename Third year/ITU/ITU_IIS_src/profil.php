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
    <link href="css/profile.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <title>PUCIS | Můj profil</title>
</head>
<body>
    <?php 
        print_notifications();
        print_sidenav(); 
    ?>

    <main>
    
    </main>
    <?php print_footer(); ?>
    <script>
        //load data about user
        function loadData()
        {
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                console.log(this.responseText);
                let user = JSON.parse(this.responseText);
                let container = document.querySelector("main");
                let content = '';

            content +=  '<div class="flex photo-container">';
                content +=  '<div class="photo">';
                    content +=  '<img src="' + user[0].picture + '" alt="profilový obrázek" title="profilový obrázek">';
                    content +=  '<label for="file"><span class="edit-photo"><i class="fa-solid fa-pencil"></i></span></label>';
                    content +=  '<input type="file" id="file" style="display:none;" onchange="changePicture(event)">';
                content +=  '</div>';
                        
                content +=  '<div>';
                    content +=  '<h2>'+ user[0].name +' '+ user[0].surname +'</h2>';
                    content +=  '<p>'+ user[0].username +'</p>';
                content +=  '</div>';
            content +=  '</div>';

            content +=  '<form>';
                content +=  '<div>';
                    content +=  '<label for="name">Jméno</label>';
                    content +=  '<input type="text" id="name1" name="name" placeholder="např. David" value="'+user[0].name+'">';
                content +=  '</div>';
                content +=  '<div>';
                    content +=  '<label for="surname">Přijmení</label>';
                    content +=  '<input type="text" id="surname" name="surname" placeholder="např. Bednář" value="'+user[0].surname+'">';
                content +=  '</div>';
                content +=  '<div>';
                    content +=  '<label for="email">E-mail</label>';
                    content +=  '<input type="mail" id="email" name="email" placeholder="bednarda@seznam.cz" value="'+user[0].email+'">';
                content +=  '</div>';
                content +=  '<div>';
                    content +=  '<label for="phone">Telefon</label>';
                    content +=  '<input type="tel" id="phone" name="phone" placeholder="+420776969420" value="'+user[0].phone+'">';
                content +=  '</div>';
                content +=  '<div>';
                    content +=  '<label for="birth">Datum narození</label>';
                    content +=  '<input type="date" id="birth" name="birth" value="'+user[0].birthdate+'">';
                content +=  '</div>';
                content +=  '<div>';
                    content +=  '<label for="school">Škola</label>';
                    content +=  '<input type="text" id="school" name="school" value="'+user[0].school+'">';
                content +=  '</div>';
                content +=  '<div>';
                    content +=  '<label for="school">Ročník</label>';
                    content +=  '<input type="number" id="year" name="year" value="'+user[0].year+'">';
                content +=  '</div>';
                content +=  '<div>';
                    content +=  '<label for="school">Fakulta</label>';
                    content +=  '<input type="text" id="faculty" name="faculty" value="'+user[0].faculty+'">';
                content +=  '</div>';

                content +=  '<input type="submit" id="submit" name="submit" value="Uložit změny">';
            content +=  '</form>';  

                container.innerHTML = content;

                //update profilové fotky i v bočním sidebaru
                $('.side-nav .profile-photo').attr("src",user[0].picture);

                document.querySelector('form').removeEventListener('submit', function(evt){
                    evt.preventDefault();
                    pushData();
                });

                document.querySelector('form').addEventListener('submit', function(evt){
                    evt.preventDefault();
                    pushData();
                });
            }
            xhttp.open("GET", "model/get-user.php");
            xhttp.send();
        }

        function pushData()
        {           
            var data = new FormData();
            data.append('id', '2');
            data.append('name', $('#name1').val());
            data.append('surname', $('#surname').val());
            data.append('email', $('#email').val());
            data.append('phone', $('#phone').val());
            data.append('school', $('#school').val());
            data.append('year', $('#year').val());
            data.append('faculty', $('#faculty').val());
            data.append('picture', $('#file')[0].files[0]); //undefined pokud nebyl změněný obrázek, soubor pokud změněný byl
    

            const xhttp2 = new XMLHttpRequest();
            xhttp2.onload = function() {
                console.log('post se povedl');
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
                    alert("error");
                }
            }
            xhttp2.open("POST", "model/get-user.php");
            xhttp2.send(data);
        }

        function changePicture(event)
        {
            var image = document.querySelector('.photo img');
            image.src = URL.createObjectURL(event.target.files[0]);

        }

        loadData();

    </script>
</body>
</html>