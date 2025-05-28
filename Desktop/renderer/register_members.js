// Function to go back to the dashboard
function goBack() {
  if (
    confirm(
      "Are you sure you want to go back without completing the registration?"
    )
  ) {
    window.location.href = "dashboard.html";
  }
}

// Function to update security question options
function updateQuestionOptions() {
  const question1 = document.getElementById("security_question1");
  const question2 = document.getElementById("security_question2");
  const question3 = document.getElementById("security_question3");

  const selectedQuestion1 = question1.value;
  const selectedQuestion2 = question2.value;

  // Reset options for question2 and question3
  question2.innerHTML = '<option value="">Select Security Question 2</option>';
  question3.innerHTML = '<option value="">Select Security Question 3</option>';

  // Get all options from question1
  const options = Array.from(question1.options).map((option) => option.value);

  // Filter out the selected question1 and add to question2 and question3
  options
    .filter((option) => option !== selectedQuestion1)
    .forEach((option) => {
      if (option) {
        const opt = document.createElement("option");
        opt.value = option;
        opt.innerHTML = option;
        question2.appendChild(opt.cloneNode(true));
        question3.appendChild(opt);
      }
    });

  // If question2 has a selected value, filter it out from question3
  if (selectedQuestion2) {
    const options2 = Array.from(question2.options).map(
      (option) => option.value
    );
    question3.innerHTML =
      '<option value="">Select Security Question 3</option>';
    options2
      .filter((option) => option !== selectedQuestion2)
      .forEach((option) => {
        if (option) {
          const opt = document.createElement("option");
          opt.value = option;
          opt.innerHTML = option;
          question3.appendChild(opt);
        }
      });
  }
}

// Function to toggle password visibility
function togglePasswordVisibility(fieldId, iconId) {
  const field = document.getElementById(fieldId);
  const icon = document.getElementById(iconId);

  if (field.type === "password") {
    field.type = "text";
    icon.classList.remove("bi-eye-slash");
    icon.classList.add("bi-eye");
  } else {
    field.type = "password";
    icon.classList.remove("bi-eye");
    icon.classList.add("bi-eye-slash");
  }
}

// Validation helper functions
function showError(fieldId, message) {
  const field = document.getElementById(fieldId);
  const errorDiv = document.getElementById(fieldId + "Error");

  field.classList.add("error");
  if (errorDiv) {
    errorDiv.textContent = message;
  }
}

function clearError(fieldId) {
  const field = document.getElementById(fieldId);
  const errorDiv = document.getElementById(fieldId + "Error");

  field.classList.remove("error");
  if (errorDiv) {
    errorDiv.textContent = "";
  }
}

function validateEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

function validatePhone(phone) {
  const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
  return phoneRegex.test(phone);
}

function validatePassword(password) {
  // At least 8 characters, one uppercase, one lowercase, one number
  const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/;
  return passwordRegex.test(password);
}

function validateAge(dateOfBirth) {
  const today = new Date();
  const birthDate = new Date(dateOfBirth);
  const age = today.getFullYear() - birthDate.getFullYear();
  const monthDiff = today.getMonth() - birthDate.getMonth();

  if (
    monthDiff < 0 ||
    (monthDiff === 0 && today.getDate() < birthDate.getDate())
  ) {
    return age - 1;
  }
  return age;
}

// Function to validate the entire form
function validateForm(data) {
  let isValid = true;

  // Clear all previous errors
  const errorElements = document.querySelectorAll(".error-message");
  errorElements.forEach((el) => (el.textContent = ""));
  const fieldElements = document.querySelectorAll(".error");
  fieldElements.forEach((el) => el.classList.remove("error"));

  // Personal Information Validation
  if (!data.username || data.username.trim().length < 3) {
    showError("username", "Username must be at least 3 characters long");
    isValid = false;
  }

  if (!data.firstname || data.firstname.trim().length < 2) {
    showError("firstname", "First name is required");
    isValid = false;
  }

  if (!data.lastname || data.lastname.trim().length < 2) {
    showError("lastname", "Last name is required");
    isValid = false;
  }

  if (!data.dateofbirth) {
    showError("dateofbirth", "Date of birth is required");
    isValid = false;
  } else {
    const age = validateAge(data.dateofbirth);
    if (age < 16) {
      showError("dateofbirth", "You must be at least 16 years old to register");
      isValid = false;
    }
  }

  if (!data.gender) {
    showError("gender", "Gender selection is required");
    isValid = false;
  }

  if (!data.address || data.address.trim().length < 5) {
    showError("address", "Please provide a valid address");
    isValid = false;
  }

  if (!data.phonenumber || !validatePhone(data.phonenumber)) {
    showError("phonenumber", "Please provide a valid phone number");
    isValid = false;
  }

  if (!data.email || !validateEmail(data.email)) {
    showError("email", "Please provide a valid email address");
    isValid = false;
  }

  if (!data.password || !validatePassword(data.password)) {
    showError(
      "password",
      "Password must be at least 8 characters with uppercase, lowercase, and number"
    );
    isValid = false;
  }

  if (data.password !== data.confirm_password) {
    showError("confirm_password", "Passwords do not match");
    isValid = false;
  }

  // Emergency Contact Validation
  // In your form validation before submission
  if (!data.contact_person || data.contact_person.trim() === "") {
    showError("contact_person", "Emergency contact person is required");
    isValid = false;
  }

  if (!data.contact_number || !validatePhone(data.contact_number)) {
    showError(
      "contact_number",
      "Please provide a valid emergency contact number"
    );
    isValid = false;
  }

  if (!data.relationship) {
    showError("relationship", "Relationship to emergency contact is required");
    isValid = false;
  }

  // PAR-Q Questions Validation
  const parqQuestions = [
    "q1",
    "q2",
    "q3",
    "q4",
    "q5",
    "q6",
    "q7",
    "q8",
    "q9",
    "q10",
  ];
  parqQuestions.forEach((question) => {
    if (!data[question]) {
      showError(question, "This question must be answered");
      isValid = false;
    }
  });

  // Security Questions Validation
  if (!data.security_question1) {
    showError("security_question1", "Security question 1 is required");
    isValid = false;
  }

  if (!data.security_answer1 || data.security_answer1.trim().length < 2) {
    showError("security_answer1", "Security answer 1 is required");
    isValid = false;
  }

  if (!data.security_question2) {
    showError("security_question2", "Security question 2 is required");
    isValid = false;
  }

  if (!data.security_answer2 || data.security_answer2.trim().length < 2) {
    showError("security_answer2", "Security answer 2 is required");
    isValid = false;
  }

  if (!data.security_question3) {
    showError("security_question3", "Security question 3 is required");
    isValid = false;
  }

  if (!data.security_answer3 || data.security_answer3.trim().length < 2) {
    showError("security_answer3", "Security answer 3 is required");
    isValid = false;
  }

  // Waiver Validation - Check the actual checkbox state
  if (!data.waiver_rules) {
    showError("waiver_rules", "You must agree to the rules and policy");
    isValid = false;
  }

  if (!data.waiver_liability) {
    showError("waiver_liability", "You must agree to the liability waiver");
    isValid = false;
  }

  if (!data.waiver_cancel) {
    showError(
      "waiver_cancel",
      "You must agree to the cancellation and refund policy"
    );
    isValid = false;
  }

  return isValid;
}

// Function to handle form submission
async function handleRegistration(event) {
  event.preventDefault();

  const form = document.getElementById("registrationForm");
  const formData = new FormData(form);
  const data = Object.fromEntries(formData.entries());

  // Convert checkbox values to strings '1' or '0'
  data.waiver_rules = form.querySelector("#waiver_rules").checked ? "1" : "0";
  data.waiver_liability = form.querySelector("#waiver_liability").checked
    ? "1"
    : "0";
  data.waiver_cancel = form.querySelector("#waiver_cancel").checked ? "1" : "0";

  // Validate form
  const isValid = validateForm(data);

  if (!isValid) {
    const firstError = document.querySelector(".error");
    if (firstError) {
      firstError.scrollIntoView({ behavior: "smooth", block: "center" });
    }
    return;
  }

  try {
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.innerHTML =
      '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Registering...';

    // Send data to server
    const response = await fetch(
      "http://localhost/Desktop/controller/RegistrationController.php?action=register",
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      }
    );

    const result = await response.json();

    if (!response.ok) {
      throw new Error(result.message || "Registration failed");
    }

    if (result.success) {
      alert("Registration successful! Member ID: " + result.member_id);
      window.location.href = "dashboard.html";
    } else {
      throw new Error(result.message || "Registration failed");
    }
  } catch (error) {
    console.error("Error:", error);
    alert("Registration failed: " + error.message);
  } finally {
    // Reset button state
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
      submitBtn.disabled = false;
      submitBtn.textContent = "Register";
    }
  }
}

// Initialize form
document.addEventListener("DOMContentLoaded", function () {
  // Add event listeners
  const form = document.getElementById("registrationForm");
  if (form) {
    form.addEventListener("submit", handleRegistration);
  }

  // Password toggle functionality
  const togglePassword = document.getElementById("togglePassword");
  if (togglePassword) {
    togglePassword.addEventListener("click", function () {
      togglePasswordVisibility("password", "togglePassword");
    });
  }

  const toggleConfirmPassword = document.getElementById(
    "toggleConfirmPassword"
  );
  if (toggleConfirmPassword) {
    toggleConfirmPassword.addEventListener("click", function () {
      togglePasswordVisibility("confirm_password", "toggleConfirmPassword");
    });
  }

  // Initialize security questions
  updateQuestionOptions();

  // Add real-time validation for some fields
  const emailField = document.getElementById("email");
  if (emailField) {
    emailField.addEventListener("blur", function () {
      if (this.value && !validateEmail(this.value)) {
        showError("email", "Please provide a valid email address");
      } else {
        clearError("email");
      }
    });
  }

  const passwordField = document.getElementById("password");
  if (passwordField) {
    passwordField.addEventListener("blur", function () {
      if (this.value && !validatePassword(this.value)) {
        showError(
          "password",
          "Password must be at least 8 characters with uppercase, lowercase, and number"
        );
      } else {
        clearError("password");
      }
    });
  }

  const confirmPasswordField = document.getElementById("confirm_password");
  if (confirmPasswordField) {
    confirmPasswordField.addEventListener("blur", function () {
      const password = document.getElementById("password").value;
      if (this.value && this.value !== password) {
        showError("confirm_password", "Passwords do not match");
      } else {
        clearError("confirm_password");
      }
    });
  }
});

// Make goBack function globally available
window.goBack = goBack;
