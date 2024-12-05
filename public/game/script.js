function StackerGame() {
  // Game constants
  this.BOARD_WIDTH = 7;
  this.BOARD_HEIGHT = 15;
  this.LIMIT_3 = 2;
  this.LIMIT_2 = 7;
  this.MIN_SPEED = 6 / 64;
  this.MAX_SPEED = 2 / 64;
  this.ANIMATION_TIME = 1.5 * 60;

  // Initialize members
  this.gameElement = document.getElementById("stacker-game");
  this.gameBoard = null;

  //  Music
  this.music = document.getElementById("retro-music");
  this.failSFX = document.getElementById("fail-sfx");
  this.placeSFX = document.getElementById("place-sfx");
  this.gameoverSFX = document.getElementById("gameover-sfx");
  this.congratulationSFX = document.getElementById("congratulation-sfx");

  // Initialize board state
  this.board = new Array(this.BOARD_HEIGHT);
  for (i = 0; i < this.BOARD_HEIGHT; i++) {
    this.board[i] = new Array(this.BOARD_WIDTH);
    for (j = 0; j < this.BOARD_WIDTH; j++) {
      this.board[i][j] = 0;
    }
  }

  // Game state variables
  this.blocks = 3;
  this.running = false;
  this.level = 0;
  this.pos = Math.floor(this.BOARD_WIDTH / 2) - Math.floor(this.blocks / 2);
  this.left = true;
  this.timer = 0;
  this.atimer = 0;

  // Build HTML elements
  this.buildHTML = function () {
    // build table
    var domTable = document.createElement("table");
    for (i = 0; i < this.BOARD_HEIGHT; i++) {
      var domTableRow = domTable.insertRow(i);
      for (j = 0; j < this.BOARD_WIDTH; j++) {
        domTableRow.insertCell(j);
      }
    }

    // Add table to HTML
    domTable.classList.add("stacker-board");
    this.gameBoard = domTable;
    this.gameElement.appendChild(this.gameBoard);
  };

  // Starts the game running
  this.run = function () {
    this.music.volume = 0.5;
    this.music.muted = false;

    this.failSFX.volume = 1.0;
    this.failSFX.muted = false;

    this.placeSFX.volume = 1.0;
    this.placeSFX.muted = false;

    this.gameoverSFX.volume = 1.0;
    this.gameoverSFX.muted = false;

    this.congratulationSFX.volume = 1.0;
    this.congratulationSFX.muted = false;

    setInterval(function () {
      game.onStep();
    }, 1000 / 60);
    window.addEventListener("keydown", function (e) {
      game.onKeyPress(e);
    });
    document.body.addEventListener("touchstart", function (e) {
      game.onTouchStart(e);
    });
  };

  // Handles each step event
  this.onStep = function () {
    if (this.atimer > 0) {
      this.placeSFX.pause();
      this.music.pause(); 
      this.failSFX.play();
      this.atimer--;
    }

    if (this.atimer == 0) {
      // Remove temporary (flashing) blocks
      for (i = 0; i < this.BOARD_HEIGHT; i++) {
        for (j = 0; j < this.BOARD_WIDTH; j++) {
          if (this.board[i][j] == 2) {
            this.board[i][j] = 0;
          }
        }
      }

      if (this.blocks == 0) {
        this.running = false;
      }

      if (this.running == true) {
        this.music.play();
      }
    }

    // Move blocks over
    if (this.running && this.atimer == 0) {
      if (this.timer <= 0) {
        if (this.left) {
          this.pos--;
          if (this.pos + this.blocks - 1 == 0) {
            this.left = false;
          }
        } else {
          this.pos++;
          if (this.pos == this.BOARD_WIDTH - 1) {
            this.left = true;
          }
        }

        // ***** SPEED CONTROL ***** //
        // FAST
        this.timer =
          (this.MAX_SPEED +
            (this.MIN_SPEED - this.MAX_SPEED) *
              Math.pow(1 - this.level / this.BOARD_HEIGHT, 2.2369)) *
          60;

        // // NORMAL
        // this.timer =
        //   (this.MAX_SPEED +
        //     (this.MIN_SPEED - this.MAX_SPEED) *
        //       (1 - this.level / this.BOARD_HEIGHT)) *
        //   60;

        // SLOW
        // this.timer = this.MIN_SPEED * 60;


      } else {
        this.timer--;
      }
    }

    // Redraw grid
    for (i = 0; i < this.BOARD_HEIGHT; i++) {
      for (j = 0; j < this.BOARD_WIDTH; j++) {
        switch (this.board[i][j]) {
          case 0:
            this.gameBoard.rows[this.BOARD_HEIGHT - 1 - i].cells[j].className =
              "";
            break;
          case 1:
            this.gameBoard.rows[this.BOARD_HEIGHT - 1 - i].cells[j].className =
              "filled";
            break;
          case 2:
            this.gameBoard.rows[this.BOARD_HEIGHT - 1 - i].cells[j].className =
              this.atimer > 0 && this.atimer % 30 < 15 ? "filled" : "";
            break;
        }
      }
    }
    // Draw bouncing blocks
    if (this.running && this.atimer == 0) {
      for (j = this.pos; j < this.pos + this.blocks; j++) {
        if (j >= 0 && j < this.BOARD_WIDTH) {
          this.gameBoard.rows[this.BOARD_HEIGHT - 1 - this.level].cells[
            j
          ].className = "filled";
        }
      }
    }
  };

  // Handles keyboard press events
  this.onKeyPress = function (e) {
    var e = e || window.event;

    switch (e.keyCode) {
      case 32: // Space
        this.onSpacePress();
        e.preventDefault();
        break;

      case 13: // Enter/return
        this.onEnterPress();
        e.preventDefault();
        break;
    }
  };

  // Handles touch screen device touch
  this.onTouchStart = function (e) {
    if (this.running) {
      this.onSpacePress();
    } else {
      this.onEnterPress();
    }
  };

  // Handles spacebar presses
  this.onSpacePress = function () {
    this.hideGameOver();
    this.hideCongratulation();
    this.music.play();
    if (this.running == true) {
      this.placeSFX.currentTime = 0;
      this.placeSFX.play();
      this.hideInstruction();
    }

    this.hideGameOver();
    this.hideCongratulation();

    if (!this.running) {
      this.onEnterPress();
    } else if (this.atimer == 0) {
      // put blocks onto board
      var iEnd = this.pos + this.blocks;
      for (i = this.pos; i < iEnd; i++) {
        if (i >= 0 && i < this.BOARD_WIDTH) {
          this.board[this.level][i] = 1;
        } else {
          this.blocks--;
        }
      }

      // Remove invalid blocks
      if (this.level > 0) {
        for (i = 0; i < this.BOARD_WIDTH; i++) {
          if (
            this.board[this.level][i] == 1 &&
            this.board[this.level - 1][i] == 0
          ) {
            this.board[this.level][i] = 2;
            this.blocks--;
            this.atimer = this.ANIMATION_TIME;
          }
        }
      }

      // Check hard limits
      if (this.blocks == 0) {
        if (this.atimer > 0) {
          setTimeout(() => {
            if (this.blocks == 0 || this.level == this.BOARD_HEIGHT - 1) {
              this.showGameOver();
              this.running = false;
            }
          }, this.ANIMATION_TIME * (1000 / 60)); // Convert frames to milliseconds
        } else {
          this.showGameOver();
          this.running = false;
        }
      }

      if (this.level == this.BOARD_HEIGHT - 1 && this.blocks != 0) {
        this.showCongratulation();
        this.running = false;
      }

      this.level++;
      this.pos = Math.floor(this.BOARD_WIDTH / 2);
    }
  };

  // Handles enter/return presses
  this.onEnterPress = function () {
    this.music.currentTime = 0;
    this.hideCongratulation();
    this.hideGameOver();
    // Initialize board
    for (i = 0; i < this.BOARD_HEIGHT; i++) {
      for (j = 0; j < this.BOARD_WIDTH; j++) {
        this.board[i][j] = 0;
      }
    }

    // Reset everything else
    this.level = 0;
    this.blocks = 3;
    this.pos = Math.floor(this.BOARD_WIDTH / 2) - Math.floor(this.blocks / 2);
    this.left = true;
    this.running = true;
    this.atimer = 0;
  };


  // Modal controls
  this.showCongratulation = function () {
    this.music.pause();
    this.congratulationSFX.play();
    document.getElementById("congratulation-overlay").style.display = "flex";
  };

  this.hideCongratulation = function () {
    document.getElementById("congratulation-overlay").style.display = "None";
  };

  this.showGameOver = function () {
    this.music.pause();
    this.gameoverSFX.play();
    document.getElementById("game-over-overlay").style.display = "flex";
  };

  this.hideGameOver = function () {
    document.getElementById("game-over-overlay").style.display = "None";
  };

  this.hideInstruction = function () {
    document.getElementById("instruction-overlay").style.display = "None";
  };

  this.showInstruction = function () {
    document.getElementById("instruction-overlay").style.display = "flex";
  };

  document
    .getElementById("try-again-button")
    .addEventListener("click", function () {
      document.getElementById("game-over-overlay").style.display = "none";
      game.onEnterPress();
    });

  document
    .getElementById("continue-button")
    .addEventListener("click", function () {
      document.getElementById("congratulation-overlay").style.display = "none";
      game.onEnterPress();
    });

  document
    .getElementById("instruction-overlay")
    .addEventListener("click", function () {
      document.getElementById("instruction-overlay").style.display = "none";
      game.onSpacePress();
    });
}

// Main
game = null;

window.addEventListener("DOMContentLoaded", function () {
  game = new StackerGame();
  game.buildHTML();
  game.run();
  game.onSpacePress();
  game.onSpacePress();
  game.showInstruction();
});
