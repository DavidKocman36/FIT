//======== Copyright (c) 2021, FIT VUT Brno, All rights reserved. ============//
//
// Purpose:     White Box - Tests suite
//
// $NoKeywords: $ivs_project_1 $white_box_code.cpp
// $Author:     David Kocman <xkocma08@stud.fit.vutbr.cz>
// $Date:       $2021-01-04
//============================================================================//
/**
 * @file white_box_tests.cpp
 * @author David Kocman
 * 
 * @brief Implementace testu prace s maticemi.
 */

#include "gtest/gtest.h"
#include "white_box_code.h"

TEST(MatrixInitialization, EmptyMatrix)
{
    Matrix m{};
    EXPECT_EQ(m.get(0,0), 0);
    Matrix m1{2,2};
    EXPECT_EQ(m1.get(1,1), 0);
    Matrix m2{3,3};
    EXPECT_EQ(m2.get(2,2), 0);
    Matrix m3{4,4};
    EXPECT_EQ(m3.get(3,3), 0);
    EXPECT_ANY_THROW(new Matrix (0,0));
}

TEST(MatrixSet, EmptyMatrix)
{
    Matrix m{3,3};
    m.set(1,1, 5);
    EXPECT_EQ(m.get(1,1), 5);
    EXPECT_FALSE(m.set(5,5, 1));
    EXPECT_ANY_THROW(m.get(6,6));
    std::vector<std::vector< double > > values = {{10,85,15}, {70, 20, 60}, {30, 50, 65}};
    m.set(values);
    EXPECT_EQ(m.get(0,0), 10);
    EXPECT_EQ(m.get(1,1), 20);
    EXPECT_EQ(m.get(2,2), 65);
    EXPECT_EQ(m.get(1,2), 60);
    Matrix m1{4,4};
    EXPECT_FALSE(m1.set(values));
    Matrix m2{4,2};
    EXPECT_FALSE(m2.set(values));
    
}

TEST(MatrixOperations, AddMultiply)
{
    Matrix m4{3,3};
    Matrix m5{3,3};
    Matrix m6{4,3};
    Matrix m7{3,3};

    EXPECT_TRUE(m4 == m5);
    m5.set(2,2, 3);
    EXPECT_FALSE(m4 == m5);
    EXPECT_ANY_THROW(m4 == m6);

    m7 = m4 + m5;
    EXPECT_EQ(m7.get(0,0), 0);
    EXPECT_EQ(m7.get(2,2), 3);
    EXPECT_ANY_THROW(m7 = m4 + m6);

    m7 = m4 * m5;
    EXPECT_EQ(m7.get(0,0), 0);
    EXPECT_EQ(m7.get(2,2), 0);

    m7 = m5 * 2;
    EXPECT_EQ(m7.get(0,0), 0);
    EXPECT_EQ(m7.get(2,2), 6);
    EXPECT_ANY_THROW(m7 = m4 * m6);
}

TEST(MatrixOperations, Transpose)
{
    Matrix m2{2,2};
    Matrix m3{3,3};
    std::vector<std::vector< double > > values3x3 = {{1,1,3}, {1, 2, 3}, {2, 1, 3}};
    std::vector<std::vector< double > > values2x2 = {{1,4}, {2,5}};
    //chosen values are the ones most likely to be transposed
    m2.set(values2x2);
    m2 = m2.transpose();
    EXPECT_EQ(m2.get(0,1), 2);
    EXPECT_EQ(m2.get(1,0), 4);

    m3.set(values3x3);
    m3 =  m3.transpose();
    EXPECT_EQ(m3.get(0,0), 1);
    EXPECT_EQ(m3.get(2,1), 3);
    EXPECT_EQ(m3.get(1,2), 1);
}

TEST(MatrixOperations, Inverse)
{
    Matrix m1{};
    Matrix m2{2,2};
    Matrix m3{3,3};
    std::vector<std::vector< double > > valuesSingular = {{1,0,1}, {0, 1, 0}, {1, 0, 1}};
    std::vector<std::vector< double > > values3x3 = {{1,1,3}, {1, 2, 3}, {2, 1, 3}};
    std::vector<std::vector< double > > values2x2 = {{2,8}, {2,4}};
    m3.set(valuesSingular);
    EXPECT_ANY_THROW(m3 = m3.inverse());
    EXPECT_ANY_THROW(m1 = m1.inverse());
    m2.set(values2x2);
    m2 = m2.inverse();
    
    EXPECT_EQ(m2.get(0,0), -0.5);
    EXPECT_EQ(m2.get(1,0), 0.25);

    m3.set(values3x3);
    m3 = m3.inverse();
    EXPECT_EQ(m3.get(0,0), -1);
    EXPECT_EQ(m3.get(1,1), 1);
    EXPECT_EQ(m3.get(2,0), 1);
}

TEST(MatrixOperations, Equations)
{
    Matrix m{1,3};
    Matrix m1{};
    Matrix m2{2,2};
    Matrix m3{3,3};
    Matrix m4{4,4};
    std::vector<std::vector< double > > values3x3 = {{1,1,3}, {1, 2, 3}, {2, 1, 3}};
    std::vector<std::vector< double > > values2x2 = {{1,1}, {1,2}};
    std::vector<std::vector< double > > values4x4 = {{1,1,1,2}, {1, 2, 2,1}, {1, 3, 2,4}, {1,2,1,3}};
    std::vector<std::vector< double > > valuesSingular = {{1,0,1}, {0, 1, 0}, {1, 0, 1}};
    std::vector<double> a = {1};
    std::vector<double> b = {2,2};
    std::vector<double> c = {2,3,3};
    std::vector<double> d = {2,3,4,5};
    std::vector<double> res;

    m1.set(0,0,1);
    m2.set(values2x2);
    m3.set(values3x3);
    m4.set(values4x4);

    res = m3.solveEquation(c);
    EXPECT_EQ(res.at(0), 1);
    EXPECT_EQ(res.at(1), 1);
    EXPECT_EQ(res.at(2), 0);

    EXPECT_ANY_THROW(res = m.solveEquation(d));
    EXPECT_ANY_THROW(res = m.solveEquation(c));

    m3.set(valuesSingular);
    EXPECT_ANY_THROW(res = m3.solveEquation(c));

    res = m1.solveEquation(a);
    EXPECT_EQ(res.at(0), 1);

    res = m2.solveEquation(b);
    EXPECT_EQ(res.at(0),2);
    EXPECT_EQ(res.at(1),0);
    
    
    res = m4.solveEquation(d);
    EXPECT_EQ(res.at(0),4);
    EXPECT_EQ(res.at(1),4);
    EXPECT_EQ(res.at(2),-4);
    EXPECT_EQ(res.at(3),-1);
}

//============================================================================//
// ** ZDE DOPLNTE TESTY **
//
// Zde doplnte testy operaci nad maticemi. Cilem testovani je:
// 1. Dosahnout maximalniho pokryti kodu (white_box_code.cpp) testy.
// 2. Overit spravne chovani operaci nad maticemi v zavislosti na rozmerech 
//    matic.
//============================================================================//

/*** Konec souboru white_box_tests.cpp ***/
