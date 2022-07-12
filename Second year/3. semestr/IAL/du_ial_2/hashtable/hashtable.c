/*
 * Tabuľka s rozptýlenými položkami
 *
 * S využitím dátových typov zo súboru hashtable.h a pripravených kostier
 * funkcií implementujte tabuľku s rozptýlenými položkami s explicitne
 * zreťazenými synonymami.
 *
 * Pri implementácii uvažujte veľkosť tabuľky HT_SIZE.
 */

#include "hashtable.h"
#include <stdlib.h>
#include <string.h>

int HT_SIZE = MAX_HT_SIZE;

/*
 * Rozptyľovacia funkcia ktorá pridelí zadanému kľúču index z intervalu
 * <0,HT_SIZE-1>. Ideálna rozptyľovacia funkcia by mala rozprestrieť kľúče
 * rovnomerne po všetkých indexoch. Zamyslite sa nad kvalitou zvolenej funkcie.
 */
int get_hash(char *key) {
  int result = 1;
  int length = strlen(key);
  for (int i = 0; i < length; i++) {
    result += key[i];
  }
  return (result % HT_SIZE);
}

/*
 * Inicializácia tabuľky — zavolá sa pred prvým použitím tabuľky.
 */
void ht_init(ht_table_t *table) {
  for (unsigned int i = 0; i < HT_SIZE; i++)
  {
    (*table)[i] = NULL;
  }
  
}

/*
 * Vyhľadanie prvku v tabuľke.
 *
 * V prípade úspechu vráti ukazovateľ na nájdený prvok; v opačnom prípade vráti
 * hodnotu NULL.
 */
ht_item_t *ht_search(ht_table_t *table, char *key) {
  //zahashování klíče
  int hash = get_hash(key);
  //pomocné proměnná ve které bude uložený aktualní a nalezený prvek
  //přiřazuji do ní ukazatel na seznam ve kterém se potenciálně vyskytuje hledaný prvek
  ht_item_t *searched = (*table)[hash];
  while(searched != NULL){

    if (strcmp(searched->key, key) == 0){
      //našel
      return searched;
    }
    searched = searched->next;
  }
  //nenašel
  return NULL;
}

/*
 * Vloženie nového prvku do tabuľky.
 *
 * Pokiaľ prvok s daným kľúčom už v tabuľke existuje, nahraďte jeho hodnotu.
 *
 * Pri implementácii využite funkciu ht_search. Pri vkladaní prvku do zoznamu
 * synonym zvoľte najefektívnejšiu možnosť a vložte prvok na začiatok zoznamu.
 */
void ht_insert(ht_table_t *table, char *key, float value) {
  //zahashování klíče
  int hashed = get_hash(key);
  //přiřazení patra na klíč
  ht_item_t *aux = (*table)[hashed];

  //pokud je řádek prázdný, vloží nový element
  if (aux == NULL)
  {
    ht_item_t *NewItem = malloc(sizeof(struct ht_item));
    NewItem->key = malloc(strlen(key) + 1);
    if(NewItem == NULL || NewItem->key == NULL){
      return;
    }

    strcpy(NewItem->key,key);
    NewItem->value = value;
    NewItem->next = NULL;
    (*table)[hashed] = NewItem;
    return;
  }
  else{
    //nalezení již existujícího klíče
    ht_item_t *search = ht_search(table, key);
    if (search != NULL){
      (*table)[hashed]->value = value;
      return;
    }
    //pokud neexistuje, vložíme nový prvek
    else{
      ht_item_t *NewItem = malloc(sizeof(struct ht_item));
      NewItem->key = malloc(strlen(key) + 1);
      if(NewItem == NULL || NewItem->key == NULL){
        return;
      }

      strcpy(NewItem->key,key);
      NewItem->value = value;
      NewItem->next = NULL;
      ht_item_t *curr = (*table)[hashed];
      (*table)[hashed] = NewItem;
      (*table)[hashed]->next = curr;
      return;
    }
  }
}

/*
 * Získanie hodnoty z tabuľky.
 *
 * V prípade úspechu vráti funkcia ukazovateľ na hodnotu prvku, v opačnom
 * prípade hodnotu NULL.
 *
 * Pri implementácii využite funkciu ht_search.
 */
float *ht_get(ht_table_t *table, char *key) {
  //nalezení elementu pomocí klíče
  ht_item_t *getVal = ht_search(table, key);
  //pokud nalezen, vrátíme hodnotu
  if (getVal != NULL)
  {
    return &getVal->value;
  }
  
  return NULL;
}

/*
 * Zmazanie prvku z tabuľky.
 *
 * Funkcia korektne uvoľní všetky alokované zdroje priradené k danému prvku.
 * Pokiaľ prvok neexistuje, nerobte nič.
 *
 * Pri implementácii NEVYUŽÍVAJTE funkciu ht_search.
 */
void ht_delete(ht_table_t *table, char *key) {
  //hashování klíče
  int hash = get_hash(key);
  //aktuální a předešlý prvek
  ht_item_t *searched = (*table)[hash];
  ht_item_t *prev = NULL;
  //pokud se nachází v seznamu
  bool found = false;
  //prohledávání příslušného řádku
  while (searched != NULL)
  {
    if (strcmp(searched->key, key) == 0)
    {
      found = true;
      break;
    }
    prev = searched;
    searched = searched->next;
  }
  //pokud byl nenalezen, nic neděláme
  if(!found){
    return;
  }
  else{
    //jinak mažeme podle 2 případů:
    //element je na začátku seznamu,
    //element je na konci/uvnitř seznamu
    if (prev == NULL){
      ht_item_t *aux = searched->next;
      free(searched->key);
      free(searched);
      searched = NULL;
      (*table)[hash] = aux;
      return;
    }
    else{
      prev->next = searched->next;
      free(searched->key);
      free(searched);
      searched = NULL;
      return;
    }
  }
}

/*
 * Zmazanie všetkých prvkov z tabuľky.
 *
 * Funkcia korektne uvoľní všetky alokované zdroje a uvedie tabuľku do stavu po
 * inicializácii.
 */
void ht_delete_all(ht_table_t *table) {
  //procházení řádků tabulky
  for (unsigned int i = 0; i < HT_SIZE; i++){
    while ((*table)[i] != NULL)
    {
      //postupné mazání jednotlivých prvků
      ht_item_t *delAux = (*table)[i];
      (*table)[i] = delAux->next;
      free(delAux->key);
      free(delAux);
    }
    //nastavení tabulky do stavu po inicializaci
    (*table)[i] = NULL;
  }
}
