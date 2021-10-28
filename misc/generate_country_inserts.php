<?php

$strCountries = file_get_contents('countries.txt');
$newString = str_replace("\n", "'\n])->execute();\n", $strCountries);
$newString2 = str_replace("|", "',\n'name' => '", $newString);
$newString3 = str_replace("])->execute();\n", "])->execute();\n\nconnection->insert('country')->fields([\n'code' => '", $newString2);
$newString4 = str_replace("connection", "\$connection", $newString3);
/* var_dump($newString4); */
file_put_contents('country_inserts.txt', $newString4);
?>
