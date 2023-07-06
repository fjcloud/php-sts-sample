<?php

/********* CONFIG ********/
$clusterEndpoint = getenv('DB_ENDPOINT');
$clusterPort = getenv('DB_PORT');
$clusterRegion = getenv('AWS_REGION');
$dbUsername = getenv('DB_USER');
$dbDatabase = getenv('DB_NAME');
/*************************/

// AWS-PHP-SDK installed via Composer
require 'vendor/autoload.php';

use Aws\Credentials\CredentialProvider;

$provider = CredentialProvider::assumeRoleWithWebIdentityCredentialProvider();
// Cache the results in a memoize function to avoid loading and parsing
// the ini file on every API operation
$provider = CredentialProvider::memoize($provider);

$RdsAuthGenerator = new Aws\Rds\AuthTokenGenerator($provider);

$token = $RdsAuthGenerator->createToken($clusterEndpoint . ":" . $clusterPort, $clusterRegion, $dbUsername);

$db = pg_connect("host=$clusterEndpoint user=$dbUsername password=$token sslmode=require");

   if(!$db) {
      echo "Error in opening database\n";
   } else {
      $sql = "select now();";
      $ret = pg_query($db, $sql);
      if(!$ret) {
         echo pg_last_error($db);
         exit;
      } 
      while($row = pg_fetch_row($ret)) {
         echo $row[0]."\n";;
      }
      pg_close($db);
   }

?>
