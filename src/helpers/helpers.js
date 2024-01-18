import { userDefinedConfigs } from './phpSessions.js';

export function removeShip(grid, x, y) {
  grid[x][y] = userDefinedConfigs.EXPLOSION_ICON;
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

// Ui Updates
export async function launchRocket(x, y) {
  await showAlert(`Rocket launched to ${x + 1}, ${y + 1}! 🎯`);
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

export async function selectCoordinates() {
  await showAlert(
    'Time to attack! \nAdjust your aim by entering the coordinates.'
  );
  const coordinateX = await getValidCoordinates(
    'X',
    userDefinedConfigs.GRID_SIZE
  );
  const coordinateY = await getValidCoordinates(
    'Y',
    userDefinedConfigs.GRID_SIZE
  );

  const shiftedX = coordinateX - 1;
  const shiftedY = coordinateY - 1;

  return { shiftedX, shiftedY };
}

async function getValidCoordinates(coordinate, maxNumber) {
  let promptMessage = `Please enter your ${coordinate} coordinate.\n Choose a number between 1 and ${maxNumber}.`;
  let errorMessage = `Invalid input. \nPlease enter a number between 1 and ${maxNumber}.`;
  let successMessage = `Coordinate ${coordinate} defined!`;
  let coordinateValue;
  let numberValue;

  do {
    coordinateValue = await showModal(promptMessage);
    numberValue = Number(coordinateValue);
    if (isNaN(numberValue) || numberValue < 1 || numberValue > maxNumber) {
      await showAlert(errorMessage);
    } else {
      await showAlert(successMessage);
    }
  } while (isNaN(numberValue) || numberValue < 1 || numberValue > maxNumber);

  return numberValue;
}

function showModal(message) {
  return new Promise((resolve) => {
    modalMessage.innerText = message;
    modal.style.display = 'block';
    modalInput.focus();

    modalSubmit.onclick = function () {
      resolve(modalInput.value);
      modalInput.value = '';
      modal.style.display = 'none';
    };
  });
}

function showAlert(message) {
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

export function closeModal(modal, startBtn) {
  modal.style.display = 'none';
  startBtn.style.display = 'block';
}
