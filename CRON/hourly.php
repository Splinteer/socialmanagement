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

$sql = "SELECT * FROM user WHERE access_token <> ''";

$limit = 20;
$defaultPP = "https://scontent.cdninstagram.com/t51.2885-19/11906329_960233084022564_1448528159_a.jpg";

$aAccount = array(
    'blkvis',
    'adidasoriginals',
    'whiteaddicted',
);

set_time_limit(0);

foreach ($pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $instagram->setAccessToken($row['access_token']);
    $user = $instagram->getUser();

    //Récupération following
    $aFollowing = array();
    $follower = $instagram->getUserFollows();
    do {
        foreach ($follower->data as $data) {
            $aFollowing[$data->id] = $data->id;
        }
    } while ($follower = $instagram->pagination($follower));

    //Récupération d'un compte
    do {
        $referer = array_rand($aAccount);
        $aSearch = $instagram->searchUser($aAccount[$referer], 1);
        $id_account = intval($aSearch->data[0]->id);
        $follower = $instagram->getUserFollower($id_account);
    } while ($follower->meta->code != 200);

    //Follow
    $i = $j = 0;
    $aFollow = array();
    do {
        while ($j < count($follower->data) && $i < $limit / 2) {
            $_user = $instagram->getUser($follower->data[$j]->id);
            if ($_user->meta->code == 200) {
                if (! isset($aFollowing[$_user->data->id]) && $_user->data->counts->media > 1 && $_user->data->profile_picture != $defaultPP) {
                    $result = $instagram->modifyRelationship('follow', $follower->data[$j]->id);
                    if ($result->meta->code == 200) {
                        $aFollow[$_user->data->id] = $user->data->id;
                        echo 'follow ' . $i . '<br>';
                        $i++;
                    }
                }
            }
            $j++;
        }
    } while (($follower = $instagram->pagination($follower)) && $i < $limit / 2);


    //Unfollow des personnes qui ne suivent pas et que l'on ne vient pas de suivre
    $i = $j = 0;
    $follower = $instagram->getUserFollower();
    do {
        while ($j < count($follower->data) && $i < $limit / 2) {
            if (! isset($aFollowing[$follower->data[$j]->id]) && ! isset($aFollow[$follower->data[$j]->id])) {
                $result = $instagram->modifyRelationship('unfollow', $follower->data[$j]->id);
                if ($result->meta->code == 200) {
                    $aFollow[$follower->data[$j]->id] = $follower->data[$j]->id;
                    echo 'unfollow ' . $i . '<br>';
                    $i++;
                }
            }
            $j++;
        }
    } while (($follower = $instagram->pagination($follower)) && $i < $limit / 2);
}
