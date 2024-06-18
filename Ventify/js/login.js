let login_title=document.querySelector('.login-title');
let register_title=document.querySelector('.register-title');
let login_box=document.querySelector('.login-box');
let register_box=document.querySelector('.register-box');

// 绑定标题点击事件
login_title.addEventListener('click',()=>{
    // 判断是否收起，收起才可以点击
    if(login_box.classList.contains('slide-up')){
        register_box.classList.add('slide-up');
        login_box.classList.remove('slide-up');
    }
})
register_title.addEventListener('click',()=>{
    if(register_box.classList.contains('slide-up')){
        login_box.classList.add('slide-up');
        register_box.classList.remove('slide-up');
    }
})

document.addEventListener("DOMContentLoaded", function() {
    const registerForm = document.querySelector('form[action*="register"]');
    const passwordInput = document.getElementById("reg_password");
    const confirmPasswordInput = document.querySelector('input[placeholder="Confirm Password"]');

    registerForm.addEventListener("submit", function(event) {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        if (password.length < 6) {
            alert("密码不能少于6位字符。");
            event.preventDefault();
            return;
        }

        if (password !== confirmPassword) {
            alert("密码和确认密码不匹配。");
            event.preventDefault();
        }
    });
});