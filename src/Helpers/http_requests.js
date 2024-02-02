import { userDefinedConfigs } from './handle_php_sessions.js';
import { showAlert } from './helpers.js';

async function fetchData(url, method, body) {
  const response = await fetch(url, {
    method: method,
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: new URLSearchParams(body),
  });

  if (!response.ok) {
    throw new Error(`HTTP error! status: ${response.status}`);
  }

  return response;
}

export async function getGrid() {
  console.log(userDefinedConfigs.NUM_SHIPS);
  const response = await fetchData('../Helpers/create_grid.php', 'POST', {
    size: userDefinedConfigs.GRID_SIZE,
    num_ships: userDefinedConfigs.NUM_SHIPS,
  });

  return response.json();
}

async function handleScore(user_id, action) {
  const response = await fetchData('../Helpers/session_handler.php', 'POST', {
    user_id: user_id,
    action: action,
  });

  const data = await response.text();

  showAlert(action === 'add' ? 'Trophy Earned!' : 'Score Reset!');

  return action === 'reset';
}

export async function addScore(user_id) {
  await handleScore(user_id, 'add');
}

export async function resetScore(user_id) {
  const shouldReload = await handleScore(user_id, 'reset');

  if (shouldReload) {
    location.reload();
  }
}
