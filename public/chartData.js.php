<?php

header("content-type: application/x-javascript");
// Set up config
require __DIR__ . '/../src/config.php';

session_start();
if (! isset($_SESSION['user_id'])) {
    die();
}

$pdo = new PDO(DB_DSN, DB_USER, DB_PSWD);

if (! isset($_SESSION['data']) || ! isset($_SESSION['data']['followers']) || $_SESSION['data']['time'] != strtotime('today')) {
    $_SESSION['data']['time'] = strtotime('today');

    $aLabel = $aData = array();

    $sql = "SELECT COUNT(date) FROM statistic WHERE user_id=" . $pdo->quote($_SESSION['user_id']);
    if ($pdo->query($sql)->fetchColumn() > 60) {
        $sql = "SELECT MAX(date) as date, followers, posts FROM statistic WHERE user_id=" . $pdo->quote($_SESSION['user_id']) . " GROUP BY MONTH(date) ORDER BY date DESC LIMIT 10";
        foreach (array_reverse($pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC)) as $row) {
            $aLabel[] = date('M', strtotime($row['date']));
            $aData['followers'][] = $row['followers'];
            $aData['posts'][] = $row['posts'];
        }
    } else {
        $sql = "SELECT * FROM statistic WHERE date > " . date('Y-m-d', (time()-86400*30)) . " AND user_id=" . $pdo->quote($_SESSION['user_id']) . " ORDER BY date";
        foreach ($pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $aLabel[] = date('d/m', strtotime($row['date']));
            $aData['followers'][] = $row['followers'];
            $aData['posts'][] = $row['posts'];
        }
    }

    $_SESSION['data']['followers']['aLabel'] = $aLabel;
    $_SESSION['data']['followers']['aData'] = $aData;
}
?>
var evolFollowersData = {
    labels: [<?php echo implode(",", array_map(array($pdo, 'quote'), $_SESSION['data']['followers']['aLabel'])) ?>],
    datasets: [
        {
            label: "Followers",
            fillColor: "rgba(220,220,220,0.2)",
            strokeColor: "rgba(220,220,220,1)",
            pointColor: "rgba(220,220,220,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(220,220,220,1)",
            data: [<?php echo implode(",", array_map('intval', $_SESSION['data']['followers']['aData']['followers'])) ?>]
        },
        {
            label: "Posts",
            fillColor: "rgba(151,187,205,0.2)",
            strokeColor: "rgba(151,187,205,1)",
            pointColor: "rgba(151,187,205,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(151,187,205,1)",
            data: [<?php echo implode(",", array_map('intval', $_SESSION['data']['followers']['aData']['posts'])) ?>]
        }
    ]
};

<?php
if (! isset($_SESSION['data']) || ! isset($_SESSION['data']['noFollowing']) || $_SESSION['data']['time'] != strtotime('today')) {
    require __DIR__ . '/../vendor/autoload.php';

    $instagram->setAccessToken($_SESSION['access_token']);

    //Récupération following
    $aFollowing = array();
    $follower = $instagram->getUserFollows();
    do {
        foreach ($follower->data as $data) {
            $aFollowing[$data->id] = $data->id;
        }
    } while ($follower = $instagram->pagination($follower));

    $notFollowing = 0;
    $follower = $instagram->getUserFollower();
    do {
        foreach ($follower->data as $data) {
            if (isset($aFollowing[$data->id])) {
                $notFollowing++;
            }
        }
    } while ($follower = $instagram->pagination($follower));
    $notFollowing = count($aFollowing) - $notFollowing;

    $_SESSION['data']['noFollowing']['following'] = count($aFollowing) - $notFollowing;
    $_SESSION['data']['noFollowing']['notFollowing'] = $notFollowing;
}
?>
var notFollowingData = [
    {
        value: <?php echo intval($_SESSION['data']['noFollowing']['following']) ?>,
        color: "#46BFBD",
        highlight: "#5AD3D1",
        label: "Following"
    },
    {
        value: <?php echo intval($_SESSION['data']['noFollowing']['notFollowing']) ?>,
        color:"#F7464A",
        highlight: "#FF5A5E",
        label: "Not following"
    }
]

window.onload = function(){
    // Line chart from swirlData for dashReport
    var ctx = document.getElementById("dashReport").getContext("2d");
    window.myLine = new Chart(ctx).Line(evolFollowersData, {
        responsive: true,
        scaleShowVerticalLines: false,
        scaleBeginAtZero : true,
        multiTooltipTemplate: "<%if (label){\%><%=label%>: <%}%><%= value %>",
    });

    // Dougnut Chart from doughnutData
    var doctx = document.getElementById("chart-area4").getContext("2d");
    window.myDoughnut = new Chart(doctx).Doughnut(notFollowingData, {responsive : true});
}
