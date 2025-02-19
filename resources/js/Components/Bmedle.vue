<template>
  <div class="bmedle-game">
    <!-- Header -->
    <header class="game-header">
      <h1>Bmedle: Wordle for Biomedical Engineering</h1>
    </header>

    <main class="game-main">
      <!-- Example top bar with Start Game, Hint, Timer -->
      <div class="top-bar">
        <button v-if="!hasStarted" @click="startGame">Start Game</button>
        <button v-else-if="!attempt.hint_used" @click="useHint">Hint</button>
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
// import { Inertia } from '@inertiajs/inertia';

// Props from controller
const props = defineProps({
  attempt: {
    type: Object,
    required: true,
  },
});
console.log('props.attempt = ', props.attempt);

// Basic puzzle settings
const totalRows = 6;
const totalCols = 5;

// Local state for the puzzle board
const board = reactive(Array.from({ length: totalRows }, () => Array(totalCols).fill('')));
const boardStatus = reactive(Array(totalRows).fill(null));

// Virtual keyboard states
const keyboardStatus = reactive({});
const alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
alphabet.forEach(letter => {
  keyboardStatus[letter] = 'default';
});

// Keyboard layout
const keyboardRows = [
  'QWERTYUIOP'.split(''),
  'ASDFGHJKL'.split(''),
  ['Enter', ...'ZXCVBNM'.split(''), '←'],
];

// Track current row/col
const currentRow = ref(0);
const currentCol = ref(0);

// Timer
const timeElapsed = ref(0);
let timerInterval = null;

// Determine if user has started
// e.g. if attempt has a started_at set
const hasStarted = ref(!!props.attempt.started_at);

/**
 * Start the game: 
 *  - If not started, call server to set started_at in attempt
 *  - Start local timer
 */
function startGame() {
  if (hasStarted.value) return;

  Inertia.post(route('bmedle.store'), {
    // Or if you have a separate route to mark the existing attempt started,
    // you'd do something like:
    // attemptId: props.attempt.id
  }, {
    onSuccess: () => {
      hasStarted.value = true;
      startTimer();
    }
  });
}

/**
 * Use a hint: calls server to mark hint_used = true
 */
function useHint() {
  Inertia.patch(route('bmedle.useHint', props.attempt.id));
}

/**
 * Called after the user has started (or page load if started_at not null).
 * It sets up an interval to increment timeElapsed every second.
 * You can compare the attempt's started_at with the current time if you want more accurate measure.
 */
function startTimer() {
  // If attempt.already has a started_at, you can compute offset
  // For now just do timeElapsed from zero
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

// If the attempt is already started, resume the timer
onMounted(() => {
  if (hasStarted.value) {
    startTimer();
  }
});

onUnmounted(() => {
  stopTimer();
});

/**
 * Handle keyboard input
 */
function handleKey(key) {
  if (!hasStarted.value) {
    // If not started, ignore
    return;
  }

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

  // Process only A-Z
  if (/^[A-Z]$/.test(key)) {
    if (currentCol.value < totalCols) {
      board[currentRow.value][currentCol.value] = key;
      currentCol.value++;
    }
  }
}

/**
 * Submits the guess to the server, which updates the attempt 
 * with next attempt_x field, checks if correct, sets status, etc.
 */
function submitGuess() {
  const guess = board[currentRow.value].join('');
  if (guess.length !== totalCols) return;

  Inertia.put(route('bmedle.update', props.attempt.id), {
    guess: guess,
  }, {
    onSuccess: (page) => {
      // The server might return new props that update attempt status, etc.
      // If the puzzle is solved, or failed, you can stop the timer
      // Or you'd re-fetch the puzzle state so you can color in board cells
      // For simplicity, we do local check. But typically you'd do more logic
      // For now we just move the row pointer forward
      // In a real scenario, you'd update boardStatus based on server response
      if (currentRow.value < totalRows - 1) {
        currentRow.value++;
        currentCol.value = 0;
      } else {
        stopTimer();
      }
    },
    onError: (errors) => {
      // If guess is not in dictionary, server might return 422
      console.log(errors);
      // Show an error message to the user
    }
  });
}

/**
 * For coloring the cell based on boardStatus 
 * (You'd typically get this from the server or do local logic)
 */
function cellStatusClass(rowIndex, colIndex) {
  if (boardStatus[rowIndex]) {
    return boardStatus[rowIndex][colIndex] || 'default';
  }
  return 'default';
}

/**
 * For coloring the keyboard key
 * (You'd typically set this after receiving server response)
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
/* Example color variables. 
   In a real app, put them in a global file or on the :root in a non-scoped style. */
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

/* Main content area */
.game-main {
  flex: 1;
  width: 100%;
  max-width: 800px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.top-bar {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
  align-items: center;
}

/* Board styling */
.board {
  display: grid;
  grid-template-rows: repeat(6, 70px);
  gap: 10px;
  margin-bottom: 20px;
}

.board-row {
  display: grid;
  grid-template-columns: repeat(5, 70px);
  gap: 10px;
}

.board-cell {
  border: 2px solid var(--white);
  background-color: var(--white);
  color: var(--dark-blue);
  font-size: 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Board cell states */
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
  background-color: var(--absent-grey);
  color: var(--white);
}

/* Keyboard styling */
.keyboard {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.keyboard-row {
  display: flex;
  gap: 6px;
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
</style>
