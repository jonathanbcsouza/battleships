import {
  buildGrid,
  getClosestShipDistance,
  launchRocket,
  locateShips,
  placeShips,
  radarFeedback,
  removeShip,
  resetGame,
  revealGrid,
  setGameState,
  updateScreen,
  useRockets,
  getValidCoordinate,
} from './helpers/helpers.js';

const gridSize = 4;
const numRockets = 2;
const numShips = 2;
const msgGameOver = `Game Over! You Lose! 😭`;
const msgYouWin = `Congratulations, You Win! 🎉`;
const welcomeMsg =
  `Welcome aboard. You are in the middle of a war and your mission is to destroy the remaining ${numShips} opponent ships! You have ${numRockets} rockets left. You can use the rockets to attack the ships. ` +
  `<br>` +
  `Good luck!`;

const trophies = document.getElementById('trophies');
const board = document.getElementById('board');
const startBtn = document.getElementById('startButton');
const restartBtn = document.getElementById('restart');

let grid = [];
let shipsCount = numShips;
let rocketsCount = numRockets;
let shipsDestroyedCount = 0;
let trophiesCount = 0;
let gameState = 'init';

let welcomeMsgElement = document.getElementById('welcome_msg');
let rockets = document.getElementById('rockets');
let shipsDestroyed = document.getElementById('shipsDestroyed');

startBtn.addEventListener('click', playGame);
restartBtn.style.display = 'none';
restartBtn.addEventListener('click', function () {
  resetGame(
    welcomeMsg,
    welcomeMsgElement,
    numRockets,
    rockets,
    shipsDestroyed,
    startBtn,
    restartBtn,
    board,
    gameState
  );
});

updateScreen(welcomeMsgElement, welcomeMsg);
updateScreen(rockets, numRockets);
updateScreen(shipsDestroyed, 0);
updateScreen(trophies, trophiesCount);

function playGame() {
  startBtn.style.display = 'none';

  if (gameState === 'init') {
    shipsCount = numShips;
    rocketsCount = numRockets;
    shipsDestroyedCount = 0;
    grid = buildGrid(gridSize);
    placeShips(grid, gridSize, numShips);
    console.table(grid);
    gameState = 'playing';
  }

  if (gameState === 'playing') {
    alert('Time to attack! Adjust your aim by entering the coordinates.');

    const coordinateX = getValidCoordinate('x', gridSize);
    const coordinateY = getValidCoordinate('y', gridSize);

    const shiftedX = coordinateX - 1;
    const shiftedY = coordinateY - 1;

    const shipsCoordinates = locateShips(grid);

    const closestShipDist = getClosestShipDistance(
      shiftedX,
      shiftedY,
      shipsCoordinates
    );

    rocketsCount = useRockets(rocketsCount);

    updateScreen(rockets, rocketsCount);

    launchRocket(shiftedX, shiftedY);

    const isShipDestroyed = radarFeedback(closestShipDist);

    if (isShipDestroyed) {
      grid = removeShip(grid, shiftedX, shiftedY);
      shipsCount = shipsCount - 1;
      shipsDestroyedCount = shipsDestroyedCount + 1;
    }

    updateScreen(shipsDestroyed, shipsDestroyedCount);

    const status = setGameState(rocketsCount, shipsCount, gameState);

    switch (status) {
      case 'lose':
        shipsCount = numShips;
        rocketsCount = numRockets;
        gameState = 'init';
        updateScreen(welcomeMsgElement, msgGameOver);
        updateScreen(restartBtn, 'Try Again');
        restartBtn.style.display = 'block';
        revealGrid(grid);
        break;
      case 'win':
        shipsCount = numShips;
        rocketsCount = numRockets;
        gameState = 'init';
        trophiesCount = trophiesCount + 1;
        updateScreen(welcomeMsgElement, msgYouWin);
        updateScreen(restartBtn, 'Start a New Game');
        updateScreen(trophies, trophiesCount);
        restartBtn.style.display = 'block';
        revealGrid(grid);
        break;
      default:
        updateScreen(
          welcomeMsgElement,
          `🎙️ Roger!` +
            `<br>` +
            `You have ${rocketsCount} rockets left and ${shipsCount} opponent ships remaining. Keep it Up!`
        );
        restartBtn.style.display = 'none';
        startBtn.style.display = 'block';
        updateScreen(startButton, 'Continue Game ➡️');
    }
  }
}
