CC=gcc
CFLAGS=-std=gnu99 -g -Wextra -Wall
TARGET=hinfosvc

all: $(TARGET)

$(TARGET): project1_server.o
	$(CC) $(CFLAGS) $^ -o $@
project1_server.o: project1_server.c
	$(CC) $(CFLAGS) -c -o $@ $<
.PHONY:
clean:
	rm -f *.o $(TARGET)