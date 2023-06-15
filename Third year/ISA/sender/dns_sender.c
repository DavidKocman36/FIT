/**
 * ISA project: DNS tunnel
 * File: dns_sender.c
 * Author: David Kocman, xkocma08
*/

#include "sender.h"
#include "base64.c"

//=============================================================================================================================
//all function comments are in sender.h file
int get_args(int argc, char *argv[], char *upstream_dns_ip, char* base_host, char* dst_filepath, char* src_filepath){
    int c;
    char optargs[5] = "u:"; 
    //argument options, in this case just -u
    while ((c = getopt (argc, argv, optargs)) != -1){
        switch (c)
        {
            case 'u':
                strcpy(upstream_dns_ip, optarg);
                break;
        }
    }

    //base host get
    strcpy(base_host, argv[optind]);
    if(base_host == NULL){
        fprintf(stderr, "Base_host is mandatory!\n");
        return 1;
    }
    
    //destination file name 
    optind+=1;
    strcpy(dst_filepath, argv[optind]);
    if(dst_filepath == NULL){
        fprintf(stderr, "Dst_filepath is mandatory!\n");
        return 1;
    }
    
    //source file name
    optind+=1;
    if(optind >= argc){
        src_filepath = NULL;
    }
    else{
        strcpy(src_filepath, argv[optind]);
    }

    return 0;
}
//
int get_address(char *upstream_dns_ip){
    FILE *fd = popen("cat /etc/resolv.conf | grep \"nameserver\" | awk '{print $2}'", "r");
    if(fd == NULL){
        return 1;
    }
    fread(upstream_dns_ip, 1, 100, fd);
    pclose(fd);
    
    return 0;
}
//
//src: https://www.binarytides.com/dns-query-code-in-c-with-linux-sockets/
void ChangetoDnsNameFormat(unsigned char* dns,unsigned char* host) 
{
	int lock = 0 , i;
    char *aux = strstr(host, ".");
    if(aux == NULL){
        return;
    }
    strcat((char *)host, ".");
	

	for(i = 0 ; i < (int)strlen((char*)host) ; i++) 
	{
		if(host[i]=='.') 
		{
			*dns++ = i-lock;
			for(;lock<i;lock++) 
			{
				*dns++=host[lock];
			}
			lock++;
		}
	}
	*dns++='\0';
}
//
void send_packet(char* qname, struct DNS_HEADER *dns, struct QUESTION *qinfo, int sockfd, int id, struct sockaddr_in server_address, unsigned char* buf, char* dst_filepath, char* coded, int size){
    int len = 0;
    unsigned char retBuf[65536];
    
    //fill qname and qinfo
    //src: https://www.binarytides.com/dns-query-code-in-c-with-linux-sockets/
    qname =(unsigned char*)&buf[sizeof(struct DNS_HEADER)];
    memset(qname, 0, sizeof(qname));
    dns->id = (unsigned short) htons(id);
    //change data in query to DNS friendly format
    ChangetoDnsNameFormat(qname, coded);
    qinfo =(struct QUESTION*)&buf[sizeof(struct DNS_HEADER) + (strlen((const char*)qname) + 1)];
    qinfo->qtype = htons(5);
    qinfo->qclass = htons(1);

    //send encoded data
    if( sendto(sockfd,(char*)buf,sizeof(struct DNS_HEADER) + (strlen((const char*)qname)+1) + sizeof(struct QUESTION),0,(struct sockaddr*)&server_address,sizeof(server_address)) < 0)
    {
        perror("sendto failed");
    }

    dns_sender__on_chunk_sent(&server_address.sin_addr, dst_filepath, id, size);

    if(recvfrom(sockfd, (char *)retBuf, 65536,  MSG_WAITALL, ( struct sockaddr *) &server_address, &len) < 0){
        //not supporting retransmit
        perror("Recvfrom");
    }

    //clear buffer
    memset(qname, 0, sizeof(qname));
    return;
}
//=============================================================================================================================

int main(int argc, char *argv[]){

    if(argc < 3){
        fprintf(stderr, "USAGE: dns_sender [-u UPSTREAM_DNS_IP] {BASE_HOST} {DST_FILEPATH} [SRC_FILEPATH]");
        return 1;
    }
    //arg_parse
    //dns_sender [-u UPSTREAM_DNS_IP] {BASE_HOST} {DST_FILEPATH} [SRC_FILEPATH]
    char upstream_dns_ip[100] = "";
    char base_host[1000] = "";
    char dst_filepath[1000] = "";
    char src_filepath[1000] = "";
    char source[1000] = ""; 

    //parse arguments
    int err = get_args(argc, argv, upstream_dns_ip, base_host, dst_filepath, src_filepath);
    if(err == 1){
        return 1;
    }

    //get address of DNS server in resolv.conf if user didnt provide any
    if(strlen(upstream_dns_ip) == 0){
        int a = get_address(upstream_dns_ip);
        if(a == 1){
            fprintf(stderr, "Error opening /etc/resolv.conf!\n");
            return 1;
        }
        upstream_dns_ip[strcspn(upstream_dns_ip, "\n")] = 0;
    }

    //open file if entered or read from stdin
    FILE *fp;
    if(strlen(src_filepath) == 0){
        fp = stdin;
        strcpy(source, "stdin");
    }
    else{    
        fp = fopen(src_filepath, "r");
        if(fp == NULL){
            fprintf(stderr, "Error opening source file!\n");
            return 1;
        }
        strcpy(source, src_filepath);
    }
    //

    /*
    socket setup
    */
    int sockfd;
    if ( (sockfd = socket(AF_INET, SOCK_DGRAM, IPPROTO_UDP)) < 0 ) { 
        perror("socket: "); 
    } 

    //set timeout
    //source: https://stackoverflow.com/questions/13547721/udp-socket-set-timeout
    struct timeval tv;
    tv.tv_sec = 1;
    tv.tv_usec = 500000;
    if (setsockopt(sockfd, SOL_SOCKET, SO_RCVTIMEO,&tv,sizeof(tv)) < 0) {
        perror("Setsockopt: ");
    }

    //setup server address
    struct sockaddr_in server_address; 
    memset(&server_address, 0, sizeof(server_address)); 
    
    server_address.sin_family = AF_INET; 
    server_address.sin_port = htons(DNS_PORT); 
    server_address.sin_addr.s_addr = inet_addr(upstream_dns_ip); 

    dns_sender__on_transfer_init(&server_address.sin_addr);
    //

    /*DNS query*/
    //src: https://www.binarytides.com/dns-query-code-in-c-with-linux-sockets/
    //setup DNS query header
    unsigned char buf[65536],*qname;

    memset(buf, 0, sizeof(buf));

    struct DNS_HEADER *dns = NULL;
	struct QUESTION *qinfo = NULL;

    //DNS struct fill
    dns = (struct DNS_HEADER *)&buf;
	dns->qr = 0; //This is a query
	dns->opcode = 0; //This is a standard query
	dns->aa = 0; //Not Authoritative
	dns->tc = 0; //This message is not truncated
	dns->rd = 1; //Recursion Desired
	dns->ra = 0; //Recursion not available
	dns->z = 0;
	dns->ad = 0;
	dns->cd = 0;
	dns->rcode = 0;
	dns->q_count = htons(1);
	dns->ans_count = 0;
	dns->auth_count = 0;
	dns->add_count = 0;
    //

    char buffer[62 + strlen(base_host) + 1];
    unsigned char* coded;
    char c;
    int id = 0;
    int size = 0;
    int fileSize = 0;
    size_t out = 0;
    int n, len;
    
    //setup first packet that contains filename
    strcpy(buffer, dst_filepath);

    //encode the data
    coded = b64_encode((const unsigned char *)buffer, strlen(buffer), base_host);
    if(coded == NULL){
        fprintf(stderr, "Error encoding data!\n");
        return 1;
    }
    out = b64_encoded_size(strlen(buffer));

    //append base host
    coded[out] = '\0';
    strcat(coded, ".");
    strcat(coded, base_host);

    //send encoded data
    send_packet(qname, dns, qinfo, sockfd, id, server_address, buf, source, coded, strlen(buffer));

    memset(buffer, 0, sizeof(buffer));
    free(coded);
    id += 1;

    //
    int cnt = 0; 
    //Now read file or stdin and every 45th character send packet
    //I send only one subdomain per query
    //Do the same things as above for the data
    while ((c = fgetc(fp)) != EOF){
        //
        buffer[cnt] = c;

        //if there are 45 chars in an array or there is an EOF coming, then send the packet
        if(cnt == CHARS_IN_QRY || ((c = fgetc(fp)) == EOF)){
            //encode
            coded = b64_encode((const unsigned char *)buffer, strlen(buffer), base_host);
            if(coded == NULL){
                fprintf(stderr, "Error encoding data!\n");
                return 1;
            }

            //append base host
            out = b64_encoded_size(strlen(buffer));
            coded[out] = '\0';
            strcat(coded, ".");
            strcat(coded, base_host);

            dns_sender__on_chunk_encoded(source, id, coded);

            //size of file
            fileSize += (cnt+1);
            //send
            send_packet(qname, dns, qinfo, sockfd, id, server_address, buf, source, coded, cnt + 1);

            //clean buffers
            memset(buffer, 0, sizeof(buffer));
            free(coded);

            cnt = 0;
            id += 1;

            continue;
        }

        ungetc(c, fp);
        cnt += 1;
    }

    //send ending packet
    strcpy(buffer, "end");

    coded = b64_encode((const unsigned char *)buffer, strlen(buffer), base_host);
    if(coded == NULL){
        fprintf(stderr, "Error encoding data!\n");
        return 1;
    }
    out = b64_encoded_size(strlen(buffer));
    
    coded[out] = '\0';
    strcat(coded, ".");
    strcat(coded, base_host);

    send_packet(qname, dns, qinfo, sockfd, id, server_address, buf, source, coded, 3);
    //

    dns_sender__on_transfer_completed(source, fileSize);

    close(sockfd);
    free(coded);
    fclose(fp);

    return 0;
}