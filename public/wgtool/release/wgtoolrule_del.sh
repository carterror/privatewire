#!/bin/sh

iptables -t nat -D POSTROUTING -s $1 -o $2 -j MASQUERADE
iptables-save > /etc/iptables/rules.v4

exit 0
