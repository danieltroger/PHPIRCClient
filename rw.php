<?php
error_reporting(0);
date_default_timezone_set("Europe/Stockholm");
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
stream_set_blocking($connection,0);
//echo "->  ";
while(1)
{
$data = fgets($connection);
if($data)
{

        $a1 = explode(' ', $data);
		$a2 = explode(':', $a1[3]);
		$a3 = explode('@', $a1[0]);
		$a4 = explode('!', $a3[0]);
		$a5 = explode(':', $a4[0]);
		$a6 = explode(':', $data);
        $user = $a5[1];
        $inchannel = $a1[2];
$args = NULL; for ($i = 4; $i < count($a1); $i++) {$args .= $a1[$i] . ' ';}
	$all = substr($args, 0, -1);
		if($a1[0] == "PING"){
			fputs($connection, "PONG ".$a1[1]."\n");
		}       
$a13 = substr($a1[3],1);
$log = date("H:i:s ") . $user . ": " . $a13 . " " . $all;
echo $log;
}
$line = fgets($handle);
if($line)
{
if($line[0] == "/")
{
$cmd = explode("/",$line);
unset($cmd[0]);
$cmd = implode("/",$cmd);
$cmd = str_replace("\n","",$cmd);
fputs($connection, "{$cmd}\n");
if($cmd == "quit")
{
die();
}
}
else
{
fputs($connection, "PRIVMSG {$channels} :{$line}");
}
//echo "->  ";
}
usleep(50000);
}

?>
