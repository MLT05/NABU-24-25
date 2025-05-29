const body = document.querySelector("body");
const themeToggle = document.getElementById("theme-toggle");

themeToggle.addEventListener("click", () => {
  body.classList.toggle("dark-theme");
});