<template>
  <nav
    class="fixed bottom-0 left-0 right-0 z-40 bg-brand-charcoal/95 backdrop-blur-md border-t border-white/10 shadow-[0_-4px_20px_rgba(0,0,0,0.6)]"
    aria-label="Bottom Navigation"
  >
    <div class="max-w-[440px] mx-auto px-4 py-1.5 flex items-center justify-around">
      <!-- Squad Tab -->
      <button
        type="button"
        @click="$emit('update:currentTab', 'squad')"
        :class="[
          'flex flex-col items-center justify-center gap-1 py-1 px-4 rounded-xl transition-all duration-150 min-w-[70px] cursor-pointer',
          currentTab === 'squad'
            ? 'text-white font-bold'
            : 'text-brand-dark-muted hover:text-brand-muted font-medium'
        ]"
        :aria-selected="currentTab === 'squad'"
        role="tab"
      >
        <div class="relative">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <span
            v-if="currentTab === 'squad'"
            class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1.5 h-1.5 rounded-full bg-brand-red"
          ></span>
        </div>
        <span class="text-[11px] tracking-wide">{{ t('nav.squad') }}</span>
      </button>

      <!-- My Kit Tab -->
      <button
        type="button"
        @click="$emit('update:currentTab', 'my-kit')"
        :class="[
          'flex flex-col items-center justify-center gap-1 py-1 px-4 rounded-xl transition-all duration-150 min-w-[70px] cursor-pointer',
          currentTab === 'my-kit'
            ? 'text-white font-bold'
            : 'text-brand-dark-muted hover:text-brand-muted font-medium'
        ]"
        :aria-selected="currentTab === 'my-kit'"
        role="tab"
      >
        <div class="relative">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"></path>
          </svg>
          <span
            v-if="currentTab === 'my-kit'"
            class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1.5 h-1.5 rounded-full bg-brand-red"
          ></span>
        </div>
        <span class="text-[11px] tracking-wide">{{ t('nav.my_kit') }}</span>
      </button>

      <!-- Payments Tab -->
      <button
        type="button"
        @click="$emit('update:currentTab', 'payments')"
        :class="[
          'flex flex-col items-center justify-center gap-1 py-1 px-4 rounded-xl transition-all duration-150 min-w-[70px] cursor-pointer',
          currentTab === 'payments'
            ? 'text-white font-bold'
            : 'text-brand-dark-muted hover:text-brand-muted font-medium'
        ]"
        :aria-selected="currentTab === 'payments'"
        role="tab"
      >
        <div class="relative">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span
            v-if="currentTab === 'payments'"
            class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1.5 h-1.5 rounded-full bg-brand-red"
          ></span>
        </div>
        <span class="text-[11px] tracking-wide">{{ t('nav.payments') }}</span>
      </button>
    </div>
  </nav>
</template>

<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps({
  currentTab: {
    type: String,
    required: true
  }
})

defineEmits(['update:currentTab'])
</script>
