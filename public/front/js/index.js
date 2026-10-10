document.addEventListener('DOMContentLoaded', () => {
    // --- MOBILE MENU ---

    const menuToggle = document.getElementById('menu-toggle');
    const closeMenu = document.getElementById('close-menu');
    const mobileMenu = document.getElementById('mobile-menu');

    if (menuToggle && closeMenu && mobileMenu) {
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.remove('-translate-x-full');
            mobileMenu.classList.add('translate-x-0');

            menuToggle.setAttribute('aria-expanded', 'true');
        });

        closeMenu.addEventListener('click', () => {
            mobileMenu.classList.remove('translate-x-0');
            mobileMenu.classList.add('-translate-x-full');

            menuToggle.setAttribute('aria-expanded', 'false');
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
                if (!rafId) {
                    rafId = requestAnimationFrame(autoScroll);
                }
            }

            function stopAutoScroll() {
                if (rafId) {
                    cancelAnimationFrame(rafId);
                    rafId = null;
                }
            }

            leftBtn.addEventListener('click', () => {
                stopAutoScroll();

                carousel.scrollBy({
                    left: -300,
                    behavior: 'smooth',
                });

                setTimeout(startAutoScroll, 3000);
            });

            rightBtn.addEventListener('click', () => {
                stopAutoScroll();

                carousel.scrollBy({
                    left: 300,
                    behavior: 'smooth',
                });

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

    if (
        coachingImages.length &&
        lightbox &&
        lightboxImg &&
        closeLightbox
    ) {
        coachingImages.forEach((image) => {
            image.addEventListener('click', () => {
                lightbox.classList.remove('hidden');
                lightboxImg.src = image.src;
                lightboxImg.alt = image.alt;
            });
        });

        closeLightbox.addEventListener('click', () => {
            lightbox.classList.add('hidden');
        });

        lightbox.addEventListener('click', (event) => {
            if (event.target === lightbox) {
                lightbox.classList.add('hidden');
            }
        });
    }

    // --- FILTER SEASON GALLERY ---

    window.filterGallery = function (season) {
        const items = document.querySelectorAll('[data-season]');

        items.forEach((item) => {
            const itemSeason = item.getAttribute('data-season');

            item.classList.toggle(
                'hidden',
                season !== 'all' && itemSeason !== season,
            );
        });
    };

    // --- LANGUAGE SWITCHER ---

    const toggleDesktop = document.getElementById(
        'langToggleDesktop',
    );
    const dropdownDesktop = document.getElementById(
        'langDropdownDesktop',
    );
    const arrowDesktop = document.getElementById(
        'langArrowDesktop',
    );

    const toggleMobile = document.getElementById(
        'langToggleMobile',
    );
    const dropdownMobile = document.getElementById(
        'langDropdownMobile',
    );
    const arrowMobile = document.getElementById(
        'langArrowMobile',
    );

    function closeDesktopLanguageDropdown() {
        if (!toggleDesktop || !dropdownDesktop) {
            return;
        }

        dropdownDesktop.classList.add('hidden');
        toggleDesktop.setAttribute('aria-expanded', 'false');
        arrowDesktop?.classList.remove('rotate-180');
    }

    function closeMobileLanguageDropdown() {
        if (!toggleMobile || !dropdownMobile) {
            return;
        }

        dropdownMobile.classList.add('hidden');
        toggleMobile.setAttribute('aria-expanded', 'false');
        arrowMobile?.classList.remove('rotate-180');
    }

    if (toggleDesktop && dropdownDesktop) {
        toggleDesktop.addEventListener('click', (event) => {
            event.stopPropagation();

            const willOpen =
                dropdownDesktop.classList.contains('hidden');

            closeMobileLanguageDropdown();

            dropdownDesktop.classList.toggle(
                'hidden',
                !willOpen,
            );

            toggleDesktop.setAttribute(
                'aria-expanded',
                willOpen ? 'true' : 'false',
            );

            arrowDesktop?.classList.toggle(
                'rotate-180',
                willOpen,
            );
        });

        dropdownDesktop.addEventListener('click', (event) => {
            event.stopPropagation();
        });
    }

    if (toggleMobile && dropdownMobile) {
        toggleMobile.addEventListener('click', (event) => {
            event.stopPropagation();

            const willOpen =
                dropdownMobile.classList.contains('hidden');

            closeDesktopLanguageDropdown();

            dropdownMobile.classList.toggle(
                'hidden',
                !willOpen,
            );

            toggleMobile.setAttribute(
                'aria-expanded',
                willOpen ? 'true' : 'false',
            );

            arrowMobile?.classList.toggle(
                'rotate-180',
                willOpen,
            );
        });

        dropdownMobile.addEventListener('click', (event) => {
            event.stopPropagation();
        });
    }

    // Tutup dropdown ketika pengguna mengeklik area lain.
    document.addEventListener('click', () => {
        closeDesktopLanguageDropdown();
        closeMobileLanguageDropdown();
    });

    // Tutup menu dan dropdown menggunakan tombol Escape.
    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        closeDesktopLanguageDropdown();
        closeMobileLanguageDropdown();

        if (menuToggle && mobileMenu) {
            mobileMenu.classList.remove('translate-x-0');
            mobileMenu.classList.add('-translate-x-full');

            menuToggle.setAttribute('aria-expanded', 'false');
        }
    });

    // Pastikan mobile menu tertutup ketika berpindah ke desktop.
    window.addEventListener('resize', () => {
        if (
            window.innerWidth >= 768 &&
            menuToggle &&
            mobileMenu
        ) {
            mobileMenu.classList.remove('translate-x-0');
            mobileMenu.classList.add('-translate-x-full');

            menuToggle.setAttribute('aria-expanded', 'false');

            closeMobileLanguageDropdown();
        }
    });
});

// --- ALPINE INIT POPUP POSTER ---

document.addEventListener('alpine:init', () => {
    Alpine.data('popupController', (totalImages) => ({
        open: true,
        images: [],
        currentIndex: 0,
        intervalId: null,
        totalImages,

        init() {
            const imgs = document.querySelectorAll(
                '#image-source img',
            );

            this.images = Array.from(imgs).map(
                (img) => img.src,
            );

            // Pastikan jumlah gambar sesuai data sebenarnya.
            this.totalImages = this.images.length;

            // Mulai auto slide hanya jika gambar lebih dari satu.
            if (this.images.length > 1) {
                setTimeout(() => {
                    this.startAutoSlide();
                }, 1000);
            }
        },

        startAutoSlide() {
            this.stopAutoSlide();

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
            if (this.images.length <= 1) {
                return;
            }

            this.currentIndex =
                (this.currentIndex + 1) %
                this.images.length;
        },

        prevImage() {
            if (this.images.length <= 1) {
                return;
            }

            this.currentIndex =
                (this.currentIndex -
                    1 +
                    this.images.length) %
                this.images.length;
        },

        goToNextImage() {
            if (this.images.length <= 1) {
                return;
            }

            this.stopAutoSlide();
            this.nextImage();
            this.startAutoSlide();
        },

        goToPrevImage() {
            if (this.images.length <= 1) {
                return;
            }

            this.stopAutoSlide();
            this.prevImage();
            this.startAutoSlide();
        },
    }));
});

// --- SOCIAL MEDIA POSTS ---

document.querySelectorAll('[data-video-id]').forEach((wrapper) => {
    const videoId = wrapper.getAttribute('data-video-id');
    const video = document.getElementById(videoId);
    const icon = wrapper.querySelector('.video-icon');

    if (!video || !icon) {
        return;
    }

    wrapper.addEventListener('mouseenter', () => {
        const playPromise = video.play();

        if (playPromise !== undefined) {
            playPromise.catch(() => {
                // Browser dapat menolak autoplay.
            });
        }

        icon.innerHTML = `
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M10 9v6m4-6v6"
            />
        `;
    });

    wrapper.addEventListener('mouseleave', () => {
        video.pause();
        video.currentTime = 0;

        icon.innerHTML = `
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M14.752 11.168l-5.197-3.03A1 1 0 008 9v6a1 1 0 001.555.832l5.197-3.03a1 1 0 000-1.664z"
            />
        `;
    });
});