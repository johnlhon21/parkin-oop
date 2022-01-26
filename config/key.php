<?php
return [
    "public-key" => [
        "cms" => file_get_contents( storage_path("ssh/keys/auth.user.key.pub") )
    ],
    "private-key" => [
        "cms" => file_get_contents( storage_path( "ssh/keys/auth.user.key" ) )
    ],
    "message-403" => "Access Forbidden",
    "message-419" => "Token expired",
];
