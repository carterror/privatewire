#Release profile

obj = release/obj/main.o 
	

CCX = gcc -o
CC = gcc -c

release: $(obj) 
	$(CCX) release/wgtool $(obj) $(libs)

release/obj/main.o: src/main.c
	$(CC) src/main.c -o release/obj/main.o $(cflags) 

	
# Debug profile

dbg = debug/obj/main.o 

CCGX = gcc -g -DDEBUG -o
CCG = gcc -g -DDEBUG -c
	
debug: $(dbg) 
	$(CCGX) debug/wgtool $(dbg) $(libs)

debug/obj/main.o: src/main.c
	$(CCG) src/main.c -o debug/obj/main.o $(cflags) 
	
#Others rules

prepare:
	mkdir -p debug release debug/obj release/obj

clean:
	rm -R -f $(obj) $(dbg) release/obj/* debug/obj/* release/wgtool debug/wgtool

install:
	release/clean.sh
	cp release/wgtoolrule_add.sh /usr/bin/
	cp release/wgtoolrule_del.sh /usr/bin/
	cp release/wgtool /usr/bin/
	chown root:root /usr/bin/wgtool
	chmod 4777 /usr/bin/wgtool
