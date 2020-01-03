<?php

return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'user.passwordResetTokenExpire' => 30600,
    'allowedOrigins' => [
        //'https://api.yo3an.io/',
        //Dev Envs
        null,
        '*' //remove this once app is live
    ]
];
