/*
 * Binárny vyhľadávací strom — rekurzívna varianta
 *
 * S využitím dátových typov zo súboru btree.h a pripravených kostier funkcií
 * implementujte binárny vyhľadávací strom pomocou rekurzie.
 */

#include "../btree.h"
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
  //nastavení kořenu na NULL
  (*tree) = NULL;

}

/*
 * Nájdenie uzlu v strome.
 *
 * V prípade úspechu vráti funkcia hodnotu true a do premennej value zapíše
 * hodnotu daného uzlu. V opačnom prípade funckia vráti hodnotu false a premenná
 * value ostáva nezmenená.
 *
 * Funkciu implementujte rekurzívne bez použitia vlastných pomocných funkcií.
 */
bool bst_search(bst_node_t *tree, char key, int *value) {
  //při prázdném stromu vracíme false
  if (tree == NULL)
  {
    return false;
  }
  else{
    //našli jsme klíč
    if (tree->key == key){
      (*value) = tree->value;
      return true;
    }
    //pokud zatím ne a nenarazili jsme na konec konec, jdeme doleva
    else if(key < tree->key){
      if (tree->left != NULL){
        bst_search(tree->left, key, value);
      }
      else{
        return false;
      }
    }
    //nebo doprava
    else{
      if (tree->right != NULL){
        bst_search(tree->right, key, value);
      }
      else{
        return false;
      }
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
 * Funkciu implementujte rekurzívne bez použitia vlastných pomocných funkcií.
 */
void bst_insert(bst_node_t **tree, char key, int value) {
  //při prázdném stromě vložíme kořen
  if((*tree) == NULL){
    bst_node_t *newNode = malloc(sizeof(struct bst_node));
    if(newNode == NULL){
      return;
    }
    newNode->key = key;
    newNode->value = value;
    newNode->left = NULL;
    newNode->right = NULL;
    (*tree) = newNode;
  }
  else{
    //pokud se ve stromě nachází již tento uzel,přepíšeme jeho hodnotu
    if((*tree)->key == key){
      (*tree)->value = value;
      return;
    }
    else{
      //jinak se posuneme po větvích
      if(key < (*tree)->key)
      {
        if ((*tree)->left!=NULL){
          bst_insert(&(*tree)->left, key, value);
        }
        else{
          //pokud levá větev není, vložíme nový prvek
          bst_node_t *newNodeLeft = malloc(sizeof(struct bst_node));
          if(newNodeLeft == NULL){
            return;
          }
          newNodeLeft->key = key;
          newNodeLeft->value = value;
          newNodeLeft->left = NULL;
          newNodeLeft->right = NULL;
          (*tree)->left = newNodeLeft;
          return;
        }
      }
      else{
        if ((*tree)->right!=NULL){
          bst_insert(&(*tree)->right, key, value);
        }
        else{
          //pokud pravá větev není, vložíme nový prvek
          bst_node_t *newNodeRight = malloc(sizeof(struct bst_node));
          if(newNodeRight == NULL){
            return;
          }
          newNodeRight->key = key;
          newNodeRight->value = value;
          newNodeRight->left = NULL;
          newNodeRight->right = NULL;
          (*tree)->right = newNodeRight;
          return;
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
 * Funkciu implementujte rekurzívne bez použitia vlastných pomocných funkcií.
 */
void bst_replace_by_rightmost(bst_node_t *target, bst_node_t **tree) {
  //dokud nenarazíme na konec pravých větví, posunujeme se
  if ((*tree)->right==NULL)
  {
    //při nalezení nahradíme target uzel konečným uzlem
    target->key = (*tree)->key;
    target->value = (*tree)->value;
    //a odstraníme ho
    if ((*tree)->left != NULL)
    {
      //pokud má ještě levou větev, napojíme ji na předchozí prvek
      bst_node_t *auxNodePrev = (*tree)->left;
      free((*tree));
      (*tree) = auxNodePrev;
      return;
    }
    else{
      free((*tree));
      (*tree) = NULL;
      return;
    }
  }
  else{
    bst_replace_by_rightmost(target, &(*tree)->right);
  }
  
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
 * Funkciu implementujte rekurzívne pomocou bst_replace_by_rightmost a bez
 * použitia vlastných pomocných funkcií.
 */
void bst_delete(bst_node_t **tree, char key) {
  //při prázdném stromě neděláme nic
  if((*tree) == NULL){
    return;
  }
  else{
    //pokud našli
    if ((*tree)->key == key){
      //při listovému uzlu stačí uvolnit zdroje
      if ((*tree)->left == NULL && (*tree)->right == NULL){
        free((*tree));
        (*tree) = NULL;
        return;
      }
      //při prázdné levé větvi uvolníme uzel a napojíme následující na předchozí pravý
      else if((*tree)->left == NULL){
        bst_node_t *auxNodePrev = (*tree)->right;
        free((*tree));
        (*tree) = auxNodePrev;
        return;
      }
      //při prázdné pravé větvi uvolníme uzel a napojíme následující na předchozí levý
      else if((*tree)->right == NULL){
        bst_node_t *auxNodePrev = (*tree)->left;
        free((*tree));
        (*tree) = auxNodePrev;
        return;
      }
      else{
        //pokud je uvolňovaný uzel kořenem s potomky, nahradíme ho nejpravějším uzlem levého dítěte
        bst_replace_by_rightmost((*tree), &(*tree)->left);
        return;
      }
    }
    //pokud nenašli hledáme: pokud key mensi jdeme doleva, vetsi doprava
    else{
      if(key < (*tree)->key){
        if ((*tree)->left != NULL){
          bst_delete(&(*tree)->left, key);
        }
        else{
          return;
        }

      }
      else{
        if ((*tree)->right != NULL){
          bst_delete(&(*tree)->right, key);
        }
        else{
          return;
        }
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
 * Funkciu implementujte rekurzívne bez použitia vlastných pomocných funkcií.
 */
void bst_dispose(bst_node_t **tree) {
  if((*tree) != NULL){
    //uložíme si do proměnných oba potomky a pak s nimi zavoláme rekurzivně tuhle funkci
    bst_node_t *auxNodeL = (*tree)->left;
    bst_node_t *auxNodeR = (*tree)->right;
    bst_dispose(&auxNodeR);
    bst_dispose(&auxNodeL);
    //a uvolníme postupně všechny uzly
    free((*tree));
  }
  //nakonec nastavíme kořen na NULL
  (*tree) = NULL;
}

/*
 * Preorder prechod stromom.
 *
 * Pre aktuálne spracovávaný uzol nad ním zavolajte funkciu bst_print_node.
 *
 * Funkciu implementujte rekurzívne bez použitia vlastných pomocných funkcií.
 */
void bst_preorder(bst_node_t *tree) {
  if(tree!=NULL){
    bst_node_t *auxNodeL = tree->left;;
    bst_node_t *auxNodeR = tree->right;
    //procházíme strom v pořadí: kořen->levý->pravý
    bst_print_node(tree);
    bst_preorder(auxNodeL);
    bst_preorder(auxNodeR);
  }
}

/*
 * Inorder prechod stromom.
 *
 * Pre aktuálne spracovávaný uzol nad ním zavolajte funkciu bst_print_node.
 *
 * Funkciu implementujte rekurzívne bez použitia vlastných pomocných funkcií.
 */
void bst_inorder(bst_node_t *tree) {
  if(tree!=NULL){
    bst_node_t *auxNodeL = tree->left;
    bst_node_t *auxNodeR = tree->right;
    //procházíme strom v pořadí: levý->kořen->pravý
    bst_inorder(auxNodeL);
    bst_print_node(tree);
    bst_inorder(auxNodeR);
  }
}
/*
 * Postorder prechod stromom.
 *
 * Pre aktuálne spracovávaný uzol nad ním zavolajte funkciu bst_print_node.
 *
 * Funkciu implementujte rekurzívne bez použitia vlastných pomocných funkcií.
 */
void bst_postorder(bst_node_t *tree) {
  if(tree!=NULL){
    bst_node_t *auxNodeL = tree->left; 
    bst_node_t *auxNodeR = tree->right;
    //procházíme strom v pořadí: levý->pravý->kořen
    bst_postorder(auxNodeL);
    bst_postorder(auxNodeR);
    bst_print_node(tree);
  }
}
