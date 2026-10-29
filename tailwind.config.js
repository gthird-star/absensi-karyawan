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
                page: '#F3F7FC',
                surface: '#FFFFFF',
                'surface-subtle': '#F8FAFD',
                'surface-selected': '#E8F2FF',
                'text-primary': '#14264A',
                'text-secondary': '#64748B',
                'text-muted': '#8A9AB0',
                'text-inverse': '#FFFFFF',
                primary: {
                    DEFAULT: '#3988F5',
                    50: '#EFF6FF',
                    100: '#DBEAFE',
                    200: '#BFDBFE',
                    300: '#93C5FD',
                    400: '#60A5FA',
                    500: '#3988F5',
                    600: '#2563EB',
                    700: '#1D4ED8',
                    hover: '#2675E5',
                    soft: '#E7F1FF',
                },
                'border-focus': '#78AEFF',
                border: '#E6EDF5',
                'border-strong': '#D7E2EF',
                success: {
                    DEFAULT: '#1DAA79',
                    soft: '#E4F8EF',
                },
                warning: {
                    DEFAULT: '#D99A19',
                    soft: '#FFF4D8',
                },
                danger: {
                    DEFAULT: '#E64F67',
                    soft: '#FFE9ED',
                },
                info: {
                    DEFAULT: '#3988F5',
                    soft: '#E7F1FF',
                },
                'category-blue': '#4E91F7',
                'category-green': '#35C49A',
                'category-yellow': '#F2C64B',
                'category-red': '#EF7180',
                'category-purple': '#A985F5',
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
