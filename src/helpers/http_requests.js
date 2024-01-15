import * as config from '../configs/constants.js';

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
    size: config.GRID_SIZE,
    numShips: config.NUM_SHIPS,
  });

  return response.json();
}

async function handleScore(username, action) {
  const response = await fetchData('../../http_requests.php', 'POST', {
    username: username,
    action: action,
  });

  const data = await response.text();

  alert(action === 'add' ? 'Trophy Earned!' : 'Score Reset!');

  return action === 'reset';
}

export async function addScore(username) {
  await handleScore(username, 'add');
}

export async function resetScore(username) {
  const shouldReload = await handleScore(username, 'reset');

  if (shouldReload) {
    location.reload();
  }
}
