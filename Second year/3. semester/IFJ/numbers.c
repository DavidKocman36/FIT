#include <stdio.h>
#include <stdlib.h>

int main(int argc, char**argv){
    float f = atof(argv[1]);
    printf("Number %s = %a\n", argv[1], f);
}