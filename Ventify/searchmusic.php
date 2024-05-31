<?php
session_start();

include('./core/conn.php');

if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    $id_query = "SELECT id, pfp, role FROM users WHERE username = '$username'";
    $id_result = $conn->query($id_query);

    if ($id_result->num_rows == 1) {
        // Fetch the user's ID, avatar path, and role
        $row = $id_result->fetch_assoc();
        $user_id = $row['id'];
        $avatarPath = $row['pfp'];
        $userRole = $row['role'];

        // Send the user's role to the frontend
        echo "<script>var userRole = '$userRole';</script>";
    } else {
        $avatarPath = 'default_avatar.jpg';
    }
} else {
    header("Location:login.php");
    exit();
}

$song_downloaded = false;
$audio_player = "";
$downloads_dir = 'downloads/';

$downloaded_songs = [];
if ($handle = opendir($downloads_dir)) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            $downloaded_songs[] = $entry;
        }
    }
    closedir($handle);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST["search"])) {
        $song = escapeshellarg($_POST["search"]);
        // Replace with the actual path to music.py script
        $command = escapeshellcmd("python music.py " . $song);
        $output = [];
        $return_var = 0;
        // Execute the command and capture the output
        exec($command, $output, $return_var);
        // Output the result of the Python script execution
        $search_results = $output; // Store search results
    } elseif (!empty($_POST["song"])) {
        $selected_song = $_POST["song"];
        $file_path = $downloads_dir . $selected_song;

        if (file_exists($file_path)) {
            if (is_readable($file_path)) {
                $song_downloaded = true;
                $audio_player = "<audio controls autoplay><source src='$file_path' type='audio/mp3'>Your browser does not support the audio element.</audio>";
            } else {
                $audio_player = "<p>File cannot be accessed. Please check file permissions.</p>";
            }
        } else {
            $audio_player = "<p>File does not exist. Please check download path and server configuration.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>音乐播放器</title>
    <link rel="stylesheet" href="./css/searchmusic.css">    
    <link rel="icon" href="image/logo.ico" type="image/x-icon">
    <link rel="stylesheet" href="./css/font_header.css">
    <link rel="stylesheet" href="./css/font_leftBox.css">
    <link rel="stylesheet" href="css/font_footer.css">
    <link rel="stylesheet" href="css/song_lyrics.css">
</head>
<body>

<div class="container">
    <!-- 头部 -->
    <div class="header">
        <div class="logo">
            <img src="./image/logo.png" alt="">
            <span>Ventify</span>
        </div>

        <div class="middle">
            <i class="iconfont icon-jiantou-xiangzuo"></i>
            <i class="iconfont icon-jiantou-xiangyou"></i>
            <div class="search">
                <i class="iconfont icon-sousuo"><a href="searchmusic.php">Search</a></i>
            </div>
        </div>


        <div class="other">
            <div class="userInfo">
                <img src="<?php echo $avatarPath; ?>" alt="<?php echo $username; ?>">
                <span><?php echo $username; ?></span>
            </div>
            <ul>
                <li><a href="login.php"><i class="iconfont icon-zhuti"></i></a></li>
                <li><a href="userpfp.php"><i class="iconfont icon-shezhi"></i></a></li>
                <li><a href="premium.php"><i class="iconfont icon-xinfeng"></i></a></li>
            </ul>
    
        </div>
    </div>

    <!-- 渐变线条 -->
    <div class="line"></div>

    <!-- 中间 -->
    <div class="main">
        <div class="search-box">
            <h2>输入歌曲名以下载并播放</h2>
            <form method="post">
                <div class="search-bar">
                    <input type="text" name="search" placeholder="Search" value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>">
                    <button type="submit">查找并下载</button>
                </div>
            </form>

            <?php if (!empty($search_results)): ?>
                <h2>搜索结果：</h2>
                <form method="post">
                    <select name="song" required>
                        <?php $count = 0; foreach ($search_results as $result): ?>
                            <?php if ($count >= 8) break; ?>
                            <option value="<?php echo htmlspecialchars($result); ?>"><?php echo htmlspecialchars($result); ?></option>
                            <?php $count++; ?>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">下载</button>
                </form>
            <?php endif; ?>

            <?php if ($song_downloaded || $audio_player != ""): ?>
                <h3>正在播放: <?php echo htmlspecialchars(!empty($selected_song) ? $selected_song : $_POST["search"]); ?></h3>
                <?php echo $audio_player; ?>
            <?php endif; ?>

            <h2>选择要播放的已下载歌曲</h2>
            <form method="post">
                <select name="song" required>
                    <?php foreach ($downloaded_songs as $song): ?>
                        <option value="<?php echo htmlspecialchars($song); ?>"><?php echo htmlspecialchars($song); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">播放</button>
            </form>
        </div>
    </div>

    <!-- 底部 -->
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
            <li class="iconfont icon-yinliangkai _voice"></li><!---when click at this button the song will at into playlist--->
            <li class="iconfont icon-yiqipindan"></li>
            <!-- 图标，用于触发显示歌单 -->
            <li class="iconfont icon-24gl-playlistMusic" onclick="toggleDownloadedSongs()"></li>

            <!-- 新增的已下载歌曲列表容器 -->
            <div id="downloadedSongsContainer">
                <h4>Playlist</h4>
                <ul>
                    <?php foreach ($downloaded_songs as $song): ?>
                        <li><?php echo htmlspecialchars($song); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>





        </ul>
    </div>
</div>
<script src="./js/listen.js"></script>
    <script src="./js/song_lycris.js"></script>
    <script src="./js/changeStyle.js"></script>
</body>
</html>
