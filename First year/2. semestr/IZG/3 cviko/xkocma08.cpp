/**
 * @file        student.cpp
 * @author      Ladislav Mosner, VUT FIT Brno, imosner@fit.vutbr.cz
 * @author      Petr Kleparnik, VUT FIT Brno, ikleparnik@fit.vutbr.cz
 * @author      Kamil Behun, VUT FIT Brno, ibehun@fit.vutbr.cz
 * @date        11.03.2018
 *
 * @brief       Deklarace funkci studentu. DOPLNUJI STUDENTI
 *
 */

#include "base.h"
#include "student.h"
#include "globals.h"
#include <math.h>

/**
 * @brief Vraci barvu pixelu z pozice [x, y]
 * @param[in] x X souradnice pixelu
 * @param[in] y Y souradnice pixelu
 * @return Barva pixelu na pozici [x, y] ve formatu RGBA
 */
RGBA getPixel(int x, int y)
{
    if (x >= width || y >= height || x < 0 || y < 0) {
        IZG_ERROR("Pristup do framebufferu mimo hranice okna\n");
    }
    return framebuffer[y * width + x];
}

/**
 * @brief Nastavi barvu pixelu na pozici [x, y]
 * @param[in] x X souradnice pixelu
 * @param[in] y Y souradnice pixelu
 * @param[in] color Barva pixelu ve formatu RGBA
 */
void putPixel(int x, int y, RGBA color)
{
    if (x >= width || y >= height || x < 0 || y < 0) {
        IZG_ERROR("Pristup do framebufferu mimo hranice okna\n");
    }
    framebuffer[y * width + x] = color;
}

/**
 * @brief Vykresli usecku se souradnicemi [x1, y1] a [x2, y2]
 * @param[in] x1 X souradnice 1. bodu usecky
 * @param[in] y1 Y souradnice 1. bodu usecky
 * @param[in] x2 X souradnice 2. bodu usecky
 * @param[in] y2 Y souradnice 2. bodu usecky
 * @param[in] color Barva pixelu usecky ve formatu RGBA
 * @param[in] arrow Priznak pro vykresleni sipky (orientace hrany)
 */
void drawLine(int x1, int y1, int x2, int y2, RGBA color, bool arrow = false)
{

    if (arrow) {
        // Sipka na konci hrany
        double vx1 = x2 - x1;
        double vy1 = y2 - y1;
        double length = sqrt(vx1 * vx1 + vy1 * vy1);
        double vx1N = vx1 / length;
        double vy1N = vy1 / length;
        double vx1NN = -vy1N;
        double vy1NN = vx1N;
        int w = 3;
        int h = 10;
        int xT = (int) (x2 + w * vx1NN - h * vx1N);
        int yT = (int) (y2 + w * vy1NN - h * vy1N);
        int xB = (int) (x2 - w * vx1NN - h * vx1N);
        int yB = (int) (y2 - w * vy1NN - h * vy1N);
        pinedaTriangle(Point(x2, y2), Point(xT, yT), Point(xB, yB), color, color, false);
    }

    bool steep = abs(y2 - y1) > abs(x2 - x1);

    if (steep) {
        SWAP(x1, y1);
        SWAP(x2, y2);
    }

    if (x1 > x2) {
        SWAP(x1, x2);
        SWAP(y1, y2);
    }

    const int dx = x2 - x1, dy = abs(y2 - y1);
    const int P1 = 2 * dy, P2 = P1 - 2 * dx;
    int P = 2 * dy - dx;
    int y = y1;
    int ystep = 1;
    if (y1 > y2) ystep = -1;

    for (int x = x1; x <= x2; x++) {
        if (steep) {
            if (y >= 0 && y < width && x >= 0 && x < height) {
                putPixel(y, x, color);
            }
        } else {
            if (x >= 0 && x < width && y >= 0 && y < height) {
                putPixel(x, y, color);
            }
        }

        if (P >= 0) {
            P += P2;
            y += ystep;
        } else {
            P += P1;
        }
    }
}

/**
 * @brief Vyplni a vykresli trojuhelnik
 * @param[in] v1 Prvni bod trojuhelniku
 * @param[in] v2 Druhy bod trojuhelniku
 * @param[in] v3 Treti bod trojuhelniku
 * @param[in] color1 Barva vyplne trojuhelniku
 * @param[in] color2 Barva hranice trojuhelniku
 * @param[in] arrow Priznak pro vykresleni sipky (orientace hrany)
 *
 * SPOLECNY UKOL. Doplnuji studenti se cvicicim.
 */
void pinedaTriangle(const Point &v1, const Point &v2, const Point &v3, const RGBA &color1, const RGBA &color2, bool arrow)
{
    // Nalezeni obalky (minX, maxX), (minY, maxY) trojuhleniku.

    //////// DOPLNTE KOD /////////
    int minx = MIN(MIN(v1.x, v2.x), v3.x);
    int miny = MIN(MIN(v1.y, v2.y), v3.y);

    int maxx = MAX(MAX(v1.x, v2.x), v3.x);
    int maxy = MAX(MAX(v1.y, v2.y), v3.y);


    // Oriznuti obalky (minX, maxX, minY, maxY) trojuhleniku podle rozmeru okna.

    //////// DOPLNTE KOD /////////
    minx = MAX(minx, 0);
    miny = MAX(miny, 0);

    maxx = MIN(maxx, width - 1);
    maxy = MIN(maxy, height - 1);


    // Spocitani parametru hranove funkce (deltaX, deltaY) pro kazdou hranu.
    // Hodnoty deltaX, deltaY jsou souradnicemi vektoru, ktery ma pocatek
    // v prvnim vrcholu hrany, konec v druhem vrcholu.
    // Vypocet prvnotni hodnoty hranove funkce.

    //////// DOPLNTE KOD /////////
    int deltax1 = v2.x - v1.x;
    int deltax2 = v3.x - v2.x;
    int deltax3 = v1.x - v3.x;

    int deltay1 = v2.y - v1.y;
    int deltay2 = v3.y - v2.y;
    int deltay3 = v1.y - v3.y;

    int edgef1 = (miny - v1.y) * deltax1 - (minx - v1.x) * deltay1;
    int edgef2 = (miny - v2.y) * deltax2 - (minx - v2.x) * deltay2;
    int edgef3 = (miny - v3.y) * deltax3 - (minx - v3.x) * deltay3;
    // E(x,y) = (y - y0)dx - (x - x0)dy

    // Vyplnovani: Cyklus pres vsechny body (x, y) v obdelniku (minX, minY), (maxX, maxY).
    // Pro aktualizaci hodnot hranove funkce v bode P (x +/- 1, y) nebo P (x, y +/- 1)
    // vyuzijte hodnoty hranove funkce E (x, y) z bodu P (x, y).

    //////// DOPLNTE KOD /////////
    

    for (int y = miny; y < maxy; y++)
    {
        bool even = (y - miny) % 2 == 0;

        int startX = even ? minx : maxx;
        int endX = even ? maxx + 1 : minx - 1;
        int stepX = even ? 1 : -1;

        for (int x = startX; x != endX; x += stepX) {
            if (edgef1 >= 0 && edgef2 >= 0 && edgef3 >= 0)
                putPixel(x, y, color1);
            if (x != endX - stepX) {
                edgef1 += even ? -deltay1 : deltay1;
                edgef2 += even ? -deltay2 : deltay2;
                edgef3 += even ? -deltay3 : deltay3;
            }
        }
        edgef1 += deltax1;
        edgef2 += deltax2;
        edgef3 += deltax3;
    }

    // Prekresleni hranic trojuhelniku barvou color2.
    drawLine(v1.x, v1.y, v2.x, v2.y, color2, arrow);
    drawLine(v2.x, v2.y, v3.x, v3.y, color2, arrow);
    drawLine(v3.x, v3.y, v1.x, v1.y, color2, arrow);
}

/**
 * @brief Vyplni a vykresli polygon
 * @param[in] points Pole bodu polygonu
 * @param[in] size Pocet bodu polygonu (velikost pole "points")
 * @param[in] color1 Barva vyplne polygonu
 * @param[in] color2 Barva hranice polygonu
 *
 * SAMOSTATNY BODOVANY UKOL. Doplnuji pouze studenti.
 */
void pinedaPolygon(const Point *points, const int size, const RGBA &color1, const RGBA &color2)
{
    // Pri praci muzete vyuzit pro vas predpripravene datove typy z base.h., napriklad:
    //
    //      Pro ukladani parametru hranovych funkci muzete vyuzit prichystany vektor parametru hranovych funkci "EdgeParams":
    //
    //          EdgeParams edgeParams(size)                         // Vytvorite vektor (pole) "edgeParams" parametru hranovych funkci o velikosti "size".
    //          edgeParams[i].deltaX, edgeParams[i].deltaY          // Pristup k parametrum (deltaX, deltaY) hranove funkce v poli "edgeParams" na indexu "i".
    //
    //      Pro ukladani hodnot hranovych funkci muzete vyuzit prichystany vektor hodnot hranovych funkci "EdgeFncValues":
    //
    //          EdgeFncValues edgeFncValues(size)                   // Vytvorite vektor (pole) "edgeFncValues" hodnot hranovych funkci o velikosti "size".
    //          edgeFncValues[i]                                    // Pristup k hodnote hranove funkce v poli "edgeFncValues" na indexu "i".
    //

    // Nalezeni obalky (minX, maxX), (minY, maxY) polygonu.

    //////// DOPLNTE KOD /////////
    int minx = points[0].x;
    int maxx = points[0].x;
    int miny = points[0].y;
    int maxy = points[0].y;
    for (int i = 0; i < size; i++){
        minx = MIN(points[i].x, minx);
        maxx = MAX(points[i].x, maxx);
        miny = MIN(points[i].y, miny);
        maxy = MAX(points[i].y, maxy);
    }


    // Oriznuti obalky (minX, maxX), (minY, maxY) polygonu podle rozmeru okna

    //////// DOPLNTE KOD /////////
    minx = MAX(minx, 0);
    maxx = MIN(maxx, width - 1);

    miny = MAX(miny, 0);
    maxy = MIN(maxy, height - 1);

    // Spocitani parametru (deltaX, deltaY) hranove funkce pro kazdou hranu.
	// Hodnoty deltaX, deltaY jsou souradnicemi vektoru, ktery ma pocatek
	// v prvnim vrcholu hrany, konec v druhem vrcholu.
	// Vypocet prvnotnich hodnot hranovych funkci pro jednotlive hrany.

    //////// DOPLNTE KOD /////////
    EdgeParams edgeParams(size);
    for (int i = 0; i < size ; i++){
        edgeParams[i].deltaX = points[(i + 1) % size].x - points[i].x;
        edgeParams[i].deltaY = points[(i + 1) % size].y - points[i].y;
    }
    //int dx1 = v2.x - v1.x;

    EdgeFncValues edgeFncVal(size);
    for (int i = 0; i < size; i++) {
        edgeFncVal[i] = (miny - points[i].y) * edgeParams[i].deltaX - (minx - points[i].x) * edgeParams[i].deltaY;
    }
    //int edgef1 = (miny - v1.y) * dx1 - (minx - v1.x) * dy1;

    // Test konvexnosti polygonu   


    //////// DOPLNTE KOD /////////

    // Vyplnovani: Cyklus pres vsechny body (x, y) v obdelniku (minX, minY), (maxX, maxY).
    // Pro aktualizaci hodnot hranove funkce v bode P (x +/- 1, y) nebo P (x, y +/- 1)
    // vyuzijte hodnoty hranove funkce E (x, y) z bodu P (x, y) */

    //////// DOPLNTE KOD /////////

    
    //int edgef1 = (miny - v1.y) * dx1 - (minx - v1.x) * dy1;

    for (int y = miny; y < maxy; y++){
        bool even = (y - miny) % 2 == 0;

        int startX = even ? minx : maxx;
        int endX = even ? maxx + 1 : minx - 1;
        int stepX = even ? 1 : -1;

        for (int x = startX; x != endX; x += stepX) {
            bool inside = true;
            for (int i = 0; i < size; i++){
                if (edgeFncVal[i] < 0){
                    inside = false;
                    break;
                }
            }

            if (inside)
                putPixel(x, y, color1);

            for (int i = 0; i < size; i++){
                edgeFncVal[i] += even ? -edgeParams[i].deltaY : edgeParams[i].deltaY;
            }
            
        }
        for (int i = 0; i < size; i++){
            edgeFncVal[i] += edgeParams[i].deltaX;
        }
    }

    // Prekresleni hranic polygonu barvou color2.
    for (int i = 0; i < size; i++) {
        drawLine(points[i].x, points[i].y, points[(i + 1) % size].x, points[(i + 1) % size].y, color2/*, true*/);
    }
}
