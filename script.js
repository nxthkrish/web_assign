// script.js

function welcomeMessage() {
    alert("Hello, welcome to my page!");
}

document.getElementById("profile-pic").onmouseover = function() {
    document.body.style.backgroundColor = "#add8e6";
}

document.getElementById("profile-pic").onmouseout = function() {
    document.body.style.backgroundColor = "#f0f8ff";
}
