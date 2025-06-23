const form = document.getElementById("loginForm");

// Form validation
form.addEventListener("submit", function (event) {
  if (!form.checkValidity()) {
    event.preventDefault();
    event.stopPropagation();
  }
  form.classList.add("was-validated");
});

// Add validation feedback on blur
form.querySelectorAll("input").forEach((input) => {
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

// Email validation on input
document.getElementById("email").addEventListener("input", function () {
  const emailRegex = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i;
  if (!emailRegex.test(this.value)) {
    this.setCustomValidity("Please enter a valid email address");
    this.classList.add("is-invalid");
    this.nextElementSibling.style.display = "block";
  } else {
    this.setCustomValidity("");
    this.classList.remove("is-invalid");
    this.nextElementSibling.style.display = "none";
  }
});
