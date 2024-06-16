<?php
session_start();
include('./core/conn.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

$query = "SELECT title, url FROM recommended_songs WHERE username = '$username' ORDER BY search_time DESC LIMIT 10";
$result = $conn->query($query);

$new_song = null;
$thumbnail = null;

if (isset($_GET['action']) && $_GET['action'] == 'download' && isset($_GET['url']) && isset($_GET['title'])) {
    $url = $_GET['url'];
    $title = $_GET['title'];
    $user_dir = "downloads/$username";
    $safe_title = preg_replace('[\\/*?:"<>|]', '', urldecode($title));
    $file_path = "$user_dir/$safe_title.mp3";

    // 检查文件是否已存在，避免重复下载
    if (!file_exists($file_path)) {
        // 下载歌曲和封面
        $output = shell_exec("python ytdl.py download " . escapeshellarg($url) . " " . escapeshellarg($user_dir) . " 2>&1");

        // 查找封面文件
        $thumbnail_path_array = glob("$user_dir/$safe_title.{jpg,jpeg,png,webp}", GLOB_BRACE);
        $thumbnail_path = $thumbnail_path_array ? $thumbnail_path_array[0] : './image/main/est.jpg';

        if (file_exists($file_path)) {
            // 设置新下载的歌曲信息
            $new_song = $safe_title;
            $thumbnail = $thumbnail_path;
        } else {
            echo "下载失败，请重试。";
        }
    } else {
        // 文件已存在，直接获取封面文件
        $thumbnail_path_array = glob("$user_dir/$safe_title.{jpg,jpeg,png,webp}", GLOB_BRACE);
        $thumbnail_path = $thumbnail_path_array ? $thumbnail_path_array[0] : './image/main/est.jpg';

        // 设置新下载的歌曲信息
        $new_song = $safe_title;
        $thumbnail = $thumbnail_path;
    }
}

$jsonData = file_get_contents('album.json');
$database = json_decode($jsonData, true, 512, JSON_UNESCAPED_UNICODE);

if (!isset($database[$username])) {
    $database[$username] = array(
        "username" => $username,
        "albums" => array("favourite" => array())
    );
}

if (!isset($database[$username]['albums']['favourite'])) {
    $database[$username]['albums']['favourite'] = array();
}

if (isset($_POST['create_album'])) {
    $albumName = $_POST['album_name'];
    if ($albumName !== 'favourite' && !isset($database[$username]['albums'][$albumName])) {
        $database[$username]['albums'][$albumName] = array();
        saveDatabase($database);
        echo "<script>alert('Album \"$albumName\" created successfully!');window.location.href = 'recommended song.php';</script>";
    } else {
        echo "<script>alert('Cannot create album named \"favourite\" or album already exists.');window.location.href = 'recommended song.php';</script>";
    }
}

if (isset($_POST['add_downloaded_song'])) {
    $albumName = $_POST['album'];
    $downloadedSong = $_POST['downloaded_song'];
    $songTitle = pathinfo($downloadedSong, PATHINFO_FILENAME);

    $isSongExist = false;
    foreach ($database[$username]['albums'][$albumName] as $song) {
        if ($song['song'] === $downloadedSong) {
            $isSongExist = true;
            break;
        }
    }

    if ($isSongExist) {
        echo "<script>alert('Song \"$downloadedSong\" is already in the album \"$albumName\".');window.location.href = 'recommended song.php';</script>";
    } else {
        $thumbnailExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $thumbnail = '';
        foreach ($thumbnailExtensions as $ext) {
            $thumbnailPath = "downloads/$username/$songTitle.$ext";
            if (file_exists($thumbnailPath)) {
                $thumbnail = $thumbnailPath;
                break;
            }
        }
        if (empty($thumbnail)) {
            $thumbnail = 'default_thumbnail.jpg';
        }

        $database[$username]['albums'][$albumName][] = array('song' => $downloadedSong, 'thumbnail' => $thumbnail);
        saveDatabase($database);
        echo "<script>alert('Downloaded song \"$downloadedSong\" added to album \"$albumName\" successfully!');window.location.href = 'recommended song.php';</script>";
    }
}

if (isset($_POST['delete_song'])) {
    $albumName = $_POST['album'];
    $songToDelete = $_POST['song_to_delete'];

    foreach ($database[$username]['albums'][$albumName] as $index => $song) {
        if ($song['song'] === $songToDelete) {
            unset($database[$username]['albums'][$albumName][$index]);
            break;
        }
    }

    saveDatabase($database);
    echo "<script>alert('Song \"$songToDelete\" deleted from album \"$albumName\" successfully!');window.location.href = 'recommended song.php';</script>";
}

function deleteAlbum($username, $albumName, &$database)
{
    if ($albumName !== 'favourite' && isset($database[$username]['albums'][$albumName])) {
        unset($database[$username]['albums'][$albumName]);
        saveDatabase($database);
        return true;
    } else {
        return false;
    }
}

function saveDatabase($database)
{
    $jsonData = json_encode($database, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents('album.json', $jsonData);
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
    <link rel="stylesheet" href="./css/recommendedsong.css">
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
            <div class="left-box">
                <ul>
                    <a href="index.php"><li><span>Home</span></li></a>
                    <a href="playlist.php"><li><span>Playlist</span></li></a>
                    <li><span>视频</span></li>
                    <li><span>关注</span></li>
                    <li><span>直播</span></li>
                    <li><span>私人FM</span></li>
                </ul>
                <div class="my_music">
                    <span>我的音乐</span>
                </div>
                <ul class="mine">
                    <li><i class="iconfont icon-bendixiazai"></i><span>Downloaded Song</span></li>
                    <li><i class="iconfont icon-zuijinbofang"></i><span>History</span></li>
                    <li><i class="iconfont icon-ego-favorite"></i><span>Collection</span></li>
                </ul>
                <div class="create_list">
                    <span>
                        创建的歌单
                        <i class="iconfont icon-ico_arrowright"></i>
                        <i class="iconfont icon-jia i_last"></i>
                    </span>
                </div>
                <div class="create_list">
                    <span>
                        收藏的歌单
                        <i class="iconfont icon-ico_arrowright"></i>
                    </span>
                </div>
            </div>
            <div class="right-box">
                <div class="recommend-container">
                <div class="recommendedsong">
                    <h1>推荐歌曲</h1>
                    <ul>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <li>
                                <?php echo htmlspecialchars($row['title']); ?>
                                <a href="?action=download&url=<?php echo urlencode($row['url']); ?>&title=<?php echo urlencode($row['title']); ?>">下载并播放</a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
                <div class="play-list">
                    <h2>播放列表</h2>
                    <ul id="playlist">
                        <?php
                        $user_dir = "downloads/$username";
                        $files = scandir($user_dir);
                        foreach ($files as $file) {
                            if (pathinfo($file, PATHINFO_EXTENSION) === 'mp3') {
                                $safe_title = pathinfo($file, PATHINFO_FILENAME);
                                $thumbnail_path_array = glob("$user_dir/$safe_title.{jpg,jpeg,png,webp}", GLOB_BRACE);
                                $thumbnail_path = $thumbnail_path_array ? $thumbnail_path_array[0] : './image/main/est.jpg';
                                echo "<li><a href=\"#\" onclick=\"playSong('$user_dir/$file', '$safe_title', '$thumbnail_path')\">$safe_title</a></li>";
                            }
                        }
                        ?>
                    </ul>
                </div>
                </div>
            </div>
        </div>

        <!-- 底部 -->   
        <div class="footer">
            <div class="ft_left">
                <img class="_img" src="./image/main/est.jpg" alt="">
                <div class="songNameAndSinger">
                    <div class="songName">    
                        <span class="song_Name">春夏秋冬reprise</span><i class="iconfont icon-aixin" id="showPopup"></i>
                    </div>
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
                <li class="iconfont icon-24gl-playlistMusic" id="showDownloads"></li>
                <div id="downloadList">
                    <button class="close-btn">关闭</button>
                    <h2>Downloaded Songs</h2>
                    <ul>
                    </ul>
                </div>
            </ul>
        </div>
    </div>
    <div id="popupWindow" class="popupWindow">
    <span class="close">&times;</span>
    <div class="create_album">
        <h3>Create Album</h3>
        <form method='post'>
            <label for='album_name'>Album Name:</label>
            <input type='text' id='album_name' name='album_name' required><br><br>
            <input type='submit' name='create_album' value='Create Album'>
        </form>
    </div>
                
    <!-- 添加歌曲的表单 -->
    <div class="add_song">
        <h2>添加已下载的歌曲到专辑</h2>
        <form method="post">
            <label for="album">选择专辑:</label>
            <select id="album" name="album">
                <?php foreach ($database[$username]['albums'] as $albumName => $playlist): ?>
                    <option value="<?php echo $albumName; ?>"><?php echo $albumName; ?></option>
                <?php endforeach; ?>
            </select><br><br>
            <label for="downloaded_song">选择已下载的歌曲:</label>
            <select id="downloaded_song" name="downloaded_song">
                <?php
                $user_dir = "downloads/$username";
                $files = scandir($user_dir);
                foreach ($files as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'mp3') {
                        $safe_title = pathinfo($file, PATHINFO_FILENAME);
                        echo "<option value='$safe_title'>$safe_title</option>";
                    }
                }
                ?>
            </select><br><br>
            <input type="submit" name="add_downloaded_song" value="添加已下载的歌曲">
        </form>
    </div>
</div>

    <script src="./js/playlist.js"></script>
    <script src="./js/listen.js"></script>
    <script src="./js/changeStyle.js"></script>
    <script src="./js/show.js"></script>

    
    
</body>
</html>