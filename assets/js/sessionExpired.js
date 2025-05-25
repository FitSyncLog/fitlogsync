let inactivityTime = 6000 * 1000; // 10 minutes in milliseconds

function logoutUser() {
    // Make an AJAX call to destroy the session
    fetch('destroy_session.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
    })
    .then(response => response.text())
    .then(data => {
        console.log(data); // Log the response from the server
        window.location.href = "login.php?SessionExpired=Your session has expired. Please login again.";
    })
    .catch(error => {
        console.error('Error:', error);
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
