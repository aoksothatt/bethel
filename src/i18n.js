import { createI18n } from 'vue-i18n'
import en from './locales/en.json'
import km from './locales/km.json'

const STORAGE_KEY = 'team_jersey_locale'

export function getSavedLocale() {
  const saved = localStorage.getItem(STORAGE_KEY)
  if (saved === 'km' || saved === 'en') {
    return saved
  }
  return 'en'
}

export function saveLocale(locale) {
  localStorage.setItem(STORAGE_KEY, locale)
  document.documentElement.lang = locale
  if (locale === 'km') {
    document.documentElement.classList.add('font-khmer')
  } else {
    document.documentElement.classList.remove('font-khmer')
  }
}

const initialLocale = getSavedLocale()
document.documentElement.lang = initialLocale
if (initialLocale === 'km') {
  document.documentElement.classList.add('font-khmer')
}

const i18n = createI18n({
  legacy: false, // Composition API mode
  locale: initialLocale,
  fallbackLocale: 'en',
  messages: {
    en,
    km
  }
})

export default i18n
