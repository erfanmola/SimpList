<?php

// Load external libraries and resources installed via Composer
require_once __DIR__ . "/vendor/autoload.php";

// Load .env file configurations using vlucas/phpdotenv library
// and make them accessible via $_ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Initialize a new OpenSwoole HTTP Server using the "OPENSWOOLE_SERVER_HOST"
// and "OPENSWOOLE_SERVER_PORT" variables from the .env file
$server = new OpenSwoole\HTTP\Server($_ENV['OPENSWOOLE_SERVER_HOST'], $_ENV['OPENSWOOLE_SERVER_PORT']);

// Define a callback function when the server starts
$server->on("start", function (OpenSwoole\Http\Server $server) {

    echo "OpenSwoole http server is started at http://{$_ENV['OPENSWOOLE_SERVER_HOST']}:{$_ENV['OPENSWOOLE_SERVER_PORT']}\n";

});

// Define a callback function that handles requests
$server->on("request", function (OpenSwoole\Http\Request $OpenSwooleRequest, OpenSwoole\Http\Response $OpenSwooleResponse) {

    // Allow CORS
    $OpenSwooleResponse->header('Access-Control-Allow-Methods', 'GET, POST');
    $OpenSwooleResponse->header('Access-Control-Allow-Origin', '*');

    // Validate the initDataUnsafe hash to see if user is from a valid Telegram WebApp
    // See: https://core.telegram.org/bots/webapps#validating-data-received-via-the-mini-app
    $initDataUnsafe   = json_decode($OpenSwooleRequest->post['initDataUnsafe'] ?? '[]', true);
    $initDataHash     = $initDataUnsafe['hash'] ?? '';
    $initDataString   = '';

    unset($initDataUnsafe['hash']);
    ksort($initDataUnsafe);

    $initDataString = implode("\n", array_map(function($value, $key) {

        $value = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
    
        return "$key=$value";
    
    }, $initDataUnsafe, array_keys($initDataUnsafe)));

    $secret_key = hash_hmac("sha256", $_ENV['BOT_TOKEN'], "WebAppData", true);

    $initDataNewHash = hash_hmac("sha256", $initDataString, $secret_key);

    // Check if calculated hash of unsafe data is not equal to hash calculated by Telegram
    // Afterwards we check if the auth_date is not more than the TTL time specified in the .env file
    if (($initDataNewHash !== $initDataHash) && (($initDataUnsafe['auth_date'] ?? '') > (time() - $_ENV['WEBAPP_AUTH_TTL']))) {

        $OpenSwooleResponse->status(403);
        return $OpenSwooleResponse->end("Invalid WebAppData");

    }

    // Define a basic routing system
    switch ([ $OpenSwooleRequest->server['request_method'], $OpenSwooleRequest->server['path_info'] ]) {

        case [ 'POST', '/pusher/auth' ]:
            // Authorize the Pusher client
            require __DIR__ . "/sections/POST/pusher.php";
            break;

        default:
            // If reached here, then we have not responded to the client in the context above
            // so it means the request is not to a handled route and we should deny it
            $OpenSwooleResponse->status(403);
            $OpenSwooleResponse->end("Method Not Allowed");
            break;

    }

});

$server->start();