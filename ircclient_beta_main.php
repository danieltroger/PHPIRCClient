<?php
include "jokes.php";
include "translate.php";
error_reporting(0); //*disable dirty error messages in the chatt
date_default_timezone_set("Europe/Stockholm"); //*set europe timezone
set_time_limit(0);//*ehhh idk y
$server = "irc.freenode.org"; //* nah the server to connect to
$channels = array("#goeosbottest","#dchatt","#jailbreakqa","#atxhack"); //* array of channels
$channel = $channels[0]; //*default channel
$port = 6667; //* default irc port
$nick = "Guest_" . rand(1,2000); //*nah the nick
$connection = fsockopen("$server", $port); //*open a socket

fputs ($connection, "USER $nick $nick $nick $nick :$nick\n");//*connect as a user
fputs ($connection, "NICK dan|el\n"); //*set a nick
foreach($channels as $chann) //*join each channel of all channels
{
snd($connection, "JOIN {$chann}\n");
}
fputs ($connection, "PRIVMSG NickServ :identify almby11152\n"); //* identify
fputs ($connection, "NICK $nick\n"); //*set a nick
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
	$all = str_replace("\n","",substr($args, 0, -1));
		if($a1[0] == "PING"){
			fputs($connection, "PONG ".$a1[1]."\n");
		}
if(strpos(substr(strtolower($a1[3]),1),"!7.1") !== false )
{
snd($connection,"PRIVMSG {$inchannel} :{$user}: iOS 7.1 tweaklist: http://goo.gl/5oxNkN\n");
}
if(strpos(substr(strtolower($a1[3]),1),"!stether") !== false )
{
snd($connection,"PRIVMSG {$inchannel} :{$user}: Add repo.natur-kultur.eu to your sources in cydia and install 7.1 semitether.\n");
}
if(strpos(substr(strtolower($a1[3]),1),"!translate") !== false && $inchannel != "#jailbreakqa")
{
$tolang =$a1[4];
$tr = new GoogleTranslate();
$tr->setLangFrom("auto");
$tr->setLangTo($tolang);
$text = explode(" ",$all);
unset($text[0]);
$text = str_replace("\n","",str_replace("\r","",implode(" ",$text)));
$trn = $tr->translate($text);
snd($connection, "PRIVMSG {$inchannel} :{$user}: \"{$text}\" means \"{$trn}\" in {$tolang}\n");
}
if(strpos(substr(strtolower($a1[3]),1),"hello") !== false && $inchannel != "#jailbreakqa")
{
$j = joke($jokes);
snd($connection, "PRIVMSG {$inchannel} : \"{$j}\"\n");
}
if(strpos($data, $nick)!== false && ((strpos($data, 'thx')!== false) || (strpos($data, 'thank')!== false)|| (strpos($data, 'thanx')!== false))){
snd($connection, "PRIVMSG {$inchannel} :{$user}: No problem!\n");
}
if ($a6[2]==$nick)
{
if(file_exists("/tmp/afk"))
{
snd($connection,"PRIVMSG {$inchannel} :{$user}: I am currently afk, i'll be back later.\n");
}    
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
$logr = "\033[32m " . date(" H:i:s ") . $user . " in {$inchannel}:\033[0m " . $a13 . " " . $all . "\033[0m \n"; //*define the output
}
elseif($a1[1] == "KICK") //* if the remote event was a kick
{
$logr = "\033[34m " . date(" H:i:s ") . $user . " kicked {$a1[3]} in {$inchannel} {$args}\033[0m \n"; //*define $log to a kick message
}
elseif($a1[1] == "JOIN") //* if the remote event was a join
{
$logr = "\033[34m " . date(" H:i:s ") . "{$user} joined {$inchannel}\033[0m \n"; //* define a join message
}
elseif($a1[1] == "PART") //* if the remote event was a part (leave)
{
if(isset($a6[2])) //* if a reason was specified
{
$logr = "\033[34m " . date(" H:i:s ") . "{$user} left {$inchannel} :{$a6[2]}\033[0m \n"; //*set the output with reason
}
else
{
$logr = "\033[34m " . date(" H:i:s ") . "{$user} left {$inchannel}\033[0m \n"; //* otherwise without reason :)
}
}
elseif($a1[1] == "QUIT") //* if the remote event was a quit
{
$logr = "\033[31m " . date(" H:i:s ") . "{$user} Quit :{$a6[2]}\033[0m \n"; //* do a cool log
}
elseif($a1[1] == "MODE") //* if it was a mode
{
//echo "user = {$user} mode = {$a1[3]} targs = {$targs} inchannel = {$inchannel}\n";
$ma = str_replace("\r","",$all); //* remove the fucking \r that bugged me for days
$logr = "\033[34m " . date(" H:i:s ") . $user . " set mode " . $a1[3] . " " . $ma ." in " . $inchannel . "\033[0m \n"; //*and write the log
}
else //*if it was an unknown irc command
{
if($a1[0] != "PING") //* if it not only was a heartbeat
{
$logr = "\033[0m " . $data . "\033[0m \n"; //* just set logr to the raw data
}
}
$lpath = "/logs/{$inchannel}" . date("-m-d-Y")  .".txt"; //* define the path for the logs to be stored in
$lpath = str_replace("\r","",$lpath); //*remove the annoying \r agian
echo $logr; //*and output it
file_put_contents($lpath,file_get_contents($lpath)  . $logr); //* and append the log to the logfile
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
$wtp = "PRIVMSG {$channel} :\001ACTION {$cargs}\001\n"; //*define an \001ACTION (a /me)
snd($connection, $wtp); //*and send it to the server
}
elseif($cmd == "identify") //* if the command is /identify
{
snd($connection, "PRIVMSG NickServ :identify {$cargs}\n"); //*send a pm to NickServ with identify <password>
}
elseif($cmd == "haha") //* if the command is /haha
{
echo "\"" . joke($jokes) . "\"\n";
}
elseif($cmd == "cc") //* if the command is /cc (channel controler)
{
$action = $conts[1]; //* define $action to the flag (-someting)
$chan = $conts[2]; //* set $chan to the second argument
if($action == "j") //* if the action was j
{
snd($connection,"JOIN {$chan}\n"); //*join the channel
echo "Joined {$chan}\n"; //*and output a message to the cli
}
elseif($action == "l") //*if it was l
{
snd($connection,"PART {$chan}\n"); //* leave the channel without reason
echo "Left {$chan}\n"; //* and log to cli
}
elseif($action == "s") //*if it was s
{
$channel = $chan; //* define the var to the chan argument
echo "Switched to {$chan}\n"; //* and log to the cli
}
elseif($action == "c") //*if it was c
{
echo "Cureent channel: {$channel}\n"; //* output the current chan
}
elseif($action == "n") //* if it was n
{
snd($connection,"NICK {$chan}\n"); //* change the nick
echo "Changed nick to {$chan}\n"; //* and log to cli
}
else //* else
{
echo "Usage:\n"; //* output a usage information to the cli
echo "/cc [n nickname] [s channel] [l channel] [j channel] [c ]\n";
echo "n changes the nick\n";
echo "s switches channel\n";
echo "j joins channel\n";
echo "l leaves channel\n";
echo "c outputs the current channel\n";
}
}
elseif($cmd == "exec") //* if the command is /exec
{
echo shell_exec($cargs);
}
elseif($cmd == "kban") //* if the command is /kban
{
snd($connection,"CS OP {$channel} {$nick}\n"); //*op myself
snd($connection, "MODE {$channel} +b {$cargs}!*@*\n"); //*ban the user
snd($connection, "KICK {$channel} {$cargs} :\"Your behavior is not conducive to the desired environment.\"\n"); //* and kick theuser
}
elseif($cmd == "ubi") //* if the command is /ubani
{
snd($connection,"CS OP {$channel} {$nick}\n"); //* op myself
snd($connection, "MODE {$channel} -b {$cargs}!*@*\n"); //*unban the user
snd($connection, "INVITE {$cargs} {$channel}\n"); //* and invite the user
}
elseif($cmd == "tweaks") //* if the command is /tweaks
{
echo "Tweaklist: http://goo.gl/5oxNkN\n";
}
elseif($cmd == "colormsg") //*if the command is colormsg
{
$arguments = explode(" ",$cargs); //* explode the arguments by " "
$color = $arguments[0]; //* to get out the color
unset($arguments[0]); //*then unset the color
$arguments = implode(" ",$arguments); //* to implode the arguments that we have the text to say
if(isset($colors[$color])) //* if the color exists in the colors array
{
snd($connection, "PRIVMSG {$channel} :\x03{$colors[$color]}{$arguments}\x03\n"); //*say it in the channel
}
else //*if the color isn't in the array
{
echo "ERROR: Color not found!!!\n"; //*return an error to the console and don't send anything!
}
}
else //* if the command is not /me or others
{
snd($connection, "{$cmd} {$cargs}\n"); //* send the raw command with arguments top the server
if($cmd == "quit") //* if the command is quit
{
die(); //* exit the script
}
}
}
else //* if it's not a command
{
snd($connection, "PRIVMSG {$channel} :{$line}"); //*just put in to the channel
}
//echo "->  ";
}
usleep(50000); //* and wait 50ms for the next loop
}
function snd($conn,$data)
{
fputs($conn,$data);
$spath = "/logs/sent" .  date("-m-d-Y") . ".txt";
file_put_contents($spath,file_get_contents($spath)  . date(" H:i:s ") . "{$data}");
}
function joke($jokes)
{
$num = rand(0,count($jokes)-1);
return $jokes[$num];
}
?>
