CC=gcc
CFLAGS=-std=gnu99 -g
TARGET=ipk-sniffer

all: $(TARGET)

$(TARGET): sniffer.o
	$(CC) $(CFLAGS) $^ -lpcap -o $@
sniffer.o: sniffer.c
	$(CC) $(CFLAGS) -c -o $@ $<
.PHONY:
clean:
	rm -f *.o $(TARGET)