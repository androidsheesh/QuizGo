// import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();


/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';

const toastContainerId = 'global-toast-container';

function getToastContainer() {
    return document.getElementById(toastContainerId);
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function showGlobalToast({ type = 'success', title, message, actionUrl = null, actionLabel = 'Open' }) {
    const container = getToastContainer();

    if (!container) {
        return;
    }

    const isSuccess = type === 'success';
    const toast = document.createElement('div');

    toast.className = [
        'pointer-events-auto',
        'w-full',
        'rounded-2xl',
        'border',
        'bg-white',
        'p-4',
        'shadow-2xl',
        'shadow-slate-900/10',
        'transition-all',
        'duration-300',
        'ease-out',
        'translate-y-[-12px]',
        'opacity-0',
        isSuccess ? 'border-emerald-200' : 'border-red-200',
    ].join(' ');

    const iconClass = isSuccess
        ? 'bg-emerald-500 text-white'
        : 'bg-red-500 text-white';
    const titleClass = isSuccess ? 'text-emerald-950' : 'text-red-950';
    const messageClass = isSuccess ? 'text-emerald-700' : 'text-red-700';
    const actionClass = isSuccess
        ? 'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-300'
        : 'bg-red-600 hover:bg-red-700 focus:ring-red-300';

    const safeTitle = escapeHtml(title);
    const safeMessage = escapeHtml(message);
    const safeActionUrl = actionUrl ? escapeHtml(actionUrl) : null;
    const safeActionLabel = escapeHtml(actionLabel);

    toast.innerHTML = `
        <div class="flex items-start gap-3">
            <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full ${iconClass}">
                <span class="text-base font-black">${isSuccess ? '✓' : '!'}</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-black ${titleClass}">${safeTitle}</p>
                <p class="mt-1 text-sm leading-5 ${messageClass}">${safeMessage}</p>
                ${safeActionUrl ? `
                    <a href="${safeActionUrl}" class="mt-3 inline-flex items-center rounded-xl px-3 py-2 text-xs font-bold text-white shadow-sm transition-colors focus:outline-none focus:ring-4 ${actionClass}">
                        ${safeActionLabel}
                    </a>
                ` : ''}
            </div>
            <button type="button" class="global-toast-close rounded-lg p-1 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-700" aria-label="Dismiss notification">
                <span class="material-symbols-rounded text-xl">close</span>
            </button>
        </div>
    `;

    const closeToast = () => {
        toast.classList.add('translate-y-[-12px]', 'opacity-0');
        toast.classList.remove('translate-y-0', 'opacity-100');
        setTimeout(() => toast.remove(), 300);
    };

    toast.querySelector('.global-toast-close')?.addEventListener('click', closeToast);
    container.appendChild(toast);

    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-[-12px]', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');
    });

    setTimeout(closeToast, isSuccess ? 9000 : 12000);
}

window.showGlobalToast = showGlobalToast;

function initializeFlashcardGenerationListener() {
    const userId = window.QuizGo?.userId;

    if (!userId || !window.Echo) {
        return;
    }

    window.Echo.private(`user.${userId}`)
        .listen('.flashcard.generation.finished', (event) => {
            if (event.status === 'completed') {
                showGlobalToast({
                    type: 'success',
                    title: 'Flashcards ready',
                    message: event.deck_title
                        ? `"${event.deck_title}" has been generated successfully.`
                        : 'Success! Your generated flashcards are ready.',
                });

                return;
            }

            if (event.status === 'failed') {
                showGlobalToast({
                    type: 'error',
                    title: 'Generation failed',
                    message: event.error?.message || 'Generation failed. Please try a different prompt or try again later.',
                });
            }
        });
}

function initializeTeacherNotificationListener() {
    const teacherId = window.QuizGo?.teacherId;

    if (!teacherId || !window.Echo) {
        return;
    }

    window.Echo.private(`teacher.${teacherId}`)
        .listen('.teacher.notification.received', (event) => {
            showGlobalToast({
                type: event.type || 'success',
                title: event.title || 'Teacher notification',
                message: event.message || 'You have a new update.',
            });
        });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initializeFlashcardGenerationListener();
        initializeTeacherNotificationListener();
    });
} else {
    initializeFlashcardGenerationListener();
    initializeTeacherNotificationListener();
}
