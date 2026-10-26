/**
 * Proctoring and timer for the quiz-taking page (student/quiz-take.blade.php).
 * Imported by app.js so it runs before Alpine starts and evaluates x-data="quizTimer(...)".
 * On every other page the elements are missing and nothing is set up.
 */

// ─── Gate: Checkbox enables the start button ──────────────────────────────
document.getElementById('ackCheckbox')?.addEventListener('change', function () {
    const btn = document.getElementById('enterQuizBtn');
    if (this.checked) {
        btn.disabled = false;
        btn.classList.remove('bg-slate-700', 'text-slate-500', 'cursor-not-allowed', 'opacity-50');
        btn.classList.add('bg-gradient-to-r', 'from-emerald-500', 'to-teal-500', 'text-white', 'shadow-xl', 'shadow-emerald-900/40', 'hover:scale-[1.02]', 'cursor-pointer');
    } else {
        btn.disabled = true;
        btn.classList.add('bg-slate-700', 'text-slate-500', 'cursor-not-allowed', 'opacity-50');
        btn.classList.remove('bg-gradient-to-r', 'from-emerald-500', 'to-teal-500', 'text-white', 'shadow-xl', 'shadow-emerald-900/40', 'hover:scale-[1.02]', 'cursor-pointer');
    }
});

// ─── Enter fullscreen & reveal quiz ──────────────────────────────────────
function enterQuizFullscreen() {
    const el = document.documentElement;
    const req = el.requestFullscreen || el.webkitRequestFullscreen || el.mozRequestFullScreen || el.msRequestFullscreen;
    if (req) req.call(el);

    // Hide gate, show quiz
    document.getElementById('proctoringGate').style.display = 'none';
    const quiz = document.getElementById('quizWrapper');
    quiz.style.setProperty('display', 'flex', 'important');
    quiz.style.flexDirection = 'column';

    // Start proctoring after gate is dismissed
    startProctoring();
}

// ─── Proctoring Engine ────────────────────────────────────────────────────
const MAX_VIOLATIONS = 3;
let violations       = 0;
let countdownTimer   = null;
let warningDismissed = true; // gate to prevent stacking

function startProctoring() {
    // 1. Detect tab switch / minimize (Page Visibility API)
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) triggerViolation('You switched to another tab or minimized the window.');
    });

    // 2. Detect window blur (alt-tab, another app)
    window.addEventListener('blur', () => {
        triggerViolation('You switched away from the quiz window (Alt+Tab or another app).');
    });

    // 3. Detect fullscreen exit
    document.addEventListener('fullscreenchange', () => {
        if (!document.fullscreenElement) {
            triggerViolation('You exited fullscreen mode.');
        }
    });

    // 4. Block right-click
    document.addEventListener('contextmenu', (e) => {
        e.preventDefault();
        showToast('🚫 Right-click is disabled during the quiz.');
    });

    // 5. Block keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        const ctrl = e.ctrlKey || e.metaKey;

        // Clipboard & select-all
        if (ctrl && ['c','v','x','a'].includes(e.key.toLowerCase())) {
            e.preventDefault();
            showToast('🚫 Copy / Paste shortcuts are disabled.');
            return;
        }
        // DevTools
        if (e.key === 'F12' ||
            (ctrl && e.shiftKey && ['i','j','c'].includes(e.key.toLowerCase())) ||
            (ctrl && e.key.toLowerCase() === 'u')) {
            e.preventDefault();
            showToast('🚫 Developer tools are disabled during the quiz.');
            return;
        }
        // Escape key (could exit fullscreen / dismiss)
        if (e.key === 'Escape') {
            e.preventDefault();
        }
    });
}

function triggerViolation(reason) {
    if (!warningDismissed) return; // Don't stack violations while overlay is open
    violations++;
    updateViolationBadge();

    if (violations >= MAX_VIOLATIONS) {
        triggerAutoSubmit();
        return;
    }

    warningDismissed = false;
    showViolationOverlay(reason);
}

function updateViolationBadge() {
    const badge = document.getElementById('violationBadge');
    const count = document.getElementById('violationBadgeCount');
    document.getElementById('violationsInput').value = violations;
    count.textContent = violations;

    // Update badge color
    badge.classList.remove('bg-slate-50', 'border-slate-200', 'text-slate-500',
                           'bg-amber-50', 'border-amber-300', 'text-amber-700',
                           'bg-red-50', 'border-red-400', 'text-red-700');
    if (violations === 1) {
        badge.classList.add('bg-amber-50', 'border-amber-300', 'text-amber-700');
    } else {
        badge.classList.add('bg-red-50', 'border-red-400', 'text-red-700');
    }

    // Blink
    badge.classList.add('violations-blink');
    setTimeout(() => badge.classList.remove('violations-blink'), 1800);
}

function showViolationOverlay(reason) {
    const overlay = document.getElementById('violationOverlay');
    document.getElementById('violationCountText').textContent = `Violation ${violations} of ${MAX_VIOLATIONS}`;
    document.getElementById('violationReason').textContent = reason;

    overlay.classList.add('active');

    // Start countdown
    let secs = 5;
    document.getElementById('countdownNum').textContent = secs;
    clearInterval(countdownTimer);
    countdownTimer = setInterval(() => {
        secs--;
        document.getElementById('countdownNum').textContent = secs;
        if (secs <= 0) dismissViolationWarning();
    }, 1000);
}

function dismissViolationWarning() {
    clearInterval(countdownTimer);
    document.getElementById('violationOverlay').classList.remove('active');
    warningDismissed = true;

    // Try to re-enter fullscreen after dismissal
    if (!document.fullscreenElement) {
        const el = document.documentElement;
        const req = el.requestFullscreen || el.webkitRequestFullscreen || el.mozRequestFullScreen;
        if (req) req.call(el).catch(() => {});
    }
}

function triggerAutoSubmit() {
    // Show auto-submit screen
    document.getElementById('autoSubmitScreen').classList.add('active');
    document.getElementById('violationsInput').value = violations;

    // Submit after brief pause so the screen is visible
    setTimeout(() => {
        document.getElementById('quizForm').submit();
    }, 2500);
}

// ─── Toast helper ─────────────────────────────────────────────────────────
let toastTimeout = null;
function showToast(message) {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// ─── Alpine.js quiz timer ─────────────────────────────────────────────────
// remainingSeconds and elapsedSeconds come from the server's start time, so reloading
// the page doesn't reset the clock. remainingSeconds is null when there's no time limit.
function quizTimer(remainingSeconds, elapsedSeconds) {
    return {
        timeLimit: remainingSeconds !== null,
        timeLeft:  remainingSeconds ?? 0,
        timeTaken: elapsedSeconds,
        timer:     null,
        init() {
            setInterval(() => { this.timeTaken++; }, 1000);

            if (this.timeLimit && this.timeLeft <= 0) {
                document.getElementById('quizForm').submit();
            } else if (this.timeLimit) {
                this.timer = setInterval(() => {
                    this.timeLeft--;
                    if (this.timeLeft <= 0) {
                        clearInterval(this.timer);
                        document.getElementById('quizForm').submit();
                    }
                }, 1000);
            }
        },
        get formattedTime() {
            const val = this.timeLimit ? this.timeLeft : this.timeTaken;
            const m = Math.floor(val / 60);
            const s = val % 60;
            return `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
        }
    }
}

// Called from the page markup (onclick handlers and x-data)
Object.assign(window, { enterQuizFullscreen, dismissViolationWarning, quizTimer });
