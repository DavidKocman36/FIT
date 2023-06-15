/**
 * IMP projekt - svìtelná tabule
 * Autor: David Kocman, xkocma08
 */
#include "MK60D10.h"
/* Header file with all the essential definitions for a given type of MCU */

/* Macros for bit-level registers manipulation */
#define GPIO_PIN_MASK	0x1Fu
#define GPIO_PIN(x)		(((1)<<(x & GPIO_PIN_MASK)))

/* Masks for buttons */
#define SW2_MASK		(1 << 10)
#define SW3_MASK		(1 << 12)
#define SW4_MASK		(1 << 27)
#define SW5_MASK		(1 << 26)
#define SW6_MASK		(1 << 11)

/* Constants specifying delay loop duration */
#define tdelay2 		20

/* Configuration of the necessary MCU peripherals */
void SystemConfig() {
	/* Turn on all port clocks */
	SIM->SCGC5 = SIM_SCGC5_PORTA_MASK | SIM_SCGC5_PORTE_MASK;

	/* Set corresponding PTA pins (column activators of 74HC154) for GPIO functionality */
	PORTA->PCR[8] = ( 0|PORT_PCR_MUX(0x01) );  // A0
	PORTA->PCR[10] = ( 0|PORT_PCR_MUX(0x01) ); // A1
	PORTA->PCR[6] = ( 0|PORT_PCR_MUX(0x01) );  // A2
	PORTA->PCR[11] = ( 0|PORT_PCR_MUX(0x01) ); // A3

	/* Set corresponding PTA pins (rows selectors of 74HC154) for GPIO functionality */
	PORTA->PCR[26] = ( 0|PORT_PCR_MUX(0x01) );  // R0
	PORTA->PCR[24] = ( 0|PORT_PCR_MUX(0x01) );  // R1
	PORTA->PCR[9] = ( 0|PORT_PCR_MUX(0x01) );   // R2
	PORTA->PCR[25] = ( 0|PORT_PCR_MUX(0x01) );  // R3
	PORTA->PCR[28] = ( 0|PORT_PCR_MUX(0x01) );  // R4
	PORTA->PCR[7] = ( 0|PORT_PCR_MUX(0x01) );   // R5
	PORTA->PCR[27] = ( 0|PORT_PCR_MUX(0x01) );  // R6
	PORTA->PCR[29] = ( 0|PORT_PCR_MUX(0x01) );  // R7

	/* Set corresponding PTE pins (output enable of 74HC154) for GPIO functionality */
	PORTE->PCR[28] = ( 0|PORT_PCR_MUX(0x01) ); // #EN

	/* Set corresponding PTE button pins for interrupt detection */
	PORTE->PCR[10] = ( PORT_PCR_ISF(0x01) | PORT_PCR_IRQC(0x0A) | PORT_PCR_MUX(0x01)| PORT_PCR_PE(0x01)| PORT_PCR_PS(0x01));
	PORTE->PCR[11] = ( PORT_PCR_ISF(0x01) | PORT_PCR_IRQC(0x0A) | PORT_PCR_MUX(0x01)| PORT_PCR_PE(0x01)| PORT_PCR_PS(0x01));
	PORTE->PCR[12] = ( PORT_PCR_ISF(0x01) | PORT_PCR_IRQC(0x0A) | PORT_PCR_MUX(0x01)| PORT_PCR_PE(0x01)| PORT_PCR_PS(0x01));
	PORTE->PCR[26] = ( PORT_PCR_ISF(0x01) | PORT_PCR_IRQC(0x0A) | PORT_PCR_MUX(0x01)| PORT_PCR_PE(0x01)| PORT_PCR_PS(0x01));
	PORTE->PCR[27] = ( PORT_PCR_ISF(0x01) | PORT_PCR_IRQC(0x0A) | PORT_PCR_MUX(0x01)| PORT_PCR_PE(0x01)| PORT_PCR_PS(0x01));

	/* Change corresponding PTA port pins as outputs */
	PTA->PDDR = GPIO_PDDR_PDD(0x3F000FC0);

	/* Change corresponding PTE port pins as outputs */
	PTE->PDDR |= GPIO_PDDR_PDD( GPIO_PIN(28) );

	NVIC_ClearPendingIRQ(PORTE_IRQn);
	NVIC_EnableIRQ(PORTE_IRQn);
}

/* Variable delay loop */
void delay(int t1, int t2)
{
	int i, j;

	for(i=0; i<t1; i++) {
		for(j=0; j<t2; j++);
	}
}

/* Conversion of requested column number into the 4-to-16 decoder control.  */
void column_select(unsigned int col_num)
{
	unsigned i, result, col_sel[4];

	for (i =0; i<4; i++) {
		result = col_num / 2;	  // Whole-number division of the input number
		col_sel[i] = col_num % 2;
		col_num = result;

		switch(i) {

			// Selection signal A0
		    case 0:
				((col_sel[i]) == 0) ? (PTA->PDOR &= ~GPIO_PDOR_PDO( GPIO_PIN(8))) : (PTA->PDOR |= GPIO_PDOR_PDO( GPIO_PIN(8)));
				break;

			// Selection signal A1
			case 1:
				((col_sel[i]) == 0) ? (PTA->PDOR &= ~GPIO_PDOR_PDO( GPIO_PIN(10))) : (PTA->PDOR |= GPIO_PDOR_PDO( GPIO_PIN(10)));
				break;

			// Selection signal A2
			case 2:
				((col_sel[i]) == 0) ? (PTA->PDOR &= ~GPIO_PDOR_PDO( GPIO_PIN(6))) : (PTA->PDOR |= GPIO_PDOR_PDO( GPIO_PIN(6)));
				break;

			// Selection signal A3
			case 3:
				((col_sel[i]) == 0) ? (PTA->PDOR &= ~GPIO_PDOR_PDO( GPIO_PIN(11))) : (PTA->PDOR |= GPIO_PDOR_PDO( GPIO_PIN(11)));
				break;

			// Otherwise nothing to do...
			default:
				break;
		}
	}
}

/* Function for turning the LED on and off */
void turnLED(int delayt){
	//Turn off LED
	PTE->PDDR &= ~GPIO_PDDR_PDD( GPIO_PIN(28) );
	delay(delayt, tdelay2);
	//Turn on LED
	PTE->PDOR |= GPIO_PDOR_PDO( GPIO_PIN(28));
}

/* Forward function declarations */
/* Prints an exclamation mark */
void printEx(int i, int delayt);

/* Prints a letter three columns wide */
void print3(int i, int delayt, uint32_t value1, uint32_t value2, uint32_t value3);

/* Prints a letter four columns wide */
void print4(int i, int delayt, uint32_t value1, uint32_t value2, uint32_t value3, uint32_t value4);

/* Prints a letter five columns wide */
void print5(int i, int delayt, uint32_t value1, uint32_t value2, uint32_t value3, uint32_t value4, uint32_t value5);

/* Computes delay based on the length of a word */
int computeDelay(int len, int del){
	if(len == 0)
		return 0;
	return del / len;
}

/* Interrupt handler after pressing a button */
void PORTE_IRQHandler(void) {
		//First turn on all rows
		//PTA->PDOR |= GPIO_PDOR_PDO(0x3F000280);

		int n = 1;
		int delay = 0;
		int i;

		//Now based on the pushed button the according message is displayed
		if(PORTE->ISFR & SW2_MASK && (GPIOE->PDIR & SW2_MASK) == 0){
			//Hello World
			n = 11;
			delay = computeDelay(n, 500);
			//Maximum is increased by a number of letters * average letter width
			//Here is added 1 because there is a space between words
			for(i = 0; i < 16 + (5 * n) + 1; i++){
				for(int l = 0; l < 20; l++){
					//H
					print4(i, delay * 2, 0x3F000280, 0x02000000, 0x02000000, 0x3F000280);
					//E
					//Every letter is offset by their width + 1 so that the order is followed
					print4(i - 5, delay * 2, 0x34000000, 0x34000000, 0x34000000, 0x3F000280);
					//L
					print4(i - 10, delay * 2, 0x04000000, 0x04000000, 0x04000000 ,0x3F000280);
					//L
					print4(i - 15, delay , 0x04000000, 0x04000000, 0x04000000 ,0x3F000280);
					//O
					print4(i - 20, delay ,0x1B000280 ,0x24000000 ,0x24000000 ,0x1B000280);

					//W
					print5(i - 27, delay*2, 0x3F000280, 0x01000000 , 0x00000200 , 0x01000000 , 0x3F000280);
					//O
					print4(i - 33, delay ,0x1B000280 ,0x24000000 ,0x24000000 ,0x1B000280);
					//R
					print4(i - 38, delay, 0x0D000080, 0x30000200, 0x32000000, 0x3F000280);
					//L
					print4(i - 43, delay * 2, 0x04000000, 0x04000000, 0x04000000 ,0x3F000280);
					//D
					print4(i - 48, delay*2, 0x1B000280, 0x24000000, 0x24000000, 0x3F000280);
					//!
					printEx(i - 53, delay*2);
				}
			}
		}
		else if(PORTE->ISFR & SW3_MASK && (GPIOE->PDIR & SW3_MASK) == 0){
			//xkocma08
			n = 8;
			delay = computeDelay(n, 500);
			for(i = 0; i < 16 + (5 * n); i++){
				for(int l = 0; l < 20; l++){
					//X
					print4(i, delay, 0x2D000280, 0x12000000, 0x12000000, 0x2D000280);
					//K
					print4(i - 5, delay, 0x24000000, 0x09000000, 0x00000280, 0x3F000280);
					//O
					print4(i - 10, delay ,0x1B000280 ,0x24000000 ,0x24000000 ,0x1B000280);
					//C
					print4(i - 15, delay, 0x09000000, 0x24000000, 0x24000000, 0x1B000280);
					//M
					print5(i - 20, delay, 0x3F000280 , 0x08000000, 0x00000080, 0x08000000, 0x3F000280);
					//A
					print4(i - 26, delay, 0x1F000280,0x30000000, 0x30000000, 0x1F000280);
					//0
					print4(i - 31, delay, 0x1B000280, 0x34000000, 0x26000000, 0x1B000280);
					//8
					print4(i - 36, delay, 0x09000280, 0x36000000, 0x36000000, 0x09000280);
				}
			}

		}
		else if(PORTE->ISFR & SW4_MASK && (GPIOE->PDIR & SW4_MASK) == 0){
			//IMP good
			n = 10;
			delay = computeDelay(n, 600);
			for(i = 0; i < 16 + (5 * n) + 1; i++){
				for(int l = 0; l < 20; l++){
					//I
					print3(i, delay, 0x24000000, 0x3F000280, 0x24000000);
					//M
					print5(i - 4, delay, 0x3F000280 , 0x08000000, 0x00000080, 0x08000000, 0x3F000280);
					//P
					print4(i - 10, delay ,0x08000080 ,0x30000000 ,0x30000000 ,0x3F000280);

					//G
					print4(i - 17, delay, 0x0B000200, 0x26000000, 0x24000000, 0x1B000280);
					//O
					print4(i - 22, delay ,0x1B000280 ,0x24000000 ,0x24000000 ,0x1B000280);
					//O
					print4(i - 27, delay ,0x1B000280 ,0x24000000 ,0x24000000 ,0x1B000280);
					//D
					print4(i - 32, delay, 0x1B000280, 0x24000000, 0x24000000, 0x3F000280);
				}
			}

		}
		else if(PORTE->ISFR & SW5_MASK && (GPIOE->PDIR & SW5_MASK) == 0){
			//VUTBR FIT <3
			n = 8;
			delay = computeDelay(n, 600);
			for(i = 0; i < 16 + (5 * n) + 3; i++){
				for(int l = 0; l < 20; l++){
					//V
					print5(i, delay ,0x3A000280 ,0x1000000 ,0x4000000 , 0x1000000 ,0x3A000280);
					//U
					print4(i - 6, delay ,0x3B000280 ,0x4000000 ,0x4000000 ,0x3B000280);
					//T
					print5(i - 11, delay ,0x20000000 ,0x20000000 ,0x3F000280 ,0x20000000, 0x20000000);
					//B
					print4(i - 17, delay ,0xB000280 ,0x34000000 ,0x34000000 ,0x3F000280);
					//R
					print4(i - 22, delay, 0x0D000080, 0x30000200, 0x32000000, 0x3F000280);

					//F
					print4(i - 29, delay, 0x30000000, 0x30000000, 0x30000000, 0x3F000280);
					//I
					print3(i - 34, delay, 0x24000000, 0x3F000280, 0x24000000);
					//T
					print5(i - 38, delay ,0x20000000 ,0x20000000 ,0x3F000280 ,0x20000000, 0x20000000);
				}
			}
		}
		else if(PORTE->ISFR & SW6_MASK && (GPIOE->PDIR & SW6_MASK) == 0){
			//17 11 2022
			//XX746031XXXXXXXXXXXXXX2X5XXXXXXX
			//00100100000000000000000000000000
			n = 5;
			delay = computeDelay(n, 400);
			for(i = 0; i < 16 + (5 * n) + 2; i++){
				for(int l = 0; l < 20; l++){
					//D
					print4(i, delay, 0x1B000280, 0x24000000, 0x24000000, 0x3F000280);
					//A
					print4(i - 5, delay, 0x1F000280,0x30000000, 0x30000000, 0x1F000280);
					//V
					print5(i - 10, delay ,0x3A000280 ,0x1000000 ,0x4000000 , 0x1000000 ,0x3A000280);
					//I
					print3(i - 16, delay, 0x24000000, 0x3F000280, 0x24000000);
					//D
					print4(i - 20, delay, 0x1B000280, 0x24000000, 0x24000000, 0x3F000280);
				}
			}
		}
		//Turn off all interrupt flags
		PORTE->ISFR = SW2_MASK | SW3_MASK | SW4_MASK | SW5_MASK | SW6_MASK;
		//Turn of all rows
		PTA->PDOR &= GPIO_PDOR_PDO(0x0);
	}

//======================================================================================================

int main(void)
{
	/* Set up the pins and MCU */
	SystemConfig();

	/* Never ending loop waiting for an interrupt */
	while(42);

    return 0;
}

//======================================================================================================

/* Functions for letter writing */

/**
 * Basic logic:
 *
 * 		Turn on the according row LEDs
 * 		Select column
 * 		Turn off column
 * 		Delay
 * 		Turn on column
 */

void print3(int i, int delayt, uint32_t value1, uint32_t value2, uint32_t value3){
	//Turn on only if the row is visible
	if(i - 2 >= 0 && i - 2 < 16){
		//Clear all rows
		PTA->PCOR = GPIO_PCOR_PTCO(0x3F000280);
		//And then only turn on the desired rows
		PTA->PSOR = GPIO_PSOR_PTSO(value1);
		//Select column
		column_select((i - 2));
		//Turn on the column
		turnLED(delayt);
	}
	if(i - 1 >= 0 && i - 1 < 16){
		PTA->PCOR = GPIO_PCOR_PTCO(0x3F000280);
		PTA->PSOR = GPIO_PSOR_PTSO(value2);
		column_select((i - 1));
		turnLED(delayt);
	}
	if(i >= 0 && i < 16){
		PTA->PCOR = GPIO_PCOR_PTCO(0x3F000280);
		PTA->PSOR = GPIO_PSOR_PTSO(value3);
		column_select((i));
		turnLED(delayt);
	}
}

void print4(int i, int delayt, uint32_t value1, uint32_t value2, uint32_t value3, uint32_t value4){
	PTA->PDOR |= GPIO_PDOR_PDO(0x3F000280);
	if(i - 3 >= 0 && i - 3 < 16){
		PTA->PCOR = GPIO_PCOR_PTCO(0x3F000280);
		PTA->PSOR = GPIO_PSOR_PTSO(value1);
		column_select((i - 3));
		turnLED(delayt);
	}
	if(i - 2 >= 0 && i - 2 < 16){
		PTA->PCOR = GPIO_PCOR_PTCO(0x3F000280);
		PTA->PSOR = GPIO_PSOR_PTSO(value2);
		column_select((i - 2));
		turnLED(delayt);
	}
	if(i - 1 >= 0 && i - 1 < 16){
		PTA->PCOR = GPIO_PCOR_PTCO(0x3F000280);
		PTA->PSOR = GPIO_PSOR_PTSO(value3);
		column_select((i - 1));
		turnLED(delayt);
	}
	if(i >= 0 && i < 16){
		PTA->PCOR = GPIO_PCOR_PTCO(0x3F000280);
		PTA->PSOR = GPIO_PSOR_PTSO(value4);
		column_select((i));
		turnLED(delayt);
	}
}

void print5(int i, int delayt, uint32_t value1, uint32_t value2, uint32_t value3, uint32_t value4, uint32_t value5){
	if(i - 4 >= 0 && i - 4 < 16){
		PTA->PCOR = GPIO_PCOR_PTCO(0x3F000280);
		PTA->PSOR = GPIO_PSOR_PTSO(value1);
		column_select((i - 4));
		turnLED(delayt);
	}
	if(i - 3 >= 0 && i - 3 < 16){
		PTA->PCOR = GPIO_PCOR_PTCO(0x3F000280);
		PTA->PSOR = GPIO_PSOR_PTSO(value2);
		column_select((i - 3));
		turnLED(delayt);
	}
	if(i - 2 >= 0 && i - 2 < 16){
		PTA->PCOR = GPIO_PCOR_PTCO(0x3F000280);
		PTA->PSOR = GPIO_PSOR_PTSO(value3);
		column_select((i - 2));
		turnLED(delayt);
	}
	if(i - 1 >= 0 && i - 1 < 16){
		PTA->PCOR = GPIO_PCOR_PTCO(0x3F000280);
		PTA->PSOR = GPIO_PSOR_PTSO(value4);
		column_select((i - 1));
		turnLED(delayt);
	}
	if(i >= 0 && i < 16){
		PTA->PCOR = GPIO_PCOR_PTCO(0x3F000280);
		PTA->PSOR = GPIO_PSOR_PTSO(value5);
		column_select((i));
		turnLED(delayt);
	}
}

/* Print an exclamation mark */
void printEx(int i, int delayt){
	if(i >= 0 && i < 16){
		PTA->PCOR = GPIO_PCOR_PTCO(0x3F000280);
		PTA->PSOR = GPIO_PSOR_PTSO(0x3E000280);
		column_select((i));
		turnLED(delayt);
	}
}

