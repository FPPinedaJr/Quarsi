/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './public/admin/**/*.{html,js,php}',
    './public/officer/**/*.{html,js,php}',
    './public/student/**/*.{html,js,php}',
    './public/sign_in/**/*.{html,js,php}',
    './public/__assets__/**/*.{html,js,php}',
    './public/__includes__/**/*.{html,js,php}',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}

