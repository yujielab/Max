import defaultTheme from 'tailwindcss/defaultTheme';

export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                'icloud-slate': '#0f172a',
                'icloud-blue': '#38bdf8',
                'icloud-teal': '#5eead4',
                'icloud-purple': '#a78bfa',
                'icloud-pink': '#f472b6',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [],
};
