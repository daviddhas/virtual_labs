<?php


#$handle = popen('david_checker.pl 2>&1', 'r');
$handle = popen('perl return_commas.pl 2>&1', 'r');
echo "'$handle'; " . gettype($handle) . "\n";
$read = fread($handle, 2096);
echo $read;
pclose($handle);

#This returns the input ports that become the headers of the input table form




?>
