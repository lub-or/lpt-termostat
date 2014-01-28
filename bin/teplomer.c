#include <stdio.h>
#include <unistd.h>
#include <sys/io.h>

#define DATA 0x378
#define STATUS DATA+1
#define CONTROL DATA+2

float ZmerTo (unsigned short naportu, unsigned char odkud, int stav)

// funkce vraci teplotu ve st. Celsia
// parametry
// naportu - port na kterem se provadi mereni
// odkud  - bite na ktery je pripojen teplomer
// stav - urcuje zda je vstup negovan

#define DelkaCyklu  0xfffff

{
int t0, t1, t2;
int mask = (1 << odkud);
t1=0;
t2=0;

for (t0 = 0; t0 < DelkaCyklu; t0++)
    // vlastní mereni
    if ((inb (naportu) & mask) == 0) t1++ ;

    // negace mereni v pripade, ze je port negovan
    t2=DelkaCyklu-t1;
    if (stav!=0){
       t1=t2;
       t2=DelkaCyklu-t1;
    };
    // vlastní vypocet teploty
    return ((((double)t2 / ((double)DelkaCyklu)) - 0.32) / 0.0047);
}

int main (void)
{
    if (setuid (0) < 0) {
        printf ("Program musi byt spusten rootem\n");
        return (0);
    }
    if (ioperm (STATUS, 3, 1)) {
        printf ("Program musi byt spusten rootem\n");
        return (0);
    }
    if (ioperm (CONTROL, 3, 1)) {
        printf ("Neni pristup na port\n");
        return (0);
    }
    if (ioperm (DATA, 3, 1)) {
        printf ("Neni pristup na port\n");
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

    // nastaveni data portu 5+6+7+8 na 1 tak aby na nem bylo +5V pro napajeni
    outb (240 + stavRele, DATA);

    // zmereni teploty
    int i;
    double cnt;
    cnt = 1.0;

    // PIN 15
    double t1;
    t1 = 0;
    for (i = 0; i < cnt; i++) {
        t1 += ZmerTo (STATUS, 3, 0);
    }

    // PIN 10
    double t2;
    t2 = 0;
    for (i = 0; i < cnt; i++) {
        t2 += ZmerTo (STATUS, 6, 0);
    }

    // PIN 12
    double t3;
    t3 = 0;
    for (i = 0; i < cnt; i++) {
        t3 += ZmerTo (STATUS, 5, 0);
    }

//    printf ("Teplota je %4.1f \n",  t1/cnt);
//    printf ("Teplota je %4.1f \n",  t2/cnt);
//    printf ("Teplota je %4.1f \n",  t3/cnt);

    // nastaveni control portu na 0 tak aby bylo vypnute napajeni cidla
    outb (stavRele, DATA);

    // zapis dat do state.temp
    char buffer[20];
    //for (i=0; i<20; i++) buffer[i]='';
    nezajem = sprintf (buffer, "%+5.1f|%+5.1f|%+5.1f\n\r", t1/cnt, t2/cnt, t3/cnt);
    file = fopen ("state.temp", "wb");
    fwrite (buffer , sizeof(char), sizeof(buffer), file);
    fclose (file);

    // odregistrovanie pristupu na LPT1
    ioperm (STATUS, 3, 0);
    ioperm (CONTROL, 3, 0);
    ioperm (DATA, 3, 0);

    return (0);
}
