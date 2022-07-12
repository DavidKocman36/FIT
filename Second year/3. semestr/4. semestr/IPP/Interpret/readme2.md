## Implementační dokuemntace ke 2. úloze do IPP 2021/2022
Jméno a příjmení: David Kocman

Login: xkocma08
## Interpret
### Popis
Interpretace imperativního jazyka IPPcode22. Jako vstup bere XML strom, ve kterém je reprezentován kód zmíněného jazyka.
### Spuštění
``` 
python3 interpret.py [--help] [--source=filename] [--input=filename]
```
--help: vypíše nápovědu použití.

--source=filename: otevře soubor, ve kterém je uložena XML reprezentace kódu v jazyce IPPcode22.

--input=filename: otevře soubor, který slouží jako vstup od uživatele.

Vyjma --help, alespoň jeden přepínač musí být přítomen.

### Běh programu
Při spuštění programu se jako první skontroluje, zda-li je XML struktura zapsaána správně. Po kontrole se vytvoří instance třídy `instruction`, ve kterých jsou uloženy všechny instrukce vyskytující se v programu a předány funkci `my_interpret`.

V hlavní funkci `my_interpret()` se pak interpretují konkrétní instrukce, tj. sémantická kontrola argumentů a následná operace s nimi. Při špatné sémantice interpret vypíše příslušnou chybovou hlášku a kód.

### Rozšíření
Jako rozšíření jsem se rozhodl implementovat NVI. Objekty jsem použil na implementaci jednotlivých instrukcí a rámců. 

Pro tvorbu instrukcí jsem zvolil návrhový vzor `Abstraktní továrna`. Před voláním hlavní funkce jsou tvořeny nekonkrétní instance třídy instruction a každá obsahuje unikátní obsah. Pak se k nim přistupuje postupně, jak jsou seřazeny. Řádek 2299.

Pro tvorbu rámců jsem zvolil vzor `Továrna`. Podle toho, zda-li chceme lokální či dočasný rámec, tak se vytvoří instance třídy s nadtřídou `frame`. Podle toho, zda-li rámec existuje, potom pracuju jen s jeho instancí třídy. Řádky 622 a 636.

definice tříd na řádcích 17 až 53.

## Test
### Popis 
Testovací skript pro parse.php a interpret.py. Vytvoří přehledný HTML 5 soubor, který je vypisován na standardní výstup.
### Spuštění
```
php8.1 test.php [ARGUMENTY] > index.html
```
Všechny argumenty jsou popsány při zadání `--help`.

### Běh programu

Podle zadaných přepínačů si skript jako první projde a vyfiltruje všechny soubory s odpovídající příponou, poté chybějící doplní. Dále procházením pole, ve kterém se nachází všechna jména zdrojových a pomocných souborů, se vykonají parsovací, interpretační testy nebo jejich kombinace.

Pro porovnání výstupních souborů používám nástroj `diff` a pro porovnání výstupních XML souborů nástroj `jexamxml`.
