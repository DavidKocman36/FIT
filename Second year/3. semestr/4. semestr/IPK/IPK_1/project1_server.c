#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>

#include <arpa/inet.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <netinet/in.h>

#include <string.h>

/**
 * Gets current load of CPU in %. 
 * returns float value of CPU usage
 */
float get_load(){
    char str[100];
    float usage;
    char *delim = " ";
    FILE *fd;
    int times = 0;
    float nonidle, sum = 0;
    float lastIdle, lastSum = 0;

    //opens stat file two times to calculate correct CPU usage
    while(times != 2){
        fd = fopen("/proc/stat", "r");
        if (fd == NULL){
            return 0;
        }

        //reads first line of file, where sum of all CPU cores is
        fgets(str, 100, fd);

        fclose(fd);
        char* token = strtok(str, delim);
        int i = 0;
        int cpu[10];

        //splits string with CPU numbers and puts it in an array
        while(token!=NULL){
		    token = strtok(NULL,delim);
		    if(token!=NULL){
			    cpu[i] = atoi(token);
            }
            i++;
        }
        
        //computes current CPU usage
        sum = cpu[0] + cpu[1] + cpu[2] + cpu[3] + cpu[4] + cpu[5] + cpu[6] + cpu[7] + cpu[8] + cpu[9];
        nonidle = 1.0 - ((cpu[3] - lastIdle) / (sum - lastSum));

        lastIdle = cpu[3];
        lastSum = sum;
        //sleeps for 1 second to get different CPU numbers 
        if(times == 0)
            sleep(1);
        ++times;
    }
    //"converts" to percentage
    usage = nonidle * 100;
    return usage; 
}
/**
 * Gets name of computers CPU
 */
void get_cpu_name(char *cpu_name){

    //opens a file with CPU info and with the help of awk prints CPU name
    FILE *fd = popen("cat /proc/cpuinfo | grep \"model name\" | head -n 1 | awk -F ': ' '{split($0, array,\": \") ;print array[2]}'", "r");
    if (fd == NULL){
        return;
    }
    
    fgets(cpu_name, 100, fd);
    pclose(fd);
}
/**
 * Gets current computers hostname
 */
void get_hostname(char *hostname){
    FILE *fd;
    fd = fopen("/proc/sys/kernel/hostname", "r");
    if(fd == NULL){
        return;
    }

    fgets(hostname, 100, fd);
    fclose(fd);
}

int main(int argc, char *argv[]){
    if(argc != 2){
        fprintf(stderr, "Two arguments required! USAGE: ./hinfosvc <port>\n");
        return 1;
    }
    //gets port from the first argument
    int port = atoi(argv[1]);
    if(port < 0 || port > 65535){
        fprintf(stderr, "Invalid port number!\n");
        return 1;
    }

    //create a socket
    int server_socket = socket(AF_INET, SOCK_STREAM, IPPROTO_TCP);
    if(server_socket < 0){
        fprintf(stderr, "Error creating a socket!\n");
        return 1;
    }

    int val = 1;
    setsockopt(server_socket, SOL_SOCKET, SO_REUSEADDR | SO_REUSEPORT, (const char *)&val, sizeof(int));
    
    //configure socket to the given port
    struct sockaddr_in server_address;
    server_address.sin_family = AF_INET;
    server_address.sin_port = htons(port);
    server_address.sin_addr.s_addr = INADDR_ANY;

    //binds with the address and starts to listen
    bind(server_socket, (struct sockaddr*) &server_address, sizeof(server_address));
    listen(server_socket, 2);
    
    int client_socket;
    int err;
    char buff[6553];
    //messages
    char message_host[1000];
    char message_load[1000];
    char message_cpu[1000];
    char message_bad[1000];
    char hostname[150];
    float usage;

    while(42){
        //accepts client socket
        client_socket = accept(server_socket, NULL, NULL);
        if(client_socket < 0){
            fprintf(stderr, "Error accepting a request!\n");
            break;
        }
        //receives input from client socket
        err = recv(client_socket, buff, 1024, 0);
        if(err < 0){
            fprintf(stderr, "Error receiving a request!\n");
            break;
        }

        //in order which input client socket inputs, we send appropriate answer back 
        if (strncmp("GET /load", buff, 9) == 0){
            usage = get_load();
            char str[25];
            sprintf(str, "%d%%\n", (int)usage);
            int len_load = strlen(str);
            sprintf(message_load, "HTTP/1.1 200 OK\r\nContent-Length: %d\r\nContent-Type: text/plain;\r\n\r\n%s",len_load, str);
            int len = strlen(message_load);
            send(client_socket, message_load, len, 0);
        }
        else if (strncmp("GET /hostname", buff, 13) == 0){
            get_hostname(hostname);
            int len_host = strlen(hostname);
            sprintf(message_host, "HTTP/1.1 200 OK\r\nContent-Length: %d\r\nContent-Type: text/plain;\r\n\r\n%s",len_host, hostname);
            int len = strlen(message_host);
            send(client_socket, message_host, len, 0);
        }
        else if (strncmp("GET /cpu-name", buff, 13) == 0){
            char cpu_name[100];
            get_cpu_name(cpu_name);
            int len_cpu = strlen(cpu_name);
            sprintf(message_cpu,"HTTP/1.1 200 OK\r\nContent-Length: %d\r\nContent-Type: text/plain;\r\n\r\n%s", len_cpu, cpu_name);
            int len = strlen(message_cpu);
            send(client_socket, message_cpu, len, 0);
        }
        //else its a bad request
        else{
            char bad[] = "400 Bad Request\n\0";
            int len_bad = strlen(bad);
            sprintf(message_bad, "HTTP/1.1 200 OK\r\nContent-Length: %d\r\nContent-Type: text/plain;\r\n\r\n%s", len_bad, bad);
            int len = strlen(message_bad);
            send(client_socket, message_bad, len, 0);
        }
        //shuts down the client socket and starts accepting another request
        shutdown(client_socket, 2);
    }
    return 0;
}