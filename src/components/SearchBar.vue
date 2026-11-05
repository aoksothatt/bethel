<template>
  <div class="px-4 py-1.5">
    <div class="relative">
      <label for="player-search" class="sr-only">{{ t('search.placeholder') }}</label>

      <!-- Leading Search Icon -->
      <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-dark-muted">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div>

      <!-- Search Input -->
      <input
        id="player-search"
        ref="inputRef"
        type="search"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :placeholder="t('search.placeholder')"
        autocomplete="off"
        autocapitalize="none"
        enterkeyhint="search"
        class="w-full h-10 pl-9 pr-9 text-xs sm:text-sm bg-brand-charcoal text-white placeholder-brand-dark-muted rounded-xl border border-white/10 focus:outline-none focus:border-brand-red focus:ring-1 focus:ring-brand-red transition-colors shadow-sm"
      />

      <!-- Clear Button -->
      <button
        v-if="modelValue"
        type="button"
        @click="clearInput"
        class="absolute inset-y-0 right-0 pr-3 flex items-center text-brand-dark-muted hover:text-white transition-colors cursor-pointer"
        :aria-label="t('search.clear')"
      >
        <span class="w-4 h-4 rounded-full bg-[#262626] flex items-center justify-center text-[10px] font-bold text-white">
          ✕
        </span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:modelValue'])
const inputRef = ref(null)

const clearInput = () => {
  emit('update:modelValue', '')
  if (inputRef.value) {
    inputRef.value.focus()
  }
}
</script>
