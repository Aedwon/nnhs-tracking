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
            fontFamily: {
                sans: ['"DM Sans"', ...defaultTheme.fontFamily.sans],
                display: ['Outfit', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                eggshell: '#FAFAFA',
                navy: '#0B132B',
                crimson: '#991B1B',
            },
        },
    },

    plugins: [forms],
};
