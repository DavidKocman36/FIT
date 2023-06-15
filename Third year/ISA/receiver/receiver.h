/**
 * ISA project: DNS tunnel
 * File: receiver.h
 * Author: David Kocman, xkocma08
*/

#ifndef receiver_dot_h
#define receiver_dot_h
#include <stdio.h>
#include <arpa/inet.h>
#include <errno.h>
#include <sys/socket.h>
#include <string.h>
#include <dirent.h>
#include <sys/stat.h>
#include <unistd.h>
#include <ctype.h>

#include "custom_dns.h"

#include "dns_receiver_events.c"
#include "dns_receiver_events.h"

#define DNS_PORT 53

//had to redefine MSG_CONFIRM because EVA doesnt support it 
enum
{
    MSG_CONF = 0x800, /* Confirm path validity.  */
    #define MSG_CONF MSG_CONF
};

/*This file contains function declarations and important includes for the file receiver.c*/

/**
 * Converts DNS query friendly name to query with dots
 * 
 * @param data Data to be converted
 * 
 * NOT MY CODE, modified from:
 * https://stackoverflow.com/questions/15444567/removing-spaces-and-special-characters-from-string
*/
void pretty_print(char* data);

/**
 * Wrtites data to the corresponding file located at the given directory
 * 
 * @param dst_filename Name of the file specified by client
 * @param dst_filepath Name of te directory specified by server
 * @param data Data to be stored
 * @param path Absolute to the designated file
 * 
 * @return 1 if an error occurs else 0
*/
int write_data(char *dst_filename, char *dst_filepath, char *data, char* path);

/**
 * Gets data from DNS query.
 * 
 * @param buf Buffer containing received packet
 * @param qinfo Question info
 * @param qname Pointer to the qname string 
*/
char *get_qname(char* buf,  struct QUESTION *qinfo, unsigned char *qname);

#endif