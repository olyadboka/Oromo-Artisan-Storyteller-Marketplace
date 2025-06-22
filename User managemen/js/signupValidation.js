const roleSelect = document.getElementById("roleSelect");
const customerFields = document.getElementById("customerFields");
const artisanFields = document.getElementById("artisanFields");
const profileInput = document.getElementById("profileInput");
const profilePreview = document.getElementById("profilePreview");
const form = document.getElementById("registrationForm");
const password = document.getElementById("password");
const confirmPassword = document.querySelector(
  'input[name="confirm_password"]'
);

// Handle role selection changes
roleSelect.addEventListener("change", function () {
  if (this.value === "customer") {
    customerFields.style.display = "block";
    artisanFields.style.display = "none";
    customerFields.querySelector("input").required = true;
    artisanFields
      .querySelectorAll("input")
      .forEach((i) => (i.required = false));
  } else if (this.value === "artisan" || this.value === "storyteller") {
    customerFields.style.display = "none";
    artisanFields.style.display = "block";
    customerFields.querySelector("input").required = false;
    artisanFields.querySelectorAll("input").forEach((i) => (i.required = true));
  } else {
    customerFields.style.display = "none";
    artisanFields.style.display = "none";
    customerFields.querySelector("input").required = false;
    artisanFields
      .querySelectorAll("input")
      .forEach((i) => (i.required = false));
  }
});

// Handle profile image preview and validation
profileInput.addEventListener("change", function () {
  const file = this.files[0];
  const errorElement = document.getElementById("profileImageError");

  if (file) {
    const validTypes = ["image/jpeg", "image/png", "image/gif"];
    const fileType = file.type;

    if (!validTypes.includes(fileType)) {
      errorElement.textContent = "Only JPG, PNG, and GIF images are allowed";
      this.classList.add("is-invalid");
      errorElement.style.display = "block";
      this.value = "";
      profilePreview.src = "https://via.placeholder.com/100";
      return;
    }

    if (file.size > 2 * 1024 * 1024) {
      errorElement.textContent = "Image must be less than 2MB";
      this.classList.add("is-invalid");
      errorElement.style.display = "block";
      this.value = "";
      profilePreview.src = "https://via.placeholder.com/100";
      return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
      profilePreview.src = e.target.result;
      profileInput.classList.remove("is-invalid");
      errorElement.style.display = "none";
    };
    reader.readAsDataURL(file);
  }
});

// Password confirmation validation
confirmPassword.addEventListener("input", function () {
  if (this.value !== password.value) {
    this.setCustomValidity("Passwords must match");
    this.classList.add("is-invalid");
    this.nextElementSibling.style.display = "block";
  } else {
    this.setCustomValidity("");
    this.classList.remove("is-invalid");
    this.nextElementSibling.style.display = "none";
  }
});

// Form validation
form.addEventListener("submit", function (event) {
  if (!form.checkValidity()) {
    event.preventDefault();
    event.stopPropagation();
  }
  form.classList.add("was-validated");
});

// Add validation feedback on blur
form.querySelectorAll("input, select").forEach((input) => {
  input.addEventListener("blur", function () {
    if (!this.checkValidity()) {
      this.classList.add("is-invalid");
      this.nextElementSibling.style.display = "block";
    } else {
      this.classList.remove("is-invalid");
      this.nextElementSibling.style.display = "none";
    }
  });
});
