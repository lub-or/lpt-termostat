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

    ioperm (DATA, 3, 1);
    ioperm (STATUS, 3, 1);
    ioperm (CONTROL, 3, 1);

    printf("DATA: %4d\n", inb(DATA));
    printf("STATUS: %4d\n", inb(STATUS));
    printf("CONTROL: %4d\n", inb(CONTROL));

    ioperm (DATA, 3, 0);
    ioperm (STATUS, 3, 0);
    ioperm (CONTROL, 3, 0);

    printf("\n");
    return (0);
}
