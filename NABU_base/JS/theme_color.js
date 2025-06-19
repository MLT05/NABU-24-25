const toggle = document.getElementById("theme-toggle");

// Verificar o tema salvo ao carregar
const savedTheme = localStorage.getItem("theme");
if (savedTheme === "dark") {
  document.body.classList.add("dark");
  toggle.checked = true;
}

// Alternar o modo claro/escuro e salvar no localStorage
toggle.addEventListener("change", () => {
  if (toggle.checked) {
    document.body.classList.add("dark");
    localStorage.setItem("theme", "dark");
  } else {
    document.body.classList.remove("dark");
    localStorage.setItem("theme", "light");
  }
});