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