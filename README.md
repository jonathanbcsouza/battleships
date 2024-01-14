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
### Solution
#### Prerequisites

- PHP and MySQL installed on your system.
- Composer for managing PHP dependencies.
- Node.js and npm for the javascript test.

#### Setup and Execution

1. Clone the repository to your local machine.
2. Install Composer if you haven't already. You can download it from [here](https://getcomposer.org/download/). After downloading, you can install it globally on your system by following the instructions [here](https://getcomposer.org/doc/00-intro.md#globally).
3. Run `composer install` to install the PHP dependencies.
4. Run `npm install` to install the javascript dependencies.
5. Start a local PHP server using `php -S localhost:8000`.
6. Open your browser and navigate to `http://localhost:8000` to play the game.

#### Running Javascript Tests
Execute `npm test` to run the test suite.

#### Verifying Installed Libraries

You can verify the installed PHP libraries using Composer:

```bash
composer show
```

This will list all the installed PHP packages along with their versions.

#### Planned Enhancements

- ~~Fix bug on the first screen. The trophies counter should update once the user is changed.~~ ✅ - Login page using query strings created.
- ~~Redesign logic and convert functions for handling `buildGrid()` and `placeShips()` with `PHP`~~. ✅ Grid class created.
- Replace javascript constants.
- Replace js tests with PHP.
- Declare data types.
- Replace javascript prompts with modals.
- Use docker.