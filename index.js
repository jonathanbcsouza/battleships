import * as config from './configs/constants.js';

import {
  buildGrid,
  getClosestShipDistance,
  launchRocket,
  locateShips,
  placeShips,
  radarFeedback,
  removeShip,
  resetGame,
  resetScore,
  revealGrid,
  setGameState,
  updateScreen,
  useRockets,
  getCoordinates,
} from './helpers/helpers.js';

const msgGameOver = `Game Over! You Lose! 😭`;
const msgYouWin = `Congratulations, You Win! 🎉`;
const welcomeMsg =
  `Welcome aboard. You are in the middle of a war and your mission is to destroy the remaining ${config.NUM_SHIPS} opponent ships! You have ${config.NUM_ROCKETS} rockets left. You can use the rockets to attack the ships. ` +
  `<br>` +
  `Good luck!`;

const startBtn = document.getElementById('startButton');
const restartBtn = document.getElementById('restart');

let grid = [];
let shipsCount = config.NUM_SHIPS;
let rocketsCount = config.NUM_ROCKETS;
let shipsDestroyedCount = 0;
let trophiesCount = Number(localStorage.getItem('trophiesCount')) || 0;
let gameState = 'init';

let welcomeMsgElement = document.getElementById('welcome_msg');
let rockets = document.getElementById('rockets');
let shipsDestroyed = document.getElementById('shipsDestroyed');
let trophies = document.getElementById('trophies');

let rocketsIcon = document.getElementById('rocketsIcon');
let shipsDestroyedIcon = document.getElementById('shipsDestroyedIcon');
let trophiesIcon = document.getElementById('trophiesIcon');

startBtn.addEventListener('click', playGame);
restartBtn.style.display = 'none';
restartBtn.addEventListener('click', resetGame);

updateScreen(welcomeMsgElement, welcomeMsg);
updateScreen(rockets, config.NUM_ROCKETS);
updateScreen(shipsDestroyed, 0);
updateScreen(trophies, trophiesCount);

updateScreen(rocketsIcon, config.ROCKET_ICON);
updateScreen(shipsDestroyedIcon, config.EXPLOSION_ICON);
updateScreen(trophiesIcon, config.TROPHIE_ICON);

resetScoreButton.addEventListener('click', resetScore);

if (trophiesCount > 0) {
  resetScoreButton.style.display = 'block';
} else {
  resetScoreButton.style.display = 'none';
}

function playGame() {
  if (gameState === 'init') {
    initializeGame();
  }

  if (gameState === 'playing') {
    playTurn();
  }
}

function initializeGame() {
  gameState = 'playing';
  resetScoreButton.style.display = 'none';
  grid = buildGrid(config.GRID_SIZE);
  placeShips(grid, config.GRID_SIZE, config.NUM_SHIPS);
  console.table(grid);
}

function playTurn() {
  alert('Time to attack! Adjust your aim by entering the coordinates.');

  const { shiftedX, shiftedY } = getCoordinates();

  const shipsCoordinates = locateShips(grid, config.NUM_SHIPS);

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
    updateScreen(shipsDestroyed, shipsDestroyedCount);
  }

  handleGameState();
}

function handleGameState() {
  const state = setGameState(rocketsCount, shipsCount, gameState);

  switch (state) {
    case 'lose':
      handleGameOver();
      break;
    case 'win':
      handleGameWin();
      break;
    default:
      handleGameContinue();
  }
}

function handleGameOver() {
  updateScreen(welcomeMsgElement, msgGameOver);
  updateScreen(restartBtn, 'Try Again');
  startBtn.style.display = 'none';
  restartBtn.style.display = 'block';
  revealGrid(grid);
}

function handleGameWin() {
  trophiesCount = trophiesCount + 1;
  updateScreen(trophies, trophiesCount);
  updateScreen(welcomeMsgElement, msgYouWin);
  updateScreen(restartBtn, 'Restart Game');
  localStorage.setItem('trophiesCount', trophiesCount);
  startBtn.style.display = 'none';
  restartBtn.style.display = 'block';
  revealGrid(grid);
}

function handleGameContinue() {
  updateScreen(
    welcomeMsgElement,
    `🎙️ Roger!` +
      `<br>` +
      `You have ${rocketsCount} rockets left and ${shipsCount} opponent ships remaining. Keep it Up!`
  );
  restartBtn.style.display = 'none';
  startBtn.style.display = 'block';
  updateScreen(startBtn, 'Continue Game ➡️');
}
