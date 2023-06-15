CC=gcc
CFLAGS=-std=gnu99 -g


all: target_r target_s
sender: target_s
receiver: target_r

target_s: sender/dns_sender.c
	$(CC) $(CFLAGS) sender/dns_sender.c -o sender/dns_sender

target_r: receiver/dns_receiver.c
	$(CC) $(CFLAGS) receiver/dns_receiver.c -o receiver/dns_receiver

.PHONY:
clean: cleanr cleans

cleanr:
	rm -f *.o receiver/dns_receiver
cleans:
	rm -f *.o sender/dns_sender