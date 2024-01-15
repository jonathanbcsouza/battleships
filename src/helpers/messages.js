export function displayWelcomeMessage(username, numShips, numRockets) {
    return `
      <h3>Welcome aboard, Captain <strong>${username}</strong>!</h3>
      <p>You are now in the midst of a naval conflict. Your mission, should you choose to accept it, is to sink the remaining <strong>${numShips}</strong> enemy ${numShips > 1 ? 'ships' : 'ship'}.</p>
      <p>You have <strong>${numRockets}</strong> ${numRockets > 1 ? 'rockets' : 'rocket'} at your disposal. Use them wisely and secure our victory!</p>
    `;
  }
  
export function displayYouWinMessage() {
    return `
      <p>Congratulations, Captain! You've successfully defeated the enemy.</p>
      <p>The high seas are safe once again. 🎉</p>
    `;
  }
  
export function displayGameOverMessage() {
    return `
      <p>The battle is lost, Captain. But this is not the end.</p>
      <p>We will regroup and fight again! 💪</p>
    `;
  }
  
export function displayContinueMessage(username, rocketsCount, shipsCount) {
    return `
      <div>
        <p>Command Center to <strong>${username}</strong>. 🎙️ Do you copy?</p>
        <p>You have <strong>${rocketsCount}</strong> ${rocketsCount > 1 ? 'rockets' : 'rocket'} remaining. There are <strong>${shipsCount}</strong> enemy ${shipsCount > 1 ? 'ships' : 'ship'} left on the radar.</p>
        <p>Stay sharp and keep up the good work!</p>
      </div>
    `;
  }
