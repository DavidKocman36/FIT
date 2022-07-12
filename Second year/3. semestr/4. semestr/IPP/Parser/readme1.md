## Implementační dokuemntace k 1. úloze do IPP 2021/2022
Jméno a příjmení: David Kocman

Login: xkocma08

### Popis

Soubor `parse.php` obsahuje lexikální a syntaktický analyzátor pro jazyk IPPcode22, napsán v dynamickém skriptovacím jazyce PHP.

### Spuštění
```
php8.1 parse.php --help
```
Pro vypsání nápovědy.

```
php8.1 parse.php < filename
```
Pro spuštění syntaktické a lexikální analýzy a vypsání XML souboru.

### Běh programu

Po spuštění programu a přesměrování zdrojového souboru v jazyce IPPcode22 na strandardní vstup je porovnán první řádek, zda-li obsahuje povinnou hlavičku (funkce `check_header()`). Poté jsou načteny ostatní řádky, upraveny vlastní funkcí `remove_comm_ws()`, která odstraní komentáře (pokud se na řádku nachází) a upraví mezery, rozsekají se na jednotlivé tokeny a dále je první slovo v řádku (resp. první prvek tokenu) porovnáváno funkcí `switch()`, zda-li se shoduje s jedním z klíčových kódů. Pokud ano, jsou jednotlivé argumenty kontrolovány regulérnímy výrazy, jestli splňují požadavky. Při splňění všech syntaktických (např. správný počet argumentů) a lexikálních podmínek je na standardní výstup vypsán příslušný XML řádek. Každé kombinaci argumentů v kódu je přiřazena jedna z vlastních funkcí pro porovnávání.

Pokud má kód méňě, či více argumentů než by měl mít nebo pokud je argument špatného typu, program vrací číslo chyby 23 a příslušnou chybovou hlášku. Při neznámém či špatném kódu se vrací 22 a při špatné či chybějící hlavičče, 21.

