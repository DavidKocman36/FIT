<?php
$j = 0;
$k = 0;

/**
 * @brief Function searches for .src, .rc, .out, .in files and adds them to an array.
 * If argument --recursive is present, subfolders are recursively searched
 * @param $file_arr An array of files
 * @param $rec Bool, tells if we want to search recursively
 * @dir_path Directory containing tests
 * @returns $file_arr containing all desired files
 */
function dir_search($file_arr ,$rec, $dir_path){
    global $k;
    $aux_arr = [];
    global $j;
    $files = array_diff(scandir($dir_path, 1), array('..', '.'));
    foreach($files as $f => $file){
        $pom = $file;
        //append the right dirpath to the file
        $file = $dir_path.DIRECTORY_SEPARATOR.$file;
        //recursive function call
        if(is_dir($file) == true and ($rec == true)){
            $k = 0;
            $file_arr[$j] = $aux_arr;
            $file_arr = dir_search($file_arr ,$rec, $file);
        }
        //array add
        elseif (is_file($file) == true) {
            $pom = explode('.', $file);
            if(count($pom) == 2){
                //no other than these files are added
                if((strcmp($pom[1], "in") !== 0 ) and (strcmp($pom[1], "src") !== 0 ) and (strcmp($pom[1], "rc") !== 0 ) and (strcmp($pom[1], "out") !== 0 )){
                    continue;
                }
                else{
                    $aux_arr[$k] = $file;
                    $k++;
                }
            }
        }
    }
    $file_arr[$j] = $aux_arr;
    $j++;
    return $file_arr;
}
/**
 * @brief Adds missing .in, .rc or .out files
 * $param $array An array containing all the files
 */
function file_add($array){
    foreach($array as $item => $it){
        if(count($it) == 0){
            continue;
        }
        else{
            foreach($it as $f => $file){
                $file = explode(".", $file);
                if(!(is_file($file[0].".in"))){
                    $infile = fopen($file[0].".in", "w");
                    array_push($it, $infile);
                }
                if(!(is_file($file[0].".rc"))){
                    $rcfile = fopen($file[0].".rc", "w");
                    array_push($it, $rcfile);
                    fwrite($rcfile, "0");
                }
                if(!(is_file($file[0].".out"))){
                    $outfile = fopen($file[0].".out", "w");
                    array_push($it, $outfile);
                }
            }
        }   
    }
}
/**
 * @brief Prints HTML representation of a test
 * @param $signal Bool, tells if the test is successful or not
 * @param $element Path to the test
 * @param $rc_signal Tells if we want to print only return code type of test
 * @param $rc Return code
 * @param $ref_rc Reference return code
 * @param $ref_output reference output
 * @param $output my output 
 */
function print_HTML($signal, $element, $rc_signal, $rc, $ref_rc, $ref_output, $output){
    echo("  \n\t\t<div class=\"test_card\">\n");
    //get a name of the test
    $base = basename($element, ".src");
    echo("      \t\t<h2> Test: \"".$base."\"</h2>\n");
    //RC type of test -> only return codes are printed
    if($rc_signal == true){
        if($signal == true){
            echo("      \t\t<span style=\"color:lightgreen;\">SUCCESS</span>\n");
            echo("      \t\t<h3>Návratový kód:</h3>\n");
            echo("      \t\t<div>".$rc."</div>\n");
        }
        else{
            echo("      \t\t<span style=\"color:red;\">FAILED</span>\n");
            echo("      \t\t<h3>Očekávaný návratový kód:</h3>\n");
            echo("      \t\t<div>".$ref_rc."</div>\n");
            echo("      \t\t<h3>Skutečný návratový kód:</h3>\n");
            echo("      \t\t<div>".$rc."</div>\n");
        }
    }
    //RC and outputs are printed
    else{
        if($signal == true){
            echo("      \t\t<span style=\"color:lightgreen;\">SUCCESS</span>\n");
            echo("      \t\t<h3>Návratový kód:</h3>\n");
            echo("      \t\t<div>".$rc."</div>\n");
            echo("      \t\t<h3>Výstup:</h3>\n");
            echo("      \t\t<pre>\n".$output."</pre>\n");
        }
        else{
            echo("      \t\t<span style=\"color:red;\">FAILED</span>\n");
            echo("      \t\t<h3>Očekávaný návratový kód:</h3>\n");
            echo("      \t\t<div>".$ref_rc."</div>\n");
            echo("      \t\t<h3>Skutečný návratový kód:</h3>\n");
            echo("      \t\t<div>".$rc."</div>\n");
            echo("      \t\t<h3>Očekávaný výstup:</h3>\n");
            echo("      \t\t<pre>\n".$ref_output."</pre>\n");
            echo("      \t\t<h3>Skutečný výstup:</h3>\n");
            echo("      \t\t<pre>\n".$output."</pre>\n");
        }
    }
    echo("  \t\t</div>");   
}

/**
 * @brief Deletes a file if --noclean argument is not present
 * @param $item_exp File path
 * @param $nocl Bool, tells if --noclean arg is present 
 * @param $suffix Suffix of the deleted file
 */
function unlink_file($item_exp, $nocl, $suffix){
    if(!$nocl)
        unlink($item_exp[0].$suffix);
}

/**
 * @brief Tests the parser or the interpret. Prints HTML header and footer
 * @param $arr Array of files
 * @param $int_o Interpret only tests
 * @param $par_o Parse only tests
 * @param $nocl Noclean argument
 * @param $par_path Parser script path
 * @param $int_path Interpret script path
 * @param $jex Jexamxml path
 */
function test($arr, $int_o, $par_o, $nocl, $par_path, $int_path, $jex){
    $aux = " ";
    $signal = false;
    $rc_signal = false;
    $ref_output = " ";
    $output = " ";

    //HTML header
    echo("<!DOCTYPE html>
    <html lang=\"cs\">
    <head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
        <title>IPP - Testy</title>
        <link rel=\"stylesheet\" href=\"style.css\">
    </head>
    <body>
    <div class=\"header\">
        <h1>IPP - Automatizované testy k projektu</h1>
        <span>Autor: David Kocman, xkocma08</span>
    </div>
    <div class=\"tests\">");
    //loop through all the files
    foreach($arr as $a => $element){
        foreach($element as $e => $item){
            $item_exp = explode(".", $item);

            //skips 3 other files of the same name or if the test doesnt have .src file
            if(strcmp($item_exp[0], $aux) == 0 or !file_exists($item_exp[0].".src")){
                $aux = $item_exp[0];
                continue;
            }
            
            //parse only test
            if(!$int_o and $par_o){
                exec("php8.1 ".$par_path." < ".$item_exp[0].".src"." > ".$item_exp[0].".xml", $out, $rc);
                //Return code type of tests
                if($rc != 0){
                    //get reference return code
                    $ref_rc = file_get_contents($item_exp[0].".rc");
                    if($ref_rc == $rc){
                        $signal = true;
                        $rc_signal = true;
                        //print the test
                        print_HTML($signal, $item, $rc_signal, $rc, $ref_rc, $ref_output, $output);
                    }
                    else{
                        $signal = false;
                        $rc_signal = true;
                        print_HTML($signal, $item, $rc_signal, $rc, $ref_rc, $ref_output, $output);
                    }
                }
                else{
                    //output compare
                    $output = file_get_contents($item_exp[0].".xml");
                    $ref_output = file_get_contents($item_exp[0].".out");
                    $ref_rc = file_get_contents($item_exp[0].".rc");
                    $output = str_replace(['<', '>'], ['&lt;', '&gt;'], $output);
                    $ref_output = str_replace(['<', '>'], ['&lt;', '&gt;'], $ref_output);
                    
                    exec("java -jar ".$jex." ".$item_exp[0].".xml ".$item_exp[0].".out", $out, $error);
                    if($error != 0){
                        $signal = false;
                        $rc_signal = false;
                        print_HTML($signal, $item, $rc_signal, $rc, $ref_rc, $ref_output, $output);
                    }
                    else{
                        $signal = true;
                        $rc_signal = false;
                      
                        print_HTML($signal, $item, $rc_signal, $rc, $ref_rc, $ref_output, $output);
                    }
                    
                }
                unlink_file($item_exp,$nocl, ".xml");
            }
            //interpret only tests
            elseif($int_o and !$par_o){
                exec("python3 ".$int_path." --source=".$item_exp[0].".src"." --input=".$item_exp[0].".in > ".$item_exp[0].".o", $out, $rc);
                //RC type of tests
                if($rc != 0){
                    //get reference RC
                    $ref_rc = file_get_contents($item_exp[0].".rc");
                    if($ref_rc == $rc){
                        $signal = true;
                        $rc_signal = true;
                        print_HTML($signal, $item, $rc_signal, $rc, $ref_rc, $ref_output, $output);
                    }
                    else{
                        $signal = false;
                        $rc_signal = true;
                        print_HTML($signal, $item, $rc_signal, $rc, $ref_rc, $ref_output, $output);
                    }
                }
                else{
                    //compare outputs
                    $output = file_get_contents($item_exp[0].".o");
                    $ref_output = file_get_contents($item_exp[0].".out");
                    $ref_rc = file_get_contents($item_exp[0].".rc");
                    exec("diff ".$item_exp[0].".o ".$item_exp[0].".out", $out, $error);
                    if($error != 0){
                        $signal = false;
                        $rc_signal = false;
                        print_HTML($signal, $item, $rc_signal, $rc, $ref_rc, $ref_output, $output);
                    }
                    else{
                        $signal = true;
                        $rc_signal = false;
                        print_HTML($signal, $item, $rc_signal, $rc, $ref_rc, $ref_output, $output);
                    }
                    
                }
                unlink_file($item_exp,$nocl, ".o");
            }
            //interpret and parser tests
            elseif(!$int_o and !$par_o){
                exec("php8.1 ".$par_path." < ".$item_exp[0].".src"." > ".$item_exp[0].".xml", $out, $rc);
                exec("python3 ".$int_path." --source=".$item_exp[0].".xml"." --input=".$item_exp[0].".in > ".$item_exp[0].".o", $out, $rc);
                //RC type of output
                if($rc != 0){
                    $ref_rc = file_get_contents($item_exp[0].".rc");
                    if($ref_rc == $rc){
                        $signal = true;
                        $rc_signal = true;
                        print_HTML($signal, $item, $rc_signal, $rc, $ref_rc, $ref_output, $output);
                    }
                    else{
                        $signal = false;
                        $rc_signal = true;
                        print_HTML($signal, $item, $rc_signal, $rc, $ref_rc, $ref_output, $output);
                    }
                }
                else{
                    //output compare
                    $output = file_get_contents($item_exp[0].".o");
                    $ref_output = file_get_contents($item_exp[0].".out");
                    $ref_rc = file_get_contents($item_exp[0].".rc");
                    exec("diff ".$item_exp[0].".o ".$item_exp[0].".out", $out, $error);
                    if($error != 0){
                        $signal = false;
                        $rc_signal = false;
                        print_HTML($signal, $item, $rc_signal, $rc, $ref_rc, $ref_output, $output);
                    }
                    else{
                        $signal = true;
                        $rc_signal = false;
                        print_HTML($signal, $item, $rc_signal, $rc, $ref_rc, $ref_output, $output);
                    }
                    
                }
                unlink_file($item_exp,$nocl, ".xml");
                unlink_file($item_exp,$nocl, ".o");
            }
            $aux = $item_exp[0];
        }
    }
    //HTML footer
    echo("\n\t</div>
    <div class=\"footer\">
        <div class=\"content\">
            <p>2022</p>
        </div>
    </div>
    </body>
    </html>\n");
}

//----------------------------------------------------------------------------
//                          MAIN 
//----------------------------------------------------------------------------

//options parse
$arguments = getopt("", ["help", "directory:", "recursive", "parse-script:", "int-script:", "parse-only", "int-only", "jexampath:", "noclean"]);
if($arguments == false){
    fwrite(STDERR,"Wrong args!\n");
    exit(10);
}
$dir = false;
$rec = false;
$par_sc = false;
$int_sc = false;
$par_o = false;
$int_o = false;
$jex = false;
$nocl = false;

foreach($arguments as $arg => $val){
    switch ($arg){
        //help print
        case 'help':
            echo("php8.1 test.php [--help] [--directory=path] [--recursive] [--parse-script=file] [--int-script=file] [--parse-only] [--int-only] [--jexampath=path] [--noclean]\n");
            echo("    --help: prints how to use a script\n");
            echo("    --directory: specifies a path to tests; default is current folder\n");
            echo("    --recursive: recursively searches all subfolders in current folder\n");
            echo("    --parse-script: specifies a path to the php parse script; default is current folder\n");
            echo("    --int-script: specifies a path to the python interpret script; default is current folder\n");
            echo("    --parse-only: run only parse tests\n");
            echo("    --int-only: run only interpret tests\n");
            echo("    --jexampath: specifies a path to the JExamXML file; default is /pub/courses/ipp/jexamxml/\n");
            echo("    --noclean: script will not delete any auxillary files created during testing\n");
            exit(0);
        case 'directory':
            $dir_path = $val;
            $aux = is_dir($dir_path);
            if($aux == false){
                fwrite(STDERR,"File error!\n");
                exit(41);
            }
            $dir = true;
            break;
        case 'recursive':
            $rec = true;
            break;
        case 'parse-script':
            $par_path = $val;
            $aux = is_file($par_path);
            if($aux == false){
                fwrite(STDERR,"File error!\n");
                exit(41);
            }
            $par_sc = true;
            break;
        case 'int-script':
            $int_path = $val;
            $aux = is_file($int_path);
            if($aux == false){
                fwrite(STDERR,"File error!\n");
                exit(41);
            }
            $int_sc = true;
            break;
        case 'parse-only':
            $par_o = true;
            break;
        case 'int-only':
            $int_o = true;
            break;
        case 'jexampath':
            $jex_path = $val;
            $aux = is_file($jex_path);
            if($aux == false){
                fwrite(STDERR,"File error!\n");
                exit(41);
            }
            $jex = true;
            break;
        case 'noclean':
            $nocl = true;
            break;
        default:
            fwrite(STDERR,"Unknown argument!\n");
            exit(10);
            break;
    }
}

//check for the wrong argument combination
if($par_o == true and ($int_sc == true or $int_o == true)){
    fwrite(STDERR,"Invalid combination of args!\n");
    exit(10);
}
if($int_o == true and ($par_sc == true or $par_o == true)){
    fwrite(STDERR,"Invalid combination of args!\n");
    exit(10);
}

//get default file and dir paths
if($dir == false)
    $dir_path = getcwd();
if($par_sc == false)
    $par_path = "parse.php";
if($int_sc == false)
    $int_path = "interpret.py";
if($jex == false)
    $jex_path = "/pub/courses/ipp/jexamxml/";

//testing
$arr = [];
$arr = dir_search($arr ,$rec, $dir_path);
file_add($arr);
$arr = [];
$arr = dir_search($arr ,$rec, $dir_path);

test($arr, $int_o, $par_o, $nocl, $par_path, $int_path, $jex_path);
exit(0);
?>