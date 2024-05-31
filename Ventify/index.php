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
                    <li><span>Explore Music</span></a></li>
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
                    <li class="active"><span>Home</span></li>
                    <li><span>Recommend Song</span></li>
                    <li><a href="playlist.php"><span>Album Playlist</span></a></li>
                    <li><span>Best of Song</span></li>
                    <li><a href="singer.php"><span>Singer</span></a></li></li>
                </ul> 


                <div class="recm_list">
                    <div class="recm_word">推荐歌曲<i class="iconfont icon-jiantou-xiangyou"></i></div>
                    <ul>
                        <li>
                            <img src="./image/main/silasila.jpg" alt="">
                            <span>夜に駆ける<br>YOASOBI</span>                            
                        </li>
                        <li>   
                            <img src="./image/main/callYourName.jpg" alt="">
                            <span>Call your name<br>李阿亚</span>                           
                        </li>
                        <li>   
                            <img src="./image/main/bocchi.jpg" alt="">
                            <span>万疆<br>李玉刚</span>
                        </li>
                        <li>
                            <img src="./image/main/smadick.jpg" alt="">
                            <span>アムリタ<br>牧野由依</span>
                        </li>
                        <li>
                            <img src="./image/main/aaaaaa.jpg" alt="">
                            <span>群青<br>YOASOBI</span>
                        </li>
                    </ul>
                </div>
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
                <li class="iconfont icon-24gl-playlistMusic"></li>
            </ul>
        </div>
    </div>
    <!-- Place this div anywhere within the body tag -->
    <div id="successMessage" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: #ffffff; padding: 20px; border: 1px solid #000000; z-index: 9999;">
        Song added to playlist successfully.
    </div>
    <div id="errorMessage" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: #ffffff; padding: 20px; border: 1px solid #000000; z-index: 9999;">
    Song already exists in the playlist.
    </div>


    <div class="lyrics-section" style="display: none;">
        <div class="lyrics-overlay"></div>
            <div class="lyrics-content">
                <div class="song-details">
                    <img src="./image/logo.png" alt="Song Photo">
                    <div class="details-text">
                        <h2>Artist Name</h2>
                        <p>Song Name</p>
                    </div>
                </div>
                <div class="song-lyrics">
                    <h2>Song Lyrics</h2>
                    <p class="lyrics">These are the song lyrics.</p>
                </div>
            </div>
        </div>

    <script src="./js/listen.js"></script>
    <script src="./js/song_lycris.js"></script>
    <script src="./js/changeStyle.js"></script>
    <script>
document.addEventListener("DOMContentLoaded",   () {
    // Add event listener to the anchor tag
    document.getElementById("addToPlaylist").addEventListener("click", function(event) {
        event.preventDefault(); // Prevent default anchor behavior

        // Get the song details
        var songName = document.querySelector(".songName").textContent.trim();
        var singer = document.querySelector(".singer").textContent.trim();

        // Send AJAX request to PHP script
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "add_to_playlist.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Handle the response
                var response = xhr.responseText.trim();
                if (response === "Song added to playlist successfully.") {
                    // Show success message
                    document.getElementById("successMessage").innerText = response;
                    document.getElementById("successMessage").style.display = "block";
                    // Hide success message after 3 seconds
                    setTimeout(function() {
                        document.getElementById("successMessage").style.display = "none";
                    }, 3000);                           
                } else {
                    // Show error message
                    document.getElementById("errorMessage").innerText = response;
                    document.getElementById("errorMessage").style.display = "block";
                    // Hide error message after 3 seconds
                    setTimeout(function() {
                        document.getElementById("errorMessage").style.display = "none";
                    }, 3000);
                }
            }
        };
        xhr.send("songName=" + encodeURIComponent(songName) + "&singer=" + encodeURIComponent(singer));
    });
});
</script>
</body>
</html>