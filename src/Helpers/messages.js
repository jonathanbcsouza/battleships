export function welcomeMessage(username, numShips, numRockets) {
    return `
      <h3>Welcome aboard, captain <strong>${username}</strong>!</h3>
      <p>You are now in the midst of a naval conflict. Your mission, should you choose to accept it, is to sink the remaining <strong>${numShips}</strong> enemy ${numShips > 1 ? 'ships' : 'ship'}.</p>
      <p>You have <strong>${numRockets}</strong> ${numRockets > 1 ? 'rockets' : 'rocket'} at your disposal. Use them wisely and secure our victory!</p>
    `;
  }
  
export function youWinMessage() {
    return `
      <p>Congratulations, captain! You've successfully defeated the enemy.</p>
      <p>The high seas are safe once again. 🎉</p>
    `;
  }
  
export function gameOverMessage() {
    return `
      <p>The battle is lost, captain. But this is not the end.</p>
      <p>We will regroup and fight again! 💪</p>
    `;
  }
  
export function continueMessage(username, rocketsCount, shipsCount) {
    return `
      <div>
        <p>Command Center to <strong>${username}</strong>. 🎙️ Do you copy?</p>
        <p>You have <strong>${rocketsCount}</strong> ${rocketsCount > 1 ? 'rockets' : 'rocket'} remaining. There are <strong>${shipsCount}</strong> enemy ${shipsCount > 1 ? 'ships' : 'ship'} left on the radar.</p>
        <p>Stay sharp and keep up the good work!</p>
      </div>
    `;
  }

  export function attackMessage() {
    return `Time to attack! \nAdjust your aim by selecting the target.`;
  }

  export function launchRocketMessage(x, y) {
    return `Rocket launched to ${x + 1}, ${y + 1}! 🎯`;
  }