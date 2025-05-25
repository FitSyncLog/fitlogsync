const alertContainer = document.getElementById("alert-container");
const loginForm = document.getElementById("loginForm");
const loginBtn = document.getElementById("loginBtn");
const loginBtnText = document.getElementById("loginBtnText");
const loadingSpinner = document.getElementById("loadingSpinner");
const togglePasswordBtn = document.getElementById("togglePassword");
const passwordInput = document.getElementById("password");
const roleIndicator = document.getElementById("roleIndicator");

// Toggle password visibility
togglePasswordBtn.addEventListener("click", function () {
  const type =
    passwordInput.getAttribute("type") === "password" ? "text" : "password";
  passwordInput.setAttribute("type", type);

  const icon = this.querySelector("i");
  icon.classList.toggle("fa-eye");
  icon.classList.toggle("fa-eye-slash");
});

function showAlert(message, type = "danger") {
  alertContainer.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
          <i class="fas ${
            type === "success" ? "fa-check-circle" : "fa-exclamation-triangle"
          } me-2"></i>
          ${message}
          <button type="button" class="btn-close" aria-label="Close"></button>
        </div>
      `;

  const closeBtn = alertContainer.querySelector(".btn-close");
  closeBtn.addEventListener("click", () => {
    alertContainer.innerHTML = "";
  });

  // Auto-dismiss after 5 seconds
  setTimeout(() => {
    alertContainer.innerHTML = "";
  }, 5000);
}

function setLoading(isLoading) {
  loginBtn.disabled = isLoading;
  if (isLoading) {
    loadingSpinner.style.display = "inline-block";
    loginBtnText.style.display = "none";
  } else {
    loadingSpinner.style.display = "none";
    loginBtnText.style.display = "inline";
  }
}

function showRoleInfo(user) {
  const roleColors = {
    "Super Admin": "text-danger",
    Admin: "text-warning",
    "Front Desk": "text-info",
    Instructor: "text-success",
    Member: "text-primary",
  };

  const colorClass = roleColors[user.role] || "text-muted";
  roleIndicator.innerHTML = `<span class="${colorClass}">Logging in as: ${user.role}</span>`;
}

loginForm.addEventListener("submit", async function (e) {
  e.preventDefault();

  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value.trim();

  // Clear previous alerts and role indicator
  alertContainer.innerHTML = "";
  roleIndicator.innerHTML = "";

  // Client-side validation
  if (!email) {
    showAlert("Please enter your email address.");
    document.getElementById("email").focus();
    return;
  }

  if (!password) {
    showAlert("Please enter your password.");
    document.getElementById("password").focus();
    return;
  }

  // Validate email format
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    showAlert("Please enter a valid email address.");
    document.getElementById("email").focus();
    return;
  }

  setLoading(true);

  try {
    // Updated API endpoint
    const response = await fetch("http://localhost/Desktop/api/auth.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ email, password }),
    });

    console.log("Response status:", response.status);

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const contentType = response.headers.get("content-type");
    if (!contentType || !contentType.includes("application/json")) {
      const text = await response.text();
      console.log("Non-JSON response:", text);
      throw new Error("Server returned non-JSON response");
    }

    const data = await response.json();
    console.log("Response data:", data);

    if (data.success) {
      // Show role information
      showRoleInfo(data.user);

      showAlert(
        `Welcome ${data.user.firstname}! Redirecting to ${data.user.role} dashboard...`,
        "success"
      );

      // Store user info in sessionStorage
      sessionStorage.setItem("user", JSON.stringify(data.user));

      // Redirect to appropriate dashboard based on role
      setTimeout(() => {
        window.location.href = data.redirect_url;
      }, 1500);
    } else {
      showAlert(data.message || "Login failed. Please try again.");
    }
  } catch (error) {
    console.error("Login error:", error);

    // More specific error messages
    if (
      error.message.includes("Failed to fetch") ||
      error.message.includes("Network Error")
    ) {
      showAlert(
        "Cannot connect to server. Please check if your server is running."
      );
    } else if (error.message.includes("HTTP error")) {
      showAlert(
        "Server error occurred. Please check your PHP server configuration."
      );
    } else if (error.message.includes("non-JSON response")) {
      showAlert(
        "Server configuration error. Please check your PHP files for syntax errors."
      );
    } else {
      showAlert("Connection error: " + error.message);
    }
  } finally {
    setLoading(false);
  }
});

// Focus on email field when page loads
window.addEventListener("load", function () {
  document.getElementById("email").focus();
});

// Handle Enter key in password field
passwordInput.addEventListener("keypress", function (e) {
  if (e.key === "Enter") {
    loginForm.dispatchEvent(new Event("submit"));
  }
});

// Check if user is already logged in
window.addEventListener("load", function () {
  const storedUser = sessionStorage.getItem("user");
  if (storedUser) {
    try {
      const user = JSON.parse(storedUser);
      // Optional: Verify session is still valid with server
      showAlert(`Welcome back ${user.firstname}! Redirecting...`, "info");
      setTimeout(() => {
        // Redirect based on stored role
        const dashboards = {
          1: "dashboard.html",
          2: "admin-dashboard.html",
          3: "frontdesk-dashboard.html",
          4: "instructor-dashboard.html",
          5: "member-dashboard.html",
        };
        window.location.href =
          dashboards[user.role_id] || "member-dashboard.html";
      }, 1000);
    } catch (e) {
      // Clear invalid session data
      sessionStorage.removeItem("user");
    }
  }
});
