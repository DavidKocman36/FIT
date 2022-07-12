Autor: David Kocman, xkocma08

# Úvod
Jednoduchý server psaný v jazyce C komunikující prostředníctvím protokolu HTTP. Po spuštění příjmá pomocí příkazů _GET, curl, wget_ a prohlížeče požadavky, na které odpovídá příslušným odesláním HTTP odpovědi.
Je implementován za pomoci C socket knihoven bez použití jakékoliv knihovny na zpracování HTTP.

# Spouštění

Pomocí přiloženého Makefile můžeme projekt přeložit za pomoci příkazu _make_. Pomocí _make clean_ je možno výsledný soubor smazat.

Po úspěšném přeložení se vytvoří spustitelný soubor _hinfosvc_, který spustíme následovně:
```
./hinfosvc <cislo portu>
```
kde `<cislo portu>` je lokální port, na kterém server běží.

Stsknutím klávesové kombinace CTRL+C server vypneme.

# Implementace

Jak již bylo řečeno, server je implementovám pomocí C socket knihoven. Je vytvořen jeden socket na daném portu a po vkročení do nekonečné smyčky vždy čeká na požadavek. Podle porovnání na základě HTTP hlavičky, server odpoví příslušnou odpovědí, popsanou níže v sekci "Příklady použití". 

Pro každou odpověď je vytvořena odpovídající funkce (ve které se vykoná např. výpočet zátěže procesoru) a po jejím vykonání je pomocí funkce _send_ poslána odpověď do klientské sockety. Server poté opět čeká na další požadavek.

# Příklady použití

Po spuštění serveru můžeme posílat 3 typy požadavků.

1. /hostname: získá doménové jméno
```
curl http://localhost:12345/hostname
```
```
DESKTOP-33U52OO
```

2. /cpu-name: získá jméno procesoru

```
curl http://localhost:12345/cpu-name
```
```
Intel(R) Core(TM) i5-8250U CPU @ 1.60GHz
```

3. /load: získá aktuální zátěž

```
curl http://localhost:12345/load
```
```
2%
```
Procenta jsou vypsány celým číslem.

Jakékoliv jiné požadavky než tyto 3 vrací chybu 400 Bad Request.