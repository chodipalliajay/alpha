document.getElementById("appForm").addEventListener("submit", function (e) {
  let valid = true;
  const v = id => document.getElementById(id).value.trim();

  function check(id, condition, message) {
    const field = document.getElementById(id);
    const err = field.nextElementSibling;
    if (!condition) {
      field.classList.add("invalid");
      err.textContent = message;
      valid = false;
    } else {
      field.classList.remove("invalid");
      err.textContent = "";
    }
  }

  const pct = v("previous_percentage");

  check("full_name", /^[A-Za-z ]{3,}$/.test(v("full_name")), "Enter a valid name (letters only)");
  check("email", /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v("email")), "Enter a valid email");
  check("phone", /^[6-9]\d{9}$/.test(v("phone")), "Enter a valid 10-digit phone number");
  check("dob", v("dob") !== "", "Select date of birth");
  check("gender", v("gender") !== "", "Select gender");
  check("course", v("course") !== "", "Select a course");
  check("previous_percentage", pct !== "" && pct >= 0 && pct <= 100, "Enter percentage between 0 and 100");
  check("address", v("address").length >= 10, "Address must be at least 10 characters");

  const file = document.getElementById("document").files[0];
  if (file) {
    check("document", file.size <= 2 * 1024 * 1024, "File must be under 2MB");
  }

  if (!valid) e.preventDefault();
});