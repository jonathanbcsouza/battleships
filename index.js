const HIT_DIST = 0;
const HOT_DIST = 1;
const WARM_DIST = 3;
const COLD_DIST = 5;

const gridSize = 4;
const numRockets = 2;
const numShips = 2;

const welcomeMsg =
  `Welcome aboard. You are in the middle of a war and your mission is to destroy the remaining ${numShips} opponent ships! You have ${numRockets} rockets left. You can use the rockets to attack the ships. ` +
  `<br>` +
  `Good luck!`;

const board = document.getElementById('board');
const startBtn = document.getElementById('startButton');
startBtn.addEventListener('click', playGame);
const restartBtn = document.getElementById('restart');
restartBtn.addEventListener('click', resetGame);
restartBtn.style.display = 'none';

let welcomeMsgElement = document.getElementById('welcome_msg');
let rockets = document.getElementById('rockets');
let ships_destroyed = document.getElementById('ships_destroyed');
welcomeMsgElement.innerHTML = welcomeMsg;
rockets.innerHTML = numRockets;
ships_destroyed.innerHTML = 0;

let shipsCount = numShips;
let rocketsCount = numRockets;
let trophiesCount = 0;
let gameState = 'init';
const trophies = document.getElementById('trophies');
trophies.innerHTML = trophiesCount;

function playGame() {
  startBtn.style.display = 'none';

  if (gameState === 'init') {
    shipsCount = numShips;
    rocketsCount = numRockets;
    grid = buildGrid(gridSize);
    placeShips(numShips);
    console.table(grid);
    gameState = 'playing';
  }

  if (gameState === 'playing') {
    alert('Time to attack! Adjust your aim by entering the coordinates.');

    const coordinateX = prompt('Select your X coordinate.');
    const coordinateY = prompt('Select your Y coordinate.');
    const shiftedX = coordinateX - 1;
    const shiftedY = coordinateY - 1;

    const shipsCoordinates = locateShips();

    closestShipDist = getClosestclosestShipDistance(
      shiftedX,
      shiftedY,
      shipsCoordinates
    );

    launchRocket(shiftedX, shiftedY, closestShipDist);

    const gameStatus = setGameStatus();

    restartBtn.style.display = 'block';

    switch (gameStatus) {
      case 'lose':
        welcomeMsgElement.innerHTML = `Game Over! You Lose! 😭`;
        restartBtn.innerHTML = 'Try Again';
        shipsCount = numShips;
        rocketsCount = numRockets;
        revealGrid();
        break;
      case 'win':
        welcomeMsgElement.innerHTML = `Congratulations, You Win! 🎉`;
        trophiesCount = trophiesCount + 1;
        gameState = 'init';
        shipsCount = numShips;
        rocketsCount = numRockets;
        trophies.innerHTML = trophiesCount;
        startButton.innerHTML = 'Start a New Game';
        revealGrid();
        break;
      default:
        welcomeMsgElement.innerHTML =
          `🎙️ Roger!` +
          `<br>` +
          `You have ${rocketsCount} rockets left and ${shipsCount} opponent ships remaining. Keep it Up!`;
        startBtn.style.display = 'block';
        restartBtn.style.display = 'none';
        startButton.innerHTML = 'Continue Game ➡️';
    }
  }
}

function revealGrid() {
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

function getClosestclosestShipDistance(shiftedX, shiftedY, shipsCoordinates) {
  const distances = [];

  for (let i = 0; i < shipsCoordinates.length; i++) {
    const distFromShip =
      Math.abs(shiftedX - shipsCoordinates[i][0]) +
      Math.abs(shiftedY - shipsCoordinates[i][1]);

    distances.push(distFromShip);
  }

  return Math.min(...distances);
}

function placeShips(numShips) {
  let shipsPlaced = 0;

  while (shipsPlaced < numShips) {
    const randomRow = Math.floor(Math.random() * gridSize);
    const randomCol = Math.floor(Math.random() * gridSize);

    if (grid[randomRow][randomCol] == 0) {
      grid[randomRow][randomCol] = 1;
      shipsPlaced++;
    }
  }
}

function locateShips() {
  let shipsLocation = [];
  for (let i = 0; i < gridSize; i++) {
    for (let j = 0; j < gridSize; j++) {
      if (grid[i][j] === 1) {
        shipsLocation.push([i, j]);
      }
    }
  }

  return shipsLocation;
}

function launchRocket(x, y, d) {
  useRockets();
  rockets.innerHTML = rocketsCount;
  alert(`Rocket launched to ${x + 1}, ${y + 1}!`);
  radarFeedback(x, y, d);
}

function radarFeedback(x, y, d) {
  if (d === HIT_DIST) {
    alert('HIT!');
    hit(x, y);
  } else if (d >= COLD_DIST) {
    alert('COLD');
  } else if (d >= WARM_DIST) {
    alert('WARM');
  } else {
    alert('HOT');
  }
}

function useRockets() {
  rocketsCount = rocketsCount - 1;
}

function hit(x, y) {
  removeShip(x, y);
  shipsCount = shipsCount - 1;
  ships_destroyed.innerHTML = numShips - shipsCount;
  alert('BOOM!');
}

function removeShip(x, y) {
  grid[x][y] = 'x';
}

function setGameStatus() {
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

function buildGrid(size) {
  let columns = [];

  for (let i = 0; i < size; i++) {
    let rows = [];
    for (let j = 0; j < size; j++) {
      rows.push(0);
    }
    columns.push(rows);
  }

  return columns;
}

function resetGame() {
  while (board.firstChild) {
    board.removeChild(board.firstChild);
  }
  welcomeMsgElement.innerHTML = welcomeMsg;
  rockets.innerHTML = numRockets;
  ships_destroyed.innerHTML = 0;

  startBtn.style.display = 'block';
  restartBtn.style.display = 'none';

  gameState = 'init';
}
