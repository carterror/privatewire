* This is an automation tool for the wireguard vpn server *

It requires several softwares to work.
    -1 zip (to zip the user profile)
    -2 qrencode (to generate qr of the user profile)
    -3 wg (to manage the wireguard server)
This softwares must be in the $PATH environment variable

* Commands *

    * addserver (add new server)
    * adduser (add new user to a server)
    * delserver (del a server)
    * deluser (del a user from a server)
    * useron (activate a user from a server)
    * useroff (deactivate a user from a server)
    * serverop (stop | start | status for a server)
    * getlog (get log from a server)
    * serverrule (execute script firewall rules for a server)
    
* Usage *

    addserver
    ./release/wgtool /etc/wireguard addserver wgX.conf 10.0.0.1/24 12345
    
    1 param: Directory for server configuration files
    2 param: Command name
    3 param: Filename for the server configuration Filename (must end in .conf)
    5 param: Name for the server
    6 param: Port for wireguard listening
    
    adduser
    ./release/wgtool /etc/wireguard adduser wgX.conf /dir-for-user-profile bill 8.8.8.8
    
    1 param: Directory for server configuration files
    2 param: Command name
    3 param: Filename for the server configuration Filename (must end in .conf)
    4 param: Directory to store the user profile (bill.conf bill.conf.zip bill.conf.png)
    5 param: Name for the user
    6 param: DNS for address resolution for the user
    
    delserver
    ./release/wgtool /etc/wireguard delserver wgX.conf
    
    1 param: Directory for server configuration files
    2 param: Command name
    3 param: Filename for the server configuration Filename (must end in .conf)
    
    deluser
    ./release/wgtool /etc/wireguard deluser wgX.conf bill
    
    1 param: Directory for server configuration files
    2 param: Command name
    3 param: Filename for the server configuration Filename (must end in .conf)
    4 param: Name of the user to be deleted
    
    useron
    ./release/wgtool /etc/wireguard useron wgX.conf bill
    
    1 param: Directory for server configuration files
    2 param: Command name
    3 param: Filename for the server configuration Filename (must end in .conf)
    4 param: Name of the user to be activated
    
    useroff
    ./release/wgtool /etc/wireguard useroff wgX.conf bill
    
    1 param: Directory for server configuration files
    2 param: Command name
    3 param: Filename for the server configuration Filename (must end in .conf)
    4 param: Name of the user to be deactivated
    
    serverop
    ./release/wgtool /etc/wireguard serverop wgX.conf (start | stop | restart | status) (./stdout.log | -)
    
    1 param: Directory for server configuration files
    2 param: Command name
    3 param: Filename for the server configuration Filename (must end in .conf)
    4 param: The operation to be executed (start or stop or restart or status_) 
    5 param: An optional file to write the server result output. If - is speciefied no file will be used to save the output

    getlog
    ./release/wgtool /etc/wireguard getlog wgX.conf /etc/somedir/logfile
    
    1 param: Directory for server configuration files
    2 param: Command name
    3 param: Filename for the server configuration Filename (must end in .conf)
    4 param: Filename to save the server log
    
    serverrule
    ./release/wgtool /etc/wireguard serverrule wgX.conf 10.0.0.1/24 eth0 (add | del)
    
    1 param: Directory for server configuration files
    2 param: Command name
    3 param: Filename for the server configuration Filename (must end in .conf)
    4 param: Server ip with range.
    5 param: Interface of the server in which the rule will be applied
    6 param: Option to be executed (add or del)
    
    If the user may or may not create scripts to be executed when add or del is specifed
        *The file name for the add rule is wgtoolrule_add.sh
        *The file name for the del rule is wgtoolrule_del.sh
        
    In those files the user can specify the rules for the firewall. Thoes file should hace executing permision and must be in a directory that can be accessed from $PATH environment variable
    
    
