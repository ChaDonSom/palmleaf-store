module.exports = {
    darkMode: 'class',
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./vendor/lunarphp/stripe-payments/resources/views/**/*.blade.php",
    ],
    safelist: [
        // Yellow colors (secondary-plus-layer)
        'bg-yellow-400',
        'bg-yellow-500',
        'bg-yellow-600',
        'border-yellow-400',
        'border-yellow-500',
        'border-yellow-600',
        'hover:bg-yellow-400',
        'hover:bg-yellow-500',
        'hover:bg-yellow-600',
        'focus:ring-yellow-600',
        'focus:border-yellow-600',
        // Blue colors (primary-layer/storefront)
        'bg-blue-50',
        'bg-blue-100',
        'bg-blue-300',
        'bg-blue-400',
        'bg-blue-500',
        'bg-blue-600',
        'border-blue-100',
        'border-blue-300',
        'border-blue-400',
        'border-blue-500',
        'hover:bg-blue-50',
        'hover:bg-blue-400',
        'hover:bg-blue-500',
        'hover:bg-blue-600',
        'focus:ring-blue-400',
        'focus:border-blue-400',
        'text-blue-300',
        'text-blue-400',
        'text-blue-500',
        'text-blue-600',
        'ring-blue-100',
    ],
    theme: {
        extend: {
            keyframes: {
                "gentle-fade-in-3deg": {
                    "0%": {
                        opacity: "0.85",
                        transform: "scale(0.92) rotate(-3deg)",
                    },
                    "100%": {
                        opacity: "1",
                        transform: "scale(1) rotate(-3deg)",
                    },
                },
            },
            animation: {
                "gentle-fade-in-3deg": "gentle-fade-in-3deg 0.8s ease-out",
            },
        },
    },
    plugins: [require("@tailwindcss/forms")],
};
