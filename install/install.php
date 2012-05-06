<html>
    <head>
        <meta content="text/html; charset=ISO-8859-1"
              http-equiv="content-type">
        <title></title>
    </head>
    <body>

        <?php
        /* Skeleton Install script that requests values for system settings, database connection then installs database
         * and writes a global file to retrieve settings.
         */

        //make sure required templates can be opened before proceeding.
        $precheck = 0;

        if (($form = file_get_contents("../templates/global_settings_install_form.html", "r")) == FALSE) { //checking 
            echo 'could not open ../templates/global_settings_install_form.html <br>';
            $precheck++;
        }
        if (($globaltmp = file_get_contents("../templates/globals.php", "r")) == FALSE) { //open template for globals to insert into the dynamically written globals.inc.php
            echo 'could not open ../templates/globals.php <br>';
            $precheck++;
        }
        /* Check for a previously generated globals.inc.php and warn user if found. */
        if (file_exists("../include/globals.inc.php") == FALSE) { //check if a previous globals.inc.php exists
            //it doesn't exist good.
        } else {
            echo "A previously generated globals.inc.php was detected. If you proceed with a reinstallation a backup of the old file will be attempted. If you want to update settings without reinstalling please use <a href=\"../admin/settings.php\">admin settings</a>.<br><br>";
        }

        if ($precheck > 0) { //if required templates are not found kill the script.
            echo "installer found $precheck missing templates and cannot continue with the installation <br>";
            die();
        }

        unset($precheck);


        //Show form or process form
        if ($_SERVER['REQUEST_METHOD'] != "POST") { // if the request was not a post from a form echo form
            echo $form;
            unset($form);
        } else { // Else process form
        
            //format data           
            foreach ($_POST as $var => $val) { //cleanup spaces before/after all input
                $var = trim($var);
                $val = trim($val);
                $settings[$var] = $val;
            }

            $settings['mysql_database'] = $settings['mysql_username'] . "_" . $settings['mysql_database']; //add username to database name

            /* Need to validate all feilds data types using Java Script prior to submitting,
             * but verify that all values being passed are acceptable.
             */
            
            foreach ($settings as $var => $val){
                if (strtolower($var) == "submit") unset($settings[$var]); 
            }
            
            
            //test db connection
            $con = mysql_connect($settings['mysql_server'], $settings['mysql_username'], $settings['mysql_password']);
            if (!$con) {
                die('Could not Connect to MySQL: ' . mysql_error());
            }

            //Install DB and set options
            if (mysql_query("CREATE DATABASE " . $settings['mysql_database'], $con)) {
                echo "Database created.<br>";
            } else {
                echo "Could not create database: " . mysql_error() . "<br>";
            }

            //create tables
            mysql_select_db($settings['mysql_database'], $con);

            //create global settings table
            $sql = "CREATE TABLE tbl_settings
                (
                setting_id int NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (setting_id),
                setting_var varchar(255) NOT NULL,
                UNIQUE (setting_var),
                setting_val varchar(255) NOT NULL
                )";

            if (mysql_query($sql, $con)) {
                echo "Created settings table.<br>";
            } else {
                echo "Could not create settings table: " . mysql_error() . "<br>";
            }

            foreach ($settings as $var => $val) {
                $sql = "INSERT INTO tbl_settings (setting_var, setting_val) VALUES ('$var', '$val')";
                if (mysql_query($sql, $con)) {
                    echo "$var set to $val <br>";
                } else {
                    echo "Could not set $var to $val" . mysql_error() . "<br>";
                }
            }

/* INSERT CODE TO SETUP ADDITIONAL SYSTEM TABLES/VALUES */            
                        
mysql_close($con);            
            
            //write globals.inc.php
            $give_up = FALSE;
            tryagain:
            if (($file = fopen("../include/globals.inc.php", "x")) == FALSE) { //attempt to backup a previously set globals.inc.php. If we could not back it up do not write a new one.
                echo "globals.inc.php already exists your previous file will be backed up. <br>";
                rename("../include/globals.inc.php", "../include/globals.inc.php_" . time() . ".bak");
                if ($give_up == TRUE) {
                    echo "globals.inc.php still could not be created, aborting...<br>";
                    die();
                } else {
                    $give_up = TRUE;
                    goto tryagain;
                }
            }

            //setup contents of /include/globals.inc.php
            $globals = "<?php\n";
            $globals .= '$mysql_server = "' . $settings['mysql_server'] . "\";\n";
            $globals .= '$mysql_username = "' . $settings['mysql_username'] . "\";\n";
            $globals .= '$mysql_password = "' . $settings['mysql_password'] . "\";\n";
            $globals .= '$mysql_database = "' . $settings['mysql_database'] . "\";\n";
            $globals .= "?>\n\n";
            $globals .= $globaltmp;
            unset($globaltmp);

            if (fwrite($file, $globals)) {
                echo "installation completed successfully!<br>";
            } else {
                echo "Faild to write /include/globals.inc.php you can attempt to manually create this file by viewing source on this page and place verything below this statement inside the file<br><br>\n\n";
                echo $globals;
                }
            fclose($file);
        }
        ?>

        <br>
    </body>
</html>