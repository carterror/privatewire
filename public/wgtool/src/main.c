#include <stdlib.h>
#include <stdio.h>
#include <unistd.h>
#include <sys/wait.h>
#include <errno.h>
#include <limits.h>
#include <string.h>
#include <netinet/in.h>
#include <time.h>
#include <fcntl.h>
#include <arpa/inet.h>
#include <sys/stat.h>

#define swapbo32(value) value = (value >> 24 & 0xFF) | (value >> 16 & 0xFF) << \
    8 | (value >> 8 & 0xFF) << 16 | (value & 0xFF) << 24;

#define STDSTR 256
#define PATH "/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"
#define LOGIPPOOL "Error creating IPv4 pool"
#define LOGGETIP "Error getting new IPv4"
#define LOGEXEC "Error executing binary"
#define LOGOPENFILE "Error opening file"
#define LOGSRVVAL "Error getting value from server configuration file"
#define LOGCPTMP "Error copying to temporary file"
#define LOGWRFILE "Error writing to a file"
#define LOGDELFILE "Error deleting file"
#define LOGRENAMFILE "Error renaming file"
#define LOGDEFRAGSRV "Error defragmenting server file"
#define LOGIPTOPOOL "Error returing ip to pool"
#define LOGGENZIP "Error generating user zip file"
#define LOGGENQR "Error generating user qr code"
#define LOGSTRPROC "Error processing string"
#define WGTOOLNFO "#This is an automatic file genarted by wgtool. Don't edit " \
    "it manually. "
#define USRTOKSTART "#USRLIST Users list start.\n\n"
#define PUBKEYTOK "#PUBKEY "
#define PUBADDRTOK "#PUBADDR "
#define KEYLEN 1024
#define LSTRLEN 8192
#define BIGBUFLEN 1024 * 1024
#define IPV4LEN 16
#define IPV4LEN_EXT 19
#define HOSTPREFIX "/32"
#define WGBIN "wg"
#define ZIPBIN "zip"
#define PNGEXT ".png"
#define QRENCODEBIN "qrencode"
#define TMPEXT ".tmp"
#define LOGEXT ".log"
#define CONFEXT ".conf"
#define ZIPEXT ".zip"
#define USRDELIM "#usr_delim::"
#define STRDELIM "::"
#define SRVOPNOF "-"
#define SERVICEBIN "service"
#define WGQ "wg-quick@"
#define POOLTOK "_pool.bin"
#define SRVRULEADD_BIN "wgtoolrule_add.sh"
#define SRVRULEDEL_BIN "wgtoolrule_del.sh"
#define RC_FAIL 1
#define RC_INARG 127
#define RC_EUID 126
#define CMD_ADDSRV "addserver"
#define CMD_LOGADDSRV_INIT "Staring addserver commmand"
#define CMD_LOGADDSRV_ENDE "Ending addserver command with errors"
#define CMD_LOGADDSRV_ENDS "Ending addserver command sucessfully"
#define CMD_ADDUSR "adduser"
#define CMD_LOGADDUSR_INIT "Staring adduser commmand"
#define CMD_LOGADDUSR_ENDE "Ending adduser command with errors"
#define CMD_LOGADDUSR_ENDS "Ending adduser command sucessfully"
#define CMD_DELSRV "delserver"
#define CMD_DELUSR "deluser"
#define CMD_LOGDELUSR_INIT "Staring deluser commmand"
#define CMD_LOGDELUSR_ENDE "Ending deluser command with errors"
#define CMD_LOGDELUSR_ENDS "Ending deluser command sucessfully"
#define CMD_USRON "useron"
#define CMD_LOGUSRON_INIT "Staring useron commmand"
#define CMD_LOGUSRON_ENDE "Ending useron command with errors"
#define CMD_LOGUSRON_ENDS "Ending useron command sucessfully"
#define CMD_USROFF "useroff"
#define CMD_LOGUSROFF_INIT "Staring useroff commmand"
#define CMD_LOGUSROFF_ENDE "Ending useroff command with errors"
#define CMD_LOGUSROFF_ENDS "Ending useroff command sucessfully"
#define CMD_SRVOP "serverop"
#define CMD_LOGSRVOP_INIT "Staring serverop commmand"
#define CMD_LOGSRVOP_ENDE "Ending serverop command with errors"
#define CMD_LOGSRVOP_ENDS "Ending serverop command sucessfully"
#define CMD_GETLOG "getlog"
#define CMD_SRVRULE "serverrule"
#define CMD_SRVRULE_INIT "Staring serverrule commmand"
#define CMD_SRVRULE_ENDE "Ending serverrule command with errors"
#define CMD_SRVRULE_ENDS "Ending serverrule command sucessfully"

static int excmd(const char *bin, char **argv, char *out, size_t outsz,
    char *in, size_t insz);
static int addusr(const char *srvnam, const char *dir, const char *usr, 
    const char *dns);
static int addsrv(const char *srvnam, const char *addr, const char *port,
    const char *pubaddr);
static int svippool(const char *path, const char *ip);
static int isbigendian(void);
static int getsrvvalue(const char *path, const char *tok, char *out);
static int ipfrompool(const char *srvnam, char *ip, unsigned int *paddr);
static void mklog(const char *srvnam, const char *fncnam, const char *err);
static int cptotmp(const char *src, const char *dst);
static int srvop(const char *srvnam, const char *op, const char *ofile);
static int delsrv(const char *srvnam);
static int delusr(const char *srvnam, const char *usr);
static int defragsrv(const char *srvnam);
static int addrtopool(const char *srvnam, const unsigned int *addr);
static int getlog(const char *srvnam, const char *path);
static int usroff(const char *srvnam, const char *usr);
static int usron(const char *srvnam, const char *usr);
static int genzip(const char *path);
static int genqr(const char *path);
static int srvrule(const char *srvnam, const char *ip, const char *iface, 
    const char *op);
static void usrcleanup(const char *srvnam, const char *dir, const char *usr,
    const unsigned int *addr);

static char lstr[LSTRLEN];
static char buf[BIGBUFLEN];
static char wgdir[PATH_MAX];

int main(int argc, char **argv)
{
    if (argc < 3)
        return RC_INARG;
    if (geteuid() != 0)
        return RC_EUID;
    setuid(0);
    strcpy(wgdir, *(argv + 1));
    setenv("PATH", PATH, 1);
    if (!strcmp(*(argv + 2), CMD_ADDSRV)) {
        if (argc < 7)
            return RC_INARG;
        mklog(*(argv + 3), "main", CMD_LOGADDSRV_INIT);
        if (addsrv(*(argv + 3), *(argv + 4), *(argv + 5), *(argv + 6)) == -1) {
            mklog(*(argv + 3), "main", CMD_LOGADDSRV_ENDE);
            return RC_FAIL;
        }
        mklog(*(argv + 3), "main", CMD_LOGADDSRV_ENDS);
    } else if (!strcmp(*(argv + 2), CMD_ADDUSR)) {
        if (argc < 7)
            return RC_INARG;
        mklog(*(argv + 3), "main", CMD_LOGADDUSR_INIT);
        if (addusr(*(argv + 3), *(argv + 4), *(argv + 5), *(argv + 6)) == -1) {
            mklog(*(argv + 3), "main", CMD_LOGADDUSR_ENDE);
            return RC_FAIL;
        }
        mklog(*(argv + 3), "main", CMD_LOGADDUSR_ENDS);
    } else if (!strcmp(*(argv + 2), CMD_DELSRV)) {
        if (argc < 4)
            return RC_INARG;
        if (delsrv(*(argv + 3)) == -1)
            return RC_FAIL;
    } else if (!strcmp(*(argv + 2), CMD_DELUSR)) {
        if (argc < 5)
            return RC_INARG;
        mklog(*(argv + 3), "main", CMD_LOGDELUSR_INIT);
        if (delusr(*(argv + 3), *(argv + 4)) == -1) {
            mklog(*(argv + 3), "main", CMD_LOGDELUSR_ENDE);
            return RC_FAIL;
        }
        mklog(*(argv + 3), "main", CMD_LOGDELUSR_ENDS);
    } else if (!strcmp(*(argv + 2), CMD_USRON)) {
        if (argc < 5)
            return RC_INARG;
        mklog(*(argv + 3), "main", CMD_LOGUSRON_INIT);
        if (usron(*(argv + 3), *(argv + 4)) == -1) {
            mklog(*(argv + 3), "main", CMD_LOGUSRON_ENDE);
            return RC_FAIL;
        }
        mklog(*(argv + 3), "main", CMD_LOGUSRON_ENDS);
    } else if (!strcmp(*(argv + 2), CMD_USROFF)) {
        if (argc < 5)
            return RC_INARG;
        mklog(*(argv + 3), "main", CMD_LOGUSROFF_INIT);
        if (usroff(*(argv + 3), *(argv + 4)) == -1) {
            mklog(*(argv + 3), "main", CMD_LOGUSROFF_ENDE);
            return RC_FAIL;
        }
        mklog(*(argv + 3), "main", CMD_LOGUSROFF_ENDS);
    } else if (!strcmp(*(argv + 2), CMD_SRVOP)) {
        if (argc < 6)
            return RC_INARG;
        mklog(*(argv + 3), "main", CMD_LOGSRVOP_INIT);
        if (srvop(*(argv + 3), *(argv + 4), *(argv + 5)) == -1) {
            mklog(*(argv + 3), "main", CMD_LOGSRVOP_ENDE);
            return RC_FAIL;
        }
        mklog(*(argv + 3), "main", CMD_LOGSRVOP_ENDS);
    } else if (!strcmp(*(argv + 2), CMD_GETLOG)) {
        if (argc < 5)
            return RC_INARG;
        if (getlog(*(argv + 3), *(argv + 4)) == -1)
            return RC_FAIL;
    } else if (!strcmp(*(argv + 2), CMD_SRVRULE)) {
        if (argc < 7)
            return RC_INARG;
        mklog(*(argv + 3), "main", CMD_SRVRULE_INIT);
        if (srvrule(*(argv + 3), *(argv + 4), *(argv + 5), *(argv + 6)) == -1) {
            mklog(*(argv + 3), "main", CMD_SRVRULE_ENDE);
            return RC_FAIL;
        }
        mklog(*(argv + 3), "main", CMD_SRVRULE_ENDS);
    }
    return 0;
}

static int addusr(const char *srvnam, const char *dir, const char *usr, 
    const char *dns)
{
    char ip[IPV4LEN_EXT];
    unsigned int addr;
    if (ipfrompool(srvnam, ip, &addr)) {
        mklog(srvnam, "addusr", LOGGETIP);
        return -1;
    }
    strcat(ip, HOSTPREFIX);
    char *argv[] = { WGBIN, "genkey", NULL };
    char privkey[KEYLEN], pubkey[KEYLEN];
    if (excmd(WGBIN, argv, privkey, sizeof privkey, NULL, 0)) {
        mklog(srvnam, "addusr", LOGEXEC);
        usrcleanup(srvnam, dir, usr, &addr);
        return -1;
    }
    char *chpt = strchr(privkey, '\n');
    if (chpt)
        *chpt = '\0';
    argv[1] = "pubkey";
    if (excmd(WGBIN, argv, pubkey, sizeof pubkey, privkey, strlen(privkey))) {
        mklog(srvnam, "addusr", LOGEXEC);
        usrcleanup(srvnam, dir, usr, &addr);
        return -1;
    }
    chpt = strchr(pubkey, '\n');
    if (chpt)
        *chpt = '\0';
    strcpy(lstr, wgdir);
    strcat(lstr, srvnam);
    char endpoint[STDSTR], srv_pubkey[LINE_MAX];
    if (getsrvvalue(lstr, PUBKEYTOK, srv_pubkey)) {
        mklog(srvnam, "addusr", LOGSRVVAL);
        usrcleanup(srvnam, dir, usr, &addr);
        return -1;
    }
    if (getsrvvalue(lstr, PUBADDRTOK, endpoint)) {
        mklog(srvnam, "addusr", LOGSRVVAL);
        usrcleanup(srvnam, dir, usr, &addr);
        return -1;
    }
    char *pt = strtok(endpoint, "/");
    strcat(pt, ":");
    if (getsrvvalue(lstr, "ListenPort = ", pt + strlen(pt))) {
        mklog(srvnam, "addusr", LOGSRVVAL);
        usrcleanup(srvnam, dir, usr, &addr);
        return -1;
    }
    sprintf(lstr, "%s\n\n[Interface]\nAddress = %s\nPrivateKey = %s\nDNS = "
        "%s\n\n[Peer]\nAllowedIPs = 0.0.0.0/0\nEndpoint = %s\nPublicKey = %s\n",
        WGTOOLNFO, ip, privkey, dns, endpoint, srv_pubkey);
    char tmp[PATH_MAX], path[PATH_MAX];
    strcpy(path, dir);
    strcat(path, "/");
    strcat(path, usr);
    strcat(path, CONFEXT);
    strcpy(tmp, path);
    strcat(tmp, TMPEXT);
    FILE *fs = fopen(tmp, "w+");
    if (!fs) {
        mklog(srvnam, "addusr", LOGOPENFILE);
        usrcleanup(srvnam, dir, usr, &addr);
        return -1;
    }
    int slen = strlen(lstr);
    if (fwrite(lstr, 1, slen, fs) != slen) {
        mklog(srvnam, "addusr", LOGWRFILE);
        fclose(fs);
        usrcleanup(srvnam, dir, usr, &addr);
        return -1;
    }
    fclose(fs);
    rename(tmp, path);
    if (genzip(path) == -1) {
        mklog(srvnam, "addusr", LOGGENZIP);
        usrcleanup(srvnam, dir, usr, &addr);
        return -1;
    }
    if (genqr(path) == -1) {
        mklog(srvnam, "addusr", LOGGENQR);
        usrcleanup(srvnam, dir, usr, &addr);
        return -1;
    }
    strcpy(path, wgdir);
    strcat(path, srvnam);
    strcpy(tmp, wgdir);
    strcat(tmp, srvnam);
    strcat(tmp, TMPEXT);
    if (cptotmp(path, tmp) == -1) {
        mklog(srvnam, "addusr", LOGCPTMP);
        usrcleanup(srvnam, dir, usr, &addr);
        return -1;
    }
    fs = fopen(tmp, "a");
    if (!fs) {
        mklog(srvnam, "addusr", LOGOPENFILE);
        usrcleanup(srvnam, dir, usr, &addr);
        return -1;
    }
    sprintf(lstr, "%s%s%s%u\n[Peer]\nPublicKey = %s\nAllowedIPs = %s\n%s%s"
        "\n\n", USRDELIM, usr, STRDELIM, addr, pubkey, ip, USRDELIM, usr);
    slen = strlen(lstr);
    if (fwrite(lstr, 1, slen, fs) != slen) {
        mklog(srvnam, "addusr", LOGWRFILE);
        fclose(fs);
        usrcleanup(srvnam, dir, usr, &addr);
        return -1;
    }
    fclose(fs);
    rename(tmp, path);
    return 0;
}

static int addsrv(const char *srvnam, const char *addr, const char *port,
    const char *pubaddr)
{
    char srvpath[PATH_MAX], tmp[PATH_MAX];
    strcpy(srvpath, wgdir);
    strcat(srvpath, srvnam);
    strcat(srvpath, POOLTOK);
    if (svippool(srvpath, addr)) {
        mklog(srvnam, "addsrv", LOGIPPOOL);
        return -1;
    }
    char *argv[] = { WGBIN, "genkey", NULL };
    char privkey[KEYLEN], pubkey[KEYLEN];
    if (excmd(WGBIN, argv, privkey, sizeof privkey, NULL, 0)) {
        mklog(srvnam, "addsrv", LOGEXEC);
        return -1;
    }
    char *chpt = strchr(privkey, '\n');
    if (chpt)
        *chpt = '\0';
    sprintf(lstr, "%s\n\n%s\n%s = %s\n%s = %s\n%s = %s\n", WGTOOLNFO, 
        "[Interface]", "Address", addr, "ListenPort", port, "PrivateKey", 
        privkey);
    argv[1] = "pubkey";
    if (excmd(WGBIN, argv, pubkey, sizeof pubkey, privkey, strlen(privkey))) {
        mklog(srvnam, "addsrv", LOGEXEC);
        return -1;
    }
    strcat(lstr, PUBKEYTOK);
    strcat(lstr, pubkey);
    strcat(lstr, PUBADDRTOK);
    strcat(lstr, pubaddr);
    strcat(lstr, "\n");
    strcpy(srvpath, wgdir);
    strcat(srvpath, srvnam);
    strcpy(tmp, srvpath);
    strcat(tmp, TMPEXT);
    FILE *fs = fopen(tmp, "w+");
    if (!fs) {
        mklog(srvnam, "addsrv", LOGOPENFILE);
        return -1;
    }
    int slen = strlen(lstr);
    if (fwrite(lstr, 1, slen, fs) != slen) {
        mklog(srvnam, "addsrv", LOGWRFILE);
        fclose(fs);
        unlink(tmp);
        return -1;
    }
    fprintf(fs, USRTOKSTART, strlen(USRTOKSTART));
    fclose(fs);
    rename(tmp, srvpath);
    return 0;
}

static int excmd(const char *bin, char **argv, char *out, size_t outsz, 
    char *in, size_t insz)
{
    int pd[2][2];
    int rc = pipe(pd[0]);
    rc += pipe(pd[1]);
    if (rc)
        return -1;
    pid_t pid = fork();
    if (pid == -1) {
        close(pd[0][0]);
        close(pd[0][1]);
        close(pd[1][0]);
        close(pd[1][1]);
        return -1;
    }
    if (!pid) {
        close(1);
        close(2);
        dup(pd[0][1]);
        dup(pd[0][1]);
        close(pd[0][0]);
        close(pd[0][1]);
        close(0);
        dup(pd[1][0]);
        close(pd[1][1]);
        close(pd[1][0]);
        rc = execvp(bin, argv);
        if (rc == -1)
            return -1;
    }
    close(pd[0][1]);
    close(pd[1][0]);
    rc = 0;   
    int bp = 0;
    if (in)
        while (insz > 0) {
            do
                rc = write(pd[1][1], in + bp, insz);
            while (rc == -1 && errno == EINTR);
            if (rc == -1) {
                close(pd[1][1]);
                close(pd[0][0]);
                return -1;
            }
            bp += rc;
            insz -= rc;
        }
    close(pd[1][1]);
    bp = 0;
    if (out) {
        while (rc = read(pd[0][0], out + bp, outsz)) {
            if (rc == -1 && errno == EINTR)
                continue;
            else if (rc == -1) {
                close(pd[0][0]);
                return -1;
            }
           bp += rc; 
           outsz -= rc;
           if (outsz < 0)
               return -1;
        }
        *(out + bp) = '\0';
    }
    close(pd[0][0]);
    int st;
    do
        rc = waitpid(pid, &st, 0);
    while (rc == -1 && errno == EINTR);
    if (st)
        return -1;
    else
        return 0;
}

static int svippool(const char *path, const char *ip)
{
    char sufix[IPV4LEN];
    strcpy(sufix, ip);
    char *ptok = strtok(sufix, "/");
    if (!ptok)
        return -1;
    unsigned int addr;
    inet_pton(AF_INET, ptok, &addr);
    ptok = strtok(NULL, "");
    if (!ptok)
        return -1;
    int net = atoi(ptok);
    if (net < 8 || net > 30)
        return -1;
    net = 32 - net;
    unsigned int host = 0, c = 0;
    for (; c < net; c++)
        host |= 1 << c;
    if (!isbigendian())
        swapbo32(addr);
    unsigned int st_addr = addr + 1, end_addr = addr + host - 1;
    char tmp[PATH_MAX];
    strcpy(tmp, path);
    strcat(tmp, TMPEXT);
    FILE *fs = fopen(tmp, "w+");
    if (!fs)
        return -1;
    for (c = st_addr; c < end_addr; c++) {
        addr = c;
        swapbo32(addr);
        int slen = sizeof addr;
        if (fwrite(&addr, 1, slen, fs) != slen) {
            fclose(fs);
            unlink(tmp);
            return -1;
        }
    }
    fclose(fs);
    if (rename(tmp, path) == -1)
        unlink(tmp);
    return 0;
}

static int isbigendian(void)
{
    int value = 1;
    char *pt = (char *) &value;
    if (*pt == 1)
        return 0;
    else
        return 1;
}

static int getsrvvalue(const char *path, const char *tok, char *out)
{
    FILE *fd = fopen(path, "r");
    if (!fd)
        return -1;
    int found = 0;
    char line[LINE_MAX];
    while (fgets(line, sizeof line, fd)) {
        if (strstr(line, tok)) {
            found = 1;
            break;
        }
    }
    if (!found) {
        fclose(fd);
        return -1;
    }
    strcpy(out, line + strlen(tok));
    char *pt = strchr(out, '\n');
    if (pt)
        *pt = '\0';
    fclose(fd);
    return 0;
}

static int ipfrompool(const char *srvnam, char *ip, unsigned int *paddr)
{
    strcpy(lstr, wgdir);
    strcat(lstr, srvnam);
    strcat(lstr, POOLTOK);
    FILE *fd = fopen(lstr, "r+");
    if (!fd)
        return -1;    
    unsigned int addr;
    if (fseek(fd, -sizeof addr, SEEK_END) == -1)
        return -1;
    long int pos = ftell(fd);
    if (fread(&addr, 1, sizeof addr, fd) != sizeof addr)
        return -1;
    *paddr = addr;
    inet_ntop(AF_INET, &addr, ip, INET_ADDRSTRLEN);
    if (ftruncate(fileno(fd), pos)) {
        fclose(fd);
        return -1;
    }
    fclose(fd);
    return 0;
}

static void mklog(const char *srvnam, const char *fncnam, const char *err)
{
    char path[PATH_MAX];
    strcpy(path, wgdir);
    strcat(path, srvnam);
    strcat(path, LOGEXT);
    FILE *fs = fopen(path, "a+");
    if (!fs)
        return;
    char log[LINE_MAX];
    time_t timer;
    char tmstr[STDSTR];
    struct tm* tm_info;
    timer = time(NULL);
    tm_info = localtime(&timer);
    strftime(tmstr, sizeof tmstr, "%Y-%m-%d %H:%M:%S", tm_info);
    sprintf(log, "[%s] Function \'%s\': %s\n", tmstr, fncnam, err);
    fwrite(log, 1, strlen(log), fs);
    fclose(fs);
}

static int cptotmp(const char *src, const char *dst)
{
    int fdsrc = open(src, O_RDONLY);
    if (fdsrc == -1)
        return -1;
    int fddst = open(dst, O_WRONLY | O_CREAT | O_TRUNC, S_IRWXU | S_IRGRP |
        S_IROTH);
    if (fddst == -1) {
        close(fdsrc);
        return -1;
    }
    int rd;
    while ((rd = read(fdsrc, buf, sizeof buf)) != 0) {
        if (rd == -1 && errno == EINTR)
            continue;
        else if (rd == -1) {
            close(fdsrc);
            close(fddst);
            unlink(dst);
            return -1;
        }
        int written = 0, wr;
        do {
            wr = write(fddst, buf + written, rd - written);
            if (wr == -1 && errno == EINTR)
                continue;
            else if (wr == -1) {
                close(fdsrc);
                close(fddst);
                unlink(dst);
                return -1;
            }
            written += wr;
        } while (written < rd);
    } 
    close(fdsrc);
    close(fddst);
    return 0;
}

static int srvop(const char *srvnam, const char *op, const char *ofile)
{
    char *argv[] = { SERVICEBIN, NULL, NULL, NULL };
    char srv[STDSTR];
    strcpy(srv, WGQ);
    strcat(srv, srvnam);
    char *chptr = strtok(srv, ".");
    argv[1] = chptr;
    argv[2] = (char *) op;
    excmd(SERVICEBIN, argv, lstr, sizeof lstr, NULL, 0);
    if (strcmp(ofile, SRVOPNOF)) {
        int fddst = open(ofile, O_WRONLY | O_CREAT | O_TRUNC, S_IRWXU 
            | S_IRGRP | S_IROTH);
        write(fddst, lstr, strlen(lstr));
        close(fddst);
    }
    return 0;
}

static int delsrv(const char *srvnam)
{
    strcpy(lstr, wgdir);
    strcat(lstr, srvnam);
    int rc = unlink(lstr);
    if (rc != 0 && errno != ENOENT) {
        mklog(srvnam, "delsrv", LOGDELFILE);
        return -1;
    }
    strcat(lstr, LOGEXT);
    unlink(lstr); 
    strcpy(lstr, wgdir);
    strcat(lstr, srvnam);
    strcat(lstr, POOLTOK);
    unlink(lstr);
    return 0;
}

static int delusr(const char *srvnam, const char *usr)
{
    char src[PATH_MAX], dst[PATH_MAX];
    strcpy(src, wgdir);
    strcat(src, srvnam);
    strcpy(dst, wgdir);
    strcat(dst, srvnam);
    strcat(dst, TMPEXT);
    char schr[LINE_MAX];
    FILE *fsrc = fopen(src, "r");
    if (!fsrc) {
        mklog(srvnam, "delusr", LOGOPENFILE);
        return -1;
    }
    FILE *fdst = fopen(dst, "w+");
    if (!fdst) {
        mklog(srvnam, "delusr", LOGOPENFILE);
        fclose(fsrc);
        return -1;
    }
    int flg = 0;
    strcpy(schr, USRDELIM);
    strcat(schr, usr);
    unsigned int addr = 0;
    while (fgets(lstr, sizeof lstr, fsrc)) {
        if (strstr(lstr, schr)) {
            if (!addr)
                addr = strtoul(lstr + strlen(schr) + strlen(STRDELIM), NULL, 0);
            flg = flg ? 0 : 1;
            continue;
        }
        if (flg)
            continue;
        if (fputs(lstr, fdst) == EOF) {
            mklog(srvnam, "delusr", LOGWRFILE);
            fclose(fsrc);
            fclose(fdst);
            unlink(dst);
            return -1;
        }
    }
    fclose(fsrc);
    fclose(fdst);
    if (rename(dst, src) == -1) {
        mklog(srvnam, "delusr", LOGRENAMFILE);
        unlink(dst);
        return -1;
    }
    if (defragsrv(srvnam)) {
        mklog(srvnam, "delusr", LOGDEFRAGSRV);
        return -1;
    }
    if (addr && addrtopool(srvnam, &addr)) {
        mklog(srvnam, "delusr", LOGIPTOPOOL);
        return -1;
    }
    return 0;
}

static int defragsrv(const char *srvnam)
{
    char src[PATH_MAX], dst[PATH_MAX];
    strcpy(src, wgdir);
    strcat(src, srvnam);
    strcpy(dst, wgdir);
    strcat(dst, srvnam);
    strcat(dst, TMPEXT);
    char schr[LINE_MAX];
    FILE *fsrc = fopen(src, "r");
    if (!fsrc)
        return -1;
    FILE *fdst = fopen(dst, "w+");
    if (!fdst) {
        fclose(fsrc);
        return -1;
    }
    int nl = 0;
    while (fgets(lstr, sizeof lstr, fsrc)) {        
        if (!strcmp(lstr, "\n"))
            nl++;
        else 
            nl = 0;
        if (nl <= 1 && fputs(lstr, fdst) == EOF) {
            fclose(fsrc);
            fclose(fdst);
            unlink(dst);
            return -1;
        }
    }
    fclose(fsrc);
    fclose(fdst);
    if (rename(dst, src) == -1)
        unlink(dst);
    return 0;
}

static int addrtopool(const char *srvnam, const unsigned int *addr)
{
    char src[PATH_MAX], dst[PATH_MAX];
    strcpy(src, wgdir);
    strcat(src, srvnam);
    strcat(src, POOLTOK);
    int fdsrc = open(src, O_WRONLY);
    if (fdsrc == -1)
        return -1;
    if (lseek(fdsrc, 0, SEEK_END) == -1)
        return -1;
    int rc, bp = 0;
    while (bp < sizeof *addr) {
        do
            rc = write(fdsrc, addr + bp, sizeof *addr - bp);
        while (rc == -1 && errno == EINTR);
        if (rc == -1)
            return -1;
        bp += rc;
    }
    close(fdsrc);
    return 0;
}

static int getlog(const char *srvnam, const char *path)
{
    char src[PATH_MAX];
    strcpy(src, wgdir);
    strcat(src, srvnam);
    strcat(src, LOGEXT);
    cptotmp(src, path);
    return 0;
}

static int usroff(const char *srvnam, const char *usr)
{
    char src[PATH_MAX], dst[PATH_MAX];
    strcpy(src, wgdir);
    strcat(src, srvnam);
    strcpy(dst, wgdir);
    strcat(dst, srvnam);
    strcat(dst, TMPEXT);
    char schr[LINE_MAX];
    FILE *fsrc = fopen(src, "r");
    if (!fsrc) {
        mklog(srvnam, "delusr", LOGOPENFILE);
        return -1;
    }
    FILE *fdst = fopen(dst, "w+");
    if (!fdst) {
        mklog(srvnam, "delusr", LOGOPENFILE);
        fclose(fsrc);
        return -1;
    }
    int flg = 0;
    strcpy(schr, USRDELIM);
    strcat(schr, usr);
    while (fgets(lstr, sizeof lstr, fsrc)) {
        if (strstr(lstr, schr))
            flg = flg ? 0 : 1;
        if (flg) {
            char ln[LINE_MAX];
            *ln = '\0';
            if (*lstr != '#')
                strcpy(ln, "#");
            strcat(ln, lstr);
            if (fputs(ln, fdst) == EOF) {
                mklog(srvnam, "delusr", LOGWRFILE);
                fclose(fsrc);
                fclose(fdst);
                unlink(dst);
                return -1;
            }
        } else if (fputs(lstr, fdst) == EOF) {
                mklog(srvnam, "delusr", LOGWRFILE);
                fclose(fsrc);
                fclose(fdst);
                unlink(dst);
                return -1;
            }
    }
    fclose(fsrc);
    fclose(fdst);
    if (rename(dst, src) == -1) {
        mklog(srvnam, "delusr", LOGRENAMFILE);
        unlink(dst);
        return -1;
    }
    return 0;
}

static int usron(const char *srvnam, const char *usr)
{
    char src[PATH_MAX], dst[PATH_MAX];
    strcpy(src, wgdir);
    strcat(src, srvnam);
    strcpy(dst, wgdir);
    strcat(dst, srvnam);
    strcat(dst, TMPEXT);
    char schr[LINE_MAX];
    FILE *fsrc = fopen(src, "r");
    if (!fsrc) {
        mklog(srvnam, "delusr", LOGOPENFILE);
        return -1;
    }
    FILE *fdst = fopen(dst, "w+");
    if (!fdst) {
        mklog(srvnam, "delusr", LOGOPENFILE);
        fclose(fsrc);
        return -1;
    }
    int flg = 0;
    strcpy(schr, USRDELIM);
    strcat(schr, usr);
    while (fgets(lstr, sizeof lstr, fsrc)) {
        if (strstr(lstr, schr)) {
            flg = flg ? 0 : 1;
            if (fputs(lstr, fdst) == EOF) {
                mklog(srvnam, "delusr", LOGWRFILE);
                fclose(fsrc);
                fclose(fdst);
                unlink(dst);
                return -1;
            }
            continue;
        }
        if (flg && *lstr == '#') {
            if (fputs(lstr + 1, fdst) == EOF) {
                mklog(srvnam, "delusr", LOGWRFILE);
                fclose(fsrc);
                fclose(fdst);
                unlink(dst);
                return -1;
            }
        } else if (fputs(lstr, fdst) == EOF) {
            mklog(srvnam, "delusr", LOGWRFILE);
            fclose(fsrc);
            fclose(fdst);
            unlink(dst);
            return -1;
        }
    }
    fclose(fsrc);
    fclose(fdst);
    if (rename(dst, src) == -1) {
        mklog(srvnam, "delusr", LOGRENAMFILE);
        unlink(dst);
        return -1;
    }
    return 0;    
}

static int genzip(const char *path)
{
    char dst[PATH_MAX];
    strcpy(dst, path);
    strcat(dst, ZIPEXT);
    char *argv[] = { ZIPBIN, "-j", NULL, NULL, NULL };
    argv[2] = dst;
    argv[3] = (char *) path;
    return excmd(ZIPBIN, argv, lstr, sizeof lstr, NULL, 0);
}

static int genqr(const char *path)
{
    int fd = open(path, O_RDONLY);
    if (fd == -1)
        return -1;
    int rc, bp = 0, bufsz;
    struct stat st;
    stat(path, &st);
    bufsz = st.st_size;
    char *buf = malloc(bufsz);
    if (!buf) {
        close(fd);
        return -1;
    }
    while (rc = read(fd, buf + bp, bufsz)) {
        if (rc == -1 && errno == EINTR)
            continue;
        else if (rc == -1) {
            close(fd);
            free(buf);
            return -1;
        }
        bp += rc; 
        bufsz -= rc;
    }
    char dst[PATH_MAX];
    strcpy(dst, path);
    strcat(dst, PNGEXT); 
    char *argv[] = { QRENCODEBIN, "-t", "PNG", "-o", NULL, NULL };
    argv[4] = dst;
    if (excmd(QRENCODEBIN, argv, NULL, 0, buf, strlen(buf))) {
        free(buf);
        close(fd);
        return -1;
    }
    free(buf);
    close(fd);
    return 0;
}

static int srvrule(const char *srvnam, const char *ip, const char *iface,
    const char *op)
{
    char sufix[IPV4LEN];
    strcpy(sufix, ip);
    char *ptok = strtok(sufix, "/");
    if (!ptok)
        return -1;
    unsigned int addr;
    inet_pton(AF_INET, ptok, &addr);
    if (!isbigendian())
        swapbo32(addr);
    addr--;
    if (!isbigendian())
        swapbo32(addr);
    ptok = strtok(NULL, "");
    if (!ptok) {
        mklog(srvnam, "srvrule", LOGSTRPROC);
        return -1;
    }
    int net = strtol(ptok, NULL, 0);
    inet_ntop(AF_INET, &addr , sufix, INET_ADDRSTRLEN);
    sprintf(lstr, "%s/%d", sufix, net);
    char *chpt;
    if (!strcmp(op, "add")) {
        char *argv[] = { SRVRULEADD_BIN, NULL, NULL, NULL };
        argv[1] = lstr;
        argv[2] = (char *) iface;
        excmd(SRVRULEADD_BIN, argv, buf, sizeof buf, NULL, 0);
        chpt = strchr(buf, '\0');
        if (chpt && strlen(buf) > 0)
            *(chpt - 1) = '\0';
        mklog(srvnam, "srvrule", buf);
    } else if (!strcmp(op, "del")) {
        char *argv[] = { SRVRULEDEL_BIN, NULL, NULL, NULL };
        argv[1] = lstr;
        argv[2] = (char *) iface;
        excmd(SRVRULEDEL_BIN, argv, buf, sizeof buf, NULL, 0);
        chpt = strchr(buf, '\n');
        chpt = strchr(buf, '\0');
        if (chpt && strlen(buf) > 0)
            *(chpt - 1) = '\0';
        mklog(srvnam, "srvrule", buf);
    }
    return 0;
}

static void usrcleanup(const char *srvnam, const char *dir, const char *usr,
    const unsigned int *addr)
{
    char clpath[PATH_MAX], base[PATH_MAX];
    strcpy(base, dir);
    strcat(base, "/");
    strcat(base, usr);
    strcat(base, CONFEXT);
    unlink(base);
    strcpy(clpath, base);
    strcat(clpath, PNGEXT);
    unlink(clpath);
    strcpy(clpath, base);
    strcat(clpath, ZIPEXT);
    unlink(clpath);
    strcpy(clpath, base);
    strcat(clpath, TMPEXT);
    unlink(clpath);
    strcpy(clpath, dir);
    strcat(clpath, "/");
    strcat(clpath, srvnam);
    strcat(clpath, TMPEXT);
    unlink(clpath);
    addrtopool(srvnam, addr);
}
