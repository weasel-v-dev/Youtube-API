<?php
$search = "Английский с нуля до автоматизма";
const YOUTUBE_API = 'AIzaSyDmkh0qO0FHlWoNjN51OIJ4qIXsEDsv00s';

function getYouTubeData($url) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    $response = json_decode(curl_exec($ch), true);

//    echo '<pre>';
//    var_dump($response);
//    die;
    if(isset($response['items'])) {
        return $response;
    }
    else {
        return $response['error'];
    }
}

if (!empty(htmlspecialchars($_GET['search']))) {
    $search = htmlspecialchars($_GET['search']);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Youtube API</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body class=" bg-secondary">
    <div class="container mt-3">
        <div class="row">
            <div class="col-lg-4">
                <form action="/" method="get" class="d-flex">
                    <input type="text" name="search" value="<?= $search ?>" class="form-control w-100 me-2"  placeholder="Search for">
                    <button type="submit" class="btn btn-info">Search</button>
                </form>
                <?php
                $url = 'https://www.googleapis.com/youtube/v3/search?key='.YOUTUBE_API.'&type=video&q='.urlencode($search).'&part=snippet&maxResults=6';
                if (!empty(htmlspecialchars($_GET['search']) || array_key_exists('pageToken', $_GET))) {
                    if (array_key_exists('pageToken', $_GET)) {
                        $url .= '&pageToken=' . $_GET['pageToken'];
                    }

                    $data = getYouTubeData($url);
                }
                if(!empty($data['items'])) {
                    ?>
                    <nav class="mt-3">
                        <ul class="pagination">
                            <li class="page-item <?= !empty($data['prevPageToken']) ? '' : 'disabled' ?>">
                                <?php
                                if(!empty($data['prevPageToken'])) {
                                    echo '<a class="page-link" href="?q='.urlencode($search).'&part=snippet&maxResults=6&order=viewCount&type=video&pageToken='.$data['prevPageToken'].'">Previous</a>';
                                }
                                else {
                                    echo '<span class="page-link">Previous</span>';
                                }?>
                            </li>
                            <li class="page-item <?= !empty($data['nextPageToken']) ? '' : 'disabled' ?>">
                                <?php
                                if(!empty($data['nextPageToken'])) {
                                    echo ' <a class="page-link" href="?q='.urlencode($search).'&part=snippet&maxResults=6&order=viewCount&type=video&pageToken='.$data['nextPageToken'].'">Next</a>';
                                }
                                else {
                                    echo '<span class="page-link">Next</span>';
                                }?>
                            </li>
                        </ul>
                    </nav>
                <?php } ?>
            </div>
            <div class="col-lg-8">
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php
                    if (!empty(htmlspecialchars($_GET['search']) || array_key_exists('pageToken', $_GET))) {
                        if(!empty($data['items'])) {
                            foreach ($data['items'] as $item) { ?>
                                <div class="col">
                                    <div class="card text-white bg-dark h-100">
                                        <img src="<?= $item['snippet']['thumbnails']['medium']['url'] ?>" class="card-img-top">
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title"><?= $item['snippet']['channelTitle'] ?></h5>
                                            <p class="card-text"><?= $item['snippet']['title'] ?></p>
                                            <a target="_blank" href="https://www.youtube.com/watch?v=<?= $item['id']['videoId'] ?>" class="btn btn-warning w-100 mt-auto">Go somewhere</a>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }else {
                            if(!empty($data['errors'])) {
                                foreach ($data['errors'] as $key => $error) { ?>
                                    <div>
                                        <p><i><?= $error['message'] ?></i></p>
                                        <p><strong><?= $error['domain'] ?></strong></p>
                                    </div>
                                    <?php
                                }
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>