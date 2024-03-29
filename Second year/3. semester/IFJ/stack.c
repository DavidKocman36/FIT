/**
 *  Soubor: stack.c
 * 
 *  Předmět: IFJ - Implementace překladače imperativního jazyka IFJ21
 *  Poslední změna:	8. 12. 2021
 *  Autoři: David Kocman  - xkocma08, VUT FIT
 *          Radomír Bábek - xbabek02, VUT FIT
 *          Martin Ohnút  - xohnut01, VUT FIT
 *          Přemek Janda  - xjanda28, VUT FIT
 *  Popis: Obsahuje implementaci zásobníku
 */

#include <stdio.h>
#include <stdlib.h>
#include "stack.h"
#include "string.h"

//inits new stack item
int item_init(t_item *item)
{
    item->capacity = 10;
    item->length = 0;
    item->data = malloc(item->capacity);
    if(item->data == NULL)
        return 99;
    return 0;
}

//adds char to the stack item
int item_addchar(t_item *item, char c)
{
    item->data[item->length] = c;
    item->length++;
    if(item->length == item->capacity)
    {
        item->capacity = item->capacity * 2;
        char *newdata = realloc(item->data, sizeof(char) * item->capacity);
        if(newdata == NULL)
            return 99;
        item->data = newdata;
    }
    return 0;
}

//copies data to the stack item
int item_copy(t_item *item1, char *data)
{
    for(size_t i = 0; i < strlen(data); i++)
    {
        item_addchar(item1, data[i]);
    }
    item_addchar(item1, '\0');
    if(item1->data==NULL)
        return 1;
    return 0;
}

//deletes item
void item_delete(t_item *item)
{
    free(item->data);
}

//prints item
void print_item(t_item item)
{
    for(int i = 0; i < item.length; i++)
        printf("%c", item.data[i]);
}

//inits stack
int stack_init(t_stack *stack)
{
    stack->capacity = 10;
    stack->top = -1;
    stack->data = malloc(sizeof(t_item) * stack->capacity);
    if(stack->data == NULL)
        return 99;
    return 0;
}

//pushes value into the stack
int stack_push(t_stack *stack, char *data)
{
    stack->top++;
    t_item new_item;
    item_init(&new_item);
    item_copy(&new_item, data);
    stack->data[stack->top] = new_item;
    if(stack->top + 1 == stack->capacity)
    {
        stack->capacity = stack->capacity * 2;
        t_item *newdata = realloc(stack->data, sizeof(t_item) * stack->capacity);
        if(newdata == NULL)
            return 99;
        stack->data = newdata;
    }
    return 0;
}

//pops top item from the stack
void stack_pop(t_stack *stack)
{
    if(stack->top != -1)
    {
        item_delete(&stack->data[stack->top]);
        stack->top--;
    }
}

//returns top item in the stack
char *stack_top(t_stack stack)
{
    return stack.data[stack.top].data;
}

//deletes the stack
void stack_delete(t_stack *stack)
{
    for(int i = 0; i <= stack->top; i++)
        item_delete(&stack->data[i]);
    free(stack->data);
}

//prints the stack
void stack_print(t_stack stack)
{
    for(int i = 0; i <= stack.top; i++)
    {
        print_item(stack.data[i]);
        printf("\n");
    }
}

//returns index of the top stack item
int stack_topindex(t_stack stack)
{
    return stack.top;
}