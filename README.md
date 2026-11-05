# Team Jersey Tracker (Season 2026)

A clean, modern, mobile-first web application built with **Vue 3** and **Tailwind CSS** that lets football squad members quickly view their jersey information, shirt numbers, sizing, and payment statuses.

## 🚀 Features

- **Mobile-First Pitch Aesthetic:** Dark forest green palette, bright football green accents, rounded player cards with athletic typography. Designed and verified down to 320px screens with zero horizontal overflow.
- **Dynamic Squad Statistics:** Real-time summary counts calculated directly from player data:
  - **Total Squad:** 28 players
  - **Paid:** 4 players (Green badge)
  - **Unpaid:** 23 players (Amber badge)
  - **Not Confirmed:** 1 player (Gray badge)
  - **Kit Sizes Breakdown:** M = 9, L = 16, XL = 3
- **Instant Search:** Search in real time by player name, shirt number, kit size, or notes (including Khmer annotations). Includes an accessible one-click clear button.
- **Payment Filters:** Touch-friendly filter pill bar (`All Players`, `Paid`, `Unpaid`, `Not Confirmed`) displaying live count badges.
- **All 28 Players Displayed Immediately:** Compact, high-contrast cards showing prominent shirt number, player name, jersey size, payment badge, and special tags (GK, Khmer notes).
- **Zero Backend Required:** Fully client-side with clean static data separation in `src/data/players.js`.

---

## 📁 Project Architecture

```text
├── index.html
├── package.json
├── vite.config.js
├── tailwind.config.js
├── postcss.config.js
└── src/
    ├── main.js
    ├── style.css
    ├── App.vue
    ├── data/
    │   └── players.js         # Initial dataset (28 players)
    └── components/
        ├── Header.vue         # Club title, visual crest, Season 2026
        ├── SummaryStats.vue   # Dynamic summary cards & size pill bar
        ├── SearchBar.vue      # Instant search input with clear button
        ├── PaymentFilter.vue  # Touch-friendly status filter pills
        ├── PlayerCard.vue     # Athletic kit-styled player card
        └── PlayerList.vue     # Responsive grid & empty state
```

---

## 🛠️ Getting Started

### Development Server
```bash
npm run dev
```

### Production Build
```bash
npm run build
```

### Preview Production Build
```bash
npm run preview
```
