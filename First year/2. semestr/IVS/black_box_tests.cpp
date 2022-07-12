//======== Copyright (c) 2017, FIT VUT Brno, All rights reserved. ============//
//
// Purpose:     Red-Black Tree - public interface tests
//
// $NoKeywords: $ivs_project_1 $black_box_tests.cpp
// $Author:     David Kocman <xkocma08@stud.fit.vutbr.cz>
// $Date:       $2017-01-04
//============================================================================//
/**
 * @file black_box_tests.cpp
 * @author David Kocman
 * 
 * @brief Implementace testu binarniho stromu.
 */

#include <vector>

#include "gtest/gtest.h"

#include "red_black_tree.h"

//konstanty pro testování empty stromů (náhodně zvolená čísla), např:
const int a = 2;
const int b = 6;

TEST(EmptyTree, InsertNode)
{
    BinaryTree tree;
    
    std::pair<bool, Node_t *> res = tree.InsertNode(a);
    EXPECT_TRUE(res.first);
    EXPECT_EQ(res.second->key, a);
    EXPECT_NE(res.second->key, b);
    Node_t *root = tree.GetRoot();
    EXPECT_EQ(root->key, a);
    std::pair<bool, Node_t *> res1 = tree.InsertNode(a);
    EXPECT_FALSE(res1.first);
    EXPECT_EQ(res1.second->key, res.second->key);
    EXPECT_NE(res1.second->key, b);
}

TEST(EmptyTree, FindNode)
{
    BinaryTree tree;
    Node_t *foundN = tree.FindNode(a);
    EXPECT_EQ(foundN, nullptr);
}

TEST(EmptyTree, DeleteNode)
{
    BinaryTree tree;
    EXPECT_FALSE(tree.DeleteNode(a));
}


TEST(NonEmptyTree, InsertNode)
{
    BinaryTree tree;
    int values[] = {10,20,30,40};
    std::pair<bool, Node_t *> res;
    std::pair<bool, Node_t *> res1;
    std::pair<bool, Node_t *> res2;
    for(auto value : values)
    {
        res = tree.InsertNode(value);
        EXPECT_TRUE(res.first);
        EXPECT_EQ(res.second->key, value);
    }
    res1 = tree.InsertNode(50);
    EXPECT_TRUE(res1.first);
    EXPECT_EQ(res1.second->key, 50);

    res2 = tree.InsertNode(20);
    EXPECT_FALSE(res2.first);
    EXPECT_EQ(res2.second->key, 20);
}

TEST(NonEmptyTree, FindNode)
{
    BinaryTree tree;
    int values[] = {10,20,30,40};
    for(auto value : values)
    {
        tree.InsertNode(value);
    }
    Node_t *foundN = tree.FindNode(20);
    EXPECT_EQ(foundN->key, 20);
    EXPECT_NE(foundN, nullptr);
}

TEST(NonEmptyTree, DeleteNode)
{
    BinaryTree tree;
    int values[] = {10,20,30,40};
    for(auto value : values)
    {
        tree.InsertNode(value);
    }
    EXPECT_TRUE(tree.DeleteNode(20));
    EXPECT_FALSE(tree.DeleteNode(b));
}

TEST(NonEmptyTree, Axiom1)
{
    BinaryTree tree;
    std::vector<Node_t *> array {};
    int values[] = {10,20,30,40};
    for(auto value : values)
    {
        tree.InsertNode(value);
    }
    tree.GetLeafNodes(array);
    for(auto node : array)
    {
        EXPECT_EQ(node->color, 1);
        EXPECT_NE(node->color, 0);
    }
}

TEST(NonEmptyTree, Axiom2)
{
    BinaryTree tree;
    std::vector<Node_t *> array {};
    int values[] = {10,20,30};
    for(auto value : values)
    {
        tree.InsertNode(value);
    }
    tree.GetAllNodes(array);
    for(auto node : array)
    {
        if(node->color == 0)
        {
            EXPECT_EQ(node->pLeft->color, 1);
            EXPECT_EQ(node->pRight->color, 1);
        }
    }
}
//============================================================================//
// ** ZDE DOPLNTE TESTY **
//
// Zde doplnte testy Red-Black Tree, testujte nasledujici:
// 1. Verejne rozhrani stromu
//    - InsertNode/DeleteNode a FindNode
//    - Chovani techto metod testuje pro prazdny i neprazdny strom.
// 2. Axiomy (tedy vzdy platne vlastnosti) Red-Black Tree:
//    - Vsechny listove uzly stromu jsou *VZDY* cerne.
//    - Kazdy cerveny uzel muze mit *POUZE* cerne potomky.
//    - Vsechny cesty od kazdeho listoveho uzlu ke koreni stromu obsahuji
//      *STEJNY* pocet cernych uzlu.
//============================================================================//

/*** Konec souboru black_box_tests.cpp ***/
