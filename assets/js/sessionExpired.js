let inactivityTime = 1800000; // 5 seconds

function logoutUser() {
    fetch('../indexes/logout.php') // Ensure correct path to logout.php
        .then(() => {
            window.location.href = "../login.php?SessionExpired=Your session has expired. Please login again.";
        });
}

function resetTimer() {
    clearTimeout(window.logoutTimer);
    window.logoutTimer = setTimeout(logoutUser, inactivityTime);
}

// Attach event listeners to reset timer on user activity
document.addEventListener("mousemove", resetTimer);
document.addEventListener("keypress", resetTimer);
document.addEventListener("scroll", resetTimer);
document.addEventListener("click", resetTimer);

// Initialize timer when page loads
resetTimer();
