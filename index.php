<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>

<?php

$thelist = "";

 if ($handle = opendir('.')) {
   while (false !== ($file = readdir($handle)))
      {
          if ($file != "." && $file != "..")
	  {
          	$thelist[] = '<a href="'.$file.'">'.$file.'</a><br>';
          }
       }
  closedir($handle);
  }
?>
<P><strong>List</strong></p>

<P>
<?php
sort($thelist, SORT_STRING); 

  foreach ($thelist as $file){ 
    echo $file; 
    
    }
?>

</p>
    </body>
</html>
