<?php
include '../include/globals.inc.php';

if (isset($settings)){
    print_r($settings);
} else {
    echo 'Could not retrieve global settings, Please run the <a href="../install/install.php"> installation script</a>.<br>';
    die();
}


?>
