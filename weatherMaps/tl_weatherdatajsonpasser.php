<?php
 	require_once('/var/www/sites/silicore/Includes/security.php');
	require_once ('/var/www/sites/silicore/Includes/Security/dbaccess.php');

   //open connection to mysql db
   $dbc = databaseConnectionInfo();
   
   $connection = mysqli_connect($dbc['silicore_hostname'], $dbc['silicore_username'], $dbc['silicore_pwd'], $dbc['silicore_dbname']) or die("Error " . mysqli_error($connection));


   //fetch table rows from mysql db
   $sql = "CALL sp_tl_plc_rainfallGet";
   echo $sql;
   $result = mysqli_query($connection, $sql) or die("Error in Selecting " . mysqli_error($connection));

   //create an array
   $emparray = array();
   while($row =mysqli_fetch_assoc($result))
   {
       $emparray[] = $row;
   }

    //Convert PHP Array to JSON String
   echo json_encode($emparray);
    
    
    //OR write to json file
   $fp = fopen('tl_weatherdata.json', 'w');
   fwrite($fp, json_encode($emparray));
   fclose($fp);
?>