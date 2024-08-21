/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './public/**/*.{html,js,php}',
    './public/assets/**/*.{html,js,php}',
    './public/includes/**/*.{html,js,php}',
  ],
  theme: {
    extend: {
      fontFamily: {
        "merriweather_sans": ["Merriweather Sans", "sans-serif"],
        "mulish": ["Mulish", "sans-serif"],
        "cookie": ["Cookie", "cursive"]
      },
    },
  },
  plugins: [],
}

