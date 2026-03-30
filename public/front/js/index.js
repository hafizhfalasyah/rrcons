document.addEventListener('DOMContentLoaded', () => {
    // --- MOBILE MENU ---
    const menuToggle = document.getElementById('menu-toggle');
    const closeMenu = document.getElementById('close-menu');
    const mobileMenu = document.getElementById('mobile-menu');

    if (menuToggle && closeMenu && mobileMenu) {
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.remove('-translate-x-full');
            mobileMenu.classList.add('translate-x-0');
        });

        closeMenu.addEventListener('click', () => {
            mobileMenu.classList.remove('translate-x-0');
            mobileMenu.classList.add('-translate-x-full');
        });
    }

    // --- WHATSAPP CHAT BUTTON ---
    const waBtn = document.getElementById('waBtn');
    const chatBox = document.getElementById('chatBox');
    const closeBtn = document.getElementById('closeBtn');

    if (waBtn && chatBox && closeBtn) {
        waBtn.addEventListener('click', () => {
            chatBox.classList.toggle('hidden');
        });

        closeBtn.addEventListener('click', () => {
            chatBox.classList.add('hidden');
        });
    }

    // --- CAROUSEL ---
    const carousel = document.getElementById('carousel');
    const leftBtn = document.getElementById('scrollLeft');
    const rightBtn = document.getElementById('scrollRight');

    if (carousel && leftBtn && rightBtn) {
        const track = carousel.querySelector('div.flex');
        if (track) {
            track.innerHTML += track.innerHTML;

            const scrollSpeed = 0.5;
            let rafId;

            function autoScroll() {
                carousel.scrollLeft += scrollSpeed;
                if (carousel.scrollLeft >= track.scrollWidth / 2) {
                carousel.scrollLeft = 0;
                }
                rafId = requestAnimationFrame(autoScroll);
            }

            function startAutoScroll() {
                if (!rafId) rafId = requestAnimationFrame(autoScroll);
            }

            function stopAutoScroll() {
                if (rafId) {
                cancelAnimationFrame(rafId);
                rafId = null;
                }
            }

            leftBtn.addEventListener('click', () => {
                stopAutoScroll();
                carousel.scrollBy({ left: -300, behavior: 'smooth' });
                setTimeout(startAutoScroll, 3000);
            });

            rightBtn.addEventListener('click', () => {
                stopAutoScroll();
                carousel.scrollBy({ left: 300, behavior: 'smooth' });
                setTimeout(startAutoScroll, 3000);
            });

            startAutoScroll();
        }
    }

    // --- BUSINESS COACHING LIGHTBOX ---
    const coachingImages = document.querySelectorAll('.coaching-image');
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const closeLightbox = document.getElementById('close-lightbox');

    if (coachingImages.length && lightbox && lightboxImg && closeLightbox) {
        coachingImages.forEach(image => {
            image.addEventListener('click', () => {
                lightbox.classList.remove('hidden');
                lightboxImg.src = image.src;
                lightboxImg.alt = image.alt;
            });
        });

        closeLightbox.addEventListener('click', () => {
            lightbox.classList.add('hidden');
        });

        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) {
                lightbox.classList.add('hidden');
            }
        });
    }

    // --- FILTER SEASON GALLERY ---
    window.filterGallery = function(season) {
        const items = document.querySelectorAll('[data-season]');
        items.forEach(item => {
            item.classList.toggle('hidden', season !== 'all' && item.getAttribute('data-season') !== season);
        });
    };
});

// --- ALPINE INIT POPUP POSTER ---
document.addEventListener('alpine:init', () => {
    Alpine.data('popupController', () => ({
        open: true,
        images: [],
        currentIndex: 0,
        intervalId: null,

        init() {
            const imgs = document.querySelectorAll('#image-source img');
            this.images = Array.from(imgs).map(img => img.src);

            // Mulai auto slide setelah sedikit delay
            if (this.images.length > 1) {
                setTimeout(() => {
                    this.startAutoSlide();
                }, 1000);
            }
        },

        startAutoSlide() {
            this.stopAutoSlide(); // hindari multiple interval
            this.intervalId = setInterval(() => {
                this.nextImage();
            }, 3000);
        },

        stopAutoSlide() {
            if (this.intervalId) {
                clearInterval(this.intervalId);
                this.intervalId = null;
            }
        },

        nextImage() {
            this.currentIndex = (this.currentIndex + 1) % this.images.length;
        },

        prevImage() {
            this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        },

        goToNextImage() {
            this.stopAutoSlide();    // hentikan auto-slide
            this.nextImage();        // jalankan perpindahan manual
            this.startAutoSlide();   // mulai ulang slide otomatis
        },

        goToPrevImage() {
            this.stopAutoSlide();
            this.prevImage();
            this.startAutoSlide();
        }
    }));
});

document.addEventListener('DOMContentLoaded', function () {
    const toggleDesktop = document.getElementById('langToggleDesktop');
    const dropdownDesktop = document.getElementById('langDropdownDesktop');
    const toggleMobile = document.getElementById('langToggleMobile');
    const dropdownMobile = document.getElementById('langDropdownMobile');

    if (toggleDesktop) {
        toggleDesktop.addEventListener('click', () => {
            dropdownDesktop.classList.toggle('hidden');
        });
    }

    if (toggleMobile) {
        toggleMobile.addEventListener('click', () => {
            dropdownMobile.classList.toggle('hidden');
        });
    }

    // Klik luar dropdown untuk nutup
    document.addEventListener('click', function (e) {
        if (!toggleDesktop?.contains(e.target) && !dropdownDesktop?.contains(e.target)) {
            dropdownDesktop?.classList.add('hidden');
        }
        if (!toggleMobile?.contains(e.target) && !dropdownMobile?.contains(e.target)) {
            dropdownMobile?.classList.add('hidden');
        }
    });

    // Event untuk aksi ganti bahasa
    document.getElementById('langIDDesktop')?.addEventListener('click', () => {
        console.log('Bahasa Indonesia (desktop)');
    });

    document.getElementById('langENDesktop')?.addEventListener('click', () => {
        console.log('English (desktop)');
    });

    document.getElementById('langIDMobile')?.addEventListener('click', () => {
        console.log('Bahasa Indonesia (mobile)');
    });

    document.getElementById('langENMobile')?.addEventListener('click', () => {
        console.log('English (mobile)');
    });
});

// Social Media Posts
document.querySelectorAll('[data-video-id]').forEach(wrapper => {
    const videoId = wrapper.getAttribute('data-video-id');
    const video = document.getElementById(videoId);
    const icon = wrapper.querySelector('.video-icon');

    wrapper.addEventListener('mouseenter', () => {
        video.play();
        icon.innerHTML = `
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6" />
        `;
    });

    wrapper.addEventListener('mouseleave', () => {
        video.pause();
        video.currentTime = 0;
        icon.innerHTML = `
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-5.197-3.03A1 1 0 008 9v6a1 1 0 001.555.832l5.197-3.03a1 1 0 000-1.664z" />
        `;
    });
});
