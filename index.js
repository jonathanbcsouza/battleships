import * as config from './configs/constants.js';

import {
  buildGrid,
  getClosestShipDistance,
  getCoordinates,
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
} from './helpers/helpers.js';

const msg = {
  welcome: `Welcome aboard. You are in the middle of a war and your mission is to destroy the remaining ${config.NUM_SHIPS} opponent ships! You have ${config.NUM_ROCKETS} rockets left. You can use the rockets to attack the ships. <br>Good luck!`,
  youWin: `Congratulations, You Win! 🎉`,
  gameOver: `Game Over! You Lose! 😭`,
};

const uiElements = {
  resetScoreButton: document.getElementById('resetScoreButton'),
  restartBtn: document.getElementById('restart'),
  rockets: document.getElementById('rockets'),
  rocketsIcon: document.getElementById('rocketsIcon'),
  shipsDestroyed: document.getElementById('shipsDestroyed'),
  shipsDestroyedIcon: document.getElementById('shipsDestroyedIcon'),
  startBtn: document.getElementById('startButton'),
  trophies: document.getElementById('trophies'),
  trophiesIcon: document.getElementById('trophiesIcon'),
  welcomeMsg: document.getElementById('welcome_msg'),
  welcomeMsgElement: document.getElementById('welcome_msg'),
};

let gameState = {
  grid: [],
  shipsCount: config.NUM_SHIPS,
  rocketsCount: config.NUM_ROCKETS,
  shipsDestroyedCount: 0,
  trophiesCount: Number(localStorage.getItem('trophiesCount')) || 0,
  state: 'init',
};

uiElements.startBtn.addEventListener('click', playGame);
uiElements.restartBtn.style.display = 'none';
uiElements.restartBtn.addEventListener('click', resetGame);
uiElements.resetScoreButton.addEventListener('click', resetScore);

updateScreen(uiElements.welcomeMsgElement, msg.welcome);
updateScreen(uiElements.rockets, config.NUM_ROCKETS);
updateScreen(uiElements.shipsDestroyed, 0);
updateScreen(uiElements.trophies, gameState.trophiesCount);
updateScreen(uiElements.rocketsIcon, config.ROCKET_ICON);
updateScreen(uiElements.shipsDestroyedIcon, config.EXPLOSION_ICON);
updateScreen(uiElements.trophiesIcon, config.TROPHIE_ICON);

if (gameState.trophiesCount > 0) {
  uiElements.resetScoreButton.style.display = 'block';
} else {
  uiElements.resetScoreButton.style.display = 'none';
}

function playGame() {
  if (gameState.state === 'init') {
    initializeGame();
  }

  if (gameState.state === 'playing') {
    playTurn();
  }
}

function initializeGame() {
  gameState.state = 'playing';
  uiElements.resetScoreButton.style.display = 'none';
  gameState.grid = buildGrid(config.GRID_SIZE);
  placeShips(gameState.grid, config.GRID_SIZE, config.NUM_SHIPS);
  console.table(gameState.grid);
}

function playTurn() {
  alert('Time to attack! Adjust your aim by entering the coordinates.');

  const { shiftedX, shiftedY } = getCoordinates();

  const shipsCoordinates = locateShips(gameState.grid, config.NUM_SHIPS);

  const closestShipDist = getClosestShipDistance(
    shiftedX,
    shiftedY,
    shipsCoordinates
  );

  gameState.rocketsCount = useRockets(gameState.rocketsCount);

  updateScreen(uiElements.rockets, gameState.rocketsCount);

  launchRocket(shiftedX, shiftedY);

  const isShipDestroyed = radarFeedback(closestShipDist);

  if (isShipDestroyed) {
    gameState.grid = removeShip(gameState.grid, shiftedX, shiftedY);
    gameState.shipsCount = gameState.shipsCount - 1;
    gameState.shipsDestroyedCount = gameState.shipsDestroyedCount + 1;
    updateScreen(uiElements.shipsDestroyed, gameState.shipsDestroyedCount);
  }

  handleGameState();
}

function handleGameState() {
  const state = setGameState(
    gameState.rocketsCount,
    gameState.shipsCount,
    gameState.state
  );

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
  updateScreen(uiElements.welcomeMsgElement, msg.gameOver);
  updateScreen(uiElements.restartBtn, 'Try Again');
  uiElements.startBtn.style.display = 'none';
  uiElements.restartBtn.style.display = 'block';
  revealGrid(gameState.grid);
}

function handleGameWin() {
  gameState.trophiesCount = gameState.trophiesCount + 1;
  updateScreen(uiElements.trophies, gameState.trophiesCount);
  updateScreen(uiElements.welcomeMsgElement, msg.youWin);
  updateScreen(uiElements.restartBtn, 'Restart Game');
  localStorage.setItem('gameState.trophiesCount', gameState.trophiesCount);
  uiElements.startBtn.style.display = 'none';
  uiElements.restartBtn.style.display = 'block';
  revealGrid(gameState.grid);
}

function handleGameContinue() {
  updateScreen(
    uiElements.welcomeMsgElement,
    `🎙️ Roger!` +
      `<br>` +
      `You have ${gameState.rocketsCount} rockets left and ${gameState.shipsCount} opponent ships remaining. Keep it Up!`
  );
  uiElements.restartBtn.style.display = 'none';
  uiElements.startBtn.style.display = 'block';
  updateScreen(uiElements.startBtn, 'Continue Game ➡️');
}
