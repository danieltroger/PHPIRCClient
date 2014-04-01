<?php
error_reporting(0); //*disable dirty error messages in the chatt
date_default_timezone_set("Europe/Stockholm"); //*set europe timezone
set_time_limit(0);//*ehhh idk y
$server = "irc.freenode.org"; //* nah the server to connect to
$channels = "#goeosbottest"; //* it should be channel, atm only one channel is supported
$port = 6667; //* default irc port
$nick = "dan|el"; //*nah the nick
$connection = fsockopen("$server", $port); //*open a socket

fputs ($connection, "USER $nick $nick $nick $nick :$nick\n");//*connect as a user
fputs ($connection, "NICK $nick\n"); //*set a nick
fputs ($connection, "JOIN {$channels}\n"); //* and join the channel
$handle = fopen ("php://stdin","r"); //* open stdin as resource
stream_set_blocking($handle,0); //* turn streamblocking off, that we can check both resources in one loop
stream_set_blocking($connection,0); //*for both resources
//echo "->  ";
while(1) //*the while loop that runs eaech 50ms
{
$data = fgets($connection); //*get and
if($data) //* look for new data in the connection
{

        $a1 = explode(' ', $data); //*some variables
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
if($a1[3][0] == ":")
{
$a13 = substr($a1[3],1);
}
else
{
$a13 = $a1[3];
}
$log = date("H:i:s ") . $user . ": " . $a13 . " " . $all; //*define the output
echo $log; //*and output it
}
$line = fgets($handle); //* get and 
if($line) //* look for new data at stdin
{
if($line[0] == "/") //* check if the data is an irc command
{
$line = str_replace("\n","",$line); //* if it is, remove all \n that we can use the commands later
$conts = explode(" ",$line); //* then explode it by " "
$cmd = explode("/",$conts[0]); //* and explode the first "word" by slashes
unset($cmd[0]); //* and remove the first one ('cause we can't unset character offsets)
$cmd = implode("/",$cmd); //* implode it by slashes again
$cargs = NULL; for ($i = 1; $i < count($conts); $i++) {$cargs .= $conts[$i] . ' ';} //* set $cargs to the arguments
if($cmd == "me") //* if the command is /me
{
$wtp = "PRIVMSG {$channels} :\001ACTION {$cargs}\001\n"; //*define an \001ACTION (a /me)
fputs($connection, $wtp); //*and send it to the server
}
elseif($cmd == "colormsg") //*if the command is colormsg
{
$arguments = explode(" ",$cargs); //* explode the arguments by " "
$color = $arguments[0]; //* to get out the color
unset($arguments[0]); //*then unset the color
$arguments = implode(" ",$arguments); //* to implode the arguments that we have the text to say
if($color == "red") //*if the color is red
{
fputs($connection, "PRIVMSG {$channels} :\x034{$arguments}\x03\n"); //* say the text in red
}
elseif($color == "green") //* if it's green
{
fputs($connection, "PRIVMSG {$channels} :\x033{$arguments}\x03\n"); //* say it in green
}
elseif($color == "blue") //* the same with blue
{
fputs($connection, "PRIVMSG {$channels} :\x032{$arguments}\x03\n");
}
else //*if the color doesn't match any condition
{
echo "ERROR: Color not found!!!\n"; //*return an error to the console and don't send anything!
}
}
else // if the command is not /me or others
{
fputs($connection, "{$cmd} {$cargs}\n"); //* send the raw command with arguments top the server
if($cmd == "quit") //* if the command is quit
{
die(); //* exit the script
}
}
}
else //* if it's not a command
{
fputs($connection, "PRIVMSG {$channels} :{$line}"); //*just put in to the channel
}
//echo "->  ";
}
usleep(50000); //* and wait 50ms for the next loop
}

?>
