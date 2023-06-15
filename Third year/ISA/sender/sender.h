/**
 * ISA project: DNS tunnel
 * File: sender.h
 * Author: David Kocman, xkocma08
*/

#ifndef sender_dot_h
#define sender_dot_h

#include <stdio.h> 
#include <stdlib.h> 
#include <unistd.h> 
#include <string.h> 
#include <sys/types.h> 
#include <sys/socket.h> 
#include <arpa/inet.h> 
#include <netinet/in.h> 
#include <sys/time.h>

#include "dns_sender_events.h"
#include "dns_sender_events.c"

#include "custom_dns.h"

#define DNS_PORT 53
#define CHARS_IN_QRY 44
/*This file contains function declarations and important includes for the file sender.c*/

/**
 * This method parses command line arguments.
 * 
 * @param argc Number of command line arguments
 * @param argv Command line arguments
 * @param upstream_dns_ip IP on a remote dns server
 * @param base_host Base host domain
 * @param dst_filepath Name of the file that the data are supposed to be written into on the server
 * @param src_filepath Source of the sent data
 * 
 * @return 1 on failure and 0 on success
*/
int get_args(int argc, char *argv[], char *upstream_dns_ip, char* base_host, char* dst_filepath, char* src_filepath);

/**
 * This method gets the implicit DNS server address of this PC if upstream_dns_ip is not provided
 * 
 * @param upstream_dns_ip The variable that the address is written into
 * 
 * @return 1 on failure and 0 on success
*/
int get_address(char *upstream_dns_ip);

/**
 * This method converts encoded data to DNS friendly format
 * 
 * @param dns The "Name" part of DNS question. Output string
 * @param host The string that is to be converted
 * 
 * I am not the author of this code!
 * source: https://www.binarytides.com/dns-query-code-in-c-with-linux-sockets/
*/
void ChangetoDnsNameFormat(unsigned char* dns,unsigned char* host);

/**
 * Sends a DNS packet
 * 
 * @param qname DNS name
 * @param dns Dns header
 * @param qinfo DNS question info
 * @param sockfd Client's socket
 * @param id ID of the DNS packet
 * @param server_address Server info that the packet is sent to
 * @param buf Buffer for the UDP packet
 * @param dst_filepath Name of the file that the date are going to be written in on the server
 * @param coded Encoded data
 * @param size Size of encoded data
*/
void send_packet(char* qname, struct DNS_HEADER *dns, struct QUESTION *qinfo, int sockfd, int id, struct sockaddr_in server_address, unsigned char* buf, char* dst_filepath, char* coded, int size);

#endif