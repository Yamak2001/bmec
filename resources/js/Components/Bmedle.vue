<template>
  <div class="bmedle-game">
    <!-- Header -->
    <header class="game-header">
      <h1>Bmedle: Wordle for Biomedical Engineering</h1>
    </header>

    <main class="game-main">
      <!-- Top Bar -->
      <div class="top-bar">
        <button v-if="!hasStarted" @click="startGame">Start Game</button>
        <button v-else-if="attemptData && !attemptData.hint_used" @click="useHint">Hint</button>
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
import axios from 'axios';
import { ref, reactive, onMounted, onUnmounted } from 'vue';

/** Local puzzle settings */
const totalRows = 6;
const totalCols = 5;

/** Puzzle board */
const board = reactive(Array.from({ length: totalRows }, () => Array(totalCols).fill('')));
const boardStatus = reactive(Array(totalRows).fill(null));

/** Virtual keyboard */
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

// State
const currentRow = ref(0);
const currentCol = ref(0);
const attemptData = ref(null);
const hasStarted = ref(false);
const timeElapsed = ref(0);
let timerInterval = null;

onMounted(() => {
  console.log('Bmedle mounted!');
});

onUnmounted(() => {
  stopTimer();
});

/** Start Game => POST /api/bmedle/start */
async function startGame() {
  if (hasStarted.value) return;

  try {
    const response = await axios.post('/api/bmedle/start');
    attemptData.value = response.data.attempt;
    hasStarted.value = true;
    startTimer();
  } catch (error) {
    console.error('Failed to start the game:', error);
  }
}

/** Use Hint => PATCH /api/bmedle/attempts/{id}/hint */
async function useHint() {
  if (!attemptData.value) return;

  try {
    const { id } = attemptData.value;
    const response = await axios.patch(`/api/bmedle/attempts/${id}/hint`);
    attemptData.value = response.data.attempt;
  } catch (error) {
    console.error('Failed to apply hint:', error);
  }
}

/** Submit Guess => POST /api/bmedle/attempts/{id}/guess */
async function submitGuess() {
  const guess = board[currentRow.value].join('');
  if (guess.length !== totalCols) return;

  try {
    const { id } = attemptData.value;
    const response = await axios.post(`/api/bmedle/attempts/${id}/guess`, { guess });
    attemptData.value = response.data.attempt;

    // Move row pointer forward
    if (currentRow.value < totalRows - 1) {
      currentRow.value++;
      currentCol.value = 0;
    } else {
      stopTimer();
    }
  } catch (error) {
    if (error.response?.status === 422) {
      console.error('Not a valid word');
    } else {
      console.error('Failed to submit guess:', error);
    }
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

/** Keyboard input */
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

/** Board coloring (placeholder) */
function cellStatusClass(rowIndex, colIndex) {
  if (boardStatus[rowIndex]) {
    return boardStatus[rowIndex][colIndex] || 'default';
  }
  return 'default';
}

/** Keyboard coloring (placeholder) */
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
  We'll set the default sizes for desktop, 
  then use a media query for smaller screens.
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
  max-width: 800px; /* limit the game width on large screens */
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

/* Board Cell States */
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

/* 
  Responsive Breakpoint: For screens below 640px, 
  further reduce cell sizes for mobile.
*/
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

/* 
  Responsive Breakpoint: 
  For screens up to ~640px, scale down board and keyboard
*/
@media (max-width: 640px) {
  .game-main {
    max-width: 90%; /* reduce horizontal max to fit smaller screens */
  }
  
  .board {
    gap: 6px;
  }
  .board-row {
    gap: 6px;
    /* reduce from 70px to ~50px wide cells on small screens */
    grid-template-columns: repeat(5, 50px);
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
