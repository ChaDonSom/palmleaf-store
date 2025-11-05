module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./vendor/lunarphp/stripe-payments/resources/views/**/*.blade.php",
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
