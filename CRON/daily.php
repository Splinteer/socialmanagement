<?php

// Set up config
require __DIR__ . '/../src/config.php';

use MetzWeb\Instagram\Instagram;

$pdo = new PDO(DB_DSN, DB_USER, DB_PSWD);

$sql = "SELECT * FROM user WHERE access_token <> ''";

foreach ($pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $instagram->setAccessToken($row['access_token']);
    $user = $instagram->getUser();

    $stmt = $pdo->prepare('INSERT IGNORE INTO statistic (
        id,
        date,
        followers,
        posts
        ) VALUES (
        :id,
        :date,
        :followers,
        :posts
        )');
    $stmt->bindValue(':id', $user->data->id, PDO::PARAM_STR);
    $stmt->bindValue(':date', date('Y-m-d'), PDO::PARAM_STR);
    $stmt->bindValue(':followers', $user->data->counts->followed_by, PDO::PARAM_INT);
    $stmt->bindValue(':posts', $user->data->counts->media, PDO::PARAM_INT);
    $stmt->execute();
}
