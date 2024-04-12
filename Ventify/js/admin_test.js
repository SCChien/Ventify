const body = document.querySelector('body'),
  sidebar = body.querySelector('nav'),
  toggle = body.querySelector(".toggle"),
  searchBtn = body.querySelector(".search-box"),
  modeSwitch = body.querySelector(".toggle-switch"),
  modeText = body.querySelector(".mode-text");

function setTheme(theme) {
  document.documentElement.setAttribute('data-theme', theme);
  localStorage.setItem('theme', theme); // Store theme preference in localStorage
}

// Load theme preference from localStorage
const currentTheme = localStorage.getItem('theme');
if (currentTheme) {
  setTheme(currentTheme);
  
  // Update the toggle switch based on the stored theme preference
  if (currentTheme === 'dark') {
    modeSwitch.checked = true;
    body.classList.add('dark');
    modeText.innerText = "Light mode";
  }
}

toggle.addEventListener("click", () => {
  sidebar.classList.toggle("close");
});

searchBtn.addEventListener("click", () => {
  sidebar.classList.remove("close");
});

modeSwitch.addEventListener("click", () => {
  body.classList.toggle("dark");

  if (body.classList.contains("dark")) {
    modeText.innerText = "Light mode";
  } else {
    modeText.innerText = "Dark mode";
  }

  // Save the theme preference whenever the toggle is clicked
  const themePreference = body.classList.contains("dark") ? 'dark' : 'light';
  setTheme(themePreference);
});
