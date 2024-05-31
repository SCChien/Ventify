var closeBtn = document.querySelector(".close");
var modal = document.getElementById("popupWindow");
// When the user clicks on <span> (x), close the modal
closeBtn.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

// JavaScript code to show the popup when the heart icon is clicked
document.getElementById('showPopup').addEventListener('click', function() {
    console.log("asd");
    document.getElementById('popupWindow').style.display = 'block'; // Assuming you have a div with id 'popupWindow' for the popup content
});