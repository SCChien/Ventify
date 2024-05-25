<?php
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
</head>
<body>

<h2>输入歌曲名以下载并播放</h2>
<form method="post">
    <div class="middle">
        <i class="iconfont icon-jiantou-xiangzuo"></i>
        <i class="iconfont icon-jiantou-xiangyou"></i>
        <div class="search">
            <i class="iconfont icon-sousuo"></i>
            <input type="text" name="search" placeholder="Search" value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>">
        </div>
        <i class="iconfont icon-mic-on"><a href="searchmusic.php?search=<?php echo isset($_POST['search']) ? urlencode($_POST['search']) : ''; ?>">here</a></i>
    </div>
    <input type="submit" value="查找并下载">
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
        <input type="submit" value="下载">
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
    <input type="submit" value="播放">
</form>

</body>
</html>