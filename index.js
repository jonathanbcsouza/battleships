import * as config from './configs/constants.js';

import {
  addScore,
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
  selectCoordinates,
  setGameState,
  updateScreen,
  useRockets,
} from './helpers/helpers.js';

const uiElements = {
  userNameInput: document.getElementById('username'),
  msgElement: document.getElementById('msgElement'),
  rockets: document.getElementById('rockets'),
  rocketsIcon: document.getElementById('rocketsIcon'),
  shipsDestroyed: document.getElementById('shipsDestroyed'),
  shipsDestroyedIcon: document.getElementById('shipsDestroyedIcon'),
  trophies: document.getElementById('trophies'),
  trophiesIcon: document.getElementById('trophiesIcon'),
  startBtn: document.getElementById('startButton'),
  restartBtn: document.getElementById('restart'),
  resetScoreButton: document.getElementById('resetScoreButton'),
};

let gameState = {
  grid: [],
  shipsCount: config.NUM_SHIPS,
  rocketsCount: config.NUM_ROCKETS,
  shipsDestroyedCount: 0,
  state: 'init',
};

const msg = {
  welcome: `Welcome aboard. You are in the middle of a war and your mission is to destroy the remaining ${config.NUM_SHIPS} opponent ships! You have ${config.NUM_ROCKETS} rockets left. You can use the rockets to attack the ships. <br>Good luck!`,
  youWin: `Congratulations, You Win! 🎉`,
  gameOver: `Game Over! You Lose! 😭`,
};

let username = uiElements.userNameInput;
let trophies = uiElements.trophies;
let trophiesTot = parseInt(trophies.innerText);

uiElements.startBtn.addEventListener('click', playGame);
uiElements.restartBtn.style.display = 'none';
uiElements.restartBtn.addEventListener('click', resetGame);

if (uiElements.resetScoreButton) {
  uiElements.resetScoreButton.addEventListener('click', () => {
    resetScore(username.value);
  });
}

updateScreen(uiElements.msgElement, msg.welcome);
updateScreen(uiElements.rockets, config.NUM_ROCKETS);
updateScreen(uiElements.shipsDestroyed, 0);
updateScreen(uiElements.rocketsIcon, config.ROCKET_ICON);
updateScreen(uiElements.shipsDestroyedIcon, config.EXPLOSION_ICON);
updateScreen(uiElements.trophiesIcon, config.TROPHIE_ICON);

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
  gameState.grid = buildGrid(config.GRID_SIZE);
  placeShips(gameState.grid, config.GRID_SIZE, config.NUM_SHIPS);
  console.table(gameState.grid);

  if (uiElements.resetScoreButton) {
    uiElements.resetScoreButton.style.display = 'none';
  }
  uiElements.userNameInput.style.display = 'none';
}

function playTurn() {
  const { shiftedX, shiftedY } = selectCoordinates();
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
    gameState.shipsCount -= 1;
    gameState.shipsDestroyedCount += 1;
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

function handleGameContinue() {
  updateScreen(
    uiElements.msgElement,
    `🎙️ Roger!` +
      `<br>` +
      `You have ${gameState.rocketsCount} rockets left and ${gameState.shipsCount} opponent ships remaining. Keep it Up!`
  );
  updateScreen(uiElements.startBtn, 'Continue Game ➡️');
  updateScreen(uiElements.restartBtn, 'Restart 🔄');
  uiElements.startBtn.style.display = 'block';
  uiElements.restartBtn.style.display = 'block';
}

function handleGameOver() {
  updateScreen(uiElements.msgElement, msg.gameOver);
  updateScreen(uiElements.restartBtn, 'Try Again 🔄');
  revealGrid(gameState.grid);
  uiElements.startBtn.style.display = 'none';
}

function handleGameWin() {
  trophiesTot += 1;
  console.log(trophiesTot);
  updateScreen(trophies, trophiesTot);
  updateScreen(uiElements.msgElement, msg.youWin);
  updateScreen(uiElements.restartBtn, 'Play Again 🔄');
  addScore(username.value);
  revealGrid(gameState.grid);
  uiElements.startBtn.style.display = 'none';
  uiElements.restartBtn.style.display = 'block';
}
