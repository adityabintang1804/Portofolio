import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.tsx',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                heading: ['Manrope', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                background: 'var(--color-background)',
                surface: 'var(--color-surface)',
                'surface-secondary': 'var(--color-surface-secondary)',
                border: 'var(--color-border)',
                foreground: 'var(--color-foreground)',
                muted: 'var(--color-muted)',
                primary: 'var(--color-primary)',
                'primary-foreground': 'var(--color-primary-foreground)',
                accent: 'var(--color-accent)',
                success: 'var(--color-success)',
                warning: 'var(--color-warning)',
                destructive: 'var(--color-destructive)',
            },
            borderRadius: {
                sm: '2px',
                md: '4px',
                lg: '8px',
                xl: '12px',
                '2xl': '16px',
            },
        },
    },

    plugins: [forms],
};
