; Vernamova sifra na architekture DLX
; David Kocman xkocma08
;xkocma08-r5-r7-r15-r18-r21-r0

        .data 0x04          ; zacatek data segmentu v pameti
login:  .asciiz "xkocma08"  ; <-- nahradte vasim loginem
cipher: .space 9 ; sem ukladejte sifrovane znaky (za posledni nezapomente dat 0)

        .align 2            ; dale zarovnavej na ctverice (2^2) bajtu
laddr:  .word login         ; 4B adresa vstupniho textu (pro vypis)
caddr:  .word cipher        ; 4B adresa sifrovaneho retezce (pro vypis)

        .text 0x40          ; adresa zacatku programu v pameti
        .global main        ; 

main:   ; sem doplnte reseni Vernamovy sifry dle specifikace v zadani
	addi r18, r18, 57    ;Nastaveni registru na kontroly: r18 na kontrolu cisla
	addi r15, r15, 122   ;r15 na kontrolu "preteceni" a "podteceni" v abecede
	addi r21, r21, 1     ;r21 na index. 1 lichy, 0 sudy
   loop:
	lb r5, login(r7)     ;nacteni znaku
	sgt r18,r5,r18       ;kontrola cisla
	bnez r18, loop1
	nop
	j endLoop
	nop
   loop1:
	addi r18, r18, 57
	bnez r21, odd
	nop
	
	;pokud je pozice sudá
	subi r5, r5, 15       ;pricteni znaku "o"
	addi r21, r21, 1

	subi r15, r15, 25
	sgt r15, r15, r5      ;kontrola podteceni, pokud je znak < 97
	bnez r15, under
	nop
	addi r15, r15, 122
	j store
	nop

   odd:
	;pokud je pozice lichá
	addi r5, r5, 11       ;pricteni zanku "k"

	subi r21, r21, 1      ;dalsi znak je sudy
	sgt r15, r5, r15      ;kontrola podteceni pokud je znak > 122
	bnez r15, over
	nop
	addi r15, r15, 122
	j store
	nop
   under:
	addi r15, r15, 121    ;dorovnani registru r15 a "vraceni" zpet do abecedy
	addi r5, r5, 26
	j store
	nop
   over:
	addi r15, r15, 121    ;dorovnani registru r15 a "vraceni" zpet do abecedy
	subi r5, r5, 26
   store:	
	sb cipher(r7), r5     ;ulozeni znaku na prislusnou pozici
	addi r7, r7, 1        ;zvyseni indexu
	j loop
	nop
   endLoop:
	addi r7, r7, 1        ;vlozeni 0 na konec
	sb cipher(r7), r0

end:    addi r14, r0, caddr ; <-- pro vypis sifry nahradte laddr adresou caddr
        trap 5  ; vypis textoveho retezce (jeho adresa se ocekava v r14)
        trap 0  ; ukonceni simulace
