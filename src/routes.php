<?php
// Routes
use MetzWeb\Instagram\Instagram;

$app->get('/', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    if (! isset($_SESSION['access_token'])) {
        var_dump($_SESSION);
        return $response->withRedirect(SERVER_ROOT . 'login');
    } else {
        $instagram = new Instagram(array(
            'apiKey'      => IG_CLIENT_ID,
            'apiSecret'   => IG_CLIENT_SECRET,
            'apiCallback' => IG_CLIENT_CALLBACK
        ));
        //$instagram->setSignedHeader(true);
        $instagram->setAccessToken($_SESSION['access_token']);

        // Render index view
        return $this->view->render($response, 'index.html.twig', [
            'user' => $instagram->getUser()
        ]);
    }
})->setName('home');

$app->get('/login', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    if (! isset($_SESSION['access_token'])) {
        $instagram = new Instagram(array(
            'apiKey'      => IG_CLIENT_ID,
            'apiSecret'   => IG_CLIENT_SECRET,
            'apiCallback' => IG_CLIENT_CALLBACK
        ));
        //$instagram->setSignedHeader(true);

        $url = $instagram->getLoginUrl(array('basic', 'likes', 'relationships', 'comments'));

        // Render index view
        return $this->view->render($response, 'login.html.twig', [
            'url' => $url
        ]);
    } else {
       return $response->withRedirect(SERVER_ROOT);
    }
})->setName('login');

$app->get('/logout', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    session_destroy();
    return $response->withRedirect(SERVER_ROOT . 'login');

})->setName('logout');

$app->get('/callback', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    $instagram = new Instagram(array(
        'apiKey'      => IG_CLIENT_ID,
        'apiSecret'   => IG_CLIENT_SECRET,
        'apiCallback' => IG_CLIENT_CALLBACK
    ));
    //$instagram->setSignedHeader(true);

    $data = $instagram->getOAuthToken($_GET['code']);

    if (! is_object($data)) {
       return $response->withRedirect(SERVER_ROOT . 'login');
    } else {
        $pdo = new PDO(DB_DSN, DB_USER, DB_PSWD);
        $sql = "SELECT COUNT(username) FROM user WHERE username=" . $pdo->quote($data->user->username);
        $res = $pdo->query($sql)->fetchColumn();

        if (intval($res) == 0) {
            $stmt = $pdo->prepare('INSERT INTO user (
                username,
                access_token
                ) VALUES (
                :username,
                :access_token
                )');
        } else {
            $stmt = $pdo->prepare('UPDATE user SET
                access_token=:access_token
                WHERE username=:username');
        }
        $stmt->bindValue(':username', $data->user->username, PDO::PARAM_STR);
        $stmt->bindValue(':access_token', $data->access_token, PDO::PARAM_STR);
        $stmt->execute();

        $instagram->setAccessToken($data->access_token);
        $user = $instagram->getUser();

        $stmt = $pdo->prepare('INSERT IGNORE INTO statistic (
            username,
            date,
            followers,
            posts
            ) VALUES (
            :username,
            :date,
            :followers,
            :posts
            )');
        $stmt->bindValue(':username', $data->user->username, PDO::PARAM_STR);
        $stmt->bindValue(':date', date('Y-m-d'), PDO::PARAM_STR);
        $stmt->bindValue(':followers', $user->data->counts->followed_by, PDO::PARAM_INT);
        $stmt->bindValue(':posts', $user->data->counts->media, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['access_token'] = $data->access_token;
        return $response->withRedirect(SERVER_ROOT);
    }

})->setName('callback');

$app->get('/hashtag/{hashtag}', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    $instagram = new Instagram(array(
        'apiKey'      => IG_CLIENT_ID,
        'apiSecret'   => IG_CLIENT_SECRET,
        'apiCallback' => IG_CLIENT_CALLBACK
    ));
    //$instagram->setSignedHeader(true);

    if (! isset($_SESSION['access_token'])) {
       return $response->withRedirect(SERVER_ROOT . 'login');
    } else {
        $instagram->setAccessToken($_SESSION['access_token']);

        $photos = $instagram->getTagMedia($args['hashtag'], 60);
        $result = $instagram->pagination($photos, 5);

        //var_dump($result->data[0]);

        // Render view
        return $this->view->render($response, 'hashtag.html.twig', [
            'hashtag' => $args['hashtag'],
            'result' => $result
        ]);
    }
})->setName('hashtag');
