import { userDefinedConfigs } from './handle_php_sessions.js';

import { attackMessage, launchRocketMessage } from './messages.js';

// Game Logic
export function removeShip(grid, x, y) {
  grid[x][y] = userDefinedConfigs.EXPLOSION_ICON;
  return grid;
}

export function createGrid(grid) {
  const table = document.createElement('table');

  for (let i = 0; i < grid.length; i++) {
    const row = document.createElement('tr');
    for (let j = 0; j < grid[i].length; j++) {
      const cell = document.createElement('td');
      cell.textContent = userDefinedConfigs.EMPTY_ICON;
      row.appendChild(cell);
    }
    table.appendChild(row);
  }

  board.appendChild(table);

  showAlert(attackMessage());
}

export function replaceGrid(grid) {

  const table = document.querySelector('table');

  while (table.firstChild) {
    table.removeChild(table.firstChild);
  }

  for (let i = 0; i < grid.length; i++) {
    const row = document.createElement('tr');
    for (let j = 0; j < grid[i].length; j++) {
      const cell = document.createElement('td');
      cell.textContent = grid[i][j]; 
      row.appendChild(cell);
    }
    table.appendChild(row);
  }
}

export function defineTargets() {
  const table = document.querySelector('table');

  return new Promise((resolve) => {
    table.addEventListener('click', (event) => {
      if (event.target.tagName === 'TD') {
        const cell = event.target;
        const coordinateX = cell.parentNode.rowIndex + 1;
        const coordinateY = cell.cellIndex + 1;

        resolve({ coordinateX, coordinateY });
      }
    });
  });
}

export function updateDisplayedGrid(coordinateX, coordinateY, grid) {
  const table = document.querySelector('table');
  const cell = table.rows[coordinateX - 1].cells[coordinateY - 1];

  if (grid[coordinateX - 1][coordinateY - 1] === userDefinedConfigs.SHIP_ICON) {
    cell.textContent = userDefinedConfigs.EXPLOSION_ICON;
  } else {
    cell.textContent = 'X';
  }
}

export function shiftCoordinates(x, y) {
  const shiftedX = x - 1;
  const shiftedY = y - 1;

  return { shiftedX, shiftedY };
}

export function locateShips(grid, numShips) {
  const gridSize = grid.length;
  let shipsLocation = [];
  for (let i = 0; i < gridSize; i++) {
    for (let j = 0; j < gridSize; j++) {
      if (grid[i][j] == userDefinedConfigs.SHIP_ICON) {
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

export async function launchRocket(x, y) {
  await showAlert(launchRocketMessage(x, y));
}

export async function radarFeedback(dist) {
  let message = 'Hot! 🔥';

  if (dist == userDefinedConfigs.HIT_DIST) {
    message = 'HIT! BOOM! \nShip destroyed! 💥';
  } else if (dist >= userDefinedConfigs.COLD_DIST) {
    message = 'Cold! ❄️';
  } else if (dist >= userDefinedConfigs.WARM_DIST) {
    message = 'Warm! 🌡️';
  }

  await showAlert(message);

  return dist === parseInt(userDefinedConfigs.HIT_DIST);
}

export function updateScreen(element, value) {
  element.innerHTML = value;
}

export function showAlert(message) {
  return new Promise((resolve) => {
    alertMessage.innerText = message;
    alertModal.style.display = 'block';
    alertSubmit.focus();

    alertSubmit.onclick = function () {
      alertModal.style.display = 'none';
      resolve();
    };
  });
}
