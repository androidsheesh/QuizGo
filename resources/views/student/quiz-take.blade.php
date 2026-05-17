<x-layout>
    {{--
        ════════════════════════════════════════════════════════
         QUIZ PROCTORING SYSTEM (Responsive)
         • Fullscreen gate modal before quiz starts
         • Detects alt-tab / window blur / tab switch
         • Blocks: right-click, clipboard shortcuts, DevTools
         • 3 violations = auto-submit (flagged in DB)
        ════════════════════════════════════════════════════════
    --}}

    {{-- ── STYLES ── --}}
    <style>
        /* Prevent text selection during quiz */
        .quiz-content { user-select: none; -webkit-user-select: none; }

        /* Gate Modal */
        #proctoringGate {
            position: fixed; inset: 0; z-index: 9999;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f2027 100%);
            display: flex; align-items: center; justify-content: center;
            animation: fadeIn .3s ease;
            overflow-y: auto;
            padding: 1rem;
        }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* Warning Overlay */
        #violationOverlay {
            position: fixed; inset: 0; z-index: 9998;
            background: rgba(0,0,0,.85); backdrop-filter: blur(8px);
            display: none; align-items: center; justify-content: center;
            animation: fadeIn .2s ease;
            padding: 1rem;
        }
        #violationOverlay.active { display: flex; }

        /* Pulse ring on violation icon */
        @keyframes pulseRing {
            0%   { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239,68,68,.7); }
            70%  { transform: scale(1);    box-shadow: 0 0 0 20px rgba(239,68,68,0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239,68,68,0); }
        }
        .pulse-ring { animation: pulseRing 1.5s infinite; }

        /* Auto-submit screen */
        #autoSubmitScreen {
            position: fixed; inset: 0; z-index: 10000;
            background: linear-gradient(135deg, #1a0000, #3b0000);
            display: none; align-items: center; justify-content: center;
            flex-direction: column; gap: 1rem;
            padding: 1rem;
        }
        #autoSubmitScreen.active { display: flex; }

        /* Toast notifications */
        #toastContainer {
            position: fixed; bottom: 1rem; right: 1rem; left: 1rem;
            z-index: 9990; display: flex; flex-direction: column; gap: .5rem;
            pointer-events: none;
        }
        @media (min-width: 768px) {
            #toastContainer { left: auto; bottom: 1.5rem; right: 1.5rem; }
        }
        .toast {
            padding: .75rem 1.25rem;
            background: #1e293b; color: #f1f5f9;
            border-left: 4px solid #ef4444;
            border-radius: .75rem; font-size: .875rem; font-weight: 600;
            box-shadow: 0 8px 24px rgba(0,0,0,.4);
            animation: slideIn .25s ease;
            width: auto; max-width: 100%;
            word-wrap: break-word;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(30px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* Violation counter badge blink */
        @keyframes blink {
            0%, 100% { opacity: 1; } 50% { opacity: .5; }
        }
        .violations-blink { animation: blink .6s ease 3; }
    </style>

    {{-- ══════════════════════════════════════════════════════════
         GATE: Fullscreen instruction modal (shown before quiz)
    ══════════════════════════════════════════════════════════ --}}
    <div id="proctoringGate">
        <div class="max-w-lg w-full mx-auto my-auto">
            {{-- Shield icon --}}
            <div class="flex justify-center mb-4 md:mb-6">
                <div class="w-16 h-16 md:w-24 md:h-24 rounded-full bg-red-500/20 border-2 border-red-500/50 flex items-center justify-center text-3xl md:text-5xl pulse-ring">
                    🛡️
                </div>
            </div>

            <h1 class="text-2xl md:text-3xl font-black text-white text-center mb-2">Proctored Quiz</h1>
            <p class="text-slate-400 text-center text-xs md:text-sm mb-6 md:mb-8">
                <span class="text-emerald-400 font-bold block sm:inline">{{ $quiz->title }}</span>
                <span class="hidden sm:inline">—</span>
                <span class="block sm:inline mt-1 sm:mt-0">{{ $assignment->classroom->name }}</span>
            </p>

            {{-- Rules list --}}
            <div class="bg-white/5 border border-white/10 rounded-2xl p-4 md:p-6 mb-6 space-y-4">
                <p class="text-white font-bold text-xs md:text-sm uppercase tracking-widest mb-2 md:mb-4 opacity-60">Quiz Rules & Restrictions</p>

                @php $rules = [
                    ['icon' => '🖥️', 'color' => 'text-blue-400',   'title' => 'Fullscreen Required',    'desc' => 'You must remain in fullscreen mode for the entire quiz duration.'],
                    ['icon' => '🚫', 'color' => 'text-red-400',    'title' => 'No Tab Switching',        'desc' => 'Switching tabs or windows counts as a violation. Maximum 3 violations.'],
                    ['icon' => '📋', 'color' => 'text-amber-400',  'title' => 'No Copy & Paste',         'desc' => 'Copy, paste, and select-all shortcuts are disabled.'],
                    ['icon' => '🖱️', 'color' => 'text-purple-400', 'title' => 'No Right-Click',          'desc' => 'The right-click context menu is disabled throughout the quiz.'],
                    ['icon' => '⚡', 'color' => 'text-red-500',    'title' => 'Auto-Submit on 3 Strikes','desc' => 'After 3 violations, your quiz will be automatically submitted.'],
                ]; @endphp

                @foreach($rules as $rule)
                    <div class="flex items-start gap-3 md:gap-4">
                        <span class="text-xl md:text-2xl shrink-0 mt-0.5">{{ $rule['icon'] }}</span>
                        <div>
                            <p class="font-bold {{ $rule['color'] }} text-sm">{{ $rule['title'] }}</p>
                            <p class="text-slate-400 text-[11px] sm:text-xs mt-0.5">{{ $rule['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Acknowledgement checkbox --}}
            <label class="flex items-start sm:items-center gap-3 cursor-pointer mb-6 group" id="ackLabel">
                <input type="checkbox" id="ackCheckbox" class="w-5 h-5 mt-0.5 sm:mt-0 rounded accent-emerald-500 cursor-pointer shrink-0">
                <span class="text-slate-300 text-xs sm:text-sm group-hover:text-white transition-colors leading-relaxed">
                    I understand the rules and I am ready to begin the quiz.
                </span>
            </label>

            {{-- Enter Fullscreen button --}}
            <button id="enterQuizBtn" onclick="enterQuizFullscreen()"
                disabled
                class="w-full py-3 md:py-4 rounded-xl md:rounded-2xl font-black text-base md:text-lg transition-all duration-300
                       bg-slate-700 text-slate-500 cursor-not-allowed
                       disabled:opacity-50"
                id="startBtn">
                <span class="flex items-center justify-center gap-2">
                    🔒 <span class="hidden sm:inline">Enter Fullscreen &</span> Start Quiz
                </span>
            </button>

            <p class="text-center text-slate-600 text-[10px] md:text-xs mt-4">
                Your teacher will be notified of any suspicious activity.
            </p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         VIOLATION WARNING OVERLAY (shown on each violation 1-2)
    ══════════════════════════════════════════════════════════ --}}
    <div id="violationOverlay">
        <div class="max-w-md w-full mx-auto text-center">
            <div class="w-20 h-20 md:w-28 md:h-28 rounded-full bg-red-500/20 border-2 border-red-500 flex items-center justify-center text-4xl md:text-6xl mx-auto mb-4 md:mb-6 pulse-ring">
                ⚠️
            </div>
            <h2 class="text-2xl md:text-4xl font-black text-white mb-2">Suspicious Activity!</h2>
            <p class="text-red-400 font-bold text-lg md:text-xl mb-2" id="violationCountText">Violation 1 of 3</p>
            <p class="text-slate-300 text-xs md:text-sm mb-6 px-4" id="violationReason">You switched away from the quiz window.</p>

            <div class="bg-red-900/30 border border-red-500/30 rounded-xl md:rounded-2xl p-4 md:p-6 mb-6">
                <p class="text-slate-300 text-xs md:text-sm mb-3">
                    ⚡ <strong class="text-white">3 violations</strong> will automatically submit your quiz and flag it for your teacher.
                </p>
                <p class="text-slate-400 text-xs md:text-sm">Returning to quiz in <span id="countdownNum" class="text-red-400 font-black text-xl md:text-2xl mx-1">5</span> seconds…</p>
            </div>

            <button onclick="dismissViolationWarning()"
                class="w-full sm:w-auto px-6 md:px-8 py-3 bg-white text-slate-900 font-black rounded-xl hover:bg-slate-100 transition-colors text-sm md:text-base">
                Return to Quiz Now
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         AUTO-SUBMIT SCREEN (shown on 3rd violation)
    ══════════════════════════════════════════════════════════ --}}
    <div id="autoSubmitScreen">
        <div class="text-5xl md:text-7xl mb-2 md:mb-4">🚨</div>
        <h2 class="text-2xl md:text-4xl font-black text-red-400 text-center">Quiz Auto-Submitted</h2>
        <p class="text-slate-300 text-base md:text-lg max-w-sm text-center px-4">
            You reached 3 violations. Your quiz has been submitted and flagged for suspicious activity.
        </p>
        <div class="mt-4 w-8 h-8 md:w-10 md:h-10 border-4 border-red-500 border-t-transparent rounded-full animate-spin"></div>
        <p class="text-slate-500 text-xs md:text-sm mt-2">Submitting your answers…</p>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         TOAST NOTIFICATION CONTAINER
    ══════════════════════════════════════════════════════════ --}}
    <div id="toastContainer"></div>

    {{-- ══════════════════════════════════════════════════════════
         MAIN QUIZ CONTENT (hidden until gate is passed)
    ══════════════════════════════════════════════════════════ --}}
    <div id="quizWrapper" class="min-h-screen bg-[#F9FAFB] flex flex-col quiz-content" style="display:none !important"
         x-data="quizTimer({{ $quiz->time_limit ? $quiz->time_limit * 60 : 0 }})">

        {{-- Quiz Header --}}
        <header class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 py-3 sm:py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">

                {{-- Left: Title area + Responsive Back to Class button --}}
                <div class="flex flex-col gap-2 w-full sm:w-auto">
                    {{-- Added Back Link safely clearing mobile burger components --}}
                    <a href="{{ route('student.classroom.show', $assignment->classroom_id) }}" class="inline-flex items-center text-slate-400 hover:text-slate-600 transition-colors mt-12 sm:mt-0 text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        Back to Class
                    </a>

                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-emerald-100 text-emerald-600 rounded-lg sm:rounded-xl flex items-center justify-center font-bold text-base sm:text-lg shrink-0">
                            📝
                        </div>
                        <div class="min-w-0">
                            <h1 class="text-base sm:text-xl font-bold text-slate-800 truncate">{{ $quiz->title }}</h1>
                            <p class="text-xs sm:text-sm font-medium text-slate-400 truncate">{{ $assignment->classroom->name }}</p>
                        </div>
                    </div>
                </div>

                {{-- Right: Badges & Timer --}}
                <div class="flex items-center justify-between sm:justify-end gap-2 sm:gap-3 w-full sm:w-auto">
                    {{-- Violation counter badge --}}
                    <div id="violationBadge"
                         class="flex items-center gap-1 sm:gap-1.5 bg-slate-50 border border-slate-200 px-2.5 py-1.5 rounded-lg sm:rounded-xl text-xs sm:text-sm font-bold text-slate-500 whitespace-nowrap"
                         title="Proctoring violations">
                        🛡️ <span id="violationBadgeCount">0</span>/3 strikes
                    </div>

                    {{-- Timer Display --}}
                    <div class="flex items-center gap-2 sm:gap-3 bg-slate-50 px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg sm:rounded-xl border border-slate-100"
                         :class="{ 'bg-red-50 border-red-200 text-red-600': timeLimit > 0 && timeLeft <= 60 }">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-slate-400" :class="{ 'text-red-500': timeLimit > 0 && timeLeft <= 60 }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-mono font-bold text-base sm:text-lg"
                              :class="{ 'text-red-600': timeLimit > 0 && timeLeft <= 60, 'text-slate-700': !(timeLimit > 0 && timeLeft <= 60) }"
                              x-text="formattedTime"></span>
                    </div>
                </div>
            </div>
        </header>

        {{-- Quiz Content --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 md:p-12 w-full">
            <div class="max-w-3xl mx-auto w-full">

                @if($quiz->description)
                    <div class="bg-blue-50 border border-blue-100 p-4 sm:p-6 rounded-xl sm:rounded-2xl mb-6 sm:mb-8">
                        <h3 class="font-bold text-blue-800 mb-1 text-sm sm:text-base">Instructions</h3>
                        <p class="text-blue-600 text-xs sm:text-sm">{{ $quiz->description }}</p>
                    </div>
                @endif

                <form id="quizForm" method="POST" action="{{ route('student.quiz.submit', $assignment) }}">
                    @csrf
                    <input type="hidden" name="time_taken"  x-model="timeTaken">
                    <input type="hidden" name="violations"  id="violationsInput" value="0">

                    <div class="space-y-6 sm:space-y-8">
                        @foreach($quiz->questions as $index => $question)
                            <div class="bg-white p-5 sm:p-8 rounded-2xl sm:rounded-[2rem] border border-gray-100 shadow-sm w-full">
                                <div class="flex gap-3 sm:gap-4 mb-4 sm:mb-6">
                                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-slate-100 text-slate-600 font-bold flex items-center justify-center shrink-0 text-sm sm:text-base mt-0.5">
                                        {{ $index + 1 }}
                                    </div>
                                    <h3 class="text-base sm:text-lg font-bold text-slate-800 mt-1">{{ $question->question }}</h3>
                                </div>

                                <div class="ml-0 sm:ml-12 mt-4 sm:mt-0">
                                    @if($question->type === 'multiple_choice')
                                        <div class="space-y-2 sm:space-y-3">
                                            @foreach($question->choices as $choiceIndex => $choice)
                                                <label class="flex items-start sm:items-center gap-3 sm:gap-4 p-3 sm:p-4 rounded-xl border border-slate-100 hover:border-emerald-300 hover:bg-emerald-50 cursor-pointer transition-colors group">
                                                    <input type="radio"
                                                           name="answers[{{ $question->id }}]"
                                                           value="{{ $choiceIndex }}"
                                                           class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-500 focus:ring-emerald-400 border-slate-300 mt-0.5 sm:mt-0 shrink-0">
                                                    <span class="text-slate-700 font-medium group-hover:text-emerald-800 text-sm sm:text-base leading-snug">{{ $choice }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @elseif($question->type === 'identification')
                                        <input type="text"
                                               name="answers[{{ $question->id }}]"
                                               placeholder="Type your answer here…"
                                               autocomplete="off"
                                               class="w-full p-3 sm:p-4 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-emerald-400/30 focus:border-emerald-400 transition-all font-medium">
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 sm:mt-10 flex justify-center sm:justify-end">
                        <button type="submit"
                                class="w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-black text-base sm:text-lg rounded-xl sm:rounded-2xl shadow-xl shadow-emerald-200 hover:scale-[1.02] active:scale-95 transition-transform flex items-center justify-center gap-2 sm:gap-3">
                            Submit Quiz
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </button>
                    </div>
                </form>

            </div>
        </main>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         SCRIPTS (Unmodified Proctoring Logic)
    ══════════════════════════════════════════════════════════ --}}
    <script>
    // ─── Gate: Checkbox enables the start button ──────────────────────────────
    document.getElementById('ackCheckbox').addEventListener('change', function () {
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
    function quizTimer(limitSeconds) {
        return {
            timeLimit: limitSeconds,
            timeLeft:  limitSeconds,
            timeTaken: 0,
            timer:     null,
            init() {
                setInterval(() => { this.timeTaken++; }, 1000);

                if (this.timeLimit > 0) {
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
                const val = this.timeLimit > 0 ? this.timeLeft : this.timeTaken;
                const m = Math.floor(val / 60);
                const s = val % 60;
                return `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
            }
        }
    }
    </script>
</x-layout>
