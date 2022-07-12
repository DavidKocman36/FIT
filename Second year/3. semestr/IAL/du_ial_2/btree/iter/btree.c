/*
 * Binárny vyhľadávací strom — iteratívna varianta
 *
 * S využitím dátových typov zo súboru btree.h, zásobníkov zo súborov stack.h a
 * stack.c a pripravených kostier funkcií implementujte binárny vyhľadávací
 * strom bez použitia rekurzie.
 */

#include "../btree.h"
#include "stack.h"
#include <stdio.h>
#include <stdlib.h>

/*
 * Inicializácia stromu.
 *
 * Užívateľ musí zaistiť, že incializácia sa nebude opakovane volať nad
 * inicializovaným stromom. V opačnom prípade môže dôjsť k úniku pamäte (memory
 * leak). Keďže neinicializovaný ukazovateľ má nedefinovanú hodnotu, nie je
 * možné toto detegovať vo funkcii.
 */
void bst_init(bst_node_t **tree) {
  //nastavíme kořen na NULL
  (*tree) = NULL;
}

/*
 * Nájdenie uzlu v strome.
 *
 * V prípade úspechu vráti funkcia hodnotu true a do premennej value zapíše
 * hodnotu daného uzlu. V opačnom prípade funckia vráti hodnotu false a premenná
 * value ostáva nezmenená.
 *
 * Funkciu implementujte iteratívne bez použitia vlastných pomocných funkcií.
 */
bool bst_search(bst_node_t *tree, char key, int *value) {
  //procházíme celý strom
  while (tree != NULL)
  {
    //pokud jsme našli, končíme
    if (tree->key == key)
    {
      *value = tree->value;
      return true;
    }
    //jinak posuneme ukazatel
    else if(key < tree->key){
      tree = tree->left;
    }
    else{
      tree = tree->right;
    }
  }
  return false;
}

/*
 * Vloženie uzlu do stromu.
 *
 * Pokiaľ uzol so zadaným kľúčom v strome už existuje, nahraďte jeho hodnotu.
 * Inak vložte nový listový uzol.
 *
 * Výsledný strom musí spĺňať podmienku vyhľadávacieho stromu — ľavý podstrom
 * uzlu obsahuje iba menšie kľúče, pravý väčšie.
 *
 * Funkciu implementujte iteratívne bez použitia vlastných pomocných funkcií.
 */
void bst_insert(bst_node_t **tree, char key, int value) {
  //při prázdném stromu vložíme kořen
  if((*tree) == NULL){
    bst_node_t *newNode = malloc(sizeof(struct bst_node));
    if(newNode == NULL){
      return;
    } 
    newNode->key = key;
    newNode->value = value;
    newNode->left = newNode->right =NULL;
    (*tree) = newNode;
    return;
  }
  else{
    //jinak procházíme celý strom
    bst_node_t *tmp = (*tree);
    while (tmp!=NULL)
    {
      //při již existujícím uzlu změníme jeho hodnotu
      if (tmp->key == key)
      {
        tmp->value = value;
        break;
      }
      //jiank se zanořujeme níž
      else if(key < tmp->key){
        //pokud levý uzel není, vložíme ho, jinak jdeme dál
        if (tmp->left != NULL)
        {
          tmp = tmp->left;
        }
        else{
          bst_node_t *newNodeLeft = malloc(sizeof(struct bst_node));
          if(newNodeLeft == NULL){
            return;
          } 
          newNodeLeft->key = key;
          newNodeLeft->value = value;
          newNodeLeft->left = newNodeLeft->right =NULL;
          tmp->left = newNodeLeft;
          break;
        }
      }
      else{
        //pokud pravý uzel není, vložíme ho, jinak jdeme dál
        if (tmp->right != NULL)
        {
          tmp = tmp->right;
        }
        else{
          bst_node_t *newNodeRight = malloc(sizeof(struct bst_node));
          if(newNodeRight == NULL){
            return;
          } 
          newNodeRight->key = key;
          newNodeRight->value = value;
          newNodeRight->left = newNodeRight->right =NULL;
          tmp->right = newNodeRight;
          break;
        }
      }
    }
  }
}

/*
 * Pomocná funkcia ktorá nahradí uzol najpravejším potomkom.
 *
 * Kľúč a hodnota uzlu target budú nahradené kľúčom a hodnotou najpravejšieho
 * uzlu podstromu tree. Najpravejší potomok bude odstránený. Funkcia korektne
 * uvoľní všetky alokované zdroje odstráneného uzlu.
 *
 * Funkcia predpokladá že hodnota tree nie je NULL.
 *
 * Táto pomocná funkcia bude využitá pri implementácii funkcie bst_delete.
 *
 * Funkciu implementujte iteratívne bez použitia vlastných pomocných funkcií.
 */
void bst_replace_by_rightmost(bst_node_t *target, bst_node_t **tree) {
  bst_node_t *tmpPrev = NULL;
  bst_node_t *tmp = (*tree);
  //procházíme strom a do proměnných si ukládáme aktuální a předešlý uzel
  //jdeme dokud nenarazíme na konec pravých větví
  while (tmp->right != NULL)
  {
    tmpPrev = tmp;
    tmp = tmp->right;
  }
  //nahradíme target naším aktuálním uzlem
  target->key = tmp->key;
  target->value = tmp->value;
  //pokud existuje levá větev, připojíme ji k předchozímu uzlu
  if (tmp->left != NULL)
  {
    tmpPrev->right = tmp->left;
  }
  //jinak ztratíme spojení s aktuálním uzlem
  else{
    tmpPrev->right = NULL;
  }
  //a odstraníme ho
  free(tmp);
  tmp = NULL;
}

/*
 * Odstránenie uzlu v strome.
 *
 * Pokiaľ uzol so zadaným kľúčom neexistuje, funkcia nič nerobí.
 * Pokiaľ má odstránený uzol jeden podstrom, zdedí ho otec odstráneného uzla.
 * Pokiaľ má odstránený uzol oba podstromy, je nahradený najpravejším uzlom
 * ľavého podstromu. Najpravejší uzol nemusí byť listom!
 * Funkcia korektne uvoľní všetky alokované zdroje odstráneného uzlu.
 *
 * Funkciu implementujte iteratívne pomocou bst_replace_by_rightmost a bez
 * použitia vlastných pomocných funkcií.
 */
void bst_delete(bst_node_t **tree, char key) {
  bst_node_t *tmp = (*tree);
  bst_node_t *tmpPrev = NULL;
  //procházíme strom
  while (tmp != NULL){

    if (tmp->key == key){
      //pokud jsme našli uzel
      bst_node_t *auxNode = NULL;
      //a pokud má jen jednoho potomka
      if (tmp->left == NULL || tmp->right == NULL)
      {
        //při možnosti že uzel je kořen
        if(tmpPrev == NULL){
          //napojíme kořen na jeho potomky
          if (tmp->left == NULL){
            (*tree) = tmp->right;
          }
          else{
            (*tree) = tmp->left;
          }
        }
        else if (tmp->left == NULL){
          //pokud má pravého potomka
          auxNode = tmp->right;
          //napojíme předchozí uzel na příští uzel
          if (tmpPrev->left == tmp)
          {
            tmpPrev->left = auxNode;
          }
          else{
            tmpPrev->right = auxNode;
          }
        }
        else if(tmp->right == NULL){
          //pokud má levého
          auxNode = tmp->left;
          //napojíme předchozí uzel na příští uzel
          if (tmpPrev->left == tmp)
          {
            tmpPrev->left = auxNode;
          }
          else{
            tmpPrev->right = auxNode;
          }
        }
      }
      else{
        //jinak jsme narazili na kořen s potomky a nahradíme ho nejpravějším potomkem levého potomka 
        bst_replace_by_rightmost(tmp, &tmp->left);
        return;
      }
      //odstraníme uzel
      free(tmp);
      tmp = NULL;
      break;
    }
    else{
      //jinak jdeme dál
      tmpPrev = tmp;
      if(key < tmp->key){
        tmp = tmp->left;
      }
      else{
        tmp = tmp->right;
      }
    }
  }
}

/*
 * Zrušenie celého stromu.
 *
 * Po zrušení sa celý strom bude nachádzať v rovnakom stave ako po
 * inicializácii. Funkcia korektne uvoľní všetky alokované zdroje rušených
 * uzlov.
 *
 * Funkciu implementujte iteratívne pomocou zásobníku uzlov a bez použitia
 * vlastných pomocných funkcií.
 */
void bst_dispose(bst_node_t **tree) {
  //při prázdném stromu nemusíme nic dělat
  if((*tree) == NULL){
    return;
  }
  //inicializace zásobníku
  stack_bst_t stack;
  stack_bst_init(&stack);
  bst_node_t *tmp = (*tree);
  //procházíme strom
  do
  {
    //při prázdném tmp do něj vložíme pravou větev ze zásobníku 
    if (tmp == NULL && !stack_bst_empty(&stack))
    {
      tmp = stack_bst_top(&stack);
      stack_bst_pop(&stack);
    }
    else{
      //jinak vloříme pravou větev do zásobníku
      if (tmp->right != NULL)
      {
        stack_bst_push(&stack, tmp->right);
      }
      //a smažeme levou větev
      bst_node_t *tmpDel = tmp;
      tmp = tmp->left;
      free(tmpDel);
      tmpDel = NULL;
    }
    
  } while (tmp != NULL || !stack_bst_empty(&stack));
  //nastavíme kořen na NULL
  (*tree) = NULL;
}

/*
 * Pomocná funkcia pre iteratívny preorder.
 *
 * Prechádza po ľavej vetve k najľavejšiemu uzlu podstromu.
 * Nad spracovanými uzlami zavola bst_print_node a uloží ich do zásobníku uzlov.
 *
 * Funkciu implementujte iteratívne pomocou zásobníku uzlov a bez použitia
 * vlastných pomocných funkcií.
 */
void bst_leftmost_preorder(bst_node_t *tree, stack_bst_t *to_visit) {
  //procházíme strom po levých větvích a dáváme je do zásobníku
  while (tree != NULL){
    stack_bst_push(to_visit, tree);
    bst_print_node(tree);
    tree = tree->left;
  }
  
}

/*
 * Preorder prechod stromom.
 *
 * Pre aktuálne spracovávaný uzol nad ním zavolajte funkciu bst_print_node.
 *
 * Funkciu implementujte iteratívne pomocou funkcie bst_leftmost_preorder a
 * zásobníku uzlov bez použitia vlastných pomocných funkcií.
 */
void bst_preorder(bst_node_t *tree) {
  //inicializace zásobníku
  stack_bst_t stack;
  stack_bst_init(&stack);
  bst_leftmost_preorder(tree, &stack);
  //zpracováváme zásobník
  while(!stack_bst_empty(&stack)){
    tree = stack_bst_top(&stack);
    stack_bst_pop(&stack);
    //zpracujeme zbytek (i pravé) větví
    bst_leftmost_preorder(tree->right, &stack);
  }
}

/*
 * Pomocná funkcia pre iteratívny inorder.
 *
 * Prechádza po ľavej vetve k najľavejšiemu uzlu podstromu a ukladá uzly do
 * zásobníku uzlov.
 *
 * Funkciu implementujte iteratívne pomocou zásobníku uzlov a bez použitia
 * vlastných pomocných funkcií.
 */
void bst_leftmost_inorder(bst_node_t *tree, stack_bst_t *to_visit) {
  //procházíme strom po levých větvích a dáváme je do zásobníku
  while (tree != NULL){
    stack_bst_push(to_visit, tree);
    tree = tree->left;
  }
}

/*
 * Inorder prechod stromom.
 *
 * Pre aktuálne spracovávaný uzol nad ním zavolajte funkciu bst_print_node.
 *
 * Funkciu implementujte iteratívne pomocou funkcie bst_leftmost_inorder a
 * zásobníku uzlov bez použitia vlastných pomocných funkcií.
 */
void bst_inorder(bst_node_t *tree) {
  //inicializace zásobníku
  stack_bst_t stack;
  stack_bst_init(&stack);
  bst_leftmost_inorder(tree, &stack);
  //procházíme zásobník
  while(!stack_bst_empty(&stack)){
    tree = stack_bst_top(&stack);
    stack_bst_pop(&stack);
    //zpracujeme aktuální uzel a jdeme dále přes pravý
    bst_print_node(tree);
    bst_leftmost_inorder(tree->right, &stack);
  }
}

/*
 * Pomocná funkcia pre iteratívny postorder.
 *
 * Prechádza po ľavej vetve k najľavejšiemu uzlu podstromu a ukladá uzly do
 * zásobníku uzlov. Do zásobníku bool hodnôt ukladá informáciu že uzol
 * bol navštívený prvý krát.
 *
 * Funkciu implementujte iteratívne pomocou zásobníkov uzlov a bool hodnôt a bez použitia
 * vlastných pomocných funkcií.
 */
void bst_leftmost_postorder(bst_node_t *tree, stack_bst_t *to_visit, stack_bool_t *first_visit) {
  //procházíme strom po levých větvích a dáváme je do zásobníku
  //také si pamatujeme které jsme již navštívili
  while(tree != NULL){
    stack_bst_push(to_visit, tree);
    stack_bool_push(first_visit, true);
    tree = tree->left;
  }
}

/*
 * Postorder prechod stromom.
 *
 * Pre aktuálne spracovávaný uzol nad ním zavolajte funkciu bst_print_node.
 *
 * Funkciu implementujte iteratívne pomocou funkcie bst_leftmost_postorder a
 * zásobníkov uzlov a bool hodnôt bez použitia vlastných pomocných funkcií.
 */
void bst_postorder(bst_node_t *tree) {
  //inicializace zásobníků
  bool Left = false;
  stack_bst_t stack;
  stack_bst_init(&stack);
  stack_bool_t stack_b;
  stack_bool_init(&stack_b);
  bst_leftmost_postorder(tree, &stack, &stack_b);
  //procházíme zásobník
  while (!stack_bst_empty(&stack))
  {
    tree = stack_bst_top(&stack);
    Left = stack_bool_top(&stack_b);
    stack_bool_pop(&stack_b);
    //pokud jsme už na uzlu byli jdeme doprava
    if(Left){
      stack_bool_push(&stack_b, false);
      bst_leftmost_postorder(tree->right, &stack, &stack_b);
    }
    //jinak zpracujeme uzel
    else{
      stack_bst_pop(&stack);
      bst_print_node(tree);
    }
  }
}
