// Global variables
let currentUser = null;
let currentUserRole = 1; // Set to Super Admin by default
let isInitialized = false;

// Role hierarchy and names
const ROLES = {
  1: "Super Admin",
  2: "Admin",
  3: "Front Desk",
  4: "Instructor",
  5: "Member",
};

// Role colors for badges
const ROLE_COLORS = {
  1: "bg-danger",
  2: "bg-warning",
  3: "bg-primary",
  4: "bg-success",
  5: "bg-secondary",
};

// Initialize dashboard on page load
document.addEventListener("DOMContentLoaded", function () {
  initializeDashboard();
});

async function checkAuthentication() {
  // Bypass authentication check
  return {
    user_id: 1,
    username: 'admin',
    role_id: 1,
    firstname: 'Admin',
    lastname: 'User',
    email: 'admin@example.com'
  };
}

async function initializeDashboard() {
  if (isInitialized) {
    console.log('Dashboard already initialized');
    return;
  }

  try {
    console.log('Initializing dashboard...');
    showLoadingState();

    // Set default admin user
    currentUser = {
      user_id: 1,
      username: 'admin',
      role_id: 1,
      firstname: 'Admin',
      lastname: 'User',
      email: 'admin@example.com'
    };
    currentUserRole = 1; // Super Admin

    console.log('User data loaded:', currentUser);
    console.log('User role:', currentUserRole, '(', ROLES[currentUserRole], ')');

    // Add role selector to the top of the dashboard
    const userRoleBadge = document.getElementById("user-role-badge");
    if (userRoleBadge) {
      userRoleBadge.innerHTML = `
        <select id="role-selector" class="form-select form-select-sm" onchange="changeRole(this.value)">
          ${Object.entries(ROLES)
            .map(([id, name]) => `<option value="${id}" ${id == currentUserRole ? 'selected' : ''}>${name}</option>`)
            .join('')}
        </select>
      `;
    }

    // Update the UI
    updateUserDisplay();
    setupRoleBasedAccess();
    
    // Show the overview section by default
    const overviewSection = document.getElementById('overview-section');
    if (overviewSection) {
      overviewSection.classList.remove('d-none');
    }

    // Setup event listeners for navigation
    setupEventListeners();

    isInitialized = true;
    hideLoadingState();
    showSuccessMessage(`Welcome to the dashboard! You can test different roles using the selector above.`);
  } catch (error) {
    console.error('Dashboard initialization error:', error);
    showErrorMessage(error.message);
    hideLoadingState();
  }
}

const { ipcRenderer } = require("electron"); // Add this at the top if using IPC

async function loadUserData() {
  try {
    showLoadingState();
    console.log("Loading user data...");

    // Try to fetch from server
    const serverUrl = "../controller/UserController.php?action=getCurrentUser";
    console.log(`Attempting to fetch user data from: ${serverUrl}`);

    const response = await fetch(serverUrl, {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        "Cache-Control": "no-cache"
      },
      credentials: 'include' // Important for session cookies
    });

    if (!response.ok) {
      throw new Error(`Server error: ${response.status} ${response.statusText}`);
    }

    const data = await response.json();
    console.log("API Response:", data);

    if (data.success && data.user) {
      currentUser = data.user;
      currentUserRole = parseInt(data.user.role_id);
      
      // Store in session storage
      sessionStorage.setItem("currentUser", JSON.stringify(currentUser));
      console.log("User data stored in session:", currentUser);
      
      // Update role display
      const userRoleBadge = document.getElementById("user-role-badge");
      if (userRoleBadge) {
        userRoleBadge.textContent = ROLES[currentUserRole] || "Member";
        userRoleBadge.className = `badge ${ROLE_COLORS[currentUserRole] || "bg-secondary"} text-white mt-2 fs-6`;
      }

      return true;
    } else {
      throw new Error(data.message || "Failed to load user data");
    }
  } catch (error) {
    console.error("Error in loadUserData:", error);
    
    // If there's an error, redirect to login
    showErrorMessage("Please log in to access the dashboard");
    setTimeout(() => {
      window.location.href = "../views/login.html";
    }, 2000);
    
    return false;
  } finally {
    hideLoadingState();
  }
}

function updateUserDisplay() {
  if (!currentUser) {
    console.warn("No user data available for display");
    return;
  }

  console.log("Updating user display with:", currentUser);

  // Update user name in header
  const userNameElement = document.getElementById("user-name");
  if (userNameElement) {
    userNameElement.textContent = currentUser.firstname 
      ? `${currentUser.firstname} ${currentUser.lastname || ''}`
      : currentUser.username;
  }

  // Update profile information
  const elements = {
    'profile-name': currentUser.firstname 
      ? `${currentUser.firstname} ${currentUser.lastname || ''}`
      : currentUser.username,
    'profile-email': currentUser.email,
    'profile-role': ROLES[currentUserRole] || 'Member',
    'profile-firstname': currentUser.firstname,
    'profile-lastname': currentUser.lastname,
    'profile-email-field': currentUser.email,
    'profile-username': currentUser.username,
    'profile-role-field': ROLES[currentUserRole] || 'Member'
  };

  // Update each element if it exists
  Object.entries(elements).forEach(([id, value]) => {
    const element = document.getElementById(id);
    if (element) {
      if (element.tagName === 'INPUT') {
        element.value = value || '';
      } else {
        element.textContent = value || '';
      }
    }
  });

  // Update role badge styling
  const profileRoleElement = document.getElementById("profile-role");
  if (profileRoleElement) {
    profileRoleElement.className = `badge ${ROLE_COLORS[currentUserRole] || "bg-secondary"} rounded-pill`;
  }
}

function setupRoleBasedAccess() {
  // Remove role-based restrictions
  const navLinks = document.querySelectorAll('#sidebar-nav .nav-link');
  navLinks.forEach(link => {
    link.classList.remove('restricted');
    const lockIcon = link.querySelector('.lock-icon');
    if (lockIcon) {
      lockIcon.remove();
    }
  });
}

function showSection(sectionId) {
  console.log("Showing section:", sectionId);

  // Check if section exists
  const sectionElement = document.getElementById(`${sectionId}-section`);
  if (!sectionElement) {
    showErrorMessage("Section not found");
    return;
  }

  // Role-based access control for sections
  const roleRequirements = {
    "member-management": 3,
    "class-management": 4,
    financial: 2,
    "system-admin": 1,
  };

  if (
    roleRequirements[sectionId] &&
    currentUserRole > roleRequirements[sectionId]
  ) {
    showErrorMessage(
      `Access denied. Required role: ${
        ROLES[roleRequirements[sectionId]]
      } or higher.`
    );
    return;
  }

  // Hide all sections
  const sections = document.querySelectorAll(".dashboard-section");
  sections.forEach((section) => {
    section.classList.add("d-none");
  });

  // Show active section
  sectionElement.classList.remove("d-none");

  // Update active nav link
  const navLinks = document.querySelectorAll("#sidebar-nav .nav-link");
  navLinks.forEach((link) => {
    link.classList.remove("active");
  });

  // Find and activate the correct nav link
  const activeLink = document.querySelector(
    `#sidebar-nav .nav-link[data-section="${sectionId}"]`
  );
  if (activeLink) {
    activeLink.classList.add("active");
  }

  // Load section-specific content
  loadSectionContent(sectionId);
}

function loadSectionContent(sectionId) {
  switch (sectionId) {
    case "member-management":
      loadMemberManagement();
      break;
    case "class-management":
      loadClassManagement();
      break;
    case "financial":
      loadFinancialReports();
      break;
    case "system-admin":
      loadSystemAdmin();
      break;
  }
}

function loadMemberManagement() {
  const content = document.getElementById("member-management-content");
  if (content && currentUserRole <= 3) {
    content.innerHTML = `
      <div class="row">
        <div class="col-12">
          <h6 class="fw-bold mb-3">Member Management Tools</h6>
          <div class="btn-group mb-4" role="group">
            <button type="button" class="btn btn-gradient-primary" onclick="showAddMemberForm()">
              <i class="fas fa-user-plus me-2"></i>Add Member
            </button>
            <button type="button" class="btn btn-outline-primary">
              <i class="fas fa-users me-2"></i>View Members
            </button>
            <button type="button" class="btn btn-outline-primary">
              <i class="fas fa-chart-bar me-2"></i>Member Reports
            </button>
          </div>

          <!-- Member Registration Form -->
          <div id="add-member-form" class="card border-0 rounded-4 shadow-sm d-none">
            <div class="card-header bg-transparent border-0">
              <h6 class="fw-bold mb-0">Add New Member</h6>
            </div>
            <div class="card-body">
              <form id="member-registration-form" onsubmit="handleMemberRegistration(event)">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">First Name</label>
                    <input type="text" class="form-control rounded-3" id="member-firstname" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Last Name</label>
                    <input type="text" class="form-control rounded-3" id="member-lastname" required>
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-semibold">Email</label>
                  <input type="email" class="form-control rounded-3" id="member-email" required>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-semibold">Username</label>
                  <input type="text" class="form-control rounded-3" id="member-username" required>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-semibold">Password</label>
                  <input type="password" class="form-control rounded-3" id="member-password" required>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-semibold">Role</label>
                  <select class="form-select rounded-3" id="member-role" required>
                    <option value="">Select Role</option>
                    ${Object.entries(ROLES)
                      .filter(([id, _]) => parseInt(id) >= currentUserRole) // Only show roles lower than current user
                      .map(([id, name]) => `<option value="${id}">${name}</option>`)
                      .join('')}
                  </select>
                </div>
                <div class="d-flex justify-content-end gap-2">
                  <button type="button" class="btn btn-light" onclick="hideAddMemberForm()">
                    <i class="fas fa-times me-2"></i>Cancel
                  </button>
                  <button type="submit" class="btn btn-gradient-primary">
                    <i class="fas fa-user-plus me-2"></i>Add Member
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Members List -->
          <div id="members-list" class="card border-0 rounded-4 shadow-sm">
            <div class="card-body">
              <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i>
                Select an action above to manage members.
              </div>
            </div>
          </div>
        </div>
      </div>
    `;

    // Add event listeners for the form
    setupMemberManagementListeners();
  } else if (content) {
    content.innerHTML = `
      <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>
        You do not have permission to access Member Management. Required role: Front Desk or higher.
      </div>
    `;
  }
}

// Add these new functions for member management
function showAddMemberForm() {
  const form = document.getElementById('add-member-form');
  const list = document.getElementById('members-list');
  if (form && list) {
    form.classList.remove('d-none');
    list.classList.add('d-none');
  }
}

function hideAddMemberForm() {
  const form = document.getElementById('add-member-form');
  const list = document.getElementById('members-list');
  if (form && list) {
    form.classList.add('d-none');
    list.classList.remove('d-none');
  }
}

async function handleMemberRegistration(event) {
  event.preventDefault();
  
  const formData = {
    firstname: document.getElementById('member-firstname').value,
    lastname: document.getElementById('member-lastname').value,
    email: document.getElementById('member-email').value,
    username: document.getElementById('member-username').value,
    password: document.getElementById('member-password').value,
    role_id: document.getElementById('member-role').value
  };

  try {
    const response = await fetch('../controller/UserController.php?action=register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(formData)
    });

    const data = await response.json();

    if (data.success) {
      showSuccessMessage('Member registered successfully!');
      document.getElementById('member-registration-form').reset();
      hideAddMemberForm();
      // Optionally refresh the members list here
    } else {
      showErrorMessage(data.message || 'Failed to register member');
    }
  } catch (error) {
    console.error('Error registering member:', error);
    showErrorMessage('Failed to register member. Please try again.');
  }
}

function setupMemberManagementListeners() {
  const registrationForm = document.getElementById('member-registration-form');
  if (registrationForm) {
    registrationForm.addEventListener('submit', handleMemberRegistration);
  }
}

function loadClassManagement() {
  const content = document.getElementById("class-management-content");
  if (content && currentUserRole <= 4) {
    content.innerHTML = `
        <div class="row">
          <div class="col-12">
            <h6 class="fw-bold mb-3">Class Management Tools</h6>
            <div class="btn-group mb-3" role="group">
              <button type="button" class="btn btn-outline-success">Schedule Class</button>
              <button type="button" class="btn btn-outline-success">View Schedule</button>
              <button type="button" class="btn btn-outline-success">Attendance</button>
            </div>
            <div class="alert alert-success">
              <i class="fas fa-dumbbell me-2"></i>
              Class management features will be implemented here.
            </div>
          </div>
        </div>
      `;
  } else if (content) {
    content.innerHTML = `
      <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>
        You do not have permission to access Class Management. Required role: Instructor or higher.
      </div>
    `;
  }
}

function loadFinancialReports() {
  const content = document.getElementById("financial-content");
  if (content && currentUserRole <= 2) {
    content.innerHTML = `
        <div class="row">
          <div class="col-12">
            <h6 class="fw-bold mb-3">Financial Reports</h6>
            <div class="btn-group mb-3" role="group">
              <button type="button" class="btn btn-outline-warning">Revenue Report</button>
              <button type="button" class="btn btn-outline-warning">Expense Report</button>
              <button type="button" class="btn btn-outline-warning">Profit/Loss</button>
            </div>
            <div class="alert alert-warning">
              <i class="fas fa-chart-line me-2"></i>
              Financial reporting features will be implemented here.
            </div>
          </div>
        </div>
      `;
  } else if (content) {
    content.innerHTML = `
      <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>
        You do not have permission to access Financial Reports. Required role: Admin or higher.
      </div>
    `;
  }
}

function loadSystemAdmin() {
  const content = document.getElementById("system-admin-content");
  if (content && currentUserRole === 1) {
    content.innerHTML = `
        <div class="row">
          <div class="col-12">
            <h6 class="fw-bold mb-3">System Administration</h6>
            <div class="btn-group mb-3" role="group">
              <button type="button" class="btn btn-outline-danger">User Management</button>
              <button type="button" class="btn btn-outline-danger">System Settings</button>
              <button type="button" class="btn btn-outline-danger">Backup</button>
            </div>
            <div class="alert alert-danger">
              <i class="fas fa-cog me-2"></i>
              System administration features will be implemented here.
            </div>
          </div>
        </div>
      `;
  } else if (content) {
    content.innerHTML = `
      <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>
        You do not have permission to access System Administration. Required role: Super Admin only.
      </div>
    `;
  }
}

// FIXED: Single, proper setupEventListeners function
function setupEventListeners() {
  console.log("Setting up event listeners...");

  // Set up sidebar navigation links
  const navLinks = document.querySelectorAll("#sidebar-nav .nav-link");
  navLinks.forEach((link) => {
    // Remove any existing click handlers
    link.removeEventListener("click", handleNavClick);

    // Add new click handler
    link.addEventListener("click", handleNavClick);
  });

  // Password form
  const passwordForm = document.getElementById("password-form");
  if (passwordForm) {
    passwordForm.addEventListener("submit", function (e) {
      e.preventDefault();
      changePassword();
    });
  }

  // Registration form (if exists)
  const registrationForm = document.getElementById("registration-form");
  if (registrationForm) {
    registrationForm.addEventListener("submit", function (e) {
      e.preventDefault();
      handleRegistration();
    });
  }
}

// Separate function to handle navigation clicks
function handleNavClick(e) {
  e.preventDefault();
  console.log('Nav link clicked:', e.target);

  const link = e.target.closest('.nav-link');
  if (!link) return;

  // Get section ID from data attribute
  const sectionId = link.getAttribute('data-section');
  
  if (sectionId) {
    console.log('Navigating to section:', sectionId);
    showSection(sectionId);
  } else {
    console.warn('Could not determine section ID from clicked link');
  }
}

async function changePassword() {
  const currentPassword = document.getElementById("current-password").value;
  const newPassword = document.getElementById("new-password").value;
  const confirmPassword = document.getElementById("confirm-password").value;

  if (!currentPassword || !newPassword || !confirmPassword) {
    showErrorMessage("All password fields are required");
    return;
  }

  if (newPassword !== confirmPassword) {
    showErrorMessage("New passwords do not match");
    return;
  }

  if (newPassword.length < 6) {
    showErrorMessage("New password must be at least 6 characters long");
    return;
  }

  try {
    const response = await fetch(
      "../controller/UserController.php?action=changePassword",
      {
        method: "POST",
        credentials: "include",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          currentPassword: currentPassword,
          newPassword: newPassword,
          confirmPassword: confirmPassword,
        }),
      }
    );

    const data = await response.json();

    if (data.success) {
      showSuccessMessage("Password updated successfully");
      document.getElementById("password-form").reset();
    } else {
      showErrorMessage(data.message || "Failed to update password");
    }
  } catch (error) {
    console.error("Password change error:", error);
    showErrorMessage("Failed to update password. Please try again.");
  }
}

function handleRegistration() {
  const firstname = document.getElementById("reg-firstname")?.value;
  const lastname = document.getElementById("reg-lastname")?.value;
  const email = document.getElementById("reg-email")?.value;
  const username = document.getElementById("reg-username")?.value;
  const password = document.getElementById("reg-password")?.value;

  console.log("Registration Details:", {
    firstname,
    lastname,
    email,
    username,
    password: "***",
  });

  // Close the modal after submission
  const registrationModal = bootstrap.Modal.getInstance(
    document.getElementById("registrationModal")
  );
  if (registrationModal) {
    registrationModal.hide();
  }

  showSuccessMessage("Registration submitted successfully");
}

function showLoadingState() {
  // Create or show loading overlay
  let loadingOverlay = document.getElementById("loading-overlay");
  if (!loadingOverlay) {
    loadingOverlay = document.createElement("div");
    loadingOverlay.id = "loading-overlay";
    loadingOverlay.innerHTML = `
        <div class="d-flex justify-content-center align-items-center position-fixed top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.5); z-index: 9999;">
          <div class="text-center text-white">
            <div class="spinner-border text-warning mb-3" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <div>Loading dashboard...</div>
          </div>
        </div>
      `;
    document.body.appendChild(loadingOverlay);
  } else {
    loadingOverlay.style.display = "block";
  }
}

function hideLoadingState() {
  const loadingOverlay = document.getElementById("loading-overlay");
  if (loadingOverlay) {
    loadingOverlay.style.display = "none";
  }
}

function showSuccessMessage(message) {
  showToast(message, "success");
}

function showErrorMessage(message) {
  showToast(message, "danger");
}

function showWarningMessage(message) {
  showToast(message, "warning");
}

function showToast(message, type) {
  // Create toast container if it doesn't exist
  let toastContainer = document.getElementById("toast-container");
  if (!toastContainer) {
    toastContainer = document.createElement("div");
    toastContainer.id = "toast-container";
    toastContainer.className = "toast-container position-fixed top-0 end-0 p-3";
    toastContainer.style.zIndex = "9999";
    document.body.appendChild(toastContainer);
  }

  const toastId = `toast-${Date.now()}`;
  const toastElement = document.createElement("div");
  toastElement.id = toastId;
  toastElement.className = `toast align-items-center text-white bg-${type} border-0`;
  toastElement.setAttribute("role", "alert");
  toastElement.setAttribute("aria-live", "assertive");
  toastElement.setAttribute("aria-atomic", "true");

  toastElement.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">
          ${message}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    `;

  toastContainer.appendChild(toastElement);
  const toast = new bootstrap.Toast(toastElement, { delay: 5000 });
  toast.show();

  // Auto-remove toast after it's hidden
  toastElement.addEventListener("hidden.bs.toast", () => {
    toastElement.remove();
  });
}

function showRegistrationForm() {
  const registrationModal = new bootstrap.Modal(
    document.getElementById("registrationModal")
  );
  registrationModal.show();
}

function showLogoutModal() {
  const logoutModal = new bootstrap.Modal(
    document.getElementById("logoutModal")
  );
  logoutModal.show();
}

function confirmLogout() {
  // Clear session storage
  try {
    sessionStorage.removeItem("currentUser");
  } catch (error) {
    console.warn("Failed to clear session storage:", error);
  }

  // Redirect to login
  window.location.href = "../views/login.html";
}

// Make functions available globally
window.showSection = showSection;
window.showLogoutModal = showLogoutModal;
window.confirmLogout = confirmLogout;
window.showRegistrationForm = showRegistrationForm;

// Export for debugging
window.dashboardFunctions = {
  showSection,
  loadUserData,
  updateUserDisplay,
  showLogoutModal,
  confirmLogout,
  showRegistrationForm,
  getCurrentUser: () => currentUser,
  getCurrentUserRole: () => currentUserRole,
};

// Add role change function
function changeRole(newRoleId) {
  currentUserRole = parseInt(newRoleId);
  currentUser.role_id = currentUserRole;
  
  // Update UI to reflect new role
  updateUserDisplay();
  setupRoleBasedAccess();
  
  // Show overview section when role changes
  showSection('overview');
  
  showSuccessMessage(`Role changed to ${ROLES[currentUserRole]}`);
}

// Make the function available globally
window.changeRole = changeRole;
