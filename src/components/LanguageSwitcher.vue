<template>
  <div
    class="inline-flex items-center bg-brand-charcoal p-0.5 rounded-full border border-white/10 shadow-xs select-none"
    role="group"
    aria-label="Language Selector"
  >
    <!-- English Button -->
    <button
      type="button"
      @click="switchLocale('en')"
      :aria-pressed="currentLocale === 'en'"
      :class="[
        'px-2 py-0.5 rounded-full text-[11px] font-semibold transition-all duration-150 flex items-center gap-1 cursor-pointer',
        currentLocale === 'en'
          ? 'bg-brand-red text-white shadow-xs font-bold'
          : 'text-brand-muted hover:text-white'
      ]"
      title="Switch to English"
    >
      <span class="text-xs">🇬🇧</span>
      <span class="font-sans">EN</span>
    </button>

    <!-- Khmer Button -->
    <button
      type="button"
      @click="switchLocale('km')"
      :aria-pressed="currentLocale === 'km'"
      :class="[
        'px-2 py-0.5 rounded-full text-[11px] font-semibold transition-all duration-150 flex items-center gap-1 cursor-pointer',
        currentLocale === 'km'
          ? 'bg-brand-red text-white shadow-xs font-bold'
          : 'text-brand-muted hover:text-white'
      ]"
      title="ប្តូរទៅជាភាសាខ្មែរ"
    >
      <span class="text-xs">🇰🇭</span>
      <span class="font-khmer text-[11px]">ខ្មែរ</span>
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { saveLocale } from '../i18n'

const { locale } = useI18n()

const currentLocale = computed(() => locale.value)

const switchLocale = (newLocale) => {
  if (locale.value !== newLocale) {
    locale.value = newLocale
    saveLocale(newLocale)
  }
}
</script>
