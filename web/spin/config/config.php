<?php

return [
    'dayType' => $_ENV['DAY_TYPE'] ?? '10',
    'apiUrl' => $_ENV['API_URL'] ?? 'https://xn--riigiphad-v9a.ee/?output=json',
    'notificationDays' => $_ENV['NOTIFICATIONS_DAYS'] ?? '3',
    'fileExpiryDays' => $_ENV['EXPIRY_FILE_DAYS'] ?? '30',
];