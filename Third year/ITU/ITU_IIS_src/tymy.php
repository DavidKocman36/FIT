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
    <link href="css/tymy.css" rel="stylesheet">
    <title>PUCIS | Týmy</title>
    <style>
        .fa-chevron-right, .fa-chevron-left
        {
            color: #ccc;
        }
    </style>
</head>
<body>
    <?php 
        print_notifications();
        print_sidenav(); 
    ?>

    <main>
        <a href="tym-edit.php?create" class="btn"><i class="fa-solid fa-plus"></i>Vytvořit tým</a>
        <div class="searchbar">
            <input type="text" id="searchInput" onkeyup="filter()" placeholder="začněte vyhledávat">
            <i class="fa-sharp fa-solid fa-magnifying-glass"></i>
        </div>
        <h2>Mnou vytvořené týmy</h2>
        
       
        <div class="team-list creator">
            <i class="fa-solid fa-chevron-left"></i>
            <div class="container">
                <p>Bohužel nejsi tvůrcem žádného týmu</p>
            </div>
            
            <i class="fa-solid fa-chevron-right"></i>
        </div>

        <h2>Týmy, kde jsem členem</h2>
        <div class="team-list member">
            <i class="fa-solid fa-chevron-left"></i>
            <div class="container">
                <p>Bohužel nejsi členem žádného týmu</p>
            </div>
            
            <i class="fa-solid fa-chevron-right"></i>
        </div>
        <div id="demo"></div>
    </main>

    <?php print_footer(); ?>
    <script>
        //creator 
        const xhttp = new XMLHttpRequest();
        xhttp.onload = function() {
            let teams = JSON.parse(this.responseText);
            let container = document.querySelector(".creator .container");
            let content = '';
            for(let i = 0; i < teams.length; i++)
            {
                content += '<a href="tym-edit.php?edit='+teams[i].team_id+'">';
                content += '<div class="teams-tile">';
                content += '<img src="'+ teams[i].logo +'" alt="logo">';
                content += '<h3>'+ teams[i].name +'</h3>';
                content += '</div>';
                content += '</a>';
            }
            if(content != '')
            {
                container.innerHTML = content;
            }
           
        }
        xhttp.open("GET", "model/get-teams.php?creator");
        xhttp.send();


        //member
        const xhttp2 = new XMLHttpRequest();
        xhttp2.onload = function() {
            let teams = JSON.parse(this.responseText);
            let container = document.querySelector(".member .container");
            let content = '';
            for(let i = 0; i < teams.length; i++)
            {
                content += '<a href="tym-edit.php?edit='+teams[i].team_id+'">';
                content += '<div class="teams-tile">';
                content += '<img src="'+ teams[i].logo +'" alt="logo">';
                content += '<h3>'+ teams[i].name +'</h3>';
                content += '</div>';
                content += '</a>';
            }
            if(content != '')
            {
                container.innerHTML = content;
            }
        }
        xhttp2.open("GET", "model/get-teams.php?member");
        xhttp2.send();
    </script>
    <script>

        function filter() {
            var input = document.getElementById("searchInput");
            var filter = input.value.toUpperCase();
           
            var container = document.querySelectorAll(".container");
            for(let j = 0; j < container.length; j++)
            {
                for (let i = 0; i < container[j].children.length; i++) {
                    var tile = container[j].children[i].getElementsByTagName("h3")[0];
                    var txtValue = tile.textContent || tile.innerText;
         
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        
                        container[j].children[i].style.display = "";
                    } else {
                        container[j].children[i].style.display = "none";
                    }
                }
            }
           
        }

        window.addEventListener('load', function () {
            let tilesCreator = document.querySelectorAll('.creator .teams-tile');
            let arrowLeftCreator = document.querySelector('.creator  .fa-chevron-left');
            let arrowRightCreator = document.querySelector('.creator  .fa-chevron-right');

            let tilesMember = document.querySelectorAll('.member .teams-tile');
            let arrowLeftMember = document.querySelector('.member  .fa-chevron-left');
            let arrowRightMember = document.querySelector('.member  .fa-chevron-right');

            let containerWidth = document.querySelector('.container').offsetWidth;


            if(tilesCreator.length > 0)
            {
                //init position
                for(let i = 0; i < tilesCreator.length; i++)
                {
                    tilesCreator[i].style.left = '0';
                }

                //disable initial state arrows
                if(tilesCreator[0].offsetLeft > 1 || tilesMember.length == 0)
                {
                    arrowLeftCreator.style.color = '#ccc';
                }

                if(tilesCreator[(tilesCreator.length-1)].offsetLeft < (containerWidth-200) || tilesMember.length == 0)
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
            }
            else
            {
                arrowLeftCreator.style.color = '#ccc'; 
                arrowRightCreator.style.color = '#ccc'; 
            }
            




            if(tilesMember.length > 0)
            {
                //init position
            for(let i = 0; i < tilesMember.length; i++)
            {
                tilesMember[i].style.left = '0';
            }

            //disable initial state arrows
            if(tilesMember.length == 0 || tilesMember[0].offsetLeft > 1)
            {
                arrowLeftMember.style.color = '#ccc';
            }

            if(tilesMember.length == 0 || tilesMember[(tilesMember.length-1)].offsetLeft < (containerWidth-200))
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
            }
            else
            {
                arrowRightMember.style.color = '#ccc';
                arrowLeftMember.style.color = '#ccc';
            }
            
        })
        
    </script>
</body>
</html>