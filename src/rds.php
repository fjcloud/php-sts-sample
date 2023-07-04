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

$pgdb = pg_connect("host=$clusterEndpoint dbname=$dbDatabase user=$dbUsername password=$token");

$res = pg_query($pgdb,"SELECT NOW()");

print_r($res);
?>
