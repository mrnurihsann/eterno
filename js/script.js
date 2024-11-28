const themeToggler = document.querySelector(".theme-toggler");
const body = document.body;

if (localStorage.getItem("theme") === "dark") {
  body.classList.remove("light-theme");
  body.classList.add("dark-theme");
  themeToggler.querySelector("span:nth-child(1)").classList.remove("active");
  themeToggler.querySelector("span:nth-child(2)").classList.add("active");
} else {
  body.classList.add("light-theme");
  body.classList.remove("dark-theme");
  themeToggler.querySelector("span:nth-child(1)").classList.add("active");
  themeToggler.querySelector("span:nth-child(2)").classList.remove("active");
}

themeToggler.onclick = () => {
  body.classList.toggle("dark-theme");
  body.classList.toggle("light-theme");

  if (body.classList.contains("dark-theme")) {
    localStorage.setItem("theme", "dark");
    themeToggler.querySelector("span:nth-child(1)").classList.remove("active");
    themeToggler.querySelector("span:nth-child(2)").classList.add("active");
  } else {
    localStorage.setItem("theme", "light");
    themeToggler.querySelector("span:nth-child(1)").classList.add("active");
    themeToggler.querySelector("span:nth-child(2)").classList.remove("active");
  }
};
