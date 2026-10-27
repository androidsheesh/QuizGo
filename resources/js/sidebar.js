/**
 * Off-canvas navigation drawer used below the lg breakpoint (components/app-shell.blade.php).
 * On lg and up the sidebar is always visible: `lg:translate-x-0` overrides the hidden state.
 */
const LG_BREAKPOINT = 1024;

function initSidebar() {
    const sidebar = document.getElementById('app-sidebar');
    const backdrop = document.getElementById('app-sidebar-backdrop');
    const openButtons = document.querySelectorAll('[data-sidebar-open]');
    const closeButtons = document.querySelectorAll('[data-sidebar-close]');

    if (!sidebar || !backdrop) {
        return;
    }

    const isDesktop = () => window.innerWidth >= LG_BREAKPOINT;
    const isOpen = () => !backdrop.classList.contains('opacity-0');
    let lastFocused = null;

    // A closed drawer is off-screen, so keep Tab and screen readers out of it
    const syncInert = () => sidebar.toggleAttribute('inert', !isDesktop() && !isOpen());

    const setOpen = (open) => {
        sidebar.classList.toggle('-translate-x-full', !open);
        backdrop.classList.toggle('opacity-0', !open);
        backdrop.classList.toggle('pointer-events-none', !open);
        openButtons.forEach((button) => button.setAttribute('aria-expanded', String(open)));
        document.body.classList.toggle('overflow-hidden', open);
        syncInert();

        if (open) {
            lastFocused = document.activeElement;
            sidebar.querySelector('a, button')?.focus();
        } else {
            lastFocused?.focus();
            lastFocused = null;
        }
    };

    openButtons.forEach((button) => button.addEventListener('click', () => setOpen(true)));
    closeButtons.forEach((button) => button.addEventListener('click', () => setOpen(false)));
    backdrop.addEventListener('click', () => setOpen(false));

    // Following a link from the drawer should also close it
    sidebar.addEventListener('click', (event) => {
        if (event.target.closest('a') && isOpen()) {
            setOpen(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && isOpen()) {
            setOpen(false);
        }
    });

    window.addEventListener('resize', () => {
        if (isDesktop() && isOpen()) {
            setOpen(false);
        }
        syncInert();
    });

    syncInert();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSidebar);
} else {
    initSidebar();
}
