/* ******************************* c204.c *********************************** */
/*  Předmět: Algoritmy (IAL) - FIT VUT v Brně                                 */
/*  Úkol: c204 - Převod infixového výrazu na postfixový (s využitím c202)     */
/*  Referenční implementace: Petr Přikryl, listopad 1994                      */
/*  Přepis do jazyka C: Lukáš Maršík, prosinec 2012                           */
/*  Upravil: Kamil Jeřábek, září 2019                                         */
/*           Daniel Dolejška, září 2021                                       */
/*           David Kocman, xkocma08, říjen 2021                               */
/* ************************************************************************** */
/*
** Implementujte proceduru pro převod infixového zápisu matematického výrazu
** do postfixového tvaru. Pro převod využijte zásobník (Stack), který byl
** implementován v rámci příkladu c202. Bez správného vyřešení příkladu c202
** se o řešení tohoto příkladu nepokoušejte.
**
** Implementujte následující funkci:
**
**    infix2postfix ... konverzní funkce pro převod infixového výrazu
**                      na postfixový
**
** Pro lepší přehlednost kódu implementujte následující pomocné funkce:
**    
**    untilLeftPar ... vyprázdnění zásobníku až po levou závorku
**    doOperation .... zpracování operátoru konvertovaného výrazu
**
** Své řešení účelně komentujte.
**
** Terminologická poznámka: Jazyk C nepoužívá pojem procedura.
** Proto zde používáme pojem funkce i pro operace, které by byly
** v algoritmickém jazyce Pascalovského typu implemenovány jako procedury
** (v jazyce C procedurám odpovídají funkce vracející typ void).
**
**/

#include "c204.h"

int solved;

/**
 * Pomocná funkce untilLeftPar.
 * Slouží k vyprázdnění zásobníku až po levou závorku, přičemž levá závorka bude
 * také odstraněna.
 * Pokud je zásobník prázdný, provádění funkce se ukončí.
 *
 * Operátory odstraňované ze zásobníku postupně vkládejte do výstupního pole
 * znaků postfixExpression.
 * Délka převedeného výrazu a též ukazatel na první volné místo, na které se má
 * zapisovat, představuje parametr postfixExpressionLength.
 *
 * Aby se minimalizoval počet přístupů ke struktuře zásobníku, můžete zde
 * nadeklarovat a používat pomocnou proměnnou typu char.
 *
 * @param stack Ukazatel na inicializovanou strukturu zásobníku
 * @param postfixExpression Znakový řetězec obsahující výsledný postfixový výraz
 * @param postfixExpressionLength Ukazatel na aktuální délku výsledného postfixového výrazu
 */
void untilLeftPar( Stack *stack, char *postfixExpression, unsigned *postfixExpressionLength ) {
    //cyklus opakujeme dokud nevyprázdníme zásobník nebo nenarazíme na levou závorku
    while (!Stack_IsEmpty(stack))
    {
        //když narazíme na levou závorku, odstraníme ji ze zásobníku a ukončíme cyklus(zrušíme prozatimní 
        //dno stacku)
        if(stack->array[stack->topIndex] == '('){
            Stack_Pop(stack);
            break;
        }
        else{
            //vložíme data z pomocné proměnné do výseldného výrazu a snížíme index stacku
            postfixExpression[*postfixExpressionLength] = stack->array[stack->topIndex];
            Stack_Pop(stack);
            (*postfixExpressionLength)++;
        }
    }
}

/**
 * Pomocná funkce doOperation.
 * Zpracuje operátor, který je předán parametrem c po načtení znaku ze
 * vstupního pole znaků.
 *
 * Dle priority předaného operátoru a případně priority operátoru na vrcholu
 * zásobníku rozhodneme o dalším postupu.
 * Délka převedeného výrazu a taktéž ukazatel na první volné místo, do kterého
 * se má zapisovat, představuje parametr postfixExpressionLength, výstupním
 * polem znaků je opět postfixExpression.
 *
 * @param stack Ukazatel na inicializovanou strukturu zásobníku
 * @param c Znak operátoru ve výrazu
 * @param postfixExpression Znakový řetězec obsahující výsledný postfixový výraz
 * @param postfixExpressionLength Ukazatel na aktuální délku výsledného postfixového výrazu
 */
void doOperation( Stack *stack, char c, char *postfixExpression, unsigned *postfixExpressionLength ) {

    //pokud je zásobník prázdný, vloží se tam prvek (jinak by se podmínka whilu nesplnila a neproběhl by)
    while (!Stack_IsEmpty(stack)){
        /**
         * Zpracovávání priorit operátorů
         * 
         * Pokud na vstupu dostaneme operátor nízké priority, posleme na vystup operator z vrcholu
         * zásobníku
         * Nebo pokud je na vstupu jakýkoli operátor ale na topIndexu operátor vyššího řádu, také
         * pošleme na výstup operátor ze zásobníku
        */
        if ((c == '+') || (c == '-')){
            //pokud je na vstupu prvek se stejnou prioritou nebo vyšší, vyprázdní se stack
            if((stack->array[stack->topIndex] == '*') || (stack->array[stack->topIndex] == '/') || (stack->array[stack->topIndex] == '+') || (stack->array[stack->topIndex] == '-')){
                postfixExpression[*postfixExpressionLength] = stack->array[stack->topIndex];
                (*postfixExpressionLength)++;
                Stack_Pop(stack);
            }
        }
        else if((c == '*') || (c == '/')){
            //pokud je na vstupu stejný prvek, vyprázdní se zásobník
            if((stack->array[stack->topIndex] == '*') || (stack->array[stack->topIndex] == '/')){
                postfixExpression[*postfixExpressionLength] = stack->array[stack->topIndex];
                (*postfixExpressionLength)++;
                Stack_Pop(stack);
            }
            //jinak prvek s vyšší prioritou přidáme na začátek
            else{
                Stack_Push(stack, c);
                break;
            }            
        }
        //Pokud narazíme na levou závorku, založíme nové dno zásobníku
        if (stack->array[stack->topIndex] == '('){
            Stack_Push(stack, c);
            break;
        }
    }
    //do prázdného zásobníku přidáme operátor
    if (Stack_IsEmpty(stack)){
        Stack_Push(stack, c);
        return;
    }
}

/**
 * Konverzní funkce infix2postfix.
 * Čte infixový výraz ze vstupního řetězce infixExpression a generuje
 * odpovídající postfixový výraz do výstupního řetězce (postup převodu hledejte
 * v přednáškách nebo ve studijní opoře).
 * Paměť pro výstupní řetězec (o velikosti MAX_LEN) je třeba alokovat. Volající
 * funkce, tedy příjemce konvertovaného řetězce, zajistí korektní uvolnění zde
 * alokované paměti.
 *
 * Tvar výrazu:
 * 1. Výraz obsahuje operátory + - * / ve významu sčítání, odčítání,
 *    násobení a dělení. Sčítání má stejnou prioritu jako odčítání,
 *    násobení má stejnou prioritu jako dělení. Priorita násobení je
 *    větší než priorita sčítání. Všechny operátory jsou binární
 *    (neuvažujte unární mínus).
 *
 * 2. Hodnoty ve výrazu jsou reprezentovány jednoznakovými identifikátory
 *    a číslicemi - 0..9, a..z, A..Z (velikost písmen se rozlišuje).
 *
 * 3. Ve výrazu může být použit předem neurčený počet dvojic kulatých
 *    závorek. Uvažujte, že vstupní výraz je zapsán správně (neošetřujte
 *    chybné zadání výrazu).
 *
 * 4. Každý korektně zapsaný výraz (infixový i postfixový) musí být uzavřen
 *    ukončovacím znakem '='.
 *
 * 5. Při stejné prioritě operátorů se výraz vyhodnocuje zleva doprava.
 *
 * Poznámky k implementaci
 * -----------------------
 * Jako zásobník použijte zásobník znaků Stack implementovaný v příkladu c202.
 * Pro práci se zásobníkem pak používejte výhradně operace z jeho rozhraní.
 *
 * Při implementaci využijte pomocné funkce untilLeftPar a doOperation.
 *
 * Řetězcem (infixového a postfixového výrazu) je zde myšleno pole znaků typu
 * char, jenž je korektně ukončeno nulovým znakem dle zvyklostí jazyka C.
 *
 * Na vstupu očekávejte pouze korektně zapsané a ukončené výrazy. Jejich délka
 * nepřesáhne MAX_LEN-1 (MAX_LEN i s nulovým znakem) a tedy i výsledný výraz
 * by se měl vejít do alokovaného pole. Po alokaci dynamické paměti si vždycky
 * ověřte, že se alokace skutečně zdrařila. V případě chyby alokace vraťte namísto
 * řetězce konstantu NULL.
 *
 * @param infixExpression Znakový řetězec obsahující infixový výraz k převedení
 *
 * @returns Znakový řetězec obsahující výsledný postfixový výraz
 */
char *infix2postfix( const char *infixExpression ) {
    //alokace paměti výsledného výrazu
    char* expres = malloc(MAX_LEN * sizeof(char));
    if (expres == NULL){
        return NULL;
    }
    //inicializace zásobníku
    Stack stack;
    Stack_Init(&stack);

    unsigned int i = 0;
    unsigned int j = 0;
    //projíždění výrazu až na konec
    while (infixExpression[i] != '\0')
    {
        //pokud narazíme na operátor, určíme jeho prioritu
        if ((infixExpression[i] == '+') || (infixExpression[i] == '-') || (infixExpression[i] == '*') || (infixExpression[i] == '/')){
            doOperation(&stack, infixExpression[i], expres, &j);
        }
        //přeskočí charakter '='
        else if(infixExpression[i] == '='){
            i++;
            continue;
        }
        //založení nového dna zásobníku
        else if (infixExpression[i] == '('){
            Stack_Push(&stack, infixExpression[i]);
        } 
        //při pravé závorce spustíme vyprazdňování zásobníku
        else if(infixExpression[i] == ')'){
            untilLeftPar(&stack, expres, &j);
        }
        //jinak pošleme operand na výstup
        else{
            expres[j++] = infixExpression[i];
        }
        i++;
    }
    
    //na konci vyprázdníme stack, pokud v něm jsou operátory
    while (!Stack_IsEmpty(&stack)) {
        Stack_Top(&stack, &expres[j]);
        Stack_Pop(&stack);
        j++;
    }
    //a pak ukončíme výraz charakterem '=' a nulovým znakem
    expres[j] = '=';
    ++j;
    expres[j] = '\0';
    
    return expres;
}

/* Konec c204.c */
