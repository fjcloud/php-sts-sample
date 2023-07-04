<?php

/********* CONFIG ********/
$clusterEndpoint = getenv('DB_ENDPOINT');
$clusterPort = getenv('DB_PORT');
$clusterRegion = getenv('AWS_REGION');
$dbUsername = getenv('DB_USER');
$dbDatabase = getenv('DB_PASSWORD');
/*************************/

// AWS-PHP-SDK installed via Composer
require 'vendor/autoload.php';

use Aws\Credentials\CredentialProvider;

$provider = CredentialProvider::defaultProvider();
$RdsAuthGenerator = new Aws\Rds\AuthTokenGenerator($provider);

$token = $RdsAuthGenerator->createToken($clusterEndpoint . ":" . $clusterPort, $clusterRegion, $dbUsername);

$mysqli = mysqli_init();

mysqli_options($mysqli, MYSQLI_READ_DEFAULT_FILE, "./my.cnf");

$mysqli->real_connect($clusterEndpoint, $dbUsername, $token, $dbDatabase, $clusterPort, NULL, MYSQLI_CLIENT_SSL);

if ($mysqli->connect_errno) {
    echo "Error: Failed to make a MySQL connection, here is why: <br />";
    echo "Errno: " . $mysqli->connect_errno . "<br />";
    echo "Error: " . $mysqli->connect_error . "<br />";
    exit;
}

/***** Example code to perform a query and return all tables in the DB *****/
$tableList = array();
$res = mysqli_query($mysqli,"SHOW TABLES");
while($cRow = mysqli_fetch_array($res))
{
    $tableList[] = $cRow[0];
}
echo '<pre>';
print_r($tableList);
echo '</pre>';
