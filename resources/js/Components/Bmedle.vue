<template>
  <div class="bmedle-game">
    <!-- Header -->
    <header class="game-header">
      <h1>Bmedle: Wordle for Biomedical Engineering</h1>
    </header>

    <main class="game-main">
      <!-- Top Bar -->
      <div class="top-bar">
        <!-- When game hasn't started, show Start Game button -->
        <button v-if="!hasStarted" @click="startGame">Start Game</button>
        <!-- Once started, if hint hasn't been used, show Hint button -->
        <button v-else-if="attemptData && !attemptData.hint_used" @click="useHint">Hint</button>
        <!-- Timer display -->
        <p v-if="hasStarted">
          Timer: {{ timeElapsed }} seconds
        </p>
      </div>

      <!-- Game Board -->
      <div class="board">
        <div class="board-row" v-for="(row, rowIndex) in board" :key="rowIndex">
          <div
            class="board-cell"
            v-for="(cell, colIndex) in row"
            :key="colIndex"
            :class="cellStatusClass(rowIndex, colIndex)"
          >
            {{ cell }}
          </div>
        </div>
      </div>

      <!-- Virtual Keyboard -->
      <div class="keyboard">
        <div class="keyboard-row" v-for="(row, rowIndex) in keyboardRows" :key="rowIndex">
          <button
            class="keyboard-key"
            v-for="key in row"
            :key="key"
            :class="keyboardKeyClass(key)"
            @click="handleKey(key)"
          >
            {{ key }}
          </button>
        </div>
      </div>
    </main>

    <footer class="game-footer">
      <p>&copy; {{ new Date().getFullYear() }} Bmedle</p>
    </footer>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue';
// For local testing, we simulate the backend; remove axios usage for now.
// Later, if using Inertia shared props, you can import { usePage } from '@inertiajs/vue3'.

/**
 * For local testing we use a constant target word "FETUS".
 * In production, you'll likely fetch this from your backend.
 */
const targetWord = "FETUS";

// For local testing, we simulate a user:
const userId = 1;
const userName = "Test User";

/** Local puzzle settings */
const totalRows = 6;
const totalCols = 5;

/** Puzzle board state: a 2D array of empty strings */
const board = reactive(Array.from({ length: totalRows }, () => Array(totalCols).fill('')));

/** Board evaluation status: an array where each row contains statuses for each cell. */
const boardStatus = reactive(Array(totalRows).fill(null));

/** Virtual keyboard state */
const keyboardStatus = reactive({});
const alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
alphabet.forEach(letter => {
  keyboardStatus[letter] = 'default';
});
const keyboardRows = [
  'QWERTYUIOP'.split(''),
  'ASDFGHJKL'.split(''),
  ['Enter', ...'ZXCVBNM'.split(''), '←']
];

// Track current row and column pointers
const currentRow = ref(0);
const currentCol = ref(0);

// Local attempt data (simulated) and game state flags
const attemptData = ref(null);
const hasStarted = ref(false);
const timeElapsed = ref(0);
let timerInterval = null;

onMounted(() => {
  console.log('Bmedle mounted! (local mode)');
});

onUnmounted(() => {
  stopTimer();
});

/**
 * Evaluates the guess against the target word.
 * Returns an array of statuses: "correct", "present", or "absent" for each letter.
 */
function evaluateGuess(guess, target) {
  const guessArr = guess.split('');
  const targetArr = target.split('');
  const result = Array(totalCols).fill('absent');

  // First pass: mark letters that are correct (right letter, right position)
  for (let i = 0; i < totalCols; i++) {
    if (guessArr[i] === targetArr[i]) {
      result[i] = 'correct';
      targetArr[i] = null; // remove letter from further consideration
    }
  }

  // Second pass: mark letters that are present (right letter, wrong position)
  for (let i = 0; i < totalCols; i++) {
    if (result[i] !== 'correct' && targetArr.includes(guessArr[i])) {
      result[i] = 'present';
      const idx = targetArr.indexOf(guessArr[i]);
      targetArr[idx] = null;
    }
  }

  return result;
}

/**
 * Start the game locally.
 * This simulates starting an attempt by setting a local attempt with the target term "FETUS".
 * (Later replace this with an API call if needed.)
 */
function startGame() {
  if (hasStarted.value) return;
  
  // Simulate API response for starting an attempt
  attemptData.value = {
    id: 1,
    bmec_term: targetWord,
    hint_used: false,
    attempts_used: 0,
    started_at: new Date().toISOString()
  };
  hasStarted.value = true;
  startTimer();
  console.log("Game started locally:", attemptData.value);
}

/**
 * Simulate using a hint locally.
 * Simply marks the hint as used.
 * (Later replace with an API call.)
 */
function useHint() {
  if (!attemptData.value) return;
  attemptData.value.hint_used = true;
  console.log("Hint applied locally.");
}

/**
 * Simulate submitting a guess locally.
 * Evaluates the guess against the target word and updates the board and keyboard.
 */
function submitGuess() {
  const guess = board[currentRow.value].join('');
  if (guess.length !== totalCols) return;
  
  // Evaluate the guess using the Wordle algorithm
  const evaluation = evaluateGuess(guess, targetWord);
  boardStatus[currentRow.value] = evaluation; // Store evaluation for the current row
  
  // Update virtual keyboard statuses based on the evaluation:
  for (let i = 0; i < totalCols; i++) {
    const letter = guess[i];
    const status = evaluation[i];
    if (status === 'correct') {
      keyboardStatus[letter] = 'correct';
    } else if (status === 'present' && keyboardStatus[letter] !== 'correct') {
      keyboardStatus[letter] = 'present';
    } else if (status === 'absent' && keyboardStatus[letter] === 'default') {
      keyboardStatus[letter] = 'absent';
    }
  }
  
  // Increment attempts used in the simulated attempt data
  const nextAttemptNumber = (attemptData.value.attempts_used || 0) + 1;
  attemptData.value.attempts_used = nextAttemptNumber;
  
  // Check for correct guess or failure after max attempts
  if (guess === targetWord) {
    attemptData.value.attempt_status = 'solved_on_' + nextAttemptNumber;
    attemptData.value.completed_at = new Date().toISOString();
    stopTimer();
    console.log("Correct guess! Attempt solved.");
  } else if (nextAttemptNumber === 5) {
    attemptData.value.attempt_status = 'failed';
    attemptData.value.completed_at = new Date().toISOString();
    stopTimer();
    console.log("Game failed. Maximum attempts reached.");
  } else {
    console.log(`Guess submitted: ${guess} (Attempt ${nextAttemptNumber})`);
  }
  
  // Move row pointer forward if available
  if (currentRow.value < totalRows - 1) {
    currentRow.value++;
    currentCol.value = 0;
  }
}

/** Timer controls */
function startTimer() {
  timeElapsed.value = 0;
  timerInterval = setInterval(() => {
    timeElapsed.value += 1;
  }, 1000);
}

function stopTimer() {
  if (timerInterval) {
    clearInterval(timerInterval);
    timerInterval = null;
  }
}

/**
 * Handle keyboard input.
 * Processes key presses for building the guess.
 */
function handleKey(key) {
  if (!hasStarted.value || !attemptData.value) return;
  
  if (key === 'Enter') {
    if (currentCol.value === totalCols) {
      submitGuess();
    }
    return;
  }
  if (key === '←') {
    if (currentCol.value > 0) {
      currentCol.value--;
      board[currentRow.value][currentCol.value] = '';
    }
    return;
  }
  if (/^[A-Z]$/.test(key)) {
    if (currentCol.value < totalCols) {
      board[currentRow.value][currentCol.value] = key;
      currentCol.value++;
    }
  }
}

/**
 * Board cell coloring function.
 * Returns the CSS class for the cell based on the evaluation status.
 */
function cellStatusClass(rowIndex, colIndex) {
  if (boardStatus[rowIndex]) {
    return boardStatus[rowIndex][colIndex] || 'default';
  }
  return 'default';
}

/**
 * Keyboard key coloring function.
 * Returns the CSS class for the key based on its status.
 */
function keyboardKeyClass(key) {
  const status = keyboardStatus[key];
  if (status === 'correct') return 'key-correct';
  if (status === 'present') return 'key-present';
  if (status === 'absent') return 'key-absent';
  return 'key-default';
}
</script>

<style scoped>
/* 
  Desktop Defaults 
  (Ensure these CSS variables are defined globally, e.g., in a :root selector)
*/

/* Container */
.bmedle-game {
  background-color: var(--dark-blue);
  color: var(--white);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  font-family: Arial, sans-serif;
  padding: 20px;
}

/* Header */
.game-header {
  padding: 20px;
  text-align: center;
}
.game-header h1 {
  margin: 0;
  font-size: 2rem;
}

/* Main Content Area */
.game-main {
  flex: 1;
  width: 100%;
  max-width: 800px; /* Limit the game width on large screens */
  display: flex;
  flex-direction: column;
  align-items: center;
}

/* Top Bar */
.top-bar {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
  align-items: center;
}

/* Board (Desktop Defaults) */
.board {
  display: grid;
  grid-template-rows: repeat(6, 60px); /* decreased from 70px to 60px */
  gap: 8px; /* slightly smaller gap */
  margin-bottom: 20px;
}
.board-row {
  display: grid;
  grid-template-columns: repeat(5, 60px); /* decreased from 70px to 60px */
  gap: 8px;
}
.board-cell {
  border: 2px solid var(--white);
  background-color: var(--white);
  color: var(--dark-blue);
  font-size: 1.8rem; /* slightly smaller than 2rem */
  display: flex;
  align-items: center;
  justify-content: center;
}
.board-cell.default {
  background-color: var(--white);
}
.board-cell.correct {
  background-color: var(--correct-green);
  color: var(--white);
}
.board-cell.present {
  background-color: var(--present-yellow);
  color: var(--white);
}
.board-cell.absent {
  background-color: var(--key-absent-bg);
  color: var(--white);
}

/* Responsive Breakpoint: For screens below 640px */
@media (max-width: 640px) {
  .board {
    grid-template-rows: repeat(6, 40px); /* shrinks to 40px squares */
    gap: 6px;
  }
  .board-row {
    grid-template-columns: repeat(5, 40px);
    gap: 6px;
  }
  .board-cell {
    font-size: 1.4rem; /* smaller text on mobile */
  }
}

/* Keyboard */
.keyboard {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.keyboard-row {
  display: flex;
  gap: 8px; /* a bit more space for desktop */
  justify-content: center;
}
.keyboard-key {
  padding: 10px 15px;
  border: none;
  background-color: var(--key-default-bg);
  color: var(--white);
  font-size: 1rem;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.2s ease;
}
.keyboard-key.key-correct {
  background-color: var(--correct-green);
}
.keyboard-key.key-present {
  background-color: var(--present-yellow);
}
.keyboard-key.key-absent {
  background-color: var(--key-absent-bg);
}
.keyboard-key:hover {
  opacity: 0.9;
}

/* Footer */
.game-footer {
  text-align: center;
  padding: 20px;
  font-size: 0.9rem;
}

/* Responsive Breakpoint: For screens up to ~640px */
@media (max-width: 640px) {
  .game-main {
    max-width: 90%;
  }
  .board {
    gap: 6px;
  }
  .board-row {
    gap: 6px;
    grid-template-columns: repeat(5, 50px); /* reduce cell size on small screens */
  }
  .board-cell {
    font-size: 1.5rem;
  }
  .keyboard-row {
    gap: 5px;
  }
  .keyboard-key {
    padding: 8px 10px;
    font-size: 0.9rem;
  }
}
</style>
