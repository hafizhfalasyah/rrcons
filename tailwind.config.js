/**
 * Tailwind untuk halaman publik (resources/views/front).
 *
 * Sebelumnya dimuat lewat Play CDN (cdn.tailwindcss.com, v3.4.17) yang meng-compile CSS
 * di browser setiap kali halaman dibuka. Sekarang CSS dikompilasi sekali dengan versi yang
 * sama & konfigurasi default yang sama, jadi tampilan identik tetapi jauh lebih ringan.
 *
 * Setelah mengubah class di view/JS publik, jalankan:  npm run build:front-css
 */
module.exports = {
    content: [
        './resources/views/front/**/*.blade.php',
        './public/front/js/**/*.js',
        './app/Helpers/**/*.php',
    ],
    theme: {
        extend: {},
    },
    plugins: [],
};
