<?php

// Create our Pusher instance
$pusher = new Pusher\Pusher(
    $_ENV['PUSHER_KEY'],
    $_ENV['PUSHER_SECRET'],
    $_ENV['PUSHER_APP_ID'],
    [
        'cluster' => $_ENV['PUSHER_CLUSTTER'],
    ]
);

// This step means that we are authorizing the user to subscribe to pusher channel
// Since we did the WebAppData hash comparison before, it is safe to authorize the user at this point
// We respond with the authorizeChannel method on pusher instance
return $OpenSwooleResponse->end(
    $pusher->authorizeChannel(
        $OpenSwooleRequest->post['channel_name'],
        $OpenSwooleRequest->post['socket_id'],
    )
);