<?php
session_start();
include('./core/conn.php');

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // 从数据库中获取用户信息
    $id_query = "SELECT id, pfp, role FROM users WHERE username = '$username'";
    $id_result = $conn->query($id_query);

    if ($id_result->num_rows == 1) {
        $row = $id_result->fetch_assoc();
        $user_id = $row['id'];
        $avatarPath = $row['pfp'];
        $userRole = $row['role'];

        echo "<script>var userRole = '$userRole';</script>";

        // 创建用户下载文件夹
        $downloads_dir = 'downloads/';
        $user_dir = $downloads_dir . $username;
        if (!is_dir($user_dir)) {
            mkdir($user_dir, 0777, true);
        }
    } else {
        $avatarPath = 'default_avatar.jpg';
    }
} else {
    header("Location: login.php");
    exit();
}

// 处理搜索请求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_query'])) {
    $search_query = escapeshellarg($_POST['search_query']);
    $output = shell_exec("python ytdl.py search $search_query " . escapeshellarg($username) . " 2>&1");

    if ($output === null) {
        echo "Error: No output from Python script.";
        exit();
    }

    $results = json_decode($output, true);

    if ($results === null) {
        echo "Error: Failed to decode JSON. Output was: " . htmlspecialchars($output);
        exit();
    }

    ?>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            showResults(<?php echo json_encode($results); ?>);
        });
    </script>

    <?php
}


// 处理下载请求
if (isset($_GET['action']) && $_GET['action'] == 'download' && isset($_GET['url']) && isset($_GET['title']) && isset($_SESSION['username'])) {
    $url = $_GET['url'];
    $title = $_GET['title'];
    $username = $_SESSION['username'];
    $user_dir = "downloads/$username";
    $safe_title = preg_replace('/[\\/*?:"<>|]/', '', urldecode($title));
    $file_path = "$user_dir/$safe_title.mp3";

    // 检查文件是否已存在，避免重复下载
    if (!file_exists($file_path)) {
        // 下载歌曲和封面
        $output = shell_exec("python ytdl.py download " . escapeshellarg($url) . " " . escapeshellarg($user_dir) . " 2>&1");

        // 查找封面文件
        $thumbnail_path_array = glob("$user_dir/$safe_title.{jpg,jpeg,png,webp}", GLOB_BRACE);
        $thumbnail_path = $thumbnail_path_array ? $thumbnail_path_array[0] : './image/main/est.jpg';

        if (file_exists($file_path)) {
            // 重定向回主页面，带上下载的文件和封面信息
            header("Location: testsm.php?new_song=$safe_title&thumbnail=" . urlencode($thumbnail_path));
            exit();
        }
    } else {
        // 文件已存在，直接获取封面文件
        $thumbnail_path_array = glob("$user_dir/$safe_title.{jpg,jpeg,png,webp}", GLOB_BRACE);
        $thumbnail_path = $thumbnail_path_array ? $thumbnail_path_array[0] : './image/main/est.jpg';

        // 重定向回主页面，带上下载的文件和封面信息
        header("Location: testsm.php?new_song=$safe_title&thumbnail=" . urlencode($thumbnail_path));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IKULA</title>
    <link rel="icon" href="image/logo.ico" type="image/x-icon">
    <link rel="stylesheet" href="./css/main.css">
    <link rel="stylesheet" href="./css/font_header.css">
    <link rel="stylesheet" href="./css/font_leftBox.css">
    <link rel="stylesheet" href="css/font_footer.css">
    <link rel="stylesheet" href="css/downsongshownbeside.css">
    <link rel="stylesheet" href="./css/searchmusic.css">
</head>
<body>
<div class="header">
        <div class="logo">
                <img src="./image/logo.png" alt="Logo">
                <span style="color:white"><a href="index.php">Ventify</span>
            </a>
        </div>

            <div class="middle">
                <i class="iconfont icon-jiantou-xiangzuo"></i>
                <i class="iconfont icon-jiantou-xiangyou"></i>
            </div>

            <div class="other">
                <div class="userInfo">
                    <img src="<?php echo htmlspecialchars($avatarPath); ?>" alt="<?php echo htmlspecialchars($username); ?>">
                    <span><?php echo htmlspecialchars($username); ?></span>
                </div>
                <ul>
                    <li><a href="login.php"><i class="iconfont icon-zhuti"></i></a></li>
                    <li><a href="userpfp.php"><i class="iconfont icon-shezhi"></i></a></li>
                    <li><a href="premium.php"><i class="iconfont icon-xinfeng"></i></a></li>
                </ul>
            </div>
    </div>

    <h1>搜索歌曲</h1>
    <form method="POST">
        <input type="text" name="search_query" placeholder="输入歌曲名称">
        <button type="submit">搜索</button>
    </form>

        <!-- Modal 窗口 -->
        <div id="myModal" class="modal" style="display:none">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h1>搜索结果</h1>
            <ul id="searchResults"></ul>
        </div>
    </div>

        <!-- 重新打开弹窗按钮 -->
        <button id="openModalButton">打开搜索结果</button>

    <!-- 播放列表 -->
    <div class="playlist">
        <h2>播放列表</h2>
        <ul id="playlist">
            <?php
            $songs = [];
            if (is_dir($user_dir)) {
                $files = scandir($user_dir);
                foreach ($files as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'mp3') {
                        $thumbnail_path_array = glob("$user_dir/" . pathinfo($file, PATHINFO_FILENAME) . ".{jpg,jpeg,png,webp}", GLOB_BRACE);
                        $thumbnail_path = $thumbnail_path_array ? $thumbnail_path_array[0] : './image/main/est.jpg';
                        $songs[] = [
                            'title' => pathinfo($file, PATHINFO_FILENAME),
                            'path' => "$user_dir/$file",
                            'thumbnail' => $thumbnail_path
                        ];
                    }
                }
            }
            foreach ($songs as $song) {
                echo "<li><a href=\"#\" onclick=\"playSong('{$song['path']}', '{$song['title']}', '{$song['thumbnail']}')\">{$song['title']}</a></li>";
            }
            ?>
        </ul>
    </div>

    <div class="footer">
        <div class="ft_left">
            <img class="_img" src="./image/main/est.jpg" alt="">
            <div class="songNameAndSinger">
                <span class="songName">春夏秋冬reprise<i class="iconfont icon-aixin"></i></span>
                <span class="singer">當山みれい</span>
            </div>
        </div>

        <div class="ft_main">
            <!-- 播放栏工具 -->
            <ul class="tool_list">
                <li><i class="iconfont icon-lajitong"></i></li>
                <li><i class="iconfont icon-shangyishoushangyige"></i></li>
                <li onclick="bofang()"><i class="iconfont icon-bofang _audio"></i><audio id="ado"></audio></i></li>
                <li><i class="iconfont icon-xiayigexiayishou"></i></li>
                <li><i class="iconfont icon-geciweidianji"></i></li>
            </ul>
            <!-- 进度条 -->
            <div class="progress">                   
                <div class="slide"></div>
                <div class="fill"> </div>                  
                <!--歌曲当前时间与总时间  -->
                <span class="currentTime time">00:00</span>
                <span class="duraTime time">00:00</span>
           </div>
        </div>

        <ul class="ft_right">
            <li class="jigao">极高</li>
            <a href="#" id="addToPlaylist"><li class="iconfont icon-yinxiao"></li></a>
            <li class="iconfont icon-yinliangkai _voice"></li>
            <li class="iconfont icon-yiqipindan"></li>
            <li class="iconfont icon-24gl-playlistMusic" id="showDownloads"></li>
                <div id="downloadList">
                    <button class="close-btn">关闭</button>
                    <h2>Downloaded Songs</h2>
                    <ul>
                    </ul>
                </div>
        </ul>
    </div>

    <!-- 弹出消息 -->
    <div id="successMessage" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: #ffffff; padding: 20px; border: 1px solid #000000; z-index: 9999;">
        Song added to playlist successfully.
    </div>
    <div id="errorMessage" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: #ffffff; padding: 20px; border: 1px solid #000000; z-index: 9999;">
        Song already exists in the playlist.
    </div>

    <script src="./js/listen.js"></script>
    <script src="./js/song_lycris.js"></script>
    <script src="./js/changeStyle.js"></script>
    <script src="./js/show.js"></script>
    <script src="./js/ad.js"></script>
</body>
</html>