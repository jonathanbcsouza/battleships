import * as config from '../configs/constants.js';

// Grid management
export async function getGrid() {
  try {
    const response = await fetch('../helpers/createGrid.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({
        size: config.GRID_SIZE,
        numShips: config.NUM_SHIPS,
      }),
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const grid = await response.json();

    return grid;
  } catch (error) {
    console.log(
      'There was a problem with the fetch operation: ' + error.message
    );
  }
}

function handleScore(username, action) {
  alert(action === 'add' ? 'Trophy Earned!' : 'Score Reset!');
  fetch('../../http_requests.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: new URLSearchParams({
      username: username,
      action: action,
    }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.text();
    })
    .then((data) => {
      console.log(data);
      if (action === 'reset') {
        location.reload();
      }
    })
    .catch((error) => {
      console.error('Error:', error);
    });
}

export function addScore(username) {
  handleScore(username, 'add');
}

export function resetScore(username) {
  handleScore(username, 'reset');
}
