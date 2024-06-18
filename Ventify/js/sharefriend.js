function showSharePopup() {
    document.getElementById('sharePopup').style.display = 'block';
}

function closePopup() {
    document.getElementById('sharePopup').style.display = 'none';
}

function generateToken() {
    fetch('gentoken.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('分享Token: ' + data.token);
        } else {
            alert('生成Token时出错: ' + data.error);
        }
    });
}

function acceptSong() {
    var token = document.getElementById('tokenInput').value;

    fetch('acctoken.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ token: token })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            var downloadArea = document.getElementById('downloadArea');
            downloadArea.innerHTML = '<h3>Sharing with your</h3>';

            data.songs.forEach(function(song) {
                var listItem = document.createElement('div');
                listItem.innerHTML = song.title + 
                    ' <button onclick="downloadSong(\'' + song.path + '\', \'' + song.title + '\', \'' + song.thumbnail + '\')">Download</button>' + 
                    (song.thumbnail ? '<br><img src="' + song.thumbnail + '" alt="Thumbnail" style="width: 100px; height: 100px;">' : '');
                downloadArea.appendChild(listItem);
            });
        } else {
            alert('接受歌曲时出错: ' + data.error);
        }
    });
}

function downloadSong(songPath, songTitle, thumbnailPath) {
    fetch('download.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ songPath: songPath, songTitle: songTitle, thumbnailPath: thumbnailPath })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('歌曲已成功下载到您的目录中');
        } else {
            alert('下载歌曲时出错: ' + data.error);
        }
    });
}
