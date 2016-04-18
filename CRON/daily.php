<?php

require __DIR__ . '/../vendor/autoload.php';

// Set up config
require __DIR__ . '/../src/config.php';

use MetzWeb\Instagram\Instagram;

$pdo = new PDO(DB_DSN, DB_USER, DB_PSWD);


$instagram = new Instagram(array(
    'apiKey'      => IG_CLIENT_ID,
    'apiSecret'   => IG_CLIENT_SECRET,
    'apiCallback' => IG_CLIENT_CALLBACK
));



/*foreach (mysqli_query($mysqli, "SELECT * FROM user WHERE access_token <> ''") as $row) {
    $instagram->setAccessToken($row['access_token']);
    $user = $instagram->getUser();

    var_dump($user->data->counts->followed_by);

    /*$sql = "INSERT INTO statistic (user_id, date, followers, posts) VALUES (
        " . intval($row['id']) . ", '" . date('Y-m-d'') . "'", ?)";
}*/
