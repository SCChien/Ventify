document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('showDownloads').addEventListener('click', function() {
        fetch('get_downloads.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                let downloadListDiv = document.getElementById('downloadList');
                downloadListDiv.innerHTML = '<button class="close-btn">Close</button><h2>Downloaded Songs</h2>'; // 清空之前的内容并添加关闭按钮和标题
                downloadListDiv.style.display = 'block'; // 显示下载列表
                data.forEach(song => {
                    let songElement = document.createElement('div');
                    songElement.className = 'song-item';
                    songElement.dataset.path = song.path;
                    songElement.dataset.title = song.title;

                    // 检查缩略图的格式
                    if (song.thumbnail && isValidImageFormat(song.thumbnail)) {
                        songElement.dataset.thumbnail = song.thumbnail;
                    } else {
                        songElement.dataset.thumbnail = 'default_thumbnail.jpg'; // 默认缩略图
                    }

                    let thumbnailElement = document.createElement('img');
                    thumbnailElement.src = songElement.dataset.thumbnail;
                    songElement.appendChild(thumbnailElement);

                    let titleElement = document.createElement('span');
                    titleElement.textContent = song.title;
                    songElement.appendChild(titleElement);

                    downloadListDiv.appendChild(songElement);
                });

                // Add event listeners to each song item for playing the song
                let songItems = document.querySelectorAll('.song-item');
                songItems.forEach(item => {
                    item.addEventListener('click', function() {
                        let path = this.dataset.path;
                        let title = this.dataset.title;
                        let thumbnail = this.dataset.thumbnail;
                        playSong(path, title, thumbnail);
                    });
                });

                // 添加关闭按钮的事件监听
                document.querySelector('.close-btn').addEventListener('click', function() {
                    downloadListDiv.style.display = 'none';
                });
            })
            .catch(error => console.error('Error fetching download list:', error));
    });
});

function isValidImageFormat(thumbnail) {
    const validFormats = ['webp', 'jpg', 'jpeg', 'png'];
    const extension = thumbnail.split('.').pop().toLowerCase();
    return validFormats.includes(extension);
}

function playSong(path, title, thumbnail) {
    let audio = document.querySelector('#ado');
    let songName = document.querySelector('.songName');
    let singer = document.querySelector('.singer');
    let image = document.querySelector('._img');

    audio.src = path;
    audio.play();
    songName.innerHTML = title;
    singer.innerHTML = ''; // 你可以根据需要设置歌手信息
    image.src = thumbnail;

    // 更新进度条和时间
    changeSong();
}

// 一上来先调一次初始化函数
changeSong();

// 将audio的初始化函数封装
function changeSong() {
    let audio = document.querySelector('#ado');

    // 获取音频时长
    if (audio != null) {
        audio.load();
        audio.oncanplay = function () {
            let duraTime = document.querySelector('.duraTime');
            duraTime.innerHTML = transTime(audio.duration);
        }
    }

    // 格式化时间格式
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

    // 时长进度条
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

    // 进度条拖动
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

var modal = document.getElementById("myModal");
var span = document.getElementsByClassName("close")[0];

span.onclick = function() {
    modal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function showResults(results) {
    var searchResults = document.getElementById("searchResults");
    searchResults.innerHTML = '';
    results.forEach(result => {
        var li = document.createElement("li");
        li.innerHTML = `${result.title} <a href="?action=download&url=${encodeURIComponent(result.url)}&title=${encodeURIComponent(result.title)}">下载并播放</a>`;
        searchResults.appendChild(li);
    });
    modal.style.display = "block";
}




// 获取模态框和打开模态框的按钮
var modal = document.getElementById('myModal');
var openModalButton = document.getElementById('openModalButton');
// 获取 <span> 元素，用于关闭模态框
var span = document.getElementsByClassName('close')[0];

// 当用户点击 <span> (x), 关闭模态框
span.onclick = function() {
    modal.style.display = 'none';
}

// 当用户点击模态框外部, 关闭模态框
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

// 当用户点击按钮, 打开模态框
openModalButton.onclick = function() {
    modal.style.display = 'flex';
}

document.addEventListener('DOMContentLoaded', function() {
    // 获取模态框和打开模态框的按钮
    var modal = document.getElementById('myModal');
    var openModalButton = document.getElementById('openModalButton');
    // 获取 <span> 元素，用于关闭模态框
    var span = document.getElementsByClassName('close1')[0];

    // 当用户点击 <span> (x), 关闭模态框
    span.onclick = function() {
        modal.style.display = 'none';
    }

    // 当用户点击模态框外部, 关闭模态框
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    // 当用户点击按钮, 打开模态框
    openModalButton.onclick = function() {
        modal.style.display = 'flex';
    }
});