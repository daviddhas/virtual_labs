

//Purpose : Returns the inputs in a Verilog file


<?php
$myfile = fopen("uploader_dut.v", "r") or die("Unable to open file!");
// Output one line until end-of-file
$inputs = [];
$count = 0;
while(!feof($myfile)) {
  $line = fgets($myfile);
  $pos = strpos($line, 'input');
  if($pos != false) {
    $variables = explode(" ", $line);
    foreach($variables as $value) {
      $value = trim($value);
      if($value) {
        $value = str_replace(',', '', $value);
        $value = str_replace(';', '', $value);
        if($value == 'input' || $value == 'clk') {
          continue;
        }
        $inputs[$count++] = $value;
      }
    }
    foreach($inputs as $input) {
      echo $input;
    }
  }
}
fclose($myfile);
echo ($line);
array explode ( string $delimiter , string $line [, int $limit ] ) //explode returns an array of strings split by a delimiter
?> 
