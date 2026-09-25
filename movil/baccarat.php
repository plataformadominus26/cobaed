<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baccarat Pro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        :root {
            --casino-red: #d32f2f;
            --casino-blue: #1565c0;
            --casino-white: #ffffff;
            --casino-dark: #1a1a2e;
            --casino-gold: #ffd700;
        }
        
        body {
            background: linear-gradient(135deg, var(--casino-dark), #0f3460);
            color: var(--casino-white);
            min-height: 100vh;
            padding: 60px 0 70px 0;
            overflow-x: hidden;
        }
        
        /* Header Styles */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: linear-gradient(to right, var(--casino-red), var(--casino-blue));
            padding: 12px 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
            z-index: 1000;
            border-bottom: 3px solid var(--casino-white);
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo {
            width: 40px;
            height: 40px;
            background: var(--casino-white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: var(--casino-red);
            font-size: 20px;
            border: 2px solid var(--casino-gold);
        }
        
        .app-name {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--casino-white);
            text-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
        }
        
        /* Main Content */
        .container {
            max-width: 100%;
            padding: 15px;
        }
        
        .game-area {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }
        
        .selection-display {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .player-side, .banker-side {
            flex: 1;
            text-align: center;
            padding: 10px;
        }
        
        .player-side {
            background: rgba(21, 101, 192, 0.2);
            border-radius: 12px;
            border: 2px solid var(--casino-blue);
            margin-right: 5px;
        }
        
        .banker-side {
            background: rgba(211, 47, 47, 0.2);
            border-radius: 12px;
            border: 2px solid var(--casino-red);
            margin-left: 5px;
        }
        
        .side-title {
            font-size: 1.1rem;
            margin-bottom: 10px;
            font-weight: bold;
        }
        
        .player-side .side-title {
            color: var(--casino-blue);
        }
        
        .banker-side .side-title {
            color: var(--casino-red);
        }
        
        .slots {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 10px;
        }
        
        .slot {
            width: 50px;
            height: 70px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            background: var(--casino-white);
            color: #333;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        
        .player-slot {
            border: 2px solid var(--casino-blue);
        }
        
        .banker-slot {
            border: 2px solid var(--casino-red);
        }
        
        .player-slot.active {
            background: var(--casino-blue);
            color: white;
            box-shadow: 0 0 10px var(--casino-blue);
        }
        
        .banker-slot.active {
            background: var(--casino-red);
            color: white;
            box-shadow: 0 0 10px var(--casino-red);
        }
        
        .tie-slot.active {
            background: var(--casino-gold);
            color: #333;
            box-shadow: 0 0 10px var(--casino-gold);
        }
        
        .randomizer {
            text-align: center;
            margin: 20px 0;
        }
        
        .random-btn {
            background: linear-gradient(135deg, var(--casino-white), #f5f5f5);
            color: var(--casino-dark);
            border: none;
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 6px 0 #cccccc, 0 10px 20px rgba(0, 0, 0, 0.3);
            transition: all 0.2s;
            width: 100%;
            max-width: 280px;
        }
        
        .random-btn:active {
            transform: translateY(4px);
            box-shadow: 0 2px 0 #cccccc, 0 6px 10px rgba(0, 0, 0, 0.3);
        }
        
        .result-display {
            font-size: 1.5rem;
            margin: 15px 0;
            min-height: 40px;
            font-weight: bold;
            text-align: center;
        }
        
        .player-result {
            color: var(--casino-blue);
            text-shadow: 0 0 10px rgba(21, 101, 192, 0.5);
        }
        
        .banker-result {
            color: var(--casino-red);
            text-shadow: 0 0 10px rgba(211, 47, 47, 0.5);
        }
        
        .tie-result {
            color: var(--casino-gold);
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
        }
        
        /* Outcome Selection */
        .outcome-selection {
            margin-top: 20px;
        }
        
        .outcome-title {
            text-align: center;
            margin-bottom: 10px;
            font-size: 1.1rem;
            color: var(--casino-white);
        }
        
        .outcome-options {
            display: flex;
            justify-content: space-around;
            margin-bottom: 15px;
        }
        
        .outcome-option {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px 15px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
            width: 30%;
        }
        
        .outcome-option.selected {
            transform: scale(1.05);
        }
        
        .player-option.selected {
            border-color: var(--casino-blue);
            background: rgba(21, 101, 192, 0.2);
        }
        
        .banker-option.selected {
            border-color: var(--casino-red);
            background: rgba(211, 47, 47, 0.2);
        }
        
        .tie-option.selected {
            border-color: var(--casino-gold);
            background: rgba(255, 215, 0, 0.2);
        }
        
        .option-icon {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }
        
        .player-option .option-icon {
            color: var(--casino-blue);
        }
        
        .banker-option .option-icon {
            color: var(--casino-red);
        }
        
        .tie-option .option-icon {
            color: var(--casino-gold);
        }
        
        .submit-btn {
            background: linear-gradient(135deg, var(--casino-blue), var(--casino-red));
            color: white;
            border: none;
            padding: 12px 0;
            font-size: 1.1rem;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 4px 0 rgba(0, 0, 0, 0.3);
            transition: all 0.2s;
            width: 100%;
        }
        
        .submit-btn:active {
            transform: translateY(4px);
            box-shadow: 0 0 0 rgba(0, 0, 0, 0.3);
        }
        
        .submit-btn:disabled {
            background: #666;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        /* History Section */
        .history-section {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }
        
        .section-title {
            font-size: 1.2rem;
            margin-bottom: 15px;
            color: var(--casino-white);
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 10px;
        }
        
        .history-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .stat-card {
            background: rgba(26, 26, 46, 0.7);
            border-radius: 10px;
            padding: 12px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 5px 0;
        }
        
        .player-stat {
            color: var(--casino-blue);
        }
        
        .banker-stat {
            color: var(--casino-red);
        }
        
        .tie-stat {
            color: var(--casino-gold);
        }
        
        .history-list {
            max-height: 200px;
            overflow-y: auto;
            background: rgba(26, 26, 46, 0.7);
            border-radius: 10px;
            padding: 10px;
        }
        
        .history-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .history-item:last-child {
            border-bottom: none;
        }
        
        .history-pick {
            font-weight: bold;
        }
        
        .history-player {
            color: var(--casino-blue);
        }
        
        .history-banker {
            color: var(--casino-red);
        }
        
        .history-tie {
            color: var(--casino-gold);
        }
        
        /* Footer Styles */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: linear-gradient(to right, var(--casino-red), var(--casino-blue));
            padding: 10px 15px;
            display: flex;
            justify-content: space-around;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.5);
            z-index: 1000;
            border-top: 3px solid var(--casino-white);
        }
        
        .footer-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            background: none;
            border: none;
            color: var(--casino-white);
            cursor: pointer;
            transition: all 0.3s;
            padding: 5px 10px;
            border-radius: 8px;
        }
        
        .footer-btn:hover, .footer-btn.active {
            color: var(--casino-gold);
            background: rgba(255, 255, 255, 0.1);
        }
        
        .footer-icon {
            font-size: 1.2rem;
        }
        
        .footer-text {
            font-size: 0.7rem;
        }
        
        /* Animation */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .pulse {
            animation: pulse 0.5s ease-in-out;
        }
        
        /* Custom scrollbar */
        .history-list::-webkit-scrollbar {
            width: 6px;
        }
        
        .history-list::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
        
        .history-list::-webkit-scrollbar-thumb {
            background: var(--casino-gold);
            border-radius: 10px;
        }
        
        /* Mobile optimizations */
        @media (max-width: 380px) {
            .slots {
                gap: 5px;
            }
            
            .slot {
                width: 45px;
                height: 65px;
            }
            
            .outcome-option {
                padding: 8px 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="logo-container">
            <div class="logo">BP</div>
            <div class="app-name">Baccarat Pro</div>
        </div>
    </header>
    
    <!-- Main Content -->
    <div class="container">
        <!-- Game Area -->
        <div class="game-area">
            <div class="selection-display">
                <div class="player-side">
                    <div class="side-title">PLAYER</div>
                    <div class="slots">
                        <div class="slot player-slot" id="playerSlot1">-</div>
                        <div class="slot player-slot" id="playerSlot2">-</div>
                        <div class="slot player-slot" id="playerSlot3">-</div>
                    </div>
                </div>
                
                <div class="banker-side">
                    <div class="side-title">BANKER</div>
                    <div class="slots">
                        <div class="slot banker-slot" id="bankerSlot1">-</div>
                        <div class="slot banker-slot" id="bankerSlot2">-</div>
                        <div class="slot banker-slot" id="bankerSlot3">-</div>
                    </div>
                </div>
            </div>
            
            <div class="randomizer">
                <button class="random-btn" id="randomBtn">
                    <i class="fas fa-random"></i> PICK RANDOM
                </button>
                <div class="result-display" id="resultDisplay"></div>
            </div>
            
            <div class="outcome-selection">
                <div class="outcome-title">Select Actual Outcome</div>
                <div class="outcome-options">
                    <div class="outcome-option player-option" data-value="player">
                        <i class="fas fa-user option-icon"></i>
                        <span>Player</span>
                    </div>
                    <div class="outcome-option banker-option" data-value="banker">
                        <i class="fas fa-landmark option-icon"></i>
                        <span>Banker</span>
                    </div>
                    <div class="outcome-option tie-option" data-value="tie">
                        <i class="fas fa-equals option-icon"></i>
                        <span>Tie</span>
                    </div>
                </div>
                <button class="submit-btn" id="submitOutcome" disabled>Submit Outcome</button>
            </div>
        </div>
        
        <!-- History Section -->
        <div class="history-section">
            <div class="section-title">Game History</div>
            
            <div class="history-stats">
                <div class="stat-card">
                    <div>Player Wins</div>
                    <div class="stat-value player-stat" id="playerWins">0</div>
                    <div id="playerPercentage">0%</div>
                </div>
                
                <div class="stat-card">
                    <div>Banker Wins</div>
                    <div class="stat-value banker-stat" id="bankerWins">0</div>
                    <div id="bankerPercentage">0%</div>
                </div>
                
                <div class="stat-card">
                    <div>Ties</div>
                    <div class="stat-value tie-stat" id="tieWins">0</div>
                    <div id="tiePercentage">0%</div>
                </div>
            </div>
            
            <div class="history-list" id="historyList">
                <div class="history-item">
                    <span>No games recorded yet</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="footer">
        <button class="footer-btn active">
            <i class="fas fa-dice footer-icon"></i>
            <span class="footer-text">Game</span>
        </button>
        
        <button class="footer-btn">
            <i class="fas fa-history footer-icon"></i>
            <span class="footer-text">History</span>
        </button>
        
        <button class="footer-btn">
            <i class="fas fa-lightbulb footer-icon"></i>
            <span class="footer-text">Advice</span>
        </button>
        
        <button class="footer-btn">
            <i class="fas fa-sign-out-alt footer-icon"></i>
            <span class="footer-text">Logout</span>
        </button>
    </footer>

    <script>
        // Game state
        const gameState = {
            playerResults: ['-', '-', '-'],
            bankerResults: ['-', '-', '-'],
            history: [],
            currentPick: null,
            selectedOutcome: null,
            playerWins: 0,
            bankerWins: 0,
            tieWins: 0
        };
        
        // DOM Elements
        const playerSlots = [
            document.getElementById('playerSlot1'),
            document.getElementById('playerSlot2'),
            document.getElementById('playerSlot3')
        ];
        
        const bankerSlots = [
            document.getElementById('bankerSlot1'),
            document.getElementById('bankerSlot2'),
            document.getElementById('bankerSlot3')
        ];
        
        const resultDisplay = document.getElementById('resultDisplay');
        const randomBtn = document.getElementById('randomBtn');
        const submitOutcomeBtn = document.getElementById('submitOutcome');
        const outcomeOptions = document.querySelectorAll('.outcome-option');
        const historyList = document.getElementById('historyList');
        const playerWinsEl = document.getElementById('playerWins');
        const bankerWinsEl = document.getElementById('bankerWins');
        const tieWinsEl = document.getElementById('tieWins');
        const playerPercentageEl = document.getElementById('playerPercentage');
        const bankerPercentageEl = document.getElementById('bankerPercentage');
        const tiePercentageEl = document.getElementById('tiePercentage');
        
        // Initialize the app
        function init() {
            randomBtn.addEventListener('click', pickRandom);
            submitOutcomeBtn.addEventListener('click', submitOutcome);
            
            // Add event listeners to outcome options
            outcomeOptions.forEach(option => {
                option.addEventListener('click', () => {
                    // Remove selected class from all options
                    outcomeOptions.forEach(opt => opt.classList.remove('selected'));
                    
                    // Add selected class to clicked option
                    option.classList.add('selected');
                    
                    // Enable submit button
                    submitOutcomeBtn.disabled = false;
                    
                    // Store selected outcome
                    gameState.selectedOutcome = option.getAttribute('data-value');
                });
            });
            
            // Load any existing data from localStorage
            loadFromStorage();
            updateUI();
        }
        
        // Pick random result
        function pickRandom() {
            // Disable button during animation
            randomBtn.disabled = true;
            
            // Clear previous result
            resultDisplay.textContent = '';
            resultDisplay.className = 'result-display';
            
            // Animation sequence
            let count = 0;
            const maxCount = 15;
            const interval = setInterval(() => {
                const tempResult = Math.random() < 0.5 ? 'PLAYER' : 'BANKER';
                
                resultDisplay.textContent = tempResult;
                resultDisplay.className = `result-display ${tempResult.toLowerCase()}-result`;
                
                count++;
                if (count >= maxCount) {
                    clearInterval(interval);
                    
                    // Final result (based on Baccarat probabilities)
                    const rand = Math.random();
                    let result;
                    if (rand < 0.446) {
                        result = 'PLAYER';
                    } else if (rand < 0.892) {
                        result = 'BANKER';
                    } else {
                        result = 'TIE';
                    }
                    
                    resultDisplay.textContent = result;
                    resultDisplay.className = `result-display ${result.toLowerCase()}-result`;
                    gameState.currentPick = result.toLowerCase();
                    
                    // Re-enable button
                    setTimeout(() => {
                        randomBtn.disabled = false;
                    }, 500);
                }
            }, 100);
        }
        
        // Submit outcome
        function submitOutcome() {
            if (!gameState.selectedOutcome) return;
            
            const outcome = gameState.selectedOutcome;
            
            // Update result slots
            gameState.playerResults.pop();
            gameState.bankerResults.pop();
            
            if (outcome === 'player') {
                gameState.playerResults.unshift('W');
                gameState.bankerResults.unshift('L');
                gameState.playerWins++;
            } else if (outcome === 'banker') {
                gameState.playerResults.unshift('L');
                gameState.bankerResults.unshift('W');
                gameState.bankerWins++;
            } else {
                gameState.playerResults.unshift('T');
                gameState.bankerResults.unshift('T');
                gameState.tieWins++;
            }
            
            // Add to history
            const now = new Date();
            const timeString = `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`;
            
            gameState.history.unshift({
                pick: gameState.currentPick,
                outcome: outcome,
                time: timeString
            });
            
            // Keep history to a reasonable length
            if (gameState.history.length > 20) {
                gameState.history.pop();
            }
            
            // Reset selection
            outcomeOptions.forEach(opt => opt.classList.remove('selected'));
            gameState.selectedOutcome = null;
            submitOutcomeBtn.disabled = true;
            gameState.currentPick = null;
            
            // Update UI
            updateUI();
            updateStats();
            saveToStorage();
            
            // Add pulse animation to result display
            resultDisplay.classList.add('pulse');
            setTimeout(() => {
                resultDisplay.classList.remove('pulse');
            }, 500);
        }
        
        // Update the UI
        function updateUI() {
            // Update player slots
            playerSlots.forEach((slot, index) => {
                slot.textContent = gameState.playerResults[index];
                slot.className = 'slot player-slot';
                
                if (gameState.playerResults[index] === 'W') {
                    slot.classList.add('active');
                } else if (gameState.playerResults[index] === 'T') {
                    slot.classList.add('active');
                    slot.classList.add('tie-slot');
                }
            });
            
            // Update banker slots
            bankerSlots.forEach((slot, index) => {
                slot.textContent = gameState.bankerResults[index];
                slot.className = 'slot banker-slot';
                
                if (gameState.bankerResults[index] === 'W') {
                    slot.classList.add('active');
                } else if (gameState.bankerResults[index] === 'T') {
                    slot.classList.add('active');
                    slot.classList.add('tie-slot');
                }
            });
            
            // Update history list
            updateHistory();
        }
        
        // Update statistics
        function updateStats() {
            playerWinsEl.textContent = gameState.playerWins;
            bankerWinsEl.textContent = gameState.bankerWins;
            tieWinsEl.textContent = gameState.tieWins;
            
            const totalGames = gameState.playerWins + gameState.bankerWins + gameState.tieWins;
            
            if (totalGames > 0) {
                playerPercentageEl.textContent = `${((gameState.playerWins / totalGames) * 100).toFixed(1)}%`;
                bankerPercentageEl.textContent = `${((gameState.bankerWins / totalGames) * 100).toFixed(1)}%`;
                tiePercentageEl.textContent = `${((gameState.tieWins / totalGames) * 100).toFixed(1)}%`;
            } else {
                playerPercentageEl.textContent = '0%';
                bankerPercentageEl.textContent = '0%';
                tiePercentageEl.textContent = '0%';
            }
        }
        
        // Update history list
        function updateHistory() {
            // Clear current history
            historyList.innerHTML = '';
            
            // Add history items
            if (gameState.history.length === 0) {
                const emptyItem = document.createElement('div');
                emptyItem.className = 'history-item';
                emptyItem.textContent = 'No games recorded yet';
                historyList.appendChild(emptyItem);
                return;
            }
            
            gameState.history.forEach(item => {
                const historyItem = document.createElement('div');
                historyItem.className = 'history-item';
                
                let pickClass, outcomeClass;
                
                if (item.pick === 'player') {
                    pickClass = 'history-player';
                } else if (item.pick === 'banker') {
                    pickClass = 'history-banker';
                } else {
                    pickClass = 'history-tie';
                }
                
                if (item.outcome === 'player') {
                    outcomeClass = 'history-player';
                } else if (item.outcome === 'banker') {
                    outcomeClass = 'history-banker';
                } else {
                    outcomeClass = 'history-tie';
                }
                
                historyItem.innerHTML = `
                    <div>
                        <span class="history-pick ${pickClass}">${item.pick.toUpperCase()}</span>
                        <span> → </span>
                        <span class="${outcomeClass}">${item.outcome.toUpperCase()}</span>
                    </div>
                    <div>${item.time}</div>
                `;
                
                historyList.appendChild(historyItem);
            });
        }
        
        // Save to localStorage
        function saveToStorage() {
            localStorage.setItem('baccaratGameState', JSON.stringify(gameState));
        }
        
        // Load from localStorage
        function loadFromStorage() {
            const savedState = localStorage.getItem('baccaratGameState');
            if (savedState) {
                const parsedState = JSON.parse(savedState);
                gameState.playerResults = parsedState.playerResults || ['-', '-', '-'];
                gameState.bankerResults = parsedState.bankerResults || ['-', '-', '-'];
                gameState.history = parsedState.history || [];
                gameState.playerWins = parsedState.playerWins || 0;
                gameState.bankerWins = parsedState.bankerWins || 0;
                gameState.tieWins = parsedState.tieWins || 0;
            }
        }
        
        // Initialize the app
        document.addEventListener('DOMContentLoaded', init);
    </script>
</body>
</html>