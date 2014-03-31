<?php
set_time_limit(0);
$server = "irc.freenode.org";
$channels = "#goeosbottest";
$port = 6667;
$nick = "dan|el";
$connection = fsockopen("$server", $port);

fputs ($connection, "USER $nick $nick $nick $nick :$nick\n");//lulz
fputs ($connection, "NICK $nick\n");
fputs ($connection, "JOIN {$channels}\n");
$handle = fopen ("php://stdin","r");
stream_set_blocking($handle,0);
echo "->  ";
while(1)
{
$line = fgets($handle);
if($line)
{
fputs($connection, "PRIVMSG {$channels} :{$line}");
echo "->  ";
}
usleep(500000);
}

?>
