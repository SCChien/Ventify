document.addEventListener('DOMContentLoaded', function() {
    // Fetch user role
    fetch('noviplisten.php')  // 确保这个路径是正确的
        .then(response => response.json())
        .then(data => {
            var userRole = data.userRole;
            if (userRole === 'NORMAL USER') {  // 使用大写进行检查
                startAdInterval();
            }
        })
        .catch(error => console.error('Error fetching user info:', error));
});

function startAdInterval() {
    let audio = document.querySelector('#ado');
    let adAudio = new Audio('audio/manbo.mp3'); // 确保这个路径是正确的

    // 创建一个覆盖整个页面的遮罩层，防止用户操作
    let overlay = document.createElement('div');
    overlay.style.position = 'fixed';
    overlay.style.top = 0;
    overlay.style.left = 0;
    overlay.style.width = '100%';
    overlay.style.height = '100%';
    overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    overlay.style.display = 'none';
    overlay.style.zIndex = 9999999;
    overlay.innerHTML = `
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: white;">
            <div class="loader" style="margin-bottom: 20px;"></div>
            <p>Ads playing....</p>
        </div>
    `;

    // 创建并添加一个简单的CSS加载动画
    let style = document.createElement('style');
    style.type = 'text/css';
    style.innerHTML = `
        .loader {
            border: 16px solid #f3f3f3;
            border-top: 16px solid #3498db;
            border-radius: 50%;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
    document.body.appendChild(overlay);

    // 每5分钟播放一次广告
    function playAd() {
        if (!audio.paused) {
            audio.pause();
            overlay.style.display = 'block'; // 显示遮罩层
            adAudio.play();
        }
    }

    let adInterval = setInterval(playAd, 300000); // 300000 毫秒 = 5 分钟

    adAudio.addEventListener('ended', function() {
        // 广告播放完毕后继续播放歌曲
        overlay.style.display = 'none'; // 隐藏遮罩层
        audio.play();

        // 重启定时器，每5分钟再次播放广告
        clearInterval(adInterval);
        adInterval = setInterval(playAd, 300000);
    });
}
