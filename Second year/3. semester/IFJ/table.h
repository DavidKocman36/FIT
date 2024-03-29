/**
 *  Soubor: table.h
 * 
 *  Předmět: IFJ - Implementace překladače imperativního jazyka IFJ21
 *  Poslední změna:	8. 12. 2021
 *  Autoři: David Kocman  - xkocma08, VUT FIT
 *          Radomír Bábek - xbabek02, VUT FIT
 *          Martin Ohnút  - xohnut01, VUT FIT
 *          Přemek Janda  - xjanda28, VUT FIT
 *  Popis: Hlavičkový soubor pro table.h
 */

#ifndef __TABLE_H__
#define __TABLE_H__

typedef struct cell
{
    int capacity;
    int count;
    char *value;
}t_cell;

typedef struct row
{
    int capacity;
    int count;
    t_cell *cells;
}t_row;

typedef struct table
{
    int count;
    int capacity;
    t_row *rows;
}t_table;

void table_init(t_table *table);

int table_readfile(t_table *table, char filename[], char delim);

char *table_find(t_table table,char *nonterminal, char *terminal);

void table_delete(t_table *table);

void table_print(t_table table, char delim);

int table_linelength(t_table table, int line);

char *table_getdata(t_table table, int line, int column);

#endif