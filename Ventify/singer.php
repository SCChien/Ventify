<!DOCTYPE html>
<html>
<head>
    <title>热门歌手</title>
</head>
<body>

<h1>当前热门歌手</h1>

<ul>
    <?php
    require 'vendor/autoload.php';

    // 设置您的 YouTube API 密钥
    $api_key = 'AIzaSyAA6Se55jeExapfgf4cHCT94OBGt6F-d8E';

    // 引入所需的命名空间
    use \Google_Client;
    use \Google_Service_YouTube;

    // 创建 YouTube Data API 的服务对象
    $client = new \Google_Client();
    $client->setDeveloperKey($api_key);
    $youtube = new \Google_Service_YouTube($client);

    // 调用 search.list 方法来搜索 YouTube 频道
    $response = $youtube->search->listSearch('snippet', array(
        'q' => '歌手',  // 搜索关键字为“歌手”
        'type' => 'channel',
        'maxResults' => 10,  // 获取前 10 个结果
        'order' => 'viewCount'  // 根据观看次数排序
    ));

    // 解析响应并输出结果
    foreach ($response['items'] as $item) {
        echo '<li>' . $item['snippet']['title'] . '</li>';
    }
    ?>
</ul>

</body>
</html>
