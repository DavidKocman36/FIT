//======== Copyright (c) 2021, FIT VUT Brno, All rights reserved. ============//
//
// Purpose:     Test Driven Development - priority queue code
//
// $NoKeywords: $ivs_project_1 $tdd_code.cpp
// $Author:     David Kocman <xkocma08@stud.fit.vutbr.cz>
// $Date:       $2021-01-04
//============================================================================//
/**
 * @file tdd_code.cpp
 * @author David Kocman
 * 
 * @brief Implementace metod tridy prioritni fronty.
 */

#include <stdlib.h>
#include <stdio.h>

#include "tdd_code.h"

//============================================================================//
// ** ZDE DOPLNTE IMPLEMENTACI **
//
// Zde doplnte implementaci verejneho rozhrani prioritni fronty (Priority Queue)
// 1. Verejne rozhrani fronty specifikovane v: tdd_code.h (sekce "public:")
//    - Konstruktor (PriorityQueue()), Destruktor (~PriorityQueue())
//    - Metody Insert/Remove/Find a GetHead
//    - Pripadne vase metody definovane v tdd_code.h (sekce "protected:")
//
// Cilem je dosahnout plne funkcni implementace prioritni fronty implementovane
// pomoci tzv. "double-linked list", ktera bude splnovat dodane testy 
// (tdd_tests.cpp).
//============================================================================//

PriorityQueue::PriorityQueue()
{

}

PriorityQueue::~PriorityQueue()
{
    Element_t *next;
    while (m_pHead != NULL)
    {
        next = m_pHead->pNext;
        delete m_pHead;
        m_pHead = next;
    }
    m_pHead = NULL;
}

void PriorityQueue::Insert(int value)
{
    Element_t *newNode = new Element_t{};
    newNode->value = value;
    newNode->pNext = nullptr;
    Element_t *previous;
    if (m_pHead == nullptr)
    {
        m_pHead = newNode;
        return;
    }
    else
    {
        if (newNode->value >= m_pHead->value)
        {
            Element_t *pom2 = m_pHead;
            m_pHead = newNode;
            newNode->pNext = pom2;
            return;
        }
        else
        {
            for (Element_t *tmp = m_pHead; tmp != nullptr; tmp = tmp->pNext)
            {
                if(newNode->value >= tmp->value && tmp != m_pHead)
                {
                    Element_t *pom1 = previous->pNext;
                    previous->pNext = newNode;
                    newNode->pNext = pom1;
                    break;
                }
                previous = tmp;
            }
            if(newNode->value < previous->value)
            {
                previous->pNext = newNode;
            }
            return;
        }
    }
}
bool PriorityQueue::Remove(int value)
{
    Element_t *prev;
    Element_t *next;
    for (Element_t *tmp = m_pHead ; tmp != NULL; tmp = tmp->pNext)
    {
        next = tmp->pNext;
        if (tmp->value == value)
        {
            if (tmp == m_pHead && m_pHead->value == value)
            {
                if (m_pHead->pNext == nullptr)
                {
                    delete m_pHead;
                    m_pHead = nullptr;
                    return true;
                }
                delete m_pHead;
                m_pHead = next;
                return true;
            }
            else{
                Element_t *tmp2 = tmp->pNext;
                delete tmp;
                prev->pNext = tmp2;
                return true;
            }
        }   
        prev = tmp;
    }
    return false;
}

PriorityQueue::Element_t *PriorityQueue::Find(int value)
{
    for (Element_t *tmp = m_pHead ; tmp != NULL; tmp = tmp->pNext)
    {
        if (tmp->value == value)
        {   
            return tmp;
        }
    }
    return NULL;
}

size_t PriorityQueue::Length()
{
    size_t len = 0;
    for (Element_t *tmp = m_pHead ; tmp != NULL; tmp = tmp->pNext)
    {
        ++len;
    }
	return len;
}

PriorityQueue::Element_t *PriorityQueue::GetHead()
{
    return m_pHead;
}

/*** Konec souboru tdd_code.cpp ***/
