curl -vsILo /dev/null -A '' -e 'http://www.zhe800.com/;auto' http://out.zhe800.com/ju/deal/zhutanbaos_22729 

tcpdump -Ac5 'tcp port 80 and (((ip[2:2] - ((ip[0]&0xf)<<2)) - ((tcp[12]&0xf0)>>2)) != 0)'

