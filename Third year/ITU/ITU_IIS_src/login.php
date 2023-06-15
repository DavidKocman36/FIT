<?php
/**
 * Author: David Kocman, xkocma08
 */
?>
<!DOCTYPE html>
<html lang="cs">
    <head>
        <meta name="theme-color" content="#ffffff">
		<meta name="viewport" content="width=device-width, initial-scale=1"> 
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" > 
        <link rel="stylesheet" href="login_style.css">
    </head>
    <body>
        <section class="wrapper">
            <section class="dropdown_menu">
                <section class="logo_wrapper">
                    <H1 id="nazev">PUCIS</H1>
                    <img id="logo" src="img/onion_logo.jpg" alt="logo">
                </section>
                <form action="#">
                    <input id="login-username" type="text" name="name" placeholder="Jméno" required>
                    <input id="login-password" type="password" name="password" placeholder="Heslo" required>
                    <section class="checkbox">
                        <input id="login-remember_me" type="checkbox" name="remember">
                        <span id="remember_me">Zapamatuj si mě</span>
                    </section>
                    <input id="login-submit" type="submit" value="Přihlásit se">
                    <span  id="registr">Nemáte účet? <a href="registrace.html">Zaregistrujte se.</a></span>
                </form>
            </section>
        </section>
    </body>
</html>
