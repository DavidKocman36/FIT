# Popis
Sniffer packtů, psaný v jazyce C, je program, který na daném rozhraní a popř. portu, zachytává packety, které jsou filtrovány vloženými argumenty a následně tyhle packety jsou vypsány.

# Spuštění

```
sudo ./ipk-sniffer [ARGUMENTY]
```

například:

```
$ sudo ./ipk-sniffer -i eth0 -n 5
```
popř. s rootovskými právy:
```
# ./ipk-sniffer -i eth0 -n 5
```

## Seznam argumentů
1. -i nebo --interface: rozhraní, na kterém se bude poslouchat
2. -p: filtrování podle portu
3. -t nebo --tcp: pouze TCP packety
4. -u nebo --udp: pouze UDP packety
5. --icmp: pouze ICMP packety
6. --arp: pouze ARP rámce
7. -n: počet paketů na zobrazení

# Seznam odevzdyných souborů

1. Makefile
2. sniffer.c - zdrojový soubor
3. README.md
4. manual.pdf - soubor s dokumentací
