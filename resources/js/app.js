
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();


document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('pixel-canvas');

    if (canvas) {
        const blockSize = 40;
        const screenWidth = window.innerWidth;
        // Adjust height so it generates enough blocks to fill the Hero section
        const screenHeight = document.getElementById('hero-section').offsetHeight;

        const columns = Math.ceil(screenWidth / blockSize);
        const rows = Math.ceil(screenHeight / blockSize);
        const totalBlocks = columns * rows;

        for (let i = 0; i < totalBlocks; i++) {
            const block = document.createElement('div');
            block.classList.add('pixel-block');

            if (Math.random() > 0.90) {
                activateBlock(block);
            }

            canvas.appendChild(block);
        }

        function activateBlock(el) {
            const randomDelay = Math.random() * 5;
            el.style.animationDelay = `${randomDelay}s`;
            el.classList.add('pixel-active');
        }

        setInterval(() => {
            const blocks = document.querySelectorAll('.pixel-block');
            if(blocks.length > 0) {
                const randomIndex = Math.floor(Math.random() * blocks.length);
                blocks[randomIndex].classList.toggle('pixel-active');
            }
        }, 500);
    }
});
