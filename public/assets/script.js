const passwordInput = document.getElementById("password");
const confirmPasswordInput = document.getElementById("password_confirm");
const submitButton = document.getElementById("submit-button");
const passwordRequirements = document.getElementById("password-requirements");
const requirements = {
  lowercaseUppercase: document.getElementById("lowercase-uppercase"),
  number: document.getElementById("number"),
  specialChar: document.getElementById("special-char"),
  minLength: document.getElementById("min-length"),
};

const togglePasswordButtons = document.querySelectorAll(".toggle-password");

// Toggle password visibility
togglePasswordButtons.forEach((button) => {
  button.addEventListener("click", () => {
    const targetInput = document.getElementById(button.dataset.target);
    const type = targetInput.type === "password" ? "text" : "password";
    targetInput.type = type;
    button.innerHTML =
      type === "password"
        ? '<i class="fas fa-eye"></i>'
        : '<i class="fas fa-eye-slash"></i>';
  });
});

// Show/hide password requirements when the user focuses on the password input
passwordInput.addEventListener("focus", () => {
  passwordRequirements.style.display = "block";
  setTimeout(() => (passwordRequirements.style.opacity = 1), 10); // Inicia a transição de opacidade
});

passwordInput.addEventListener("blur", () => {
  if (passwordInput.value === "") {
    passwordRequirements.style.opacity = 0;
    setTimeout(() => (passwordRequirements.style.display = "none"), 300); // Aguarda o tempo da transição
  }
});

// Validate password strength
passwordInput.addEventListener("input", () => {
  const password = passwordInput.value;

  // Validate lowercase & uppercase letters
  if (/[a-z]/.test(password) && /[A-Z]/.test(password)) {
    requirements.lowercaseUppercase.classList.add("valid");
  } else {
    requirements.lowercaseUppercase.classList.remove("valid");
  }

  // Validate numbers
  if (/\d/.test(password)) {
    requirements.number.classList.add("valid");
  } else {
    requirements.number.classList.remove("valid");
  }

  // Validate special characters
  if (/[!@#$%^&*]/.test(password)) {
    requirements.specialChar.classList.add("valid");
  } else {
    requirements.specialChar.classList.remove("valid");
  }

  // Validate minimum length
  if (password.length >= 8) {
    requirements.minLength.classList.add("valid");
  } else {
    requirements.minLength.classList.remove("valid");
  }

  // Enable submit button if all conditions are met
  const allValid = Object.values(requirements).every((req) =>
    req.classList.contains("valid")
  );
  submitButton.disabled = !allValid;
});
