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
            alert('Token: ' + data.token);
        } else {
            alert('Error generate token: ' + data.error);
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
            alert('Error received song : ' + data.error);
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
            alert('The song has been successfully downloaded');
        } else {
            alert('Error while downloading songs : ' + data.error);
        }
    });
}
