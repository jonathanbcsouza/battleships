# Battleships

Your challenge is to implement this simplified game of Battleships using text input and output.

The computer randomly chooses the location of two single-cell "ships" on a board of 8 by 8 cells.  The user then has 20 guesses to find the two ships.

The user enters a co-ordinate, for example `3,5`, and the computer locates the nearest ship to that co-ordinate and tells them they're "hot" if they're 1 to 2 cells away, "warm" if they're 3 to 4 cells away, or "cold" if they're further away.

As an example, `3,5` is three cells away from `2,7` because (3 - 2) + (7 - 5) = 3, so they'd be told they were "warm".

If the user correctly guesses a ship's location, they're told they've got a hit and that ship is removed from the board.  The game ends when both ships have been hit by the user, or the user has used up their 20 guesses.

Some things to note:
* Write your code in a style that you consider to be production quality. 
* We're more interested in your logical thinking, process and coding style. Show us what you know about writing great software.
* Feel free to use your language of choice. We prefer C#, Java, JavaScript, TypeScript, or Python.
* Please include guidance on how to install and execute your solution.
* Please create a merge request when you are done.

---

## Prerequisites

- PHP and MySQL installed on your system.
- Node.js and npm for running the test.

## Setup and Execution

1. Clone the repository to your local machine.
2. Run `npm install` to install the dependencies.
3. Start a local PHP server using `php -S localhost:8000`.
4. Open your browser and navigate to `http://localhost:8000` to play the game.

## Running Tests

Execute `npm test` to run the test suite.