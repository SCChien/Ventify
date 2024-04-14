<!-- 添加了特定用户登入时会显示特定用户的头像和username利用username获取用户id再用id获取头像 -->


<?php
session_start();

include('conn.php');

if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    $id_query = "SELECT id, avatar_path FROM users WHERE username = '$username'";
    $id_result = $conn->query($id_query);

    if ($id_result->num_rows == 1) {
        // Fetch the user's ID and avatar path
        $row = $id_result->fetch_assoc();
        $user_id = $row['id'];
        $avatarPath = $row['avatar_path'];
    } else {
        $avatarPath = 'default_avatar.jpg';
    }
} else {
    header("Location: login.php");
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
                    <i class="iconfont icon-sousuo"></i>
                    <input type="text" placeholder="搜索">
                </div>
                <i class="iconfont icon-mic-on"></i>
            </div>


            <div class="other">
                <div class="userInfo">
                    <img src="<?php echo $avatarPath; ?>" alt="<?php echo $username; ?>">
                    <span><?php echo $username; ?></span>
                </div>
                <ul>
                    <li><a href="login.php"><i class="iconfont icon-zhuti"></i></a></li>
                    <li><a href="test.php"><i class="iconfont icon-shezhi"></i></a></li>
                    <li><i class="iconfont icon-xinfeng"></i></li>
                    <li class="vertical_bar"></li>
                    <li><i class="iconfont icon-MINIMIZE"></i></li>
                    <li><i class="iconfont icon-zuixiaohua"></i></li>
                    <li><i class="iconfont icon-zuidahua"></i></li>
                    <li><i class="iconfont icon-guanbi"></i></li>
                </ul>
        
            </div>
        </div>

        <!-- 渐变线条 -->
        <div class="line"></div>

        <!-- 中间 -->
        <div class="main">
            <div class="left-box">
                <ul>
                    <li><span>发现音乐</span></li>
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
                    <li><i class="iconfont icon-bendixiazai"></i><span>本地与下载</span></li>
                    <li><i class="iconfont icon-zuijinbofang"></i><span>最近播放</span></li>
                    <li><i class="iconfont icon-yun"></i><span>我的音乐云盘</span></li>
                    <li><i class="iconfont icon-boke1"></i><span>我的播客</span></li>
                    <li><i class="iconfont icon-ego-favorite"></i><span>我的收藏</span></li>
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
                    <li class="active"><span>个性推荐</span></li>
                    <li><span>专属定制</span></li>
                    <li><span>歌单</span></li>
                    <li><span>排行榜</span></li>
                    <li><span>歌手</span></li></li>
                    <li><span>最新音乐</span></li>
                </ul> 
                <div class="banner">
                    <div class="infomation">
                        <h4>M♭</h4>
                        <p>&nbsp;&nbsp;&nbsp;&nbsp;M♭是TV动画《路人女主的养成方法》03话14:51时的插曲，同时也是加藤惠的角色歌，收录在同名角色歌专辑中（冴えない彼女の育てかたキャラクターイメージソング加藤恵）。由其声优：安野希世乃演唱。
                        </p>
                        <div class="btn_listen btn_listen_1">
                            <span>点击试听</span>
                        </div>
                        <div class="btn_listen btn_listen_2">
                            <span>点击收藏</span>
                        </div>
                    </div>
                    <img src="./image/qwe.jpg" alt="">
                </div>

                <div class="dot">
                    <ul class="dots">
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                    </ul>
                </div>

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
                <li class="iconfont icon-yinxiao"></li>
                <li class="iconfont icon-yinliangkai _voice"></li>
                <li class="iconfont icon-yiqipindan"></li>
                <li class="iconfont icon-24gl-playlistMusic"></li>
            </ul>
        </div>
    </div>
    <script src="./js/listen.js"></script>
</body>
</html>