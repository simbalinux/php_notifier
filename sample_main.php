<?php

date_default_timezone_set('America/Los_Angeles');

return [
        "smtp" => [
                "host"       => "192.168.x.x",
                "port"       => 587,
                "username"   => "alert@acme.com",
                "password"   => "xxxxxxxx",
                "wordwrap"   => 75,
                "smtpsecure" => "tls"
        ],

        "sender" => [
                "email"     => "alert@notifications.com",
                "name" => "Inc. Notification"
        ],

        "recipient" => [
                "email" => "nagios-notifications@acme.com"
        ]
];
