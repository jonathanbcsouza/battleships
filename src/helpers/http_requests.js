import { userDefinedConfigs } from './phpSessions.js';

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
  const response = await fetchData('../helpers/createGrid.php', 'POST', {
    size: userDefinedConfigs.GRID_SIZE,
    numShips: userDefinedConfigs.NUM_SHIPS,
  });

  return response.json();
}

async function handleScore(user_id, action) {
  const response = await fetchData('../../http_requests.php', 'POST', {
    user_id: user_id,
    action: action,
  });

  const data = await response.text();

  alert(action === 'add' ? 'Trophy Earned!' : 'Score Reset!');

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
