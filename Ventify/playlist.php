<!-- 添加了特定用户登入时会显示特定用户的头像和username利用username获取用户id再用id获取头像 -->
<?php
session_start();

include('./core/conn.php');

// 检查用户是否登录，如果未登录则跳转到登录页面
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    $id_query = "SELECT id, pfp FROM users WHERE username = '$username'";
    $id_result = $conn->query($id_query);

    if ($id_result->num_rows == 1) {
        // Fetch the user's ID and avatar path
        $row = $id_result->fetch_assoc();
        $user_id = $row['id'];
        $avatarPath = $row['pfp'];
    } else {
        $avatarPath = 'default_avatar.jpg';
    }
} else {
    header("Location:login.php");
    exit();
}

// 获取当前登录的用户名
$username = $_SESSION['username'];

// 读取 JSON 数据库
$jsonData = file_get_contents('./sql/album.json');
$database = json_decode($jsonData, true);
$userPlaylist = './sql/album.json';

// 检查用户是否存在于数据库中，如果不存在则创建新用户记录
if(!isset($database[$username])) {
    $database[$username] = array(
        "username" => $username,
        "albums" => array()
    );
}

// 处理用户的专辑操作
if(isset($_POST['create_album'])) {
    $albumName = $_POST['album_name'];
    $database[$username]['albums'][$albumName] = array();
    saveDatabase($database);
    echo "<p>Album '$albumName' created successfully!</p>";
    header("Location: playlist.php");
    exit();
}

if(isset($_POST['add_song'])) {
    $albumName = $_POST['album'];
    $songName = $_POST['song_name'];
    $author = $_POST['author'];
    $database[$username]['albums'][$albumName][] = "$songName, $author";
    saveDatabase($database);
    echo "<p>Song '$songName' added to album '$albumName' successfully!</p>";
    header("Location: playlist.php");
    exit();
}

// 保存数据库到 JSON 文件
function saveDatabase($database) {
    $jsonData = json_encode($database, JSON_PRETTY_PRINT);
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
    <link rel="stylesheet" href="css/song_lyrics.css">
    <link rel="stylesheet" href="css/playlist.css">
    
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
                    <li><span>Explore Music</span></li>
                    <li><span>播客</span></li>
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
                <ul class="navigation">
                    <li class="active"><a href="index.php"><span>Home</span></a></li>
                    <li><span>Recommend Song</span></li>
                    <li><a href="playlist.php"><span>Album Playlist</span></a></li>
                    <li><span>Best of Song</span></li>
                    <li><a href="singer.php"><span>Singer</span></a></li></li>
                </ul> 
                <div class="playlist">
                        <div class="album">
                            <h2>Albums for <?php echo $username; ?></h2>
                            <div id="playlistNames">
                                <?php
                                foreach($database[$username]['albums'] as $albumName => $playlist) {
                                    echo "<p class='playlistName' data-album='$albumName'>$albumName</p>";
                                }
                                ?>
                            </div>
                            <div id="playlistContent" style="display: none;">
                                
                                <!-- 用于显示 playlist 中的歌曲 -->
                            </div>
                            <button id="backButton" style="display: none;">Back to Playlists</button>
                        </div>
                    </div>
            </div>
        </div>
        

        <!-- 底部 -->   
        <div class="footer">
            <div class="ft_left">
                <img class="_img" src="./image/main/est.jpg" alt="">
                <div class="songNameAndSinger">
                    <span class="songName">春夏秋冬reprise<i class="iconfont icon-aixin" id="showPopup"></i></span>
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
                <li class="iconfont icon-yinxiao"></li>
                <li class="iconfont icon-yinliangkai _voice"></li><!---when click at this button the song will at into playlist--->
                <li class="iconfont icon-yiqipindan"></li>
                <li class="iconfont icon-24gl-playlistMusic"></li>
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
            <h3>Add Song</h3>
            <form method='post'>
            <label for='album'>Select Album:</label>
            <select id='album' name='album'>
            <?php
            foreach($database[$username]['albums'] as $albumName => $playlist) {
                echo "<option value='$albumName'>$albumName</option>";
            }
            ?>
            </select><br><br>
            <label for='title'>Song Title:</label>
            <input type='text' id='title' name='title' required><br><br>
            <label for='author'>Song Author:</label>
            <input type='text' id='author' name='author' required><br><br>
            <input type='submit' name='add_song' value='Add Song'>
            </form>
        </div>
    </div>
    
    <!-- JavaScript 部分 -->
    <script src="./js/listen.js"></script>
    <script src="./js/song_lycris.js"></script>
    <script src="./js/changeStyle.js"></script>
    <script src="./js/playlist.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const playlistNames = document.querySelectorAll('.playlistName');
        const playlistNamesContainer = document.getElementById('playlistNames');
        const playlistContent = document.getElementById('playlistContent');
        const backButton = document.getElementById('backButton');

        playlistNames.forEach(name => {
            name.addEventListener('click', function() {
                const albumName = this.getAttribute('data-album');
                
                fetch('get_playlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `album=${albumName}`
                })
                .then(response => response.json())
                .then(data => {
                    let html = `<h3>Playlist: ${albumName}</h3>`;
                    html += "<table>";
                    html += "<thead>";
                    html += "<tr>";
                    html += "<th>#</th>";
                    html += "<th>Title</th>";
                    html += "<th>Author</th>";
                    html += "</tr>";
                    html += "</thead>";
                    html += "<tbody>";
                    
                    data.songs.forEach((song, index) => {
                        html += "<tr>";
                        html += `<td>${index + 1}</td>`;
                        html += `<td>${song.title}</td>`;
                        html += `<td>${song.author}</td>`;
                        html += "</tr>";
                    });
                    
                    html += "</tbody>";
                    html += "</table>";

                    playlistContent.innerHTML = html;
                    backButton.style.display = 'block';
                    playlistNamesContainer.style.display = 'none';
                    playlistContent.style.display = 'block';
                });
            });
        });

        backButton.addEventListener('click', function() {
            playlistContent.style.display = 'none';
            backButton.style.display = 'none';
            playlistNamesContainer.style.display = 'block';
        });
    });
</script>


    
</body>
</html>