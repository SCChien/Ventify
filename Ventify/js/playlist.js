document.addEventListener('DOMContentLoaded', function() {
    const playlistNames = document.querySelectorAll('.playlistName');
    const playlistContent = document.getElementById('playlistContent');
    const backButton = document.getElementById('backButton');
    const deleteAlbumButtons = document.querySelectorAll('.deleteAlbumBtn');

    // 处理弹窗显示和关闭
    const closeBtn = document.querySelector(".close");
    const modal = document.getElementById("popupWindow");

    closeBtn.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    document.getElementById('showPopup').addEventListener('click', function() {
        modal.style.display = 'block';
        console.log('asd');
    });

    // 处理删除专辑按钮事件
    deleteAlbumButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const albumName = this.getAttribute('data-album');
            if (confirm(`Are you sure you want to delete the album "${albumName}"?`)) {
                fetch(`delete_album.php?album=${albumName}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert(data.message);
                        }
                    });
            }
        });
    });

    // 处理点击播放列表事件
    playlistNames.forEach(playlist => {
        playlist.addEventListener('click', function() {
            const albumName = this.dataset.album;
            fetch(`playlist_content.php?album=${albumName}`)
                .then(response => response.text())
                .then(data => {
                    playlistContent.innerHTML = data;
                    playlistContent.style.display = 'block';
                    backButton.style.display = 'block';
                    document.getElementById('playlistNames').style.display = 'none';
                });
        });
    });

    // 处理返回按钮事件
    backButton.addEventListener('click', function() {
        playlistContent.style.display = 'none';
        backButton.style.display = 'none';
        document.getElementById('playlistNames').style.display = 'block';
    });
});
