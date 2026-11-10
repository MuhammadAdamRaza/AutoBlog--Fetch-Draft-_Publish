import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                paper: '#f3f6fb',
                ink: '#17243a',
                rule: '#dce4ef',
                accent: '#3458c8',
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                serif: ['Newsreader', 'Georgia', 'Times New Roman', 'serif'],
            },
        },
    },

    plugins: [forms],
};
