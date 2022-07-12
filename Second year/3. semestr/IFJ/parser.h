/**
 *  Soubor: parser.h
 * 
 *  Předmět: IFJ - Implementace překladače imperativního jazyka IFJ21
 *  Poslední změna:	8. 12. 2021
 *  Autoři: David Kocman  - xkocma08, VUT FIT
 *          Radomír Bábek - xbabek02, VUT FIT
 *          Martin Ohnút  - xohnut01, VUT FIT
 *          Přemek Janda  - xjanda28, VUT FIT
 *  Popis: Hlavičkový soubor pro parser.h
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include "stack.h"
#include "table.h"
#include "tree.h"
#include "lexikon.h"

char *token_get(int i);

int find_rule(t_table table, t_stack stack, char *token);

int is_terminal(char *string);

int bottom_up(t_table precedence_table);

tToken *token_init();

int syntax_analyzer(t_node *tree);