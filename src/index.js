import { userDefinedConfigs } from './helpers/phpSessions.js';
import { getGrid, addScore, resetScore } from './helpers/http_requests.js';

import {
  getClosestShipDistance,
  launchRocket,
  locateShips,
  radarFeedback,
  removeShip,
  resetGame,
  revealGrid,
  selectCoordinates,
  setGameState,
  updateScreen,
  useRockets,
  closeModal,
} from './helpers/helpers.js';

import {
  welcomeMessage,
  youWinMessage,
  gameOverMessage,
  continueMessage,
} from './helpers/messages.js';

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
  modalPanel: document.getElementById('modal'),
  modalCloseButton: document.getElementById('modalCloseBtn'),
  modalMsgPlaceholder: document.getElementById('modalMessage'),
  modalInput: document.getElementById('modalInput'),
  modalSubmitBtn: document.getElementById('modalSubmit'),
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

uiElements.startBtn.addEventListener('click', playGame);
uiElements.restartBtn.style.display = 'none';
uiElements.restartBtn.addEventListener('click', resetGame);

uiElements.modalCloseButton.addEventListener('click', () => {
  closeModal(uiElements.modalPanel, uiElements.startBtn);
});

uiElements.modalInput.addEventListener('keydown', (event) => {
  if (event.key === 'Enter' && !event.repeat) {
    event.preventDefault();
    uiElements.modalSubmitBtn.click();
  }
});

if (uiElements.resetScoreButton) {
  uiElements.resetScoreButton.addEventListener('click', () => {
    resetScore(userIdValue);
  });
}

updateScreen(
  uiElements.msgContainer,
  welcomeMessage(userNameValue, gameState.shipsCount, gameState.rocketsCount)
);

updateScreen(uiElements.rockets, userDefinedConfigs.NUM_ROCKETS);
updateScreen(uiElements.shipsDestroyed, 0);
updateScreen(uiElements.rocketsIcon, userDefinedConfigs.ROCKET_ICON);
updateScreen(uiElements.shipsDestroyedIcon, userDefinedConfigs.EXPLOSION_ICON);
updateScreen(uiElements.trophiesIcon, userDefinedConfigs.TROPHIE_ICON);

function playGame() {
  if (gameState.state === 'init') {
    initializeGame();
  }

  if (gameState.state === 'playing') {
    playTurn();
  }
}

async function initializeGame() {
  uiElements.startBtn.style.display = 'none';
  if (uiElements.resetScoreButton) {
    uiElements.resetScoreButton.style.display = 'none';
  }

  gameState.state = 'playing';
  gameState.grid = await getGrid();

  console.table(gameState.grid);
}

async function playTurn() {
  const { shiftedX, shiftedY } = await selectCoordinates(
    uiElements.modalMsgPlaceholder
  );
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
  await launchRocket(shiftedX, shiftedY);

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
  updateScreen(uiElements.startBtn, 'Continue ➡️');
  updateScreen(uiElements.restartBtn, 'Restart 🔄');
  uiElements.startBtn.style.display = 'block';
  uiElements.restartBtn.style.display = 'block';
}

function handleGameOver() {
  updateScreen(uiElements.msgContainer, gameOverMessage());
  updateScreen(uiElements.restartBtn, 'Try Again 🔄');
  revealGrid(gameState.grid);
  uiElements.startBtn.style.display = 'none';
}

function handleGameWin() {
  updateScreen(trophies, gameState.trophiesCount + 1);
  updateScreen(uiElements.msgContainer, youWinMessage());
  updateScreen(uiElements.restartBtn, 'Play Again 🔄');
  addScore(userIdValue);
  revealGrid(gameState.grid);
  uiElements.startBtn.style.display = 'none';
  uiElements.restartBtn.style.display = 'block';
}
