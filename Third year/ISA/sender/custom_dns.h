#ifndef dns_dot_h
#define dns_dot_h
    /**
     * A header file made for dns header definition
     * source: https://www.binarytides.com/dns-query-code-in-c-with-linux-sockets/
     * 
     * I am not the author of this code.
    */
    struct DNS_HEADER
    {
        unsigned short id; // identification number

        unsigned char rd :1; // recursion desired
        unsigned char tc :1; // truncated message
        unsigned char aa :1; // authoritive answer
        unsigned char opcode :4; // purpose of message
        unsigned char qr :1; // query/response flag

        unsigned char rcode :4; // response code
        unsigned char cd :1; // checking disabled
        unsigned char ad :1; // authenticated data
        unsigned char z :1; // its z! reserved
        unsigned char ra :1; // recursion available

        unsigned short q_count; // number of question entries
        unsigned short ans_count; // number of answer entries
        unsigned short auth_count; // number of authority entries
        unsigned short add_count; // number of resource entries
    };

    //Constant sized fields of query structure
    struct QUESTION
    {
        unsigned short qtype;
        unsigned short qclass;
    };

    //Structure of a Query
    typedef struct
    {
        unsigned char *name;
        struct QUESTION *ques;
    } QUERY;
    //
#endif