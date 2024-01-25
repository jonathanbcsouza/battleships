<?php
require 'vendor/autoload.php';

use App\Classes\Database;
use Aws\Ssm\SsmClient;


// Retrieve credentials from AWS SSM / Parameter Store
$client = new SsmClient([
  'version' => 'latest',
  'region'  => 'ap-southeast-2' 
]);

$parameters = $client->getParameters([
  'Names' => ['SERVER', 'USERNAME', 'PASSWORD', 'DATABASE'],
  'WithDecryption' => true
])->toArray();

$params = [];

foreach ($parameters['Parameters'] as $param) {
  $params[$param['Name']] = $param['Value'];
}

$server_name = $params['SERVER'];
$db_username = $params['USERNAME'];
$password = $params['PASSWORD'];
$db_name = $params['DATABASE'];

$conn = new mysqli($server_name, $db_username, $password);

if ($conn->connect_error) {
  throw new Exception("Connection failed: " . $conn->connect_error);
}

// Bootstrapping a new DB adn table
$database = new Database($conn);
$database->createDatabase($db_name);
$database->createUsersTable();
$database->createUsersConfigTable();
