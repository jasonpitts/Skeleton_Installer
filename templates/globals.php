<?php
/* create settings array */
$con = mysql_connect($mysql_server, $mysql_username, $mysql_password);
if (!$con) {
    die('Could not Connect to MySQL: ' . mysql_error()) . "<br><br> You may need to run the <a href=\"../install/install.php\">install script</a>.";
            }

            mysql_select_db($mysql_database, $con);
            
            $sql = "SELECT setting_var, setting_val FROM tbl_settings";
            
            $result = mysql_query($sql);
            
            while ($row = mysql_fetch_array($result)){
                $settings[$row['setting_var']] = $row['setting_val'];
            }
            mysql_close($con);
?>
