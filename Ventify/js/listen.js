// 获取全局音频播放器
let audio = document.querySelector('#ado');
// 播放按钮
const _audio = document.querySelector('._audio');
const _voice = document.querySelector('._voice');

// 播放列表数据
let songs = [];
let recommendedSongs = [
    { path: './audio/4.mp3', title: '夜に駆ける', thumbnail: './image/main/silasila.jpg', artist: 'YOASOBI' },
    { path: './audio/Call your name.mp3', title: 'Call your name', thumbnail: './image/main/callYourName.jpg', artist: '李阿亚' },
    { path: './audio/wanjiang.mp3', title: '万疆', thumbnail: './image/main/bocchi.jpg', artist: '李玉刚' },
    { path: './audio/lo1.mp3', title: 'アムリタ', thumbnail: './image/main/smadick.jpg', artist: '牧野由依' },
    { path: './audio/3.mp3', title: '群青', thumbnail: './image/main/aaaaaa.jpg', artist: 'YOASOBI' }
];

let currentSongIndex = 0;

// 初始化音频设置
audio.controls = false;
audio.loop = false; // 禁用循环播放，以便使用自定义的下一首逻辑
audio.volume = 0.3;

// 播放开始与暂停以及相关的图标字体修改
function bofang() {
    if (audio.paused) {
        audio.play();
        _audio.classList.remove('icon-bofang');
        _audio.classList.add('icon-zanting');
    } else {
        audio.pause();
        _audio.classList.remove('icon-zanting');
        _audio.classList.add('icon-bofang');
    }
}

// 是否静音与相关的图标字体修改
_voice.addEventListener('click', () => {
    if (audio.muted) {
        audio.muted = false;
        _voice.classList.remove('icon-yinliangguanbi');
        _voice.classList.add('icon-yinliangkai');
    } else {
        audio.muted = true;
        _voice.classList.remove('icon-yinliangkai');
        _voice.classList.add('icon-yinliangguanbi');
    }
});

// 初始化播放功能
function changeSong() {
    if (audio != null) {
        audio.load();
        audio.oncanplay = function () {
            let duraTime = document.querySelector('.duraTime');
            duraTime.innerHTML = transTime(audio.duration);
        }
    }

    function transTime(time) {
        let duration = parseInt(time);
        let minute = parseInt(duration / 60);
        let sec = (duration % 60) + '';
        let isM0 = ':';
        if (minute == 0) {
            minute = '00';
        } else if (minute < 10) {
            minute = "0" + minute;
        }
        if (sec.length == 1) {
            sec = "0" + sec;
        }
        return minute + isM0 + sec;
    }

    const progress = document.querySelector(".progress");
    const slide = document.querySelector(".slide");
    const fill = document.querySelector(".fill");
    audio.ontimeupdate = function () {
        let l = (audio.currentTime / audio.duration) * 100;
        slide.style.left = l + "%";
        fill.style.width = l + "%";
        if (audio.currentTime == 0) {
            slide.style.left = "0%";
        }
        const currentTime = document.querySelector(".currentTime");
        currentTime.innerHTML = transTime(parseInt(audio.currentTime));
        const duraTime = document.querySelector(".duraTime");
        duraTime.innerHTML = transTime(audio.duration);
    };

    slide.onmousedown = function (e) {
        let x = e.clientX - this.offsetLeft;
        document.onmousemove = function (e) {
            let jlx = ((e.clientX - x) / progress.clientWidth) * 100;
            if (jlx <= 100 && jlx >= 0) {
                slide.style.left = jlx + "%";
            }
            audio.currentTime = (jlx / 100) * audio.duration;
        }
        document.onmouseup = function () {
            document.onmousemove = null;
            document.onmouseup = null;
        }
    }
    slide.ontouchstart = function (e) {
        let x = e.targetTouches[0].clientX - this.offsetLeft;
        document.ontouchmove = function (e) {
            let jlx = ((e.targetTouches[0].clientX - x) / progress.clientWidth) * 100;
            if (jlx <= 100 && jlx >= 0) {
                slide.style.left = jlx + '%';
            }
            audio.currentTime = (jlx / 100) * audio.duration;
        }
        document.ontouchend = function (e) {
            document.ontouchmove = null;
            document.ontouchend = null;
        }
    }
}

// 切换到指定索引的歌曲
function playSongByIndex(index) {
    const song = songs[index];
    audio.src = song.path;
    document.querySelector('.songName').textContent = song.title;
    document.querySelector('._img').src = song.thumbnail;
    document.querySelector('.singer').textContent = song.artist;
    audio.play();
    savePlaybackState(index, audio.currentTime);
}

// 切换到下一首歌曲
function playNextSong() {
    currentSongIndex = (currentSongIndex + 1) % songs.length;
    playSongByIndex(currentSongIndex);
}

// 切换到上一首歌曲
function playPrevSong() {
    currentSongIndex = (currentSongIndex - 1 + songs.length) % songs.length;
    playSongByIndex(currentSongIndex);
}

// 保存播放状态到 localStorage
function savePlaybackState(index, currentTime) {
    localStorage.setItem('currentSongIndex', index);
    localStorage.setItem('currentTime', currentTime);
}

// 从 localStorage 恢复播放状态
function restorePlaybackState() {
    const savedIndex = localStorage.getItem('currentSongIndex');
    const savedTime = localStorage.getItem('currentTime');
    if (savedIndex !== null) {
        currentSongIndex = parseInt(savedIndex);
        playSongByIndex(currentSongIndex);
        if (savedTime !== null) {
            audio.currentTime = parseFloat(savedTime);
        }
    }
}

// 页面卸载时保存播放状态
window.addEventListener('beforeunload', () => {
    savePlaybackState(currentSongIndex, audio.currentTime);
});

// 添加事件监听器到“下一首”和“上一首”按钮
document.querySelector('.next').addEventListener('click', playNextSong);
document.querySelector('.prev').addEventListener('click', playPrevSong);

// 加载推荐歌曲到播放列表
recommendedSongs.forEach(song => {
    songs.push(song);
    const li = document.createElement('li');
    li.innerHTML = `<a href="#" onclick="playSongByIndex(${songs.length - 1})">${song.title}</a>`;
    document.getElementById('playlist').appendChild(li);
});

// 加载下载的歌曲并添加到播放列表
window.onload = function () {
    fetch('get_downloads.php')
        .then(response => response.json())
        .then(data => {
            data.forEach(song => {
                songs.push(song);
                const li = document.createElement('li');
                li.innerHTML = `<a href="#" onclick="playSongByIndex(${songs.length - 1})">${song.title}</a>`;
                document.getElementById('playlist').appendChild(li);
            });
            // 恢复播放状态
            restorePlaybackState();
        });

    // 设置歌曲结束时播放下一首
    audio.onended = playNextSong;
};

// 获取推荐歌曲  切歌功能
const image = document.querySelector('._img')
const recm_list = document.querySelectorAll('.recm_list ul li')
const audio_list = ['4', 'Call your name', 'wanjiang', 'lo1', '3']
const image_list = ['silasila', 'callYourName', 'bocchi', 'smadick', 'aaaaaa']
// ftleft 切哥后对应的图片歌名和歌手名称也需要切换
const songName = document.querySelector('.songName')
const singer = document.querySelector('.singer')
const songAndSinger_list = [
    ['夜に駆ける','YOASOBI'],
    ['Call your name','李阿亚'],
    ['万疆','李玉刚'],
    ['アムリタ','牧野由依'],
    ['群青','YOASOBI']
]

for (let i = 0; i < recm_list.length; i++) {
    recm_list[i].addEventListener('click', function() {
        audio.src = "./audio/" + audio_list[i] + ".mp3"
        image.src = "./image/main/" + image_list[i] + ".jpg"
        songName.innerHTML = songAndSinger_list[i][0]
        singer.innerHTML = songAndSinger_list[i][1]
        changeSong()
        audio.play()
    })
}