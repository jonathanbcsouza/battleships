import * as config from '../configs/constants.js';

// Grid management
export function buildGrid(size) {
  let columns = [];

  for (let i = 0; i < size; i++) {
    let rows = [];
    for (let j = 0; j < size; j++) {
      rows.push(config.EMPTY_ICON);
    }
    columns.push(rows);
  }

  return columns;
}

export function placeShips(grid, gridSize, numShips) {
  let shipsPlaced = 0;

  while (shipsPlaced < numShips) {
    const randomRow = Math.floor(Math.random() * gridSize);
    const randomCol = Math.floor(Math.random() * gridSize);

    if (grid[randomRow][randomCol] == config.EMPTY_ICON) {
      grid[randomRow][randomCol] = config.SHIP_ICON;
      shipsPlaced++;
    }
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

export function resetScore() {
  localStorage.removeItem('trophiesCount');
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

export function getValidCoordinate(coordinate, maxNumber) {
  let promptMessage = `Select your ${coordinate} coordinate (1 to ${maxNumber}):`;

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
