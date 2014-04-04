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
$colors = array(
"white" => 0,
"black" => 1,
"blue" => 2,
"green" => 3,
"red" => 4,
"brown" => 5,
"purple" => 6,
"orange" => 7,
"yellow" => 8,
"lightgreen" => 9,
"silver" => 15,
"grey" => 14,
"pink" => 13); //* array of color codes
while(1) //*the while loop that runs eaech 50ms
{
$data = fgets($connection); //*get and
if($data) //* look for new data in the connection
{
$data = str_replace("\n","",$data);

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
if($a1[1] == "PRIVMSG") //* if the remote event was a send message
{
if($a1[3][0] == ":") //*if someone said it in a channel
{
$a13 = substr($a1[3],1); //* remove the : at the beginning of the text
}
else //*if it was a pm
{
$a13 = $a1[3]; //* just set it
}
$logr = date(" H:i:s ") . $user . " in {$inchannel}: " . $a13 . " " . $all . "\n"; //*define the output
}
elseif($a1[1] == "KICK") //* if the remote event was a kick
{
$logr = date(" H:i:s ") . $user . " kicked {$a1[3]} in {$inchannel} {$args}\n"; //*define $log to a kick message
}
elseif($a1[1] == "JOIN") //* if the remote event was a join
{
$logr = date(" H:i:s ") . "{$user} joined {$inchannel}\n"; //* define a join message
}
elseif($a1[1] == "PART") //* if the remote event was a part (leave)
{
if(isset($a6[2])) //* if a reason was specified
{
$logr = date(" H:i:s ") . "{$user} left {$inchannel} :{$a6[2]}\n"; //*set the output with reason
}
else
{
$logr = date(" H:i:s ") . "{$user} left {$inchannel}\n"; //* otherwise without reason :)
}
}
elseif($a1[1] == "MODE") //* if it was a mode
{
//echo "user = {$user} mode = {$a1[3]} targs = {$targs} inchannel = {$inchannel}\n";
$ma = str_replace("\r","",$all); //* remove the fucking \r that bugged me for days
$logr = date(" H:i:s ") . $user . " set mode " . $a1[3] . " " . $ma ." in " . $inchannel . "\n"; //*and write the log
}
else //*if it was an unknown irc command
{
$logr = $data . "\n"; //* just set logr to the raw data
}
echo $logr; //*and output it
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
if(isset($colors[$color])) //* if the color exists in the colors array
{
fputs($connection, "PRIVMSG {$channels} :\x03{$colors[$color]}{$arguments}\x03\n"); //*say it in the channel
}
else //*if the color isn't in the array
{
echo "ERROR: Color not found!!!\n"; //*return an error to the console and don't send anything!
}
}
else //* if the command is not /me or others
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
