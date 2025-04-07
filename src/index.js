import { userDefinedConfigs } from './Helpers/handle_php_sessions.js';
import { getGrid, addScore, resetScore } from './Helpers/http_requests.js';

import {
  createGrid,
  defineTargets,
  getClosestShipDistance,
  launchRocket,
  locateShips,
  radarFeedback,
  removeShip,
  resetGame,
  replaceGrid,
  setGameState,
  shiftCoordinates,
  updateDisplayedGrid,
  updateScreen,
  useRockets,
} from './Helpers/helpers.js';

import {
  welcomeMessage,
  youWinMessage,
  gameOverMessage,
  continueMessage,
} from './Helpers/messages.js';

const uiElements = {
  userNameInput: document.getElementById('username'),
  msgContainer: document.getElementById('msgContainer'),
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

let username = uiElements.userNameInput;
let userIdValue = username.dataset.id;
let userNameValue =
  username.dataset.user.charAt(0).toUpperCase() +
  username.dataset.user.slice(1);

let gameState = {
  grid: [],
  shipsCount: userDefinedConfigs.NUM_SHIPS,
  rocketsCount: userDefinedConfigs.NUM_ROCKETS,
  trophiesCount: parseInt(uiElements.trophies.textContent),
  shipsDestroyedCount: 0,
  state: 'init',
};

// Add debug logging
console.log('Game state initialized:', gameState);
console.log('User defined configs:', userDefinedConfigs);

uiElements.startBtn.addEventListener('click', playGame);
uiElements.restartBtn.style.display = 'none';
uiElements.restartBtn.addEventListener('click', resetGame);

if (uiElements.resetScoreButton) {
  uiElements.resetScoreButton.addEventListener('click', () => {
    resetScore(userIdValue);
  });
}

updateScreen(
  uiElements.msgContainer,
  welcomeMessage(userNameValue, gameState.shipsCount, gameState.rocketsCount)
);

async function playGame() {
  if (gameState.state === 'init') {
    await initializeGame();
  }

  if (gameState.state === 'playing') {
    await playTurn();
  }
}

async function initializeGame() {
  uiElements.startBtn.style.display = 'none';
  if (uiElements.resetScoreButton) {
    uiElements.resetScoreButton.style.display = 'none';
  }

  gameState.state = 'playing';
  gameState.grid = await getGrid();

  createGrid(gameState.grid);
}

async function playTurn() {
  const { coordinateX, coordinateY } = await defineTargets();
  const { shiftedX, shiftedY } = shiftCoordinates(coordinateX, coordinateY);
  await launchRocket(shiftedX, shiftedY);

  updateDisplayedGrid(coordinateX, coordinateY, gameState.grid);

  const shipsCoordinates = locateShips(
    gameState.grid,
    userDefinedConfigs.NUM_SHIPS
  );

  const closestShipDist = getClosestShipDistance(
    shiftedX,
    shiftedY,
    shipsCoordinates
  );

  gameState.rocketsCount = useRockets(gameState.rocketsCount);

  updateScreen(uiElements.rockets, gameState.rocketsCount);

  const isShipDestroyed = await radarFeedback(closestShipDist);

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
    uiElements.msgContainer,
    continueMessage(userNameValue, gameState.rocketsCount, gameState.shipsCount)
  );

  updateScreen(uiElements.restartBtn, 'Restart 🔄');
  uiElements.restartBtn.style.display = 'block';
  playGame();
}

function handleGameOver() {
  updateScreen(uiElements.msgContainer, gameOverMessage());
  updateScreen(uiElements.restartBtn, 'Try Again 🔄');
  replaceGrid(gameState.grid);
  uiElements.startBtn.style.display = 'none';
}

function handleGameWin() {
  updateScreen(trophies, gameState.trophiesCount + 1);
  updateScreen(uiElements.msgContainer, youWinMessage());
  updateScreen(uiElements.restartBtn, 'Play Again 🔄');
  addScore(userIdValue);
  uiElements.startBtn.style.display = 'none';
  uiElements.restartBtn.style.display = 'block';
}
