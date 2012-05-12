<html>
    <head>
        <meta content="text/html; charset=ISO-8859-1"
              http-equiv="content-type">
        <title></title>
    </head>
    <body>
        
<?php
include '../include/globals.inc.php';
$precheck = 0;
$rows = "";

//check that required templates are available
        if (($form = file_get_contents("../templates/settings_form.html", "r")) == FALSE) { //checking 
            echo 'could not open ../templates/settings_form.html <br>';
            $precheck++;
        }
        
        if ($precheck > 0) { //if required templates are not found kill the script.
            echo "installer found $precheck missing templates and cannot continue with the installation <br>";
            die();
        }

if (isset($settings)){
    
  if ($_SERVER['REQUEST_METHOD'] != "POST"){ //the method was not post, echo the form
      
     foreach ($settings as $var => $val){ //list variables and values
         $rows .= '<tr><td style="vertical-align: top;">' . "$var: " . '<br></td><td style="vertical-align: top;"><input value="' . $val . '"' . ' name="' . $var . '"></td></tr>' . "\n";
    }
    
    echo str_replace("{rows}", $rows, $form);
    
    /* Start Add new var/val - Remove for production environment */
    $new_var_form = <<<NEWVARFORM
<br><br>
<form target="_self" method="post">
<div style="text-align: center;"> </div>
<table>
   <tbody>
<tr align="center">
<td colspan="2" rowspan="1"
style="vertical-align: top; width: 195px;"><span
style="font-weight: bold;">Add New Global</span><br>
</td>
</tr>
<tr>
<td style="vertical-align: top;">New Variable<br>
</td>
<td style="vertical-align: top;">New Value<br>
</td>
</tr>
<tr>
<td style="vertical-align: top;"><input name="new_var"><br>
</td>
<td style="vertical-align: top;"><input name="new_val">
</td>
</tr>
<tr>
<td style="vertical-align: top;"><br>
</td>
<td style="vertical-align: top; text-align: right;"><button
value="submit" name="submit">Add</button><br>
</td>
</tr>
</tbody>
</table>
</form>    
NEWVARFORM;
    
    echo $new_var_form;
    
    /* End: Add new var/val - Remove for production environment */
    
} else { //the method was post process the form
    
    foreach ($_POST as $var => $val) { //cleanup spaces before/after all input
       $var = trim($var);
       $val = trim($val);
       if (strtolower($var) == "submit"){
           
       } else {
       $update_settings[$var] = $val;
       }
    }
    

    
    if (isset($update_settings['new_var']) && isset($update_settings['new_val'])){ //check if we are adding a new global
        
        //test db connection
            $con = mysql_connect($settings['mysql_server'], $settings['mysql_username'], $settings['mysql_password']);
            if (!$con) {
                die('Could not Connect to MySQL: ' . mysql_error());
            }
        //select db
            mysql_select_db($settings['mysql_database'], $con);
        
            $var = $update_settings['new_var'];
            $val = $update_settings['new_val'];
            
        //Insert new global
            $sql = "INSERT INTO tbl_settings (setting_var, setting_val) VALUES ('$var', '$val')";
                
                if (mysql_query($sql, $con)) {
                    echo "$var added and set to $val <br>";
                } else {
                    echo "Could not add $var with a value of $val" . " | " . mysql_error() . "<br>";
                }
            
            mysql_close($con);
            
    } elseif (isset($update_settings) && isset($update_settings['new_var']) == FALSE){ //update settings not adding new var
          
        //test db connection
            $con = mysql_connect($update_settings['mysql_server'], $update_settings['mysql_username'], $update_settings['mysql_password']);
            if (!$con) {
                die('Could not Connect to MySQL: ' . mysql_error());
            }
        //select db
            mysql_select_db($update_settings['mysql_database'], $con);     
        
        foreach ($update_settings as $var => $val) {
                $sql = "UPDATE tbl_settings SET setting_val='$val' WHERE setting_var='$var'";
                if (mysql_query($sql, $con)) {
                    echo "$var set to $val <br>";
                } else {
                    echo "Could not set $var to $val" . " | " . mysql_error() . "<br>";
                }
            }        
            mysql_close($con);
    }
     else {
        echo 'no settings to update...<br>';
        die();
    }
            
    
    
    
    
}
  
    
 
    
    
} else {
    echo 'Could not retrieve global settings, Please run the <a href="../install/install.php"> installation script</a>.<br>';
    die();
}


?>

        <br>
    </body>
</html>