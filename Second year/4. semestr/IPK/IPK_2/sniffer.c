/**
 * IPK - Second project
 * Packet sniffer
 * Author: David Kocman, xkocma08
 */

#include <stdio.h>
#include <stdlib.h>
#include <unistd.h> 
#include <string.h>
#include <stdbool.h>
#include <time.h>

#include <pcap/pcap.h>

#include <arpa/inet.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <netinet/in.h>

#include <netinet/ip_icmp.h>
#include <netinet/udp.h>
#include <netinet/tcp.h>
#include <netinet/ip.h>
#include <netinet/ip6.h>
#include <netinet/if_ether.h>

#define ETHER_LEN 14
//IPv6 header length is always the same
#define IPV6_LEN 40

bool pres_tc = false;
bool pres_u = false;
bool pres_a = false;
bool pres_ic = false;
bool show_all = false;
struct sockaddr_in source,dest;
struct sockaddr_in6 source6,dest6;
struct in6_addr in6addr;

/**
 * @brief Print whole packet in hexadecimal and ASCII form
 * @param u_char Buffer cointaining the packet
 * @param int Size of the part we want to print
 */
//Inspired by: https://www.binarytides.com/packet-sniffer-code-c-libpcap-linux-sockets/
void print_data(const u_char *buffer, int size){

    int line = 1;
    int i,j;
    //print first line offset
    printf("0x0000: ");
    for (i = 0; i < size; i++)
    {
        //when at the end of a line, go back the same 16 characters and print them in
        //ASCII form
        if (i!=0 && i%16==0){

            for(j=i-16 ; j<i ; j++)
			{
                //if printable, then print else print a dot
				if(buffer[j]>=32 && buffer[j]<=128)
					printf("%c", (unsigned char)buffer[j]);
				
				else 
                    printf(".");
			}

			printf("\n");
            //print offset
            if (line<10){
                printf("0x00%d: ", line*10);
            }
            else if (line<100){
                printf("0x0%d: ", line*10);
            }
            else{
                printf("0x%d: ", line*10);
            }
            ++line;
        }
        //hexadecimal print
        printf("%02x ", (unsigned char) buffer[i]);
        
        //print the rest of incomplete (less than 16 characters) line
        if(i == size-1){
            //print the according number of spaces so its nicely aligned
            for(j = 0 ; j < 15 - (i % 16) ; j++){
                printf("   ");
            }
            for(j = i - (i % 16) ; j <= i ; j++)
            {
                if(buffer[j]>=32 && buffer[j]<=128)
                    printf("%c", (unsigned char)buffer[j]);
                
                else 
                    printf(".");
            }
        }
    }
    printf("\n");
    
}
/**
 * @brief prints all available devices
 * @param struct linked list containing devices
 */
void print_devices(pcap_if_t *alldevsp){
    pcap_if_t *device;
    for(device = alldevsp ; device != NULL ; device = device->next)
    {
        printf("%s\n" , device->name);
    }
}

/**
 * @brief Prints and parses an IPv4 address
 * @param struct containing the address
 */
//Inspired by: https://www.binarytides.com/packet-sniffer-code-c-libpcap-linux-sockets/
void print_IPv4(struct iphdr *iph){
    memset(&source, 0, sizeof(source));
    memset(&dest, 0, sizeof(dest));
    source.sin_addr.s_addr = iph->saddr;
    dest.sin_addr.s_addr = iph->daddr;

    printf("src IP: %s\n", inet_ntoa(source.sin_addr));
    printf("dst IP: %s\n", inet_ntoa(dest.sin_addr));
}

/**
 * @brief Prints and parses an IPv6 address
 * @param struct containing the address
 */
//Inspired by: https://linuxhint.com/c-init-ntop-function/
void print_IPv6(struct ip6_hdr *ip6h){
    char Sstring[INET6_ADDRSTRLEN];
    char Dstring[INET6_ADDRSTRLEN];

    dest6.sin6_addr = ip6h->ip6_dst;
    source6.sin6_addr = ip6h->ip6_src;
	inet_ntop(AF_INET6, &(source6.sin6_addr), Sstring, INET6_ADDRSTRLEN);
    inet_ntop(AF_INET6, &(dest6.sin6_addr), Dstring, INET6_ADDRSTRLEN);

    printf("src IP: %s\n",Sstring);
    printf("dst IP: %s\n",Dstring);
}

/**
 * @brief Defines what to do when a packet is captured.
 * @param u_char containing the handle
 * @param struct Header containing length and timestamp of the packet
 * @param u_char Buffer containing the packet
 */
//Inspired by: https://www.binarytides.com/packet-sniffer-code-c-libpcap-linux-sockets/
void callback(u_char *args, const struct pcap_pkthdr *header, const u_char *buffer)
{
    static int count = 1;
    int size = header->caplen;
    char timestamp[64], buf[64];
    struct tm *tm;
    //ethernet header
    struct ethhdr *eth = (struct ethhdr*)buffer;

    //IP/ARP headers
    //have to offset buffer in order to make the correct data read; exclude ethernet header
    struct iphdr *iph = (struct iphdr*)(buffer + sizeof(struct ethhdr));
    struct ip6_hdr *ip6h = (struct ip6_hdr*)(buffer + sizeof(struct ethhdr));
    struct ether_arp *arp = (struct ether_arp*)(buffer + sizeof(struct ethhdr));

    //ICMP/UDP/TCP headers
    //have to offset buffer in order to make the correct data read; exclude ethernet header and IP header
    unsigned short iphdrlen = iph->ihl*4;
    struct tcphdr *tcp=(struct tcphdr*)(buffer + iphdrlen + sizeof(struct ethhdr));
    struct icmphdr *icmp = (struct icmphdr *)(buffer + iphdrlen  + sizeof(struct ethhdr));
    struct udphdr *udp = (struct udphdr*)(buffer + iphdrlen  + sizeof(struct ethhdr));

    //IPV6 headers
    struct tcphdr *tcp6=(struct tcphdr*)(buffer + IPV6_LEN + sizeof(struct ethhdr));
    struct icmphdr *icmp6 = (struct icmphdr *)(buffer + IPV6_LEN  + sizeof(struct ethhdr));
    struct udphdr *udp6 = (struct udphdr*)(buffer + IPV6_LEN + sizeof(struct ethhdr));
    
    //Timestamp
    //Inspired by: https://stackoverflow.com/questions/1551597/using-strftime-in-c-how-can-i-format-time-exactly-like-a-unix-timestamp
    if((tm = localtime(&header->ts.tv_sec)) != NULL)
    {
        strftime(buf, 64, "%Y-%m-%dT%H:%M:%S.%%06u%z", tm);
        snprintf(timestamp, 64, buf, &header->ts.tv_sec); 
    }

    //STARTING PRINT
    //MAC, timestamp and frame len print
    printf("\ntimestamp: %s\n", timestamp);
    printf("src MAC: %.2x:%.2x:%.2x:%.2x:%.2x:%.2x\n", eth->h_source[0], eth->h_source[1], eth->h_source[2], eth->h_source[3], eth->h_source[4], eth->h_source[5]);
    printf("dst MAC: %.2x:%.2x:%.2x:%.2x:%.2x:%.2x\n", eth->h_dest[0], eth->h_dest[1], eth->h_dest[2], eth->h_dest[3], eth->h_dest[4], eth->h_dest[5]);
    printf("frame length: %d bytes\n", size);
    
    //recognising different types of protocols
    switch(ntohs(eth->h_proto)){
        case 2048:
            //IPv4 header
            if(iph->protocol == 17){ //UDP
                print_IPv4(iph);
                printf("src port: %u\n", ntohs(udp->source));
                printf("dst port: %u\n", ntohs(udp->dest));
                printf("\n");

                int header_size =  sizeof(struct ethhdr) + iphdrlen;
                print_data(buffer , header->caplen); 
            }
            else if(iph->protocol == 6){ //TCP
                print_IPv4(iph);
                printf("src port: %u\n", ntohs(tcp->source));
                printf("dst port: %u\n", ntohs(tcp->dest));
                printf("\n");

                int header_size =  sizeof(struct ethhdr) + iphdrlen;
                print_data(buffer , header->caplen ); 
            }
            else if(iph->protocol == 1){ //ICMP
                //ICMP protocol has no port
                print_IPv4(iph);
                printf("type: %u\n", icmp->type);
                printf("code: %u\n", icmp->code);
                printf("\n");

                int header_size =  sizeof(struct ethhdr) + iphdrlen;
                print_data(buffer , header->caplen ); 
            }
            break;
        case 2054:
            //ARP has no port, only IPv4
            //IP parse and print
            memset(&source, 0, sizeof(source));
            uint32_t spa = arp->arp_spa[0] | (arp->arp_spa[1] << 8) | (arp->arp_spa[2] << 16) | (arp->arp_spa[3] << 24);
	        source.sin_addr.s_addr = spa;

            uint32_t tpa = arp->arp_tpa[0] | (arp->arp_tpa[1] << 8) | (arp->arp_tpa[2] << 16) | (arp->arp_tpa[3] << 24);
	        memset(&dest, 0, sizeof(dest));
	        dest.sin_addr.s_addr = tpa;
            printf("src IP: %s\n", inet_ntoa(source.sin_addr));
            printf("dst IP: %s\n", inet_ntoa(dest.sin_addr));
            printf("opcode: %u\n", ntohs(arp->ea_hdr.ar_op));
            printf("\n");

            int header_size =  sizeof(struct ethhdr);
            print_data(buffer , header->caplen); 
            break;  
        case 34525:
            //IPv6 header
            if(ip6h->ip6_ctlun.ip6_un1.ip6_un1_nxt == 17){ //UDP
                print_IPv6(ip6h);
                printf("src port: %u\n", ntohs(udp6->source));
                printf("dst port: %u\n", ntohs(udp6->dest));
                printf("\n");

                int header_size =  sizeof(struct ethhdr) + IPV6_LEN;
                print_data(buffer , header->caplen ); 
            }
            else if(ip6h->ip6_ctlun.ip6_un1.ip6_un1_nxt == 6){ //TCP
                print_IPv6(ip6h);
                printf("src port: %u\n", ntohs(tcp6->source));
                printf("dst port: %u\n", ntohs(tcp6->dest));
                printf("\n");

                int header_size =  sizeof(struct ethhdr) + IPV6_LEN;
                print_data(buffer , header->caplen ); 
            }
            else if(ip6h->ip6_ctlun.ip6_un1.ip6_un1_nxt == 1){ //ICMP
                //ICMP protocol has no port
                print_IPv6(ip6h);
                printf("type: %u\n", icmp6->type);
                printf("code: %u\n", icmp6->code);
                printf("\n");
                
                int header_size =  sizeof(struct ethhdr) + IPV6_LEN;
                print_data(buffer , header->caplen ); 
            }
            break;
        default:
            printf("Unsupported protocol!\n");
            break;
    }
}

int main(int argc, char **argv){
    char arg[100] = " ";
    int port = 0;
    int num = 1;
    bool pres_i = false;
    bool noarg_p = false;
    bool pres_p = false;
    bool pres_n = false;

    int j = 0;
    struct bpf_program bpf;
    pcap_if_t *alldevsp , *device;
    uint32_t sip, netmask;
	pcap_t *handle;
    char errbuf[100];
    char filter[1000] = "";
    char str[25];
    int header_length;

    if (pcap_findalldevs(&alldevsp, errbuf))
    {
        printf("%s\n", errbuf);
        return 1;
    }
    //ARGUMENT PARSING
    for (int i = 1; i < argc; i++){
        if(strcmp(argv[i] ,"-i")==0 || strcmp(argv[i] ,"--interface")==0){
            j = i;
            pres_i = true;
            ++j;
            if(argv[j] != NULL){
                if (argv[j][0] != '-'){
                    strcpy(arg, argv[j]);
                    ++i;
                }
                else{
                    print_devices(alldevsp);
                    return 0;
                }
            }
            else{
                print_devices(alldevsp);
                return 0;
            }
        }
        else if(strcmp(argv[i] ,"-p")==0){
            j = i;
            pres_p = true;
            ++j;
            if(argv[j] != NULL){
                if (argv[j][0] != '-'){
                    port = atoi(argv[j]);
                    ++i;
                    sprintf(str, " port %d", port);
                }
                else{
                    noarg_p = true;
                }
            }
            else{
                noarg_p = true;
            }
        }
        else if(strcmp(argv[i] ,"-n")==0){
            j = i;
            pres_n = true;
            ++j;
            if(argv[j] != NULL){
                if (argv[j][0] != '-'){
                    num = atoi(argv[j]);
                    ++i;
                }
            }
        }
        else if(strcmp(argv[i] ,"--tcp")==0 || strcmp(argv[i] ,"-t")==0){
            pres_tc = true;
        }
        else if(strcmp(argv[i] ,"--udp" )==0 || strcmp(argv[i] ,"-u")==0){
            pres_u = true;
        }
        else if(strcmp(argv[i] ,"--arp")==0){
            pres_a = true;
        }
        else if(strcmp(argv[i] ,"--icmp")==0){
            pres_ic = true;
        }
        else{
            printf("Spatny argument!!\n");
            return 1;
        }
    }

    //FILTER SETUP
    if ((pres_tc && pres_a && pres_ic && pres_u) ||(!pres_tc && !pres_a && !pres_ic && !pres_u)){
        show_all = true;
        if(pres_p == false){
            strcat(filter, "tcp or udp or arp or icmp");
        }
        else if(pres_p){
            char pom[150];
            sprintf(pom, "icmp or arp or tcp port %d or udp port %d", port, port);
            strcat(filter, pom);
        }
    }
    else{
        if(pres_tc){
            if(strlen(filter)!=0){
                strcat(filter, " or ");
            }
            strcat(filter, "tcp");
            if(pres_p){
                strcat(filter, str);
        }
        }
        if (pres_u){
            if(strlen(filter)!=0){
                strcat(filter, " or ");
            }
            strcat(filter, "udp");
            if(pres_p){
                strcat(filter, str);
        }
        }
        if (pres_a){
            if(strlen(filter)!=0){
                strcat(filter, " or ");
            }
            strcat(filter, "arp");
        }
        if (pres_ic){
            if(strlen(filter)!=0){
                strcat(filter, " or ");
            }
            strcat(filter, "icmp");
        }
    }

    if (!pres_i){
        print_devices(alldevsp);
        return 0;
    }
    //https://www.tcpdump.org/pcap.html
    //https://www.tcpdump.org/manpages/pcap.3pcap.html
    //PCAP HANDLE SETUP
    handle = pcap_open_live(arg, BUFSIZ, 1, 1000, errbuf);
    if(handle == NULL){
        printf("%s\n", errbuf);
        return 1;
    }
    pcap_set_immediate_mode(handle, 1);
    int link_type; 

    //support only devices with ethernet headers
    if (pcap_datalink(handle) != DLT_EN10MB) {
	    fprintf(stderr, "Device %s doesn't provide Ethernet headers - not supported\n", arg);
	    return 1;
    }

    //check the chosen device
    if(pcap_lookupnet(arg, &sip, &netmask, errbuf) < 0){
        printf("%s\n", pcap_geterr(handle));
        return 1;
    }

    //apply the filter
    if (pcap_compile(handle, &bpf, (char *) filter, 0, netmask)){
        printf("%s\n", pcap_geterr(handle));
        return 1;
    }

    if (pcap_setfilter(handle, &bpf) < 0) {
        printf("%s\n", pcap_geterr(handle));
        return 1;
    }

    //start sniffing
    //Inspired by: https://www.ibm.com/docs/en/aix/7.2?topic=p-pcap-loop-subroutine
    pcap_loop(handle, num, callback, errbuf);  

    pcap_close(handle);  
    return 0;
}