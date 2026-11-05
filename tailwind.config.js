/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          black: '#090909',
          charcoal: '#171717',
          'charcoal-light': '#212121',
          'charcoal-hover': '#262626',
          white: '#FFFFFF',
          red: '#D52E38',
          'red-hover': '#B8232C',
          'red-dark': '#8B181E',
          'red-soft': 'rgba(213, 46, 56, 0.15)',
          'red-border': 'rgba(213, 46, 56, 0.35)',
          gold: '#D5AC50',
          'gold-hover': '#BF9940',
          'gold-light': '#E5C478',
          'gold-soft': 'rgba(213, 172, 80, 0.15)',
          'gold-border': 'rgba(213, 172, 80, 0.35)',
          muted: '#A3A3A3',
          'dark-muted': '#737373',
          border: 'rgba(255, 255, 255, 0.08)',
          'border-strong': 'rgba(255, 255, 255, 0.16)',
        },
      },
      fontFamily: {
        sans: ['Inter', 'Kantumruy Pro', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
        khmer: ['Kantumruy Pro', 'Inter', 'sans-serif'],
        display: ['Space Grotesk', 'Kantumruy Pro', 'Inter', 'sans-serif'],
        mono: ['JetBrains Mono', 'ui-monospace', 'monospace'],
      },
      boxShadow: {
        card: '0 4px 20px -2px rgba(0, 0, 0, 0.5), 0 2px 6px -1px rgba(0, 0, 0, 0.3)',
        'card-hover': '0 8px 24px -4px rgba(213, 46, 56, 0.15), 0 4px 10px -2px rgba(0, 0, 0, 0.4)',
        'glow-red': '0 0 25px rgba(213, 46, 56, 0.3)',
        'glow-gold': '0 0 25px rgba(213, 172, 80, 0.3)',
      }
    },
  },
  plugins: [],
}
