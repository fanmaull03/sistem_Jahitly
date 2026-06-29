import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        'page-enter',
        'hover-lift',
        'press-scale',
        'slide-in-right',
        'modal-backdrop-enter',
        'modal-content-enter',
        'row-enter',
    ],

    theme: {
        extend: {
            colors: {
                ink: '#1A1A2E',
                surface: '#F7F5F2',
                primary: {
                    DEFAULT: '#2B4FFF',
                    hover: '#1A3DE0',
                    light: '#EEF1FF',
                },
                accent: {
                    DEFAULT: '#FF6B35',
                    soft: '#FFF1EB',
                },
                muted: '#6B7280',
                border: '#E8E4DF',
                sidebar: '#0F1729',
            },
            fontFamily: {
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
                display: ['Playfair Display', ...defaultTheme.fontFamily.serif],
            },
            borderRadius: {
                '3xl': '1.5rem',
            },
        },
    },

    plugins: [forms],
};
