<?php
while(1)
{
echo "->  ";
sleep(4);
$handle = fopen ("php://stdin","r");
$line = fgets($handle);
echo "PRIVMSG #jailbreakqa :{$line}";
fclose($handle);
}
?>
