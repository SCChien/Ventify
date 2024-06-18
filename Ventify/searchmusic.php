<?php
session_start();
include('./core/conn.php');
error_reporting(E_WARNING);

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

    <!-- <h1>搜索结果</h1>
    <ul>
        <?php foreach ($results as $result): ?>
            <li>
                <img src="<?php echo htmlspecialchars($result['thumbnail']); ?>" alt="Thumbnail" width="100">
                <?php echo htmlspecialchars($result['title']); ?> (<?php echo gmdate("H:i:s", $result['duration']); ?>)
                <a href="?action=download&url=<?php echo urlencode($result['url']); ?>&title=<?php echo urlencode($result['title']); ?>">下载并播放</a>
            </li>
        <?php endforeach; ?>
    </ul> -->

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
            header("Location: searchmusic.php?new_song=$safe_title&thumbnail=" . urlencode($thumbnail_path));
            exit();
        } else {
            echo "Download failed,please try again。";
        }
    } else {
        // 文件已存在，直接获取封面文件
        $thumbnail_path_array = glob("$user_dir/$safe_title.{jpg,jpeg,png,webp}", GLOB_BRACE);
        $thumbnail_path = $thumbnail_path_array ? $thumbnail_path_array[0] : './image/main/est.jpg';

        // 重定向回主页面，带上下载的文件和封面信息
        header("Location: searchmusic.php?new_song=$safe_title&thumbnail=" . urlencode($thumbnail_path));
        exit();
    }
}
$jsonData = file_get_contents('./sql/album.json');
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
    file_put_contents('./sql/album.json', $jsonData);
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
    <script src="https://kit.fontawesome.com/4ad611b6f2.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="header">
    <div class="logo">
                <img src="./image/logo.png" alt="Logo">
                <span style="color:white"><a href="index.php">Ventify</span>
            </a>
        </div>
        <div class="middle">
            <div class="search">
                <i class="iconfont icon-sousuo"><a href="searchmusic.php">Search</a></i>
            </div>
        </div>
        <div class="other">
            <a href="userpfp.php"><div class="userInfo">
                <img src="<?php echo $avatarPath; ?>" alt="<?php echo $username; ?>">
                <span><?php echo $username; ?></span>
            </div></a>
            <ul>
                <li><a href="login.php"><i class="fa-solid fa-right-from-bracket"></i></i></a></li>
            </ul>
    
        </div>
    </div>
<!-- 渐变线条 -->
<div class="line"></div>
    <h1>Search Music</h1>
    <form method="POST">
        <input type="text" name="search_query" placeholder="Enter song name">
        <button type="submit">Search</button>
    </form>

        <!-- Modal 窗口 -->
        <div id="myModal" class="modal" style="display:none">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h1>Search Result</h1>
            <ul id="searchResults">
            <?php if (isset($results) && is_array($results)): ?>
                <?php foreach ($results as $result): ?>
                <li>
                    <img src="<?php echo htmlspecialchars($result['thumbnail']); ?>" alt="Thumbnail" width="100">
                    <?php echo htmlspecialchars($result['title']); ?> (<?php echo gmdate("H:i:s", $result['duration']); ?>)
                    <a href="?action=download&url=<?php echo urlencode($result['url']); ?>&title=<?php echo urlencode($result['title']); ?>">Download & Play</a>
                </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No result</li>
            <?php endif; ?>
            </ul>
        </div>

    </div>

        <!-- 重新打开弹窗按钮 -->
        <button id="openModalButton">Search Result</button>

    <!-- 播放列表 -->
    <div class="playlist1">
        <h2>Playlist</h2>
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
            <div class="song_Name">    
                        <span class="songName">春夏秋冬reprise</span><i class="iconfont icon-aixin" id="showPopup"></i>
                    </div>
                <span class="singer">當山みれい</span>
            </div>
        </div>

        <div class="ft_main">
            <!-- 播放栏工具 -->
            <ul class="tool_list">
                <li class="prev"><i class="iconfont icon-shangyishoushangyige"></i></li>
                <li onclick="bofang()"><i class="iconfont icon-bofang _audio"></i><audio id="ado"></audio></li>
                <li class="next"><i class="iconfont icon-xiayigexiayishou"></i></li>
            </ul>
            <!-- 进度条 -->
            <div class="progress">                   
                <div class="slide"></div>
                <div class="fill"></div>                  
                <!--歌曲当前时间与总时间  -->
                <span class="currentTime time">00:00</span>
                <span class="duraTime time">00:00</span>
            </div>
        </div>

        <ul class="ft_right">
            <li class="iconfont icon-yinliangkai _voice"></li>
            <li class="iconfont icon-yiqipindan" onclick="showSharePopup()"></li>
            <li class="iconfont icon-24gl-playlistMusic" id="showDownloads"></li>
            <div id="downloadList">
                <button class="close-btn">Close</button>
                <h2>Downloaded Songs</h2>
                <ul></ul>
            </div>
        </ul>
    </div>

<div id="popupWindow" class="popupWindow">
    <span class="close">&times;</span>
    <div class="create_album">
        <h3>Create Playlist</h3>
        <form method='post'>
            <label for='album_name'>Playlist Name:</label>
            <input type='text' id='album_name' name='album_name' required><br><br>
            <input type='submit' name='create_album' value='Create Album'>
        </form>
    </div>
                
    <!-- 添加歌曲的表单 -->
    <div class="add_song">
        <h2>Add to Playlist</h2>
        <form method="post">
            <label for="album">Select Playlist:</label>
            <select id="album" name="album">
                <?php foreach ($database[$username]['albums'] as $albumName => $playlist): ?>
                    <option value="<?php echo $albumName; ?>"><?php echo $albumName; ?></option>
                <?php endforeach; ?>
            </select><br><br>
            <label for="downloaded_song">Select Downloaded Song:</label>
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
            <input type="submit" name="add_downloaded_song" value="Add Downloaded Song">
        </form>
    </div>
</div>
<!-- 分享和接受歌曲弹窗 -->
<div id="sharePopup" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: #ffffff; padding: 20px; border: 1px solid #000000; z-index: 9999;">
        <button onclick="generateToken()">Generate Share Token</button>
        <br>
        <input type="text" id="tokenInput" placeholder="输入Token">
        <button onclick="acceptSong()">Received Friend Song</button>
        <button onclick="closePopup()">Close</button>
        <div id="downloadArea" style="margin-top: 20px;"></div>
    </div>



    <script src="./js/listen.js"></script>
    <script src="./js/changeStyle.js"></script>
    <script src="./js/show.js"></script>
    <script src="./js/sharefriend.js"></script>
    <script src="./js/ad.js"></script>
</body>
</html>