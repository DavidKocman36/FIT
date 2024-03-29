/**
 *  Soubor: tree.h
 * 
 *  Předmět: IFJ - Implementace překladače imperativního jazyka IFJ21
 *  Poslední změna:	8. 12. 2021
 *  Autoři: David Kocman  - xkocma08, VUT FIT
 *          Radomír Bábek - xbabek02, VUT FIT
 *          Martin Ohnút  - xohnut01, VUT FIT
 *          Přemek Janda  - xjanda28, VUT FIT
 *  Popis: Hlavičkový soubor pro tree.c
 */

#ifndef __TREE_H__
#define __TREE_H__

#include <stddef.h>

typedef struct tree_item
{
    int length;
    char *data;
} t_tree_item;

typedef struct node
{
    int next_capacity;
    int next_count;
    int line;
    struct node **next;
    struct node *prev;
    t_tree_item *data;
} t_node;

typedef struct tree
{
    t_node *first;
    t_node *active;
} t_tree;

#define ALLOC_CHECK(__ptr) \
    if (__ptr == NULL) \
        return 1; 

int node_init(t_node *node);

int node_addnext(t_node *node, t_node *next);

int node_setdata(t_node *node, char *data, int index);

void node_delete(t_node *node);

void tree_delete(t_node *node);

void tree_print(t_node node, size_t tabs);

void node_print(t_node node, size_t tabs);

void tree_item_init(t_tree_item *tree_item);

void tree_item_delete(t_tree_item *tree_item);

#endif