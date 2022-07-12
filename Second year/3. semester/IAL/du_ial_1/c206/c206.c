
/* ******************************* c206.c *********************************** */
/*  Předmět: Algoritmy (IAL) - FIT VUT v Brně                                 */
/*  Úkol: c206 - Dvousměrně vázaný lineární seznam                            */
/*  Návrh a referenční implementace: Bohuslav Křena, říjen 2001               */
/*  Vytvořil: Martin Tuček, říjen 2004                                        */
/*  Upravil: Kamil Jeřábek, září 2020                                         */
/*           Daniel Dolejška, září 2021                                       */
/*           David Kocman, xkocma08, říjen 2021                                */
/* ************************************************************************** */
/*
** Implementujte abstraktní datový typ dvousměrně vázaný lineární seznam.
** Užitečným obsahem prvku seznamu je hodnota typu int. Seznam bude jako datová
** abstrakce reprezentován proměnnou typu DLList (DL znamená Doubly-Linked
** a slouží pro odlišení jmen konstant, typů a funkcí od jmen u jednosměrně
** vázaného lineárního seznamu). Definici konstant a typů naleznete
** v hlavičkovém souboru c206.h.
**
** Vaším úkolem je implementovat následující operace, které spolu s výše
** uvedenou datovou částí abstrakce tvoří abstraktní datový typ obousměrně
** vázaný lineární seznam:
**
**      DLL_Init ........... inicializace seznamu před prvním použitím,
**      DLL_Dispose ........ zrušení všech prvků seznamu,
**      DLL_InsertFirst .... vložení prvku na začátek seznamu,
**      DLL_InsertLast ..... vložení prvku na konec seznamu,
**      DLL_First .......... nastavení aktivity na první prvek,
**      DLL_Last ........... nastavení aktivity na poslední prvek,
**      DLL_GetFirst ....... vrací hodnotu prvního prvku,
**      DLL_GetLast ........ vrací hodnotu posledního prvku,
**      DLL_DeleteFirst .... zruší první prvek seznamu,
**      DLL_DeleteLast ..... zruší poslední prvek seznamu,
**      DLL_DeleteAfter .... ruší prvek za aktivním prvkem,
**      DLL_DeleteBefore ... ruší prvek před aktivním prvkem,
**      DLL_InsertAfter .... vloží nový prvek za aktivní prvek seznamu,
**      DLL_InsertBefore ... vloží nový prvek před aktivní prvek seznamu,
**      DLL_GetValue ....... vrací hodnotu aktivního prvku,
**      DLL_SetValue ....... přepíše obsah aktivního prvku novou hodnotou,
**      DLL_Previous ....... posune aktivitu na předchozí prvek seznamu,
**      DLL_Next ........... posune aktivitu na další prvek seznamu,
**      DLL_IsActive ....... zjišťuje aktivitu seznamu.
**
** Při implementaci jednotlivých funkcí nevolejte žádnou z funkcí
** implementovaných v rámci tohoto příkladu, není-li u funkce explicitně
 * uvedeno něco jiného.
**
** Nemusíte ošetřovat situaci, kdy místo legálního ukazatele na seznam
** předá někdo jako parametr hodnotu NULL.
**
** Svou implementaci vhodně komentujte!
**
** Terminologická poznámka: Jazyk C nepoužívá pojem procedura.
** Proto zde používáme pojem funkce i pro operace, které by byly
** v algoritmickém jazyce Pascalovského typu implemenovány jako procedury
** (v jazyce C procedurám odpovídají funkce vracející typ void).
**
**/

#include "c206.h"

int error_flag;
int solved;

/**
 * Vytiskne upozornění na to, že došlo k chybě.
 * Tato funkce bude volána z některých dále implementovaných operací.
 */
void DLL_Error() {
	printf("*ERROR* The program has performed an illegal operation.\n");
	error_flag = TRUE;
}

/**
 * Provede inicializaci seznamu list před jeho prvním použitím (tzn. žádná
 * z následujících funkcí nebude volána nad neinicializovaným seznamem).
 * Tato inicializace se nikdy nebude provádět nad již inicializovaným seznamem,
 * a proto tuto možnost neošetřujte.
 * Vždy předpokládejte, že neinicializované proměnné mají nedefinovanou hodnotu.
 *
 * @param list Ukazatel na strukturu dvousměrně vázaného seznamu
 */
void DLL_Init( DLList *list ) {

    //Inicializuje všechny prvky na NULL
    list->firstElement = NULL;
    list->activeElement = NULL;
    list->lastElement = NULL;
}

/**
 * Zruší všechny prvky seznamu list a uvede seznam do stavu, v jakém se nacházel
 * po inicializaci.
 * Rušené prvky seznamu budou korektně uvolněny voláním operace free.
 *
 * @param list Ukazatel na inicializovanou strukturu dvousměrně vázaného seznamu
 */
void DLL_Dispose( DLList *list ) {

    //vytvoří si pomocnou proměnnou, přes kterou se bude mazat prvek listu
    DLLElementPtr tempDelete = list->firstElement;
    while (list->firstElement != NULL)
    {
        //dokud není seznam prázdný, uvolňujeme prvky z paměti
        tempDelete = list->firstElement;
        list->firstElement = list->firstElement->nextElement;
        free(tempDelete);
    }
    //po vymazání všech prvků je seznam navrácen do stavu při inicializaci
    list->firstElement = NULL;
    list->activeElement = NULL;
    list->lastElement = NULL;
}

/**
 * Vloží nový prvek na začátek seznamu list.
 * V případě, že není dostatek paměti pro nový prvek při operaci malloc,
 * volá funkci DLL_Error().
 *
 * @param list Ukazatel na inicializovanou strukturu dvousměrně vázaného seznamu
 * @param data Hodnota k vložení na začátek seznamu
 */
void DLL_InsertFirst( DLList *list, int data ) {

    //vyhrazení paměti pro nový prvek a ošetření stavu, pokud se malloc nepovede
    DLLElementPtr tempFirst = malloc(sizeof(struct DLLElement));
    if (tempFirst == NULL){
        DLL_Error();
        return;
    }
    else{

        tempFirst->data = data;
        //2 stavy: pokud voláme funkci na prázdný seznam a pokud ji voláme do již exstujícího seznamu
        if(list->firstElement == NULL){
            //nastavení obou ukazatelů na NULL
            tempFirst->nextElement = NULL;
            tempFirst->previousElement = NULL;
            //přiřazení prvního prvku do prázdného seznamu; je zároveň první i poslední
            list->lastElement = tempFirst;
            list->firstElement = tempFirst;
        }
        else{
            //linknutí nového prvku na již existující
            tempFirst->nextElement = list->firstElement;
            tempFirst->previousElement = NULL;
            //linknutí již existujícího prvku na nový a zařazení do seznamu
            list->firstElement->previousElement = tempFirst;
            list->firstElement = tempFirst;
        }
    }
}

/**
 * Vloží nový prvek na konec seznamu list (symetrická operace k DLL_InsertFirst).
 * V případě, že není dostatek paměti pro nový prvek při operaci malloc,
 * volá funkci DLL_Error().
 *
 * @param list Ukazatel na inicializovanou strukturu dvousměrně vázaného seznamu
 * @param data Hodnota k vložení na konec seznamu
 */
void DLL_InsertLast( DLList *list, int data ) {

    //vyhrazení paměti pro nový prvek a ošetření stavu, pokud se malloc nepovede
    DLLElementPtr tempLast = malloc(sizeof(struct DLLElement));
    if (tempLast == NULL){
        DLL_Error();
        return;
    }
    else{
        tempLast->data = data;
        //2 stavy: pokud voláme funkci na prázdný seznam a pokud ji voláme do již exstujícího seznamu
        if(list->lastElement == NULL){
            //nastavení obou ukazatelů na NULL
            tempLast->nextElement = NULL;
            tempLast->previousElement = NULL;
            //vložení prvku do seznamu; je zároveň první i poslední
            list->firstElement = tempLast;
            list->lastElement = tempLast;
        }
        else{
            //linknutí nového prvku na již existující
            tempLast->nextElement = NULL;
            tempLast->previousElement = list->lastElement;
            //linknutí již existujícího prvku na nový a zařazení do seznamu
            list->lastElement->nextElement = tempLast;
            list->lastElement = tempLast;
        }
    }
}

/**
 * Nastaví první prvek seznamu list jako aktivní.
 * Funkci implementujte jako jediný příkaz (nepočítáme-li return),
 * aniž byste testovali, zda je seznam list prázdný.
 *
 * @param list Ukazatel na inicializovanou strukturu dvousměrně vázaného seznamu
 */
void DLL_First( DLList *list ) {

    //nastavení prvního prvku na aktivní
    list->activeElement = list->firstElement;
}

/**
 * Nastaví poslední prvek seznamu list jako aktivní.
 * Funkci implementujte jako jediný příkaz (nepočítáme-li return),
 * aniž byste testovali, zda je seznam list prázdný.
 *
 * @param list Ukazatel na inicializovanou strukturu dvousměrně vázaného seznamu
 */
void DLL_Last( DLList *list ) {

    //nastavení posledního prvku na aktivní
    list->activeElement = list->lastElement;
}

/**
 * Prostřednictvím parametru dataPtr vrátí hodnotu prvního prvku seznamu list.
 * Pokud je seznam list prázdný, volá funkci DLL_Error().
 *
 * @param list Ukazatel na inicializovanou strukturu dvousměrně vázaného seznamu
 * @param dataPtr Ukazatel na cílovou proměnnou
 */
void DLL_GetFirst( DLList *list, int *dataPtr ) {

    //ošetření možnosti získávání prvku z prázdného seznamu
    if (list->firstElement == NULL){
        DLL_Error();
        return;
    }
    //získání dat prvního prvku
    else
        *dataPtr = list->firstElement->data;
}

/**
 * Prostřednictvím parametru dataPtr vrátí hodnotu posledního prvku seznamu list.
 * Pokud je seznam list prázdný, volá funkci DLL_Error().
 *
 * @param list Ukazatel na inicializovanou strukturu dvousměrně vázaného seznamu
 * @param dataPtr Ukazatel na cílovou proměnnou
 */
void DLL_GetLast( DLList *list, int *dataPtr ) {

    //ošetření možnosti získávání prvku z prázdného seznamu
    if (list->lastElement == NULL){ 
        DLL_Error();
        return;
    }
    //získání dat posledního prvku
    else 
        *dataPtr = list->lastElement->data;
}

/**
 * Zruší první prvek seznamu list.
 * Pokud byl první prvek aktivní, aktivita se ztrácí.
 * Pokud byl seznam list prázdný, nic se neděje.
 *
 * @param list Ukazatel na inicializovanou strukturu dvousměrně vázaného seznamu
 */
void DLL_DeleteFirst( DLList *list ) {

    //při prázdném seznamu nic nedělá
	if(list->firstElement == NULL) 
        return;
    else{
        //vytvoření mazací pomocné
        DLLElementPtr tempDeleteF = list->firstElement;
        //2 stavy: pokud je prvek poslední v seznamu nebo když je první ale má pod sebou
        //ještě prvky
        if(list->firstElement->nextElement == NULL){
            //zrušení aktivity prvku
            if (list->firstElement == list->activeElement){
                list->activeElement = NULL;
            }
            //zrušení všech linků
            list->firstElement->nextElement = NULL;
            list->firstElement->previousElement = NULL;
            //uvolnění paměti
            free(tempDeleteF);
            list->firstElement = NULL;
            list->lastElement = NULL;
        }
        else{
            //zrušení aktivity prvku
            if (list->firstElement == list->activeElement){
                list->activeElement = NULL;
            }
            //přepsání linkování seznamu o 1 prvek doprava
            list->firstElement = list->firstElement->nextElement;
            list->firstElement->previousElement = NULL;
            //uvolnění paměti
            free(tempDeleteF);
        }
    }
}

/**
 * Zruší poslední prvek seznamu list.
 * Pokud byl poslední prvek aktivní, aktivita seznamu se ztrácí.
 * Pokud byl seznam list prázdný, nic se neděje.
 *
 * @param list Ukazatel na inicializovanou strukturu dvousměrně vázaného seznamu
 */
void DLL_DeleteLast( DLList *list ) {

    //při prázdném seznamu nic nedělá
	if(list->firstElement == NULL) 
        return;
    else{
        //vytvoření mazací pomocné
        DLLElementPtr tempDeleteL = list->lastElement;
        //2 stavy: pokud je prvek úplně poslední nebo když je poslední ale má před sebou
        //ještě prvky
        if (list->lastElement->previousElement == NULL){
            //zrušení aktivity prvku
            if (list->lastElement == list->activeElement){
                list->activeElement = NULL;
            }
            //zrušíme všechny linky
            list->lastElement->previousElement = NULL;
            list->lastElement->nextElement = NULL;
            //uvolnění paměti
            free(tempDeleteL);
            list->firstElement = NULL;
            list->lastElement = NULL;
        }
        else{
            //zrušení aktivity prvku
            if (list->lastElement == list->activeElement){
                list->activeElement = NULL;
            }
            //přepsání linkování seznamu o 1 prvek doleva
            list->lastElement = list->lastElement->previousElement;
            list->lastElement->nextElement = NULL;
            //uvolnění paměti
            free(tempDeleteL);
        }
    }
}

/**
 * Zruší prvek seznamu list za aktivním prvkem.
 * Pokud je seznam list neaktivní nebo pokud je aktivní prvek
 * posledním prvkem seznamu, nic se neděje.
 *
 * @param list Ukazatel na inicializovanou strukturu dvousměrně vázaného seznamu
 */
void DLL_DeleteAfter( DLList *list ) {
    
    //ošetření možosti pokud rušíme prvek z neaktivního seznamu nebo když aktivní prvek je poslední
	if(list->activeElement == NULL || list->activeElement == list->lastElement) 
        return;
    else{
        //pomocná proměnná, kterou budeme mazat
        DLLElementPtr tempDeleteA = list->activeElement->nextElement;
        //2 stavy: pokud je prvek předposlední nebo je kdekoli uvnitř seznamu
        if (list->activeElement->nextElement == list->lastElement){
            //zrušení vazby na poslední prvek a posunutí 
            list->activeElement->nextElement = NULL;
            list->lastElement = list->activeElement;
            //uvolnění paměti
            free(tempDeleteA);
        }
        else{
            //vazbování předešlého prvku na následující
            list->activeElement->nextElement = list->activeElement->nextElement->nextElement;
            list->activeElement->nextElement->previousElement = list->activeElement;
            //uvolnění odstraňovaného prvku
            free(tempDeleteA);
        }  
    }
}

/**
 * Zruší prvek před aktivním prvkem seznamu list .
 * Pokud je seznam list neaktivní nebo pokud je aktivní prvek
 * prvním prvkem seznamu, nic se neděje.
 *
 * @param list Ukazatel na inicializovanou strukturu dvousměrně vázaného seznamu
 */
void DLL_DeleteBefore( DLList *list ) {
    
    //ošetření možosti pokud rušíme prvek z neaktivního seznamu nebo když aktivní prvek je první
	if(list->activeElement == NULL || list->activeElement == list->firstElement) 
        return;
    else{
        //pomocná proměnná, kterou budeme mazat
        DLLElementPtr tempDeleteB = list->activeElement->previousElement;
        //2 stavy: pokud je prvek druhý nebo je kdekoli uvnitř seznamu
        if (list->activeElement->previousElement == list->firstElement){
            //zrušení vazby na první prvek a posunutí 
            list->activeElement->previousElement = NULL;
            list->firstElement = list->activeElement;
            //uvolnění paměti
            free(tempDeleteB);
            
        }
        else{
            //vazbování předešlého prvku na následující
            list->activeElement->previousElement = list->activeElement->previousElement->previousElement;
            list->activeElement->previousElement->nextElement = list->activeElement;
            //uvolnění odstraňovaného prvku
            free(tempDeleteB);
        }  
    }
}

/**
 * Vloží prvek za aktivní prvek seznamu list.
 * Pokud nebyl seznam list aktivní, nic se neděje.
 * V případě, že není dostatek paměti pro nový prvek při operaci malloc,
 * volá funkci DLL_Error().
 *
 * @param list Ukazatel na inicializovanou strukturu dvousměrně vázaného seznamu
 * @param data Hodnota k vložení do seznamu za právě aktivní prvek
 */
void DLL_InsertAfter( DLList *list, int data ) {

    //nedělat nic pokud seznam není aktivní
    if(list->activeElement == NULL)
        return;
    
    //vyhrazení  paměti pro přidávaný prvek
    DLLElementPtr tempIA = malloc(sizeof(struct DLLElement));
    if(tempIA == NULL){
        DLL_Error();
        return;
    }
    tempIA->data = data;
    //2 stavy: pokud přidáváme na konec seznamu nebo pokud přidáváme dovnitř seznamu
    if(list->activeElement->nextElement == NULL){
        //linkneme nový prvek s předešlým
        tempIA->nextElement = NULL;
        tempIA->previousElement = list->activeElement;
        //vložíme do seznamu 
        list->activeElement->nextElement = tempIA;
        list->lastElement = tempIA;
    }
    else{
        //linkneme nový prvek s aktivním a jeho dalším prvkem
        tempIA->nextElement = list->activeElement->nextElement;
        tempIA->previousElement = list->activeElement;
        //vložíme do seznamu; linkneme aktivní prvek s novým
        list->activeElement->nextElement->previousElement = tempIA;
        list->activeElement->nextElement = tempIA;
    }
}

/**
 * Vloží prvek před aktivní prvek seznamu list.
 * Pokud nebyl seznam list aktivní, nic se neděje.
 * V případě, že není dostatek paměti pro nový prvek při operaci malloc,
 * volá funkci DLL_Error().
 *
 * @param list Ukazatel na inicializovanou strukturu dvousměrně vázaného seznamu
 * @param data Hodnota k vložení do seznamu před právě aktivní prvek
 */
void DLL_InsertBefore( DLList *list, int data ) {

    //nedělat nic pokud seznam není aktivní
    if(list->activeElement == NULL)
        return;
    
    //vyhrazení  paměti pro přidávaný prvek
    DLLElementPtr tempIB = malloc(sizeof(struct DLLElement));
    if(tempIB == NULL){
        DLL_Error();
        return;
    }
    //2 stavy: pokud přidáváme na začátek seznamu nebo pokud přidáváme dovnitř seznamu
    tempIB->data = data;
    if(list->activeElement->previousElement == NULL){
        //linkneme nový prvek s dalším
        tempIB->previousElement = NULL;
        tempIB->nextElement = list->activeElement;
        //vložíme do seznamu 
        list->activeElement->previousElement = tempIB;
        list->firstElement = tempIB;
    }
    else{
        //linkneme nový prvek s aktivním a jeho dalším prvkem
        tempIB->previousElement = list->activeElement->previousElement;
        tempIB->nextElement = list->activeElement;
        //vložíme do seznamu; linkneme aktivní prvek s novým
        list->activeElement->previousElement->nextElement = tempIB;
        list->activeElement->previousElement = tempIB;
    }
}

/**
 * Prostřednictvím parametru dataPtr vrátí hodnotu aktivního prvku seznamu list.
 * Pokud seznam list není aktivní, volá funkci DLL_Error ().
 *
 * @param list Ukazatel na inicializovanou strukturu dvousměrně vázaného seznamu
 * @param dataPtr Ukazatel na cílovou proměnnou
 */
void DLL_GetValue( DLList *list, int *dataPtr ) {

    //ošetření možnosti získávání prvku z neaktivního seznamu
    if(list->activeElement == NULL){
        DLL_Error();
        return;
    }
    //získání dat aktivního prvku
	else 
        *dataPtr = list->activeElement->data;
}

/**
 * Přepíše obsah aktivního prvku seznamu list.
 * Pokud seznam list není aktivní, nedělá nic.
 *
 * @param list Ukazatel na inicializovanou strukturu dvousměrně vázaného seznamu
 * @param data Nová hodnota právě aktivního prvku
 */
void DLL_SetValue( DLList *list, int data ) {

    //ošetření možnosti přepsání prvku v neaktivním seznamu
    if(list->activeElement == NULL){
        return;
    }
    //přepsání aktivního prvku
    else 
        list->activeElement->data = data;
}

/**
 * Posune aktivitu na následující prvek seznamu list.
 * Není-li seznam aktivní, nedělá nic.
 * Všimněte si, že při aktivitě na posledním prvku se seznam stane neaktivním.
 *
 * @param list Ukazatel na inicializovanou strukturu dvousměrně vázaného seznamu
 */
void DLL_Next( DLList *list ) {
    //ošetření možnosti posunutí aktivity v neaktivním seznamu
    if(list->activeElement == NULL) 
        return;
    //posunutí aktivity o 1 doprava
    else 
        list->activeElement = list->activeElement->nextElement;
}


/**
 * Posune aktivitu na předchozí prvek seznamu list.
 * Není-li seznam aktivní, nedělá nic.
 * Všimněte si, že při aktivitě na prvním prvku se seznam stane neaktivním.
 *
 * @param list Ukazatel na inicializovanou strukturu dvousměrně vázaného seznamu
 */
void DLL_Previous( DLList *list ) {

    //ošetření možnosti posunutí aktivity v neaktivním seznamu
    if(list->activeElement == NULL) 
        return;
    //posunutí aktivity o 1 doleva
    else 
        list->activeElement = list->activeElement->previousElement;
}

/**
 * Je-li seznam list aktivní, vrací nenulovou hodnotu, jinak vrací 0.
 * Funkci je vhodné implementovat jedním příkazem return.
 *
 * @param list Ukazatel na inicializovanou strukturu dvousměrně vázaného seznamu
 *
 * @returns Nenulovou hodnotu v případě aktivity prvku seznamu, jinak nulu
 */
int DLL_IsActive( DLList *list ) {

    //zjištění aktivity
    return (list->activeElement == NULL)? 0 : 1;
}

/* Konec c206.c */
