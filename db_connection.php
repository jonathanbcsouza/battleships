<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

class TursoConnection
{
  private string $url;
  private string $authToken;
  private string $dbName;

  public function __construct(string $url, string $authToken, string $dbName)
  {
    // Convert libsql:// to https:// and remove any trailing slashes
    $this->url = str_replace('libsql://', 'https://', rtrim($url, '/'));
    $this->authToken = $authToken;
    $this->dbName = $dbName;
  }

  public function query(string $sql, array $params = []): array
  {
    $formattedParams = [];
    foreach ($params as $key => $value) {
      $formattedParams[] = $this->formatValue($value);
    }

    $requestBody = [
      'requests' => [
        [
          'type' => 'execute',
          'stmt' => [
            'sql' => $sql,
            'args' => $formattedParams
          ]
        ]
      ]
    ];

    // Use the correct API endpoint format for Turso HTTP API
    $apiUrl = "{$this->url}/v2/pipeline";
    error_log("Sending request to Turso: " . json_encode($requestBody));

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Authorization: Bearer ' . $this->authToken,
      'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    error_log("Turso response: " . $response);

    if (curl_errno($ch)) {
      throw new Exception("Curl error: " . curl_error($ch));
    }

    curl_close($ch);

    if ($httpCode !== 200) {
      throw new Exception("Turso query failed with HTTP code: {$httpCode}");
    }

    $result = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
      throw new Exception("Failed to decode Turso response: " . json_last_error_msg());
    }

    // Return the full result for INSERT operations (when last_insert_rowid is present)
    if (isset($result['results'][0]['response']['result']['last_insert_rowid'])) {
      return $result['results'];
    }

    // For SELECT operations, return rows as before
    if (!isset($result['results'][0]['response']['result']['rows'])) {
      return [];
    }

    return array_map(function ($row) {
      return array_map(function ($col) {
        return ['value' => $col['value']];
      }, $row);
    }, $result['results'][0]['response']['result']['rows']);
  }

  private function formatValue($value)
  {
    if ($value === null) {
      return ['type' => 'null', 'value' => null];
    } elseif (is_int($value)) {
      return ['type' => 'integer', 'value' => (string)$value];
    } elseif (is_float($value)) {
      return ['type' => 'float', 'value' => (string)$value];
    } elseif (is_bool($value)) {
      return ['type' => 'boolean', 'value' => $value ? 'true' : 'false'];
    } else {
      return ['type' => 'text', 'value' => (string)$value];
    }
  }
}

// Ensure environment variables are set
if (!isset($_ENV['SERVER']) || !isset($_ENV['TURSO_AUTH_TOKEN']) || !isset($_ENV['DATABASE'])) {
  throw new Exception("Missing required environment variables. Please check your .env file.");
}

$conn = new TursoConnection(
  $_ENV['SERVER'],
  $_ENV['TURSO_AUTH_TOKEN'],
  $_ENV['DATABASE']
);

// Test connection
try {
  $conn->query("SELECT 1");
  error_log("Database connection successful!");
} catch (Exception $e) {
  error_log("Database connection failed: " . $e->getMessage());
  die("Database connection failed. Please check your configuration.");
}
