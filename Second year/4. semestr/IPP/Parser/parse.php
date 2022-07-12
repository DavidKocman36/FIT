<?php
    /**
     * Lexikalni a syntakticky analyzator pro jazyk IPPcode22.
     * Autor: David Kocman, xkocma08
     * Datum posledni upravy: 17.02.2022
     */

    /**
     * @brief Funkce zjisti, zda-li zdrojovy dokument obsahuje hlavicku ".IPPcode22". Prochazi prvni radky dokud
     * nenarazi na nebily znak. Pak zkontroluje, jestli radek obsahuje dany header.
     * @return errcode 21 pokud je hlavicka spatna ci chybi.
     */
    function check_header(){
        $cont = true;
        while($cont == true){
            $string = fgets(STDIN);
            $string = trim($string, "\n");
            $string = remove_comm_ws($string);
            $str_low = $string;
            $string = strtoupper($string);
            if(strcmp($string, ".IPPCODE22") == 0){
                $str_low = trim($str_low, ".");
                echo("<program language=\"$str_low\">\n");
                $continue = false;
                break;
            }
            elseif(strlen($string>0)){
                fwrite(STDERR,"Chybna nebo chybejici hlavicka!\n");
                exit(21);
            }
            if(feof(STDIN)){
                fwrite(STDERR,"Chybna nebo chybejici hlavicka!\n");
                exit(21);
            }
        }
    }

    /**
     * @brief Funkce nahradi vsechny specialni XML znaky vyskytujici se v retezcovem literalu nebo nazvu promenne.
     * @param $variable Retezec, ktery pravdepodobne obsahuje znaky.
     * @return Upraveny retezec.
     */

    function escape($variable){
        if(strpos($variable, "&") !== false){
            $variable = str_replace("&", "&amp;", $variable);
        }
        if(strpos($variable, "<") !== false){
            $variable = str_replace("<", "&lt;", $variable);
        }
        if(strpos($variable, ">") !== false){
            $variable = str_replace(">", "&gt;", $variable);
        }
        if(strpos($variable, "'") !== false){
            $variable = str_replace("'", "&apos;", $variable);
        }
        if(strpos($variable, "\"") !== false){
            $variable = str_replace("\"", "&quot;", $variable);
        }
        return $variable;
    }

    /**
     * @brief Funkce zkontroluje, zda jsou vsechny escaped sekvence napsany spravne ve formatu \xyz.
     * @param $variable Retezec, ktery pravdepodobne obsahuje escaped sekvence.
     * @return errcode 23 pokud sekvence nevyhovuje.
     */

    function escape_check($variable){
        $end = strlen($variable);
        for($i = 0; $i < strlen($variable);$i++){
            if(strcmp($variable[$i],"\\") == 0){
                $j = $i + 4;
                if($j > $end){
                    fwrite(STDERR, "Wrong escaped sequence!\n");
                    exit(23);
                }
                else{
                    if(is_numeric($variable[$i+1]) && is_numeric($variable[$i+2]) && is_numeric($variable[$i+3])){
                        $i = $i + 3;
                        continue;
                    }
                    else{
                        fwrite(STDERR, "Wrong escaped sequence!\n");
                        exit(23);
                    }
                }
            }
        }
    }

    /**
     * @brief Funkce odstrani nadmerne bile znaky a (pokud se vyskytuji) komentare.
     * @param $variable Retezec, ktery chceme upravit
     * @return Upraveny retezec bez vicenasobnych bilych znaku a komentaru
     */
    function remove_comm_ws($variable){
        if(strpos($variable, "#") !== false){
            $variable = strstr($variable, "#", true);
            if(strpos($variable, "#") > 0){
                $variable = $variable."\n";
            }
        }
        $variable = trim(preg_replace('/\s+/', ' ', $variable));
        return $variable;
    }

    //FUNKCE PRO VYPSANI ARGUMENTU
    /**
     * @brief Funkce zpracuje argument <symb>. Rozpozna, zda-li se jedna o jeden z moznych typu a podle toho
     * zkontroluje lexikalni a syntakticke pravidla a vypise prislusny XML radek.
     * @param $token Vstupni retezec, ve kterem se <symb> nachazi
     * @param $a Pozice argumentu
     * @param $name Nazev kodu, kteremu argumenty patri
     * @return errcode 23 pokud argument nevyhovuje
     */

    function symbol_print($token, $a, $name){
        //lexikalni analyza za pomoci regulerniho vyrazu
        if(preg_match("/^int@(\-|\+)?[0-9]+$/", $token[$a])){//INT
            $printable = explode('@',$token[$a]); 
            echo("        <arg$a type=\"int\">$printable[1]</arg$a>\n");
        }
        elseif(preg_match("/^string@/", $token[$a])){//STRING
            $printable = explode('@', $token[$a], 2);
            escape_check($printable[1]);
            $printable[1] = escape($printable[1]);
            echo("        <arg$a type=\"string\">$printable[1]</arg$a>\n");
        }
        elseif(preg_match("/^bool@(true|false)$/", $token[$a])){//BOOL
            $printable = explode('@',$token[$a]); 
            echo("        <arg$a type=\"bool\">$printable[1]</arg$a>\n");
        }
        elseif(preg_match("/^nil@nil$/", $token[$a])){//NIL
            $printable = explode('@',$token[$a]); 
            echo("        <arg$a type=\"nil\">$printable[1]</arg$a>\n");
        }
        else{
            fwrite(STDERR, "Wrong syntax of $name argument!\n");
            exit(23);
        }
    }

    /**
     * @brief Funkce zpracuje kombinaci argumentu <var> <symb1> <symb2>. Provede lexikalni a syntaktickou kontrolu a vypise prislusny
     * radek XML. 
     * @param $token Vstupni retezec, ve kterem se <symb> nachazi
     * @param $name Nazev kodu, kteremu argumenty patri 
     * @param $order Poradi zpracovaneho kodu
     * @return errcode 23 pokud argument nevyhovuje
     */

    function VSS_print($token, $name, $order){
        //pokud ma kod mene ci vice argumentu, vrati 23
        if(count($token)!==4){
            fwrite(STDERR, "$name takes three arguments!\n");
            exit(23);
        }
        //zkontroluje argument <var>
        if(preg_match("/^(GF|LF|TF)@[_\-$&%*!?a-zA-Z][a-zA-Z0-9_\-$&%*!?]*$/" , $token[1])){
            $token[1] = escape($token[1]);
            echo("    <instruction order=\"$order\" opcode=\"$token[0]\">\n");
            echo("        <arg1 type=\"var\">$token[1]</arg1>\n");

            //zkontroluje , zda-li <symb1> je promenna nebo literal a provede lexikalni analyzu
            if(preg_match("/^(GF|LF|TF)@[_\-$&%*!?a-zA-Z][a-zA-Z0-9_\-$&%*!?]*$/" , $token[2])){ //SYMB
                $token[2] = escape($token[2]);
                echo("        <arg2 type=\"var\">$token[2]</arg2>\n");
            }
            elseif( preg_match("/^(string|int|bool|nil)@/" , $token[2])){
                symbol_print($token, 2, $name);
            }
            else{
                fwrite(STDERR, "Wrong syntax of $name argument!\n");
                exit(23);
            }
            //zkontroluje , zda-li <symb2> je promenna nebo literal a provede lexikalni analyzu
            if(preg_match("/^(GF|LF|TF)@[_\-$&%*!?a-zA-Z][a-zA-Z0-9_\-$&%*!?]*$/" , $token[3])){ //SYMB
                $token[3] = escape($token[3]);
                echo("        <arg3 type=\"var\">$token[3]</arg3>\n");
            }
            elseif( preg_match("/^(string|int|bool|nil)@/" , $token[3])){
                symbol_print($token, 3, $name);
            }
            else{
                fwrite(STDERR, "Wrong syntax of $name argument!\n");
                exit(23);
            }
            echo("    </instruction>\n");
        }
        else{
            fwrite(STDERR, "Wrong syntax of $name argument!\n");
            exit(23);
        }
    }
    
    /**
     * @brief Funkce zpracuje argument typu <label>. Provede lexikalni a syntaktickou analyzu a vypise 
     * prislusny XML radek. 
     * @param $token Vstupni retezec, ve kterem se <symb> nachazi
     * @param $order Poradi zpracovaneho kodu
     * @param $name Nazev kodu, kteremu argumenty patri
     * @return errcode 23 pokud argument nevyhovuje
     */

    function label_print($token, $order, $name){
        //pokud ma kod mene ci vice argumentu, vrati 23
        if(count($token)!==2){
            fwrite(STDERR, "$name takes one argument!\n");
            exit(23);
        }
        //zkontroluje lexikalni pozadavky pro typ label
        if(preg_match("/^[_\-$&%*!?a-zA-Z][a-zA-Z0-9_\-$&%*!?]*$/" , $token[1])){
            $token[1] = escape($token[1]);
            echo("    <instruction order=\"$order\" opcode=\"$token[0]\">\n");
            echo("        <arg1 type=\"label\">$token[1]</arg1>\n");
            echo("    </instruction>\n");
        }
        else{
            fwrite(STDERR, "Wrong syntax of $name argument!\n");
            exit(23);
        }
    }

    /**
     * @brief Funkce zpracuje kombinaci argumentu <label> <symb1> <symb2>. Provede lexikalni a syntaktickou kontrolu a vypise prislusny
     * radek XML. 
     * @param $token Vstupni retezec, ve kterem se <symb> nachazi
     * @param $order Poradi zpracovaneho kodu 
     * @param $name Nazev kodu, kteremu argumenty patri 
     * @return errcode 23 pokud argument nevyhovuje
     */

    function LSS_print($token, $order, $name){
        //pokud ma kod mene ci vice argumentu, vrati 23
        if(count($token)!==4){
            fwrite(STDERR, "$name takes three arguments!\n");
            exit(23);
        }
        //lexikalni analyza pro typ label
        if(preg_match("/^[_\-$&%*!?a-zA-Z][a-zA-Z0-9_\-$&%*!?]*$/" , $token[1])){
            $token[1] = escape($token[1]);
            echo("    <instruction order=\"$order\" opcode=\"$token[0]\">\n");
            echo("        <arg1 type=\"label\">$token[1]</arg1>\n");

            //zkontroluje, zda-li <symb1> je promenna nebo literal a provede lexikalni analyzu
            if(preg_match("/(GF|LF|TF)@[_\-$&%*!?a-zA-Z][a-zA-Z0-9_\-$&%*!?]*/" , $token[2])){ //SYMB
                $token[2] = escape($token[2]);
                echo("        <arg2 type=\"var\">$token[2]</arg2>\n");
            }
            elseif( preg_match("/^(string|int|bool|nil)@/" , $token[2])){
                symbol_print($token, 2, $name);
            }
            else{
                fwrite(STDERR, "Wrong syntax of $name argument!\n");
                exit(23);
            }
            //zkontroluje, zda-li <symb2> je promenna nebo literal a provede lexikalni analyzu
            if(preg_match("/^(GF|LF|TF)@[_\-$&%*!?a-zA-Z][a-zA-Z0-9_\-$&%*!?]*$/" , $token[3])){ //SYMB
                $token[3] = escape($token[3]);
                echo("        <arg3 type=\"var\">$token[3]</arg3>\n");
            }
            elseif( preg_match("/^(string|int|bool|nil)@/" , $token[3])){
                symbol_print($token, 3, $name);
            }
            else{
                fwrite(STDERR, "Wrong syntax of $name argument!\n");
                exit(23);
            }
            echo("    </instruction>\n");
        }
        else{
            fwrite(STDERR, "Wrong syntax of $name argument!\n");
            exit(23);
        }
    }

    /**
     * @brief Funkce zpracuje kombinaci argumentu <var> <symb>. Provede lexikalni a syntaktickou kontrolu a vypise prislusny
     * radek XML. 
     * @param $token Vstupni retezec, ve kterem se <symb> nachazi
     * @param $order Poradi zpracovaneho kodu 
     * @param $name Nazev kodu, kteremu argumenty patri 
     * @return errcode 23 pokud argument nevyhovuje
     */


    function var_symb_print($token, $order, $name){
        //pokud ma kod mene ci vice argumentu, vrati 23
        if(count($token)!==3){
            fwrite(STDERR, "$name takes two arguments!\n");
            exit(23);
        }
        //lexikalni analyza <var>
        if(preg_match("/^(GF|LF|TF)@[_\-$&%*!?a-zA-Z][a-zA-Z0-9_\-$&%*!?]*$/" , $token[1])){
            $token[1] = escape($token[1]);
            echo("    <instruction order=\"$order\" opcode=\"$token[0]\">\n");
            echo("        <arg1 type=\"var\">$token[1]</arg1>\n");
            
        }
        else{
            fwrite(STDERR, "Wrong syntax of $name argument!\n");
            exit(23);
        }
        //rozpozna, zda je <symb> promenna ci literal a provede syntaktickou analyzu
        if(preg_match("/^(GF|LF|TF)@[_\-$&%*!?a-zA-Z][a-zA-Z0-9_\-$&%*!?]*$/" , $token[2])){
            $token[1] = escape($token[1]);
            echo("        <arg2 type=\"var\">$token[2]</arg2>\n");
            echo("    </instruction>\n");
            
        }
        elseif( preg_match("/^(string|int|bool|nil)@/" , $token[2])){
            symbol_print($token, 2, $name);
            echo("    </instruction>\n");
        }
        else{
            fwrite(STDERR, "Wrong syntax of $name argument!\n");
            exit(23);
        }    
    }

    /**
     * @brief Funkce zpracuje argument <symb>. Provede lexikalni a syntaktickou kontrolu a vypise prislusny
     * radek XML. Tahle funkce rozpoznava, zda je <symb> promenna nebo literal.
     * @param $token Vstupni retezec, ve kterem se <symb> nachazi
     * @param $order Poradi zpracovaneho kodu 
     * @param $name Nazev kodu, kteremu argumenty patri 
     * @return errcode 23 pokud argument nevyhovuje
     */

    function symb_print_final($token, $order, $name){
        //pokud ma kod vice ci mene argumentu, vrati 23
        if(count($token)!==2){
            fwrite(STDERR, "$name takes one argument!\n");
            exit(23);
        }
        if(preg_match("/^(GF|LF|TF)@[_\-$&%*!?a-zA-Z][a-zA-Z0-9_\-$&%*!?]*$/" , $token[1])){
            $token[1] = escape($token[1]);
            echo("    <instruction order=\"$order\" opcode=\"$token[0]\">\n");
            echo("        <arg1 type=\"var\">$token[1]</arg1>\n");
            echo("    </instruction>\n");
        }
        elseif( preg_match("/^(string|int|bool|nil)@/" , $token[1])){
            echo("    <instruction order=\"$order\" opcode=\"$token[0]\">\n");
            symbol_print($token, 1, $name);
            echo("    </instruction>\n");
        }
        else{
            fwrite(STDERR, "Wrong syntax of $name argument!\n");
            exit(23);
        }
    }

    /**
     * @brief Funkce zpracuje argument <var>. Provede lexikalni a syntaktickou kontrolu a vypise prislusny
     * radek XML. 
     * @param $token Vstupni retezec, ve kterem se <symb> nachazi
     * @param $order Poradi zpracovaneho kodu 
     * @param $name Nazev kodu, kteremu argumenty patri 
     * @return errcode 23 pokud argument nevyhovuje
     */

    function var_print($token, $order, $name){
        //pokud ma kod vice ci mene argumentu, vrati 23
        if(count($token)!==2){
            fwrite(STDERR, "$name takes one argument!\n");
            exit(23);
        }
        //provede lexikalni analyzu promenne
        if(preg_match("/^(GF|LF|TF)@[_\-$&%*!?a-zA-Z][a-zA-Z0-9_\-$&%*!?]*$/" , $token[1])){ //VAR
            $token[1] = escape($token[1]);
            echo("    <instruction order=\"$order\" opcode=\"$token[0]\">\n");
            echo("        <arg1 type=\"var\">$token[1]</arg1>\n");
            echo("    </instruction>\n");
        }
        else{
        fwrite(STDERR, "Wrong syntax of $name argument!\n");
            exit(23);
        }
    }

    /**
     * @brief Funkce provede syntaktickou a lexikalni analyzu pro kody, ktere nemaji zadne argumenty.
     * @param $token Vstupni retezec, ve kterem se <symb> nachazi
     * @param $order Poradi zpracovaneho kodu 
     * @param $name Nazev kodu, kteremu argumenty patri 
     * @return errcode 23 pokud argument obsahuje
     */

    function no_arg($token, $order, $name){
        if(count($token)!==1){
            fwrite(STDERR, "$name takes no argument!\n");
            exit(23);
        }
        echo("    <instruction order=\"$order\" opcode=\"$token[0]\">\n");
        echo("    </instruction>\n");
    }

    //MAIN
    ini_set('display_errors', 'stderr');

    //zpracovani argumentu, pokud obsahuje --help, vypise pomoc a ukonci se, jinak error 10
    if($argc > 1){
        if($argc > 2){
            exit(10);
        }
        else if(strcmp($argv[1], "--help") == 0){
            echo("parse.php [--help] < filename\n");
            echo("    --help : vypise kratky popis aplikace, nepovinne\n");
            echo("    filename : nazev souboru se zdrojovym kodem IPPcode22\n");
            echo("\n");
            echo("    Neprijma zadne dalsi argumenty. --help a filename nejsou navzajem kompatibilni.\n");
            echo("\n");
            echo("    Provede lexikalni a syntaktickou analyzu nad danym souborem v jazyce IPPcode22.\n");
            echo("    Pri uspesnem provedeni vraci 0, jinak vraci errory 10, 21, 22, 23.\n");
            exit(0);
        }
        else{
            exit(10);
        }
    }
    echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
    check_header();
    $order = 1;
    //ziskavani radku ze vstupniho souboru
    while($string = fgets(STDIN)){

        $string = remove_comm_ws($string);
        if(strlen($string) <  1){
            continue;
        }

        $token = explode(' ',trim($string, "\n"));
        $token[0] = strtoupper($token[0]); 
        switch($token[0]){

            //Instrukce rozdeleny do skupin podle zadani. je nad nimi provedena lexikalni
            //a syntakticka analyza.
            //Na stdout se vypisuji prislusne XML radky.

            //LADICÍ INSTRUKCE
            case 'DPRINT': //SYMB 
                symb_print_final($token, $order, "DPRINT");
                $order++;
                break;

            case 'BREAK': //NIC
                no_arg($token, $order, "BREAK");
                $order++;
                break;

            //INSTRUKCE PRO ŘÍZENÍ TOKU PROGRAMU
            case 'LABEL': //LABEL
                label_print($token, $order, "LABEL");
                $order++;
                break;

            case 'JUMP': //LABEL
                label_print($token, $order, "JUMP");
                $order++;
                break;

            case 'JUMPIFEQ': //LABEL SYMB SYMB
                LSS_print($token, $order, "JUMPIFEQ");
                $order++;
                break;

            case 'JUMPIFNEQ': //LABEL SYMB SYMB
                LSS_print($token, $order, "JUMPIFNEQ");
                $order++;
                break;

            case 'EXIT':
                symb_print_final($token, $order, "EXIT");
                $order++;
                break;

            //PRÁCE S TYPY

            case 'TYPE': //VAR SYMB
                var_symb_print($token, $order, "TYPE");
                $order++;
                break;

            //PRÁCE S ŘETĚZCI

            case 'CONCAT':
                VSS_print($token, "CONCAT", $order);
                $order++;
                break;
            case 'STRLEN':
               var_symb_print($token, $order, "STRLEN");
                $order++;
                break;
            case 'GETCHAR':
                VSS_print($token, "GETCHAR", $order);
                $order++;
                break;
            case 'SETCHAR':
                VSS_print($token, "SETCHAR", $order);
                $order++;
                break;

            //VSTUPNĚ VÝSTUPNÍ INSTRUKCE
            
            //READ nema vlastni fuknci, protoze ma jedinecnou kombinaci argumentu <var> <type>
            case 'READ':
                if(count($token)!==3){
                    fwrite(STDERR, "READ takes two arguments!\n");
                    exit(23);
                }
                if(preg_match("/^(GF|LF|TF)@[_\-$&%*!?a-zA-Z][a-zA-Z0-9_\-$&%*!?]*$/" , $token[1])){ //VAR
                    $token[1] = escape($token[1]);
                    echo("    <instruction order=\"$order\" opcode=\"$token[0]\">\n");
                    echo("        <arg1 type=\"var\">$token[1]</arg1>\n");
                }
                else{
                    fwrite(STDERR, "Wrong syntax of READ argument!\n");
                    exit(23);
                }
                if( preg_match("/^(string|int|bool)$/" , $token[2])){
                    echo("        <arg2 type=\"type\">$token[2]</arg2>\n");
                    echo("    </instruction>\n");
                }    
                else{
                    fwrite(STDERR, "Wrong syntax of READ argument!\n");
                    exit(23);
                }
                $order++;
                break;
            
            case 'WRITE':
                symb_print_final($token, $order, "WRITE");
                $order++;
                break;

            //ARITMETICKÉ, RELAČNÍ, BOOLEOVSKÉ A KONVERZNÍ INSTRUKCE

            case 'ADD':
                VSS_print($token, "ADD", $order);
                $order++;
                break;
            case 'SUB':
                VSS_print($token, "SUB", $order);
                $order++;
                break;
            case 'MUL':
                VSS_print($token, "MUL", $order);
                $order++;
                break;
            case 'IDIV':
                VSS_print($token, "IDIV", $order);
                $order++;
                break;
            case 'LT':
                VSS_print($token, "LT", $order);
                $order++;
                break;
            case 'GT':
                VSS_print($token, "GT", $order);
                $order++;
                break;
            case 'EQ':
                VSS_print($token, "EQ", $order);
                $order++;
                break;
            case 'AND':
                VSS_print($token, "AND", $order);
                $order++;
                break;
            case 'OR':
                VSS_print($token, "OR", $order);
                $order++;
                break;
            case 'NOT':
                var_symb_print($token, $order, "NOT");
                $order++;
                break;
            case 'INT2CHAR':
                var_symb_print($token, $order, "INT2CHAR");
                $order++;
                break;
            case 'STRI2INT':
                VSS_print($token, "STR2INT", $order);
                $order++;
                break;

            //PRÁCE S DATOVÝM ZÁSOBNÍKEM

            case 'PUSHS':
                symb_print_final($token, $order, "PUSHS");
                $order++;
                break;
            case 'POPS':
                var_print($token, $order, "POPS");
                $order++;
                break;

            //PRÁCE S RÁMCI A VOLÁNÍ FUNKCÍ
            case 'MOVE':
                var_symb_print($token, $order, "MOVE");
                $order++;
                break;
            
            case 'CREATEFRAME':
                no_arg($token, $order, "CREATEFRAME");
                $order++;
                break;

            case 'PUSHFRAME':
                no_arg($token, $order, "PUSHFRAME");
                $order++;
                break;
            
            case 'POPFRAME':
                no_arg($token, $order, "POPFRAME");
                $order++;
                break;

            case 'DEFVAR':
                var_print($token, $order, "DEFVAR");
                $order++;
                break;
            
            case 'CALL':
                label_print($token, $order, "CALL");
                $order++;
                break;

            case 'RETURN':
                no_arg($token, $order, "RETURN");
                $order++;
                break;

            default:
                //pokud se tu vyskytuje nejaka neznama instrukce, vypise se errorova hlaska a vrati se 22.
                fwrite(STDERR, "Unknown code \"$token[0]\"!\n");
                exit(22);
                break;
        }
    }
    echo("</program>\n");
    exit(0);
?>