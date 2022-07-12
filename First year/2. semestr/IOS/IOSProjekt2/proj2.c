#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <stdbool.h>
#include <sys/stat.h>
#include <limits.h>
#include <fcntl.h>
#include <semaphore.h>
#include <sys/types.h>
#include <sys/mman.h>
#include <sys/wait.h>
#include <unistd.h>
#include <pthread.h>
#include <errno.h>
/**
 * Název: IOS-2.projekt
 * Autor: David Kocman
 * Login: xkocma08
 * Datum: 26.4.2021
*/


//shared variables
int *shared;
int *elfs_waiting;
int *elfs_in_workshop;
int *rd_hitched;
bool *christmas;
//defining memory mapping and unmapping functions
#define MMAP(shared){shared = mmap(NULL, sizeof(*(shared)), PROT_READ | PROT_WRITE, MAP_SHARED | MAP_ANONYMOUS, -1, 0);} 
#define UNMAP(shared){munmap((shared), sizeof(*(shared)));}
//output file
FILE *file = NULL;

//semaphores
sem_t *mutex;
sem_t *semaphoreSanta;
sem_t *semaphoreElf;
sem_t *semaphoreElf_ws;
sem_t *semaphoreElf_ws_write;
sem_t *semaphoreRD;
sem_t *semaphoreRD_write;
sem_t *semaphoreElf_holiday;
sem_t *semaphore_Wait;

//deallocating all resourcef if there is an error or at the end of programme
void cleanup(){
    //unmapping shared variables
    UNMAP(shared);
    UNMAP(semaphoreSanta);
    UNMAP(semaphoreElf);
    UNMAP(semaphoreRD);
    UNMAP(mutex);
    UNMAP(elfs_waiting);
    UNMAP(elfs_in_workshop);
    UNMAP(rd_hitched);
    UNMAP(christmas);
    UNMAP(semaphoreElf_holiday);
    UNMAP(semaphoreElf_ws);
    UNMAP(semaphoreRD_write);
    UNMAP(semaphoreElf_ws_write);
    UNMAP(file);
    UNMAP(semaphore_Wait);
    
    //destroying semaphores
    sem_destroy(semaphoreSanta);
    sem_destroy(mutex);
    sem_destroy(semaphoreElf);
    sem_destroy(semaphoreElf_ws);
    sem_destroy(semaphoreElf_ws_write);
    sem_destroy(semaphoreElf_holiday);
    sem_destroy(semaphoreRD);
    sem_destroy(semaphoreRD_write);
    sem_destroy(semaphore_Wait);
}

//allocating all resources
int init(){
    //mapping shared variables
    MMAP(shared);
    MMAP(semaphoreSanta);
    MMAP(semaphoreElf);
    MMAP(semaphoreElf_ws);
    MMAP(semaphoreElf_ws_write);
    MMAP(semaphoreRD);
    MMAP(semaphoreRD_write);
    MMAP(mutex);
    MMAP(elfs_waiting);
    MMAP(elfs_in_workshop);
    MMAP(rd_hitched);
    MMAP(christmas);
    MMAP(file);
    MMAP(semaphoreElf_holiday);
    MMAP(semaphore_Wait);

    //initializing all semaphores 
    sem_init(semaphoreSanta, 1, 0);
    //if semaphore failed, dealloc all resources and exit the process
    if(semaphoreSanta == SEM_FAILED){
        fprintf(stderr, "Failed to allocate Santa semaphore!\n");
        cleanup();
        return 1;
    }
    sem_init(semaphoreRD, 1, 0);
    if(semaphoreRD == SEM_FAILED){
        fprintf(stderr, "Failed to allocate Reindeer semaphore!\n");
        cleanup();
        return 1;
    }
    sem_init(semaphoreRD_write, 1, 0);
    if(semaphoreRD_write == SEM_FAILED){
        fprintf(stderr, "Failed to allocate reindeer_write semaphore!\n");
        cleanup();
        return 1;
    }
    sem_init(semaphoreElf, 1, 1);
    if(semaphoreElf == SEM_FAILED){
        fprintf(stderr, "Failed to allocate Elf semaphore!\n");
        cleanup();
        return 1;
    }
    sem_init(semaphoreElf_ws, 1, 0);
    if(semaphoreElf_ws == SEM_FAILED){
        fprintf(stderr, "Failed to allocate Elf_ws semaphore!\n");
        cleanup();
        return 1;
    }
    sem_init(semaphoreElf_ws_write, 1, 0);
    if(semaphoreElf_ws_write == SEM_FAILED){
        fprintf(stderr, "Failed to allocate Elf_ws_write semaphore!\n");
        cleanup();
        return 1;
    }
    sem_init(semaphoreElf_holiday, 1, 0);
    if(semaphoreElf_holiday == SEM_FAILED){
        fprintf(stderr, "Failed to allocate Elf_holiday semaphore!\n");
        cleanup();
        return 1;
    }
    sem_init(mutex, 1, 1);
    if(mutex == SEM_FAILED){
        fprintf(stderr, "Failed to allocate mutex semaphore!\n");
        cleanup();
        return 1;
    }
    sem_init(semaphore_Wait, 1, 1);
    if(semaphore_Wait == SEM_FAILED){
        fprintf(stderr, "Failed to allocate mutex semaphore!\n");
        cleanup();
        return 1;
    }

    return 0;
}


//write functions
void vypis( char *s,char *ent, int i){
    file = fopen("proj2.out", "a");

    sem_wait(mutex);
    fprintf(file,"%d: %s %d: %s\n",(*shared)++, ent ,i, s);
    sem_post(mutex);

    fclose(file);
}
void vypis_debug(char *s){
    file = fopen("proj2.out", "a");

    sem_wait(mutex);
    fprintf(file,"%d :%s\n",(*rd_hitched), s);
    sem_post(mutex);

    fclose(file);
}
void vypis_santa( char *s,char *ent){
    file = fopen("proj2.out", "a");

    sem_wait(mutex);
    fprintf(file,"%d: %s: %s\n",(*shared)++, ent, s);
    sem_post(mutex);

    fclose(file);
}

//the elf process
void process_elf( int TE, int i){
    int pom = 1;
    char *entity = "Elf";
    char *start = "started";
    char *n_help = "need help";
    char *g_help = "get help";
    char *holiday = "taking holidays";

    while (1)
    {
        //generating a random nuber for work simulation
        srand(getpid());
        int a = (rand()%(TE + 1 - 0) + 0 ) * 1000;
        if (pom == 1)
        {
            vypis(start,entity, i);
        }
        //working
        usleep(a);
        vypis(n_help,entity, i);
        //ends if santa closed workshop
        if((*christmas) == true){
            sem_wait(semaphoreElf_holiday);
            vypis(holiday, entity, i);
            break;
        }
    
        sem_wait(semaphoreElf);
        sem_wait(semaphore_Wait);
        (*elfs_waiting)+=1;
        sem_post(semaphore_Wait);
        
        //awakes santa if there are 3 in front of workshop
        if ((*elfs_waiting) == 3 && (*christmas) != true)
        {
            sem_post(semaphoreSanta);
        }
        else{
            sem_post(semaphoreElf);
        }
        //waiting for santa to help them
        sem_wait(semaphoreElf_ws);
        //ends if santa closed workshop
        if((*christmas) == true){
            sem_wait(semaphoreElf_holiday);
            vypis(holiday, entity, i);
            break;
        }
        vypis(g_help, entity, i);
        sem_wait(semaphore_Wait);
        (*elfs_waiting) -= 1;
        sem_post(semaphore_Wait);
        //if there are none elves in workshop, lets another ebatch of elves in
        if ((*elfs_waiting) == 0)
        {
            sem_post(semaphoreElf_ws_write);
            sem_post(semaphoreElf);
        }
        
        pom ++;
    }
    exit(0);
    
}

//the reindeer process
void process_Reindeer( int TR,int i, int NR){
    
    char *ent = "RD";
    char *rstart = "rstarted";
    char *rethome = "return home";
    char *hitched = "get hitched";

    while (1)
    {
        //generating a random number to simulate vacation
        srand(getpid());
        int a = (rand()%(TR + 1 - (TR/2)) + (TR/2))*1000;
        vypis( rstart, ent, i);
        //on vacation
        usleep(a);
        vypis( rethome, ent, i);
        
        sem_wait(semaphore_Wait);
        (*rd_hitched)+=1;
        sem_post(semaphore_Wait);
        //last reindeer awakes santa
        if((*rd_hitched) == NR){
            sem_post(semaphoreSanta);
            
        }
        //and waits till santa hitches them
        sem_wait(semaphoreRD);
        vypis(hitched, ent, i);
        
        sem_post(semaphoreRD_write);
        break;
    }
    exit(0);
}

//the santa process
void process_Santa(int NR, int NE){
    int pom = 1;
    char *s = "going to sleep";
    char *entity = "Santa";
    char *helping = "helping elves";
    char *closing = "closing workshop";
    char *christmas_started = "Christmas started";
    
    while (1)
    {
        if(pom == 1)
            vypis_santa(s, entity);
        //waits untill somebody awakes him
        sem_wait(semaphoreSanta);
        
        if((*rd_hitched) == NR){
            //if all reindeers are back, santa closes workshop and sends all his elves on holiday
            vypis_santa(closing, entity);
            sem_wait(semaphore_Wait);
            (*christmas) = true;
            sem_post(semaphore_Wait);
            //sending elves that are waiting in front of the workshop on holiday
            for (int l = 0; l < (*elfs_waiting); l++)
            {
                sem_post(semaphoreElf_ws);
                
            }
            //sending the rest on holiday
            for (int k = 0; k < NE; k++)
            {
                sem_post(semaphoreElf_holiday);
            }
            int j = 1;
            //hitching the reindeers
            for (; j <= NR; j++)
            {
                sem_post(semaphoreRD);
            }
            sem_wait(semaphoreRD_write);
            vypis_santa(christmas_started, entity);
            break;
        }
        else if((*elfs_waiting) == 3){
            //helping elves if there are 3 waiting in front of the workshop
            vypis_santa(helping, entity);
            for (int i = 1; i <= 3; i++)
            {
                sem_post(semaphoreElf_ws);
            }
            sem_wait(semaphoreElf_ws_write);
            vypis_santa(s, entity);
            
        }
        pom++;
        
    }
    exit(0);
}


//the elf process generator
void gen_elf(int NE, int TE){
    for(int i = 1; i <= NE; i++){
        pid_t Elf = fork();
        //if fork fails, exit the process
        if(Elf < 0){
            fprintf(stderr, "Elf Fork number %d had failed!\n", i);
            cleanup();
            exit(1);
        }
        if(Elf == 0){
            process_elf( TE, i);
        }
    }
}
//the reindeer process generator
void gen_Reindeer(int NR, int TR){
    for(int i = 1; i <= NR; i++){
        pid_t Sob = fork();
        //if fork fails, exit the process
        if(Sob < 0){
            fprintf(stderr, "Reindeer Fork number %d had failed!\n", i);
            cleanup();
            exit(1);
        }
        if(Sob == 0){
            process_Reindeer( TR, i, NR);
        }
    }
}


int main(int argc, char *argv[]){
    //number of arguments check
    if (argc != 5)
    {
        fprintf(stderr, "Not enough arguments!\n");
        return 1;
    }
    int NE = atoi(argv[1]);//number of elves
    int NR = atoi(argv[2]);//number of reindeer
    int TE = atoi(argv[3]);//work time for elves
    int TR = atoi(argv[4]);//vacation time for reindeer

    //argument check
    if(NE >= 1000 || NE <= 0){
        fprintf(stderr, "Invalid NE (1.) argument, must be between 1 and 999!\n");
        return 1;
    }
    if(NR >= 20 || NR <= 0){
        fprintf(stderr, "Invalid NR (2.) argument, must be between 1 and 19!\n");
        return 1;
    }
    if(TE > 1000 || TE < 0){
        fprintf(stderr, "Invalid TE (3.) argument, must be between 0 and 1000!\n");
        return 1;
    }
    if(TR > 1000 || TR < 0){
        fprintf(stderr, "Invalid TR (4.) argument, must be between 0 and 1000!\n");
        return 1;
    }

    //initializing, if error, programme ends
    int initialize = init();
    if(initialize == 1){
        return 1;
    }

    //file open
    char *filename = "proj2.out";
    file = fopen(filename, "w");
    (*shared)++;

    //creation of processes
    pid_t dite = fork();
    if(dite < 0){
        fprintf(stderr, "Santa Fork had failed!\n");
        cleanup();
        return 1;
    }
    if(dite != 0){
        gen_elf(NE, TE);
        gen_Reindeer(NR, TR);
        exit(0);
    }
    else if(dite == 0){
        process_Santa(NR, NE);
    }
    //finish main process when all children ale killed
    while(wait(NULL) != -1 || errno != ECHILD);

    //cleanup and file close
    cleanup();
    fclose(file);
    return 0;
}