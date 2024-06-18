function validateForm() {
    const newPassword = document.querySelector('input[name="new_password"]').value;
    const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
    
    if (newPassword.length < 6) {
        alert("密码不能少于6位字符。");
        return false;
    }
    
    if (newPassword !== confirmPassword) {
        alert("密码和确认密码不匹配。");
        return false;
    }
    
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    form.onsubmit = validateForm;
});