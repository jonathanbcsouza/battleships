const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');
const toggleButton = document.getElementById('toggleFormButton');

toggleButton.addEventListener('click', () => {
  const isLoginFormDisplayed = loginForm.style.display !== 'none';

  loginForm.style.display = isLoginFormDisplayed ? 'none' : 'flex';
  registerForm.style.display = isLoginFormDisplayed ? 'flex' : 'none';
  toggleButton.innerText = isLoginFormDisplayed ? 'Login' : 'Sign Up';
});