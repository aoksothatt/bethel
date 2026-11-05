<template>
  <header class="sticky top-0 z-30 bg-[#090909]/95 backdrop-blur-md border-b border-white/10 px-3.5 sm:px-4 py-2.5">
    <div class="flex items-center justify-between gap-2 sm:gap-3">
      <!-- Left: Emblem & Title -->
      <div class="flex items-center gap-2 sm:gap-2.5 min-w-0">
        <!-- Official Bethel High School Crest Logo -->
        <div class="w-8 h-8 rounded-full overflow-hidden flex items-center justify-center flex-shrink-0 ring-1 ring-brand-gold/30 bg-black shadow-sm">
          <img
            src="/images/bethel-logo.jpg"
            alt="Bethel High School Crest"
            class="w-full h-full object-contain"
            loading="eager"
          />
        </div>

        <div class="min-w-0">
          <h1 class="text-xs sm:text-sm font-bold tracking-tight text-white leading-tight truncate flex items-center gap-1.5">
            <span>{{ t('app.title') }}</span>
          </h1>
          <p class="text-[10px] sm:text-[11px] text-brand-muted font-medium tracking-wide truncate">
            {{ t('app.subtitle') }}
          </p>
        </div>
      </div>

      <!-- Right: Language Switcher, Season & Actions -->
      <div class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
        <!-- Language Switcher -->
        <LanguageSwitcher />

        <!-- Season 2026 Badge -->
        <div class="hidden xs:inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-brand-charcoal border border-brand-gold/30 text-[10px] font-semibold text-brand-gold">
          <span>{{ t('app.season') }}</span>
          <strong>2026</strong>
        </div>

        <slot name="right-action"></slot>
      </div>
    </div>
  </header>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
import LanguageSwitcher from './LanguageSwitcher.vue'

const { t } = useI18n()
</script>
