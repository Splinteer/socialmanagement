<?php
// Routes

$app->get('/', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    if (! isset($_SESSION['access_token'])) {
        return $response->withRedirect(SERVER_ROOT . 'login');
    } else {
        global $instagram;
        $instagram->setAccessToken($_SESSION['access_token']);

        // Render index view
        return $this->view->render($response, 'index.html.twig', [
            'user' => $instagram->getUser()
        ]);
    }
})->setName('home');

$app->get('/login', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/login' route");

    if (! isset($_SESSION['access_token'])) {
        global $instagram;

        $url = $instagram->getLoginUrl(array(
            'basic',
            'likes',
            'relationships'
        ));;

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
    $this->logger->info("Slim-Skeleton '/logout' route");

    session_destroy();
    return $response->withRedirect(SERVER_ROOT . 'login');

})->setName('logout');

$app->get('/callback', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/callback' route");

    global $instagram;

    $data = $instagram->getOAuthToken($_GET['code']);

    if (! is_object($data)) {
       return $response->withRedirect(SERVER_ROOT . 'login');
    } else {
        $pdo = new PDO(DB_DSN, DB_USER, DB_PSWD);
        $sql = "SELECT COUNT(id) FROM user WHERE id=" . intval($data->user->id);
        $res = $pdo->query($sql)->fetchColumn();

        if (intval($res) == 0) {
            $stmt = $pdo->prepare('INSERT INTO user (
                id,
                username,
                access_token
                ) VALUES (
                :id,
                :username,
                :access_token
                )');
        } else {
            $stmt = $pdo->prepare('UPDATE user SET
                username=:username
                access_token=:access_token
                WHERE id=:id');
        }
        $stmt->bindValue(':id', $data->user->id, PDO::PARAM_STR);
        $stmt->bindValue(':username', $data->user->username, PDO::PARAM_STR);
        $stmt->bindValue(':access_token', $data->access_token, PDO::PARAM_STR);
        $stmt->execute();

        $instagram->setAccessToken($data->access_token);
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
        $stmt->bindValue(':id', $data->user->id, PDO::PARAM_STR);
        $stmt->bindValue(':date', date('Y-m-d'), PDO::PARAM_STR);
        $stmt->bindValue(':followers', $user->data->counts->followed_by, PDO::PARAM_INT);
        $stmt->bindValue(':posts', $user->data->counts->media, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['access_token'] = $data->access_token;
        $_SESSION['user_id'] = $data->user->id;
        return $response->withRedirect(SERVER_ROOT);
    }

})->setName('callback');

$app->get('/notfollowing', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/notfollowing' route");

    if (! isset($_SESSION['access_token'])) {
        return $response->withRedirect(SERVER_ROOT . 'login');
    } else {
        global $instagram;
        $instagram->setAccessToken($_SESSION['access_token']);

        //Récupération following
        $aFollowing = array();
        $follower = $instagram->getUserFollows();
        do {
            foreach ($follower->data as $data) {
                $aFollowing[$data->id] = $data;
            }
        } while ($follower = $instagram->pagination($follower));

        //Récupération followers
        $aFollowers = array();
        $follower = $instagram->getUserFollower();
        do {
            foreach ($follower->data as $data) {
                $aFollowers[$data->id] = null;
            }
        } while ($follower = $instagram->pagination($follower));

        $aNotFollowing = array();
        foreach ($aFollowing as $id => $data) {
            if (! array_key_exists($id, $aFollowers)) {
                $aNotFollowing[$id] = $data;
            }
        }

        // Render index view
        return $this->view->render($response, 'notfollowing.html.twig', [
            'user' => $instagram->getUser(),
            'notfollowing' => $aNotFollowing
        ]);
    }
})->setName('notfollowing');

$app->get('/hashtag/{hashtag}', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/hashtag/{$args['hashtag']}' route");

    global $instagram;

    if (! isset($_SESSION['access_token'])) {
       return $response->withRedirect(SERVER_ROOT . 'login');
    } else {
        $instagram->setAccessToken($_SESSION['access_token']);

        $photos = $instagram->getTagMedia($args['hashtag'], 60);
        $result = $instagram->pagination($photos, 5);

        // Render view
        return $this->view->render($response, 'hashtag.html.twig', [
            'hashtag' => $args['hashtag'],
            'result' => $result
        ]);
    }
})->setName('hashtag');
