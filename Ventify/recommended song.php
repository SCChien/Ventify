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
    <link rel="stylesheet" href="./css/recommendedsong.css">
    <script src="https://kit.fontawesome.com/4ad611b6f2.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <!-- 头部 -->
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

        <!-- 中间 -->
        <div class="main">
            <div class="left-box">
                <ul>
                    <a href="index.php"><li><span>Home</span></li></a>
                    <a href="playlist.php"><li><span>Playlist</span></li></a>
                    <a href="recommended song.php"><li><span>Recommended</span></li></a>
                    <a href="userpfp.php"><li><span>Profile</span></li></a>
                    <a href="premium.php"><li><span>Premium</span></li></a>
                </ul>
                <div class="my_music">
                    <span></span>
                </div>
                <ul class="mine">
                    <li><i class="iconfont icon-bendixiazai"></i><span>Downloaded Song</span></li>
                    
                </ul>
                
            </div>
            <div class="right-box">
                <div class="recommend-container">
                <div class="recommendedsong">
                    <h1>Recommend</h1>
                    <ul>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <li>
                                <?php echo htmlspecialchars($row['title']); ?>
                                <a href="?action=download&url=<?php echo urlencode($row['url']); ?>&title=<?php echo urlencode($row['title']); ?>">Download</a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
                <div class="play-list">
                    <h2>Playlist</h2>
                    <ul id="playlist">
                        <?php
                        $user_dir = "downloads/$username";

                        if (is_dir($user_dir)) {
                            $files = @scandir($user_dir);

                            if ($files !== false) {
                                foreach ($files as $file) {
                                    if (pathinfo($file, PATHINFO_EXTENSION) === 'mp3') {
                                        $safe_title = pathinfo($file, PATHINFO_FILENAME);
                                        $thumbnail_path_array = glob("$user_dir/$safe_title.{jpg,jpeg,png,webp}", GLOB_BRACE);
                                        $thumbnail_path = $thumbnail_path_array ? $thumbnail_path_array[0] : './image/main/est.jpg';
                                        echo "<li><a href=\"#\" onclick=\"playSong('$user_dir/$file', '$safe_title', '$thumbnail_path')\">$safe_title</a></li>";
                                    }
                                }
                            } else {
                                echo "<li>No MP3 files found.</li>";
                            }
                        } else {
                            echo "<li>Directory not found.</li>";
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
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
        <h3>Create Album</h3>
        <form method='post'>
            <label for='album_name'>Album Name:</label>
            <input type='text' id='album_name' name='album_name' required><br><br>
            <input type='submit' name='create_album' value='Create Album'>
        </form>
    </div>
                
    <!-- 添加歌曲的表单 -->
    <div class="add_song">
        <h2>Add to Playlist</h2>
        <form method="post">
            <label for="album">Select Ablum:</label>
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
    <script src="./js/playlist.js"></script>
    <script src="./js/changeStyle.js"></script>
    <script src="./js/show.js"></script>
    <script src="./js/sharefriend.js"></script>
    <script src="./js/ad.js"></script>
</body>
</html>