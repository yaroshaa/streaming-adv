const colors = require('tailwindcss/colors');

module.exports = {
    purge: [],
    darkMode: false, // or 'media' or 'class'
    theme: {
        extend: {},
        colors: {
            debug: '#00ffc2',
            core: '#102a43',
            container: '#2a2a2a',
            ...colors,
        },
    },
    variants: {
        extend: {},
    },
    plugins: [],
};
