$(document).ready(function () {
    // 给左侧菜单栏添加点击事件处理程序
    $("#sidebar .nav a").click(function () {
        var target = $(this).data('target');

        // 隐藏所有页面内容
        $('.page').hide();

        // 显示目标页面内容
        $('#' +  target).show();
    })
    // 显示默认的页面 仪表盘
    $('#dashboard').show();
})

function validateForm() {
    const duration = document.querySelector('input[name="plan_duration"]').value;
    const price = document.querySelector('input[name="plan_price"]').value;

    if (duration < 0 || price < 0) {
        alert('Duration and price must be non-negative.');
        return false;
    }
    return true;
}