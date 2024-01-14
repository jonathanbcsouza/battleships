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

export function removeShip(grid, x, y) {
  grid[x][y] = config.EXPLOSION_ICON;
  return grid;
}

export function revealGrid(grid) {
  const table = document.createElement('table');

  for (let i = 0; i < grid.length; i++) {
    const row = document.createElement('tr');
    for (let j = 0; j < grid[i].length; j++) {
      const cell = document.createElement('td');
      cell.textContent = grid[i][j];
      row.appendChild(cell);
    }
    table.appendChild(row);
  }
  board.appendChild(table);
}

// Game Logic
export function locateShips(grid, numShips) {
  const gridSize = grid.length;
  let shipsLocation = [];
  for (let i = 0; i < gridSize; i++) {
    for (let j = 0; j < gridSize; j++) {
      if (grid[i][j] === config.SHIP_ICON) {
        shipsLocation.push([i, j]);
        if (shipsLocation.length === numShips) {
          return shipsLocation;
        }
      }
    }
  }

  return shipsLocation;
}

export function getClosestShipDistance(shiftedX, shiftedY, shipsCoordinates) {
  const distances = shipsCoordinates.map(
    ([x, y]) => Math.abs(shiftedX - x) + Math.abs(shiftedY - y)
  );

  // console.log(...distances);
  return Math.min(...distances);
}

export function useRockets(rocketsCount) {
  return --rocketsCount;
}

export function setGameState(rocketsCount, shipsCount, gameState) {
  if (rocketsCount === 0) {
    gameState = 'lose';
  }

  if (shipsCount === 0) {
    gameState = 'win';
  }

  if (rocketsCount > 0 && shipsCount > 0) {
    gameState = 'playing';
  }

  return gameState;
}

export function resetGame() {
  location.reload();
}

// Ui Updates
export function launchRocket(x, y) {
  alert(`Rocket launched to ${x + 1}, ${y + 1}!`);
}

export function radarFeedback(d) {
  let message = 'HOT';

  if (d === config.HIT_DIST) {
    message = 'HIT!\nBOOM!';
  } else if (d >= config.COLD_DIST) {
    message = 'COLD';
  } else if (d >= config.WARM_DIST) {
    message = 'WARM';
  }

  alert(message);

  return d === config.HIT_DIST;
}

export function updateScreen(element, value) {
  element.innerHTML = value;
}

export function selectCoordinates() {
  alert('Time to attack! Adjust your aim by entering the coordinates.');
  const coordinateX = getValidCoordinates('X', config.GRID_SIZE);
  const coordinateY = getValidCoordinates('Y', config.GRID_SIZE);

  const shiftedX = coordinateX - 1;
  const shiftedY = coordinateY - 1;

  return { shiftedX, shiftedY };
}

export function getValidCoordinates(coordinate, maxNumber) {
  let promptMessage = `Please enter your ${coordinate} coordinate. Choose a number between 1 and ${maxNumber}:`;
  let coordinateValue;
  do {
    coordinateValue = parseInt(prompt(promptMessage), 10);
  } while (
    isNaN(coordinateValue) ||
    coordinateValue <= 0 ||
    coordinateValue > maxNumber
  );

  return coordinateValue;
}

// DB Operations
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
