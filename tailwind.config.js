import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                page: 'rgb(243 247 252)',
                surface: 'rgb(255 255 255)',
                'surface-subtle': 'rgb(248 250 253)',
                'surface-selected': 'rgb(232 242 255)',
                'text-primary': 'rgb(20 38 74)',
                'text-secondary': 'rgb(100 116 139)',
                'text-muted': 'rgb(138 154 176)',
                'text-inverse': 'rgb(255 255 255)',
                primary: {
                    DEFAULT: 'rgb(57 136 245)',
                    50: '#EFF6FF',
                    100: '#DBEAFE',
                    200: '#BFDBFE',
                    300: '#93C5FD',
                    400: '#60A5FA',
                    500: 'rgb(57 136 245)',
                    600: '#2563EB',
                    700: '#1D4ED8',
                    hover: 'rgb(38 117 229)',
                    soft: 'rgb(231 241 255)',
                },
                'border-focus': 'rgb(120 174 255)',
                border: 'rgb(230 237 245)',
                'border-strong': 'rgb(215 226 239)',
                success: {
                    DEFAULT: 'rgb(29 170 121)',
                    soft: 'rgb(228 248 239)',
                },
                warning: {
                    DEFAULT: 'rgb(217 154 25)',
                    soft: 'rgb(255 244 216)',
                },
                danger: {
                    DEFAULT: 'rgb(230 79 103)',
                    soft: 'rgb(255 233 237)',
                },
                info: {
                    DEFAULT: 'rgb(57 136 245)',
                    soft: 'rgb(231 241 255)',
                },
                'category-blue': 'rgb(78 145 247)',
                'category-green': 'rgb(53 196 154)',
                'category-yellow': 'rgb(242 198 75)',
                'category-red': 'rgb(239 113 128)',
                'category-purple': 'rgb(169 133 245)',
            },
            borderRadius: {
                sm: '6px',
                DEFAULT: '8px',
                md: '10px',
                lg: '14px',
                xl: '18px',
                '2xl': '22px',
                '3xl': '28px',
                pill: '999px',
            },
            boxShadow: {
                card: '0 2px 10px rgba(35, 72, 120, 0.05)',
                'card-hover': '0 8px 24px rgba(35, 72, 120, 0.08)',
                popover: '0 8px 28px rgba(35, 72, 120, 0.12)',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [forms, typography],
};
