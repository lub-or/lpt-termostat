#include <stdio.h>
#include <unistd.h>
#include <sys/io.h>

#define DATA 0x378
#define STATUS DATA+1
#define CONTROL DATA+2

int main (void)
{
    if (setuid (0) < 0) {
        printf ("Program musi byt spusten rootem\n");
        return (0);
    }

    if (ioperm (DATA, 3, 1)) {
        printf ("Neni pristup na datovy port\n");
        return (0);
    }

    // nacitat stav rele a zachovat ho
    char str[999];
    FILE *file;
    int nezajem;
    file = fopen( "state.rele" , "r");
    if (file) {
        nezajem = fscanf(file, "%s", str);
        fclose(file);
    }
    int stavRele;
    stavRele = (*str=='1' ? 1 : 0);

    if (stavRele == 1)
	outb(1, DATA);
    else
	outb(0, DATA);

    ioperm (DATA, 3, 0);

    printf("\n");
    return (0);
}
