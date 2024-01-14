import * as config from '../configs/constants.js';

// DB Operations
export async function fetchData(url, method, body) {
  try {
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

    const data = await response.json();

    return data;
  } catch (error) {
    console.log(
      'There was a problem with the fetch operation: ' + error.message
    );
  }
}

export async function getGrid() {
  return await fetchData('../helpers/createGrid.php', 'POST', {
    size: config.GRID_SIZE,
    numShips: config.NUM_SHIPS,
  });
}

function handleScore(username, action) {
  return fetchData('../../http_requests.php', 'POST', {
    username: username,
    action: action,
  });
}

export function addScore(username) {
  handleScore(username, 'add');
}

export function resetScore(username) {
  handleScore(username, 'reset');
}
