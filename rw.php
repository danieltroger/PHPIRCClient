
<?php
error_reporting(0);
set_time_limit(0);
$server = "irc.freenode.org";
$channels = "#goeosbottest";
$port = 6667;
$nick = "dan|el";
$connection = fsockopen("$server", $port);

fputs ($connection, "USER $nick $nick $nick $nick :$nick\n");//lulz
fputs ($connection, "NICK $nick\n");
fputs ($connection, "JOIN {$channels}\n");
while(1)
{
while($data = fgets($connection)){
		flush();
        
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
if($inchannel == $channels)
{
$a13 = substr($a1[3],1);
$log = date("m-d-Y H:i:s \G\M\T P ") . $user . ": " . $a13 . " " . $all;
echo $log . "->  ";
$handle = fopen ("php://stdin","r");
$line = fgets($handle);
fputs($connection, "PRIVMSG {$channels} :{$line}");
fclose($handle);
}

}
}
?>
