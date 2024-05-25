document.getElementById("edit").addEventListener("click", function() {
    var editSection = document.querySelector(".edit");
    if (editSection.style.display === "none") {
        editSection.style.display = "block";
    } else {
        editSection.style.display = "none";
    }
});


// 检查用户在线状态的函数
function checkOnlineStatus() {
    // 假设用户在线状态为true或false，根据实际情况修改
    var userIsOnline = true;

    // 根据用户的在线状态设置在线状态指示器的颜色
    var onlineIndicator = document.getElementById("onlineIndicator");
    console.log("onlineIndicator:", onlineIndicator); // 检查 onlineIndicator 是否正确获取到了元素
    if (userIsOnline) {
        onlineIndicator.style.backgroundColor = "#00FF00"; // 在线状态为绿色
    } else {
        onlineIndicator.style.backgroundColor = "#FF0000"; // 离线状态为红色
    }
}

// 在页面加载时检查用户在线状态
window.onload = function() {
    checkOnlineStatus();
};


// 显示弹窗
function showPopup() {
    document.getElementById("editProfilePopup").style.display = "block";
}

// 隐藏弹窗
function closePopup() {
    document.getElementById("editProfilePopup").style.display = "none";
}


function showPopup() {
    document.getElementById('editProfilePopup').style.display = 'block';
}

function closePopup() {
    document.getElementById('editProfilePopup').style.display = 'none';
}