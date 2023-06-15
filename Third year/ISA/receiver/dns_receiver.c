/**
 * ISA project: DNS tunnel
 * File: dns_receiver.c
 * Author: David Kocman, xkocma08
*/

#include "receiver.h"
#include "base64.c"

//=============================================================================================================================
//all function comments are in receiver.h file
int write_data(char *dst_filename, char *dst_filepath, char *data, char* path){
    DIR* dir = opendir(dst_filepath);
    if (dir) {
        closedir(dir);
        FILE *fd = fopen(path, "a+");
        if(fd == NULL){
            return 1;
        }
        if(data != NULL){
            fwrite(data, 1 ,strlen(data), fd);
        }
        
        fclose(fd);

        
    } 
    else if (ENOENT == errno) {
        int check = mkdir(dst_filepath, 0777);
        if(check == -1){
            return 1;
        }

        FILE *fd = fopen(path, "a+");
        if(fd == NULL){
            return 1;
        }
        if(data != NULL){
            fwrite(data, 1 ,strlen(data), fd);
        }
        fclose(fd);
        //closedir(dir);
    } 
    else {
        fprintf(stderr, "Could not open directory %s!\n", dst_filepath);
        return 1;
    }

    return 0;
}
//
/**
 * modified my_strip function from: 
 * source: https://stackoverflow.com/questions/15444567/removing-spaces-and-special-characters-from-string
*/
void pretty_print(char *data){
    unsigned long i = 0;
    unsigned long x = 0;
    char c;

    while ((c = data[i++]) != '\0') {
        if (isalnum(c) || c == '=' || c == '+' || c == '/') {
            data[x++] = c;
        }
        else{
            data[x++] = '.';
        }
    }
    data[x] = '\0';
}
//
char* get_qname(char* buf,  struct QUESTION *qinfo, unsigned char *qname){
    qname =(unsigned char*)&buf[sizeof(struct DNS_HEADER)];
    qinfo =(struct QUESTION*)&buf[sizeof(struct DNS_HEADER) + (strlen((const char*)qname) + 1)];
    qname++;

    return qname;
}
//=============================================================================================================================

int main(int argc, char *argv[])
{

    if(argc != 3){
        fprintf(stderr, "ERROR: Requires two parameters!\nUsage: dns_receiver {BASE_HOST} {DST_FILEPATH}\n");
        return 1;
    }

    //get arguments
    char *base_host = argv[1];
    char *dst_filepath = argv[2];

    /* SETUP */
    //setup socket
    int sockfd; 
    if ((sockfd = socket(AF_INET, SOCK_DGRAM, 0)) < 0 ) { 
        perror("socket creation failed"); 
        return 1;
    } 

    //set socket option to reuse the IP address
    if (setsockopt(sockfd, SOL_SOCKET, SO_REUSEADDR, &(int){1}, sizeof(int)) < 0)
        perror("setsockopt(SO_REUSEADDR) failed");
    
    //server structs
    struct sockaddr_in server_address, client_address; 
    memset(&server_address, 0, sizeof(server_address)); 
    memset(&client_address, 0, sizeof(server_address)); 

    server_address.sin_family = AF_INET;
    server_address.sin_port = htons(DNS_PORT);
    server_address.sin_addr.s_addr = INADDR_ANY;

    //split string in order to find host
    //char *host = strtok(base_host, ".");

    unsigned char buf[65536],*qname,*reader;
    struct DNS_HEADER *dns = NULL;
	struct QUESTION *qinfo = NULL;
    
    //bind socket to server adress and port
    if ( bind(sockfd, (const struct sockaddr *)&server_address, sizeof(server_address)) < 0 ) 
    { 
        perror("bind failed"); 
        return 1;
    } 
        
    int len;
    char filename[200] = "";
    unsigned char* decoded = NULL;
	size_t out_len = 0;
    int filesize = 0;
    char path[300];
    int prev_id = -1;

    /* RECEIVE */
    //now loop through all the data packets
    while(42){

        len = sizeof(client_address); 

        //receive packet
        recvfrom(sockfd, (char *)buf, 65536,  MSG_WAITALL, ( struct sockaddr *) &client_address, &len); 

        char copy[65536];
        strcpy(copy, buf);

        //get the DNS header from buffer
        dns = (struct DNS_HEADER *)&buf;
        /*if(htons(dns->id) != prev_id + 1){
            continue;
        }*/

        //get encoded data
        qname = get_qname(buf, qinfo, qname);        

        //basehost check
        char temp[300];
        char *tmp;
        //put the dots back in
        pretty_print(qname);
        strcpy(temp, qname);
        //

        tmp = strstr(temp, base_host);
        if(tmp == NULL){
            continue;
        }

        //extract the filename from the first data packet
        if(dns->id == htons(0)){

            dns_receiver__on_transfer_init(&client_address.sin_addr);
            char *aux = calloc(strlen(qname) + 1, 1);
            strcpy(aux, qname);

            //split the string on the first occurence of a dot and alloc the appropriate size of final buffer
            qname = strtok(qname, ".");
            out_len = b64_decoded_size(qname)+1;
            decoded = calloc(out_len+1, 1);

            //decode
            if (!b64_decode(qname, decoded, out_len)) {
                fprintf(stderr ,"Decode Failure\n");
                return 1;
            }
            decoded[out_len] = '\0';

            //since this is first packet I'll extract the file name
            strcpy(filename, decoded);

            //check if there is a "/" at the end of directory path and create a filepath
            if(dst_filepath[strlen(dst_filepath) - 1] == '/'){
                sprintf(path, "%s%s", dst_filepath, filename);
            }
            else{
                sprintf(path, "%s/%s", dst_filepath, filename);
            }
            
            dns_receiver__on_query_parsed(path, aux);

            //delete all data from the file if exists or create it
            FILE *tmp = fopen(path, "w");
            if(tmp != NULL)
                fclose(tmp);

            //send a response
            dns->qr = 1;
            dns->rcode = 3;
            sendto(sockfd,(char *)buf,sizeof(struct DNS_HEADER) + (strlen((const char*)qname)+1) + sizeof(struct QUESTION),MSG_CONF,(struct sockaddr*)&client_address,sizeof(client_address));

            free(aux);
            free(decoded);

            continue;
        }
        else{
            dns_receiver__on_query_parsed(path, qname);
            //split the string on the first occurence of a dot
            qname = strtok(qname, ".");
            
            //source: https://nachtimwald.com/2017/11/18/base64-encode-and-decode-in-c/
            out_len = b64_decoded_size(qname) + 1;
            decoded = calloc(out_len + 1, 1);
            
            if (!b64_decode(qname, decoded, out_len)) {
                fprintf(stderr ,"Decode Failure\n");
                return 1;
            }
            
            decoded[out_len] = '\0';

            //if the packet is ending packet then exit the loop
            if(strcmp(decoded, "end") == 0){
                //send a response
                dns->qr = 1;
                dns->rcode = 3;
                sendto(sockfd,(char *)buf,sizeof(struct DNS_HEADER) + (strlen((const char*)qname)+1) + sizeof(struct QUESTION),MSG_CONF,(struct sockaddr*)&client_address,sizeof(client_address));

                dns_receiver__on_transfer_completed(path, filesize);
                filesize = 0;
                //clean up after myself
                free(decoded);
                prev_id = -1;
                continue;
            }
            else{
                //count size of file, minus one because the 0 offset
                filesize += (out_len - 1);
                dns_receiver__on_chunk_received(&client_address.sin_addr, path, htons(dns->id), out_len - 1);
                //write data into the file
                int err = write_data(filename, dst_filepath, decoded, path);
                if(err == 1){
                    fprintf(stderr, "Error writing data!\n");
                    return 1;
                }
                //send a response
                dns->qr = 1;
                dns->rcode = 3;
                sendto(sockfd,(char *)buf,sizeof(struct DNS_HEADER) + (strlen((const char*)qname)+1) + sizeof(struct QUESTION),MSG_CONF,(struct sockaddr*)&client_address,sizeof(client_address));

                //clean up after myself            
                free(decoded);
            }
        }
        //store the previous DNS ID
        prev_id = htons(dns->id);
    }
    
    return 0; 
}
