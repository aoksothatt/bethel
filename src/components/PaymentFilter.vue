<template>
  <div class="px-4 py-1.5" role="group" aria-label="Filter players by payment status">
    <!-- Horizontally scrollable filter pills -->
    <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar -mx-4 px-4 pb-1">
      <button
        v-for="filter in filterOptions"
        :key="filter.value"
        type="button"
        @click="$emit('update:modelValue', filter.value)"
        :aria-pressed="modelValue === filter.value"
        :class="[
          'flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold transition-all duration-150 select-none min-h-[34px] shadow-sm cursor-pointer border',
          modelValue === filter.value
            ? filter.activeClass
            : 'bg-brand-charcoal text-brand-muted border-white/10 hover:text-white hover:bg-[#222222]'
        ]"
      >
        <span>{{ filter.label }}</span>
        <!-- Count badge inside pill -->
        <span
          class="px-1.5 py-0.2 rounded-full text-[10px] font-mono font-bold"
          :class="modelValue === filter.value ? filter.badgeActiveClass : 'bg-[#262626] text-brand-muted'"
        >
          {{ filter.count }}
        </span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  modelValue: {
    type: String,
    default: 'All'
  },
  counts: {
    type: Object,
    default: () => ({
      All: 28,
      Paid: 4,
      Unpaid: 23,
      'Not Confirmed': 1
    })
  }
})

defineEmits(['update:modelValue'])

const filterOptions = computed(() => [
  {
    value: 'All',
    label: t('filters.all_short'),
    count: props.counts.All ?? 0,
    activeClass: 'bg-white text-black border-white font-bold',
    badgeActiveClass: 'bg-black text-white'
  },
  {
    value: 'Paid',
    label: t('filters.paid'),
    count: props.counts.Paid ?? 0,
    activeClass: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/60 font-bold',
    badgeActiveClass: 'bg-emerald-500 text-black'
  },
  {
    value: '50% Paid',
    label: t('filters.half_paid'),
    count: props.counts['50% Paid'] ?? props.counts.halfPaid ?? 0,
    activeClass: 'bg-amber-500/20 text-amber-400 border-amber-500/60 font-bold',
    badgeActiveClass: 'bg-amber-500 text-black'
  },
  {
    value: 'Unpaid',
    label: t('filters.unpaid'),
    count: props.counts.Unpaid ?? 0,
    activeClass: 'bg-brand-red/20 text-brand-red border-brand-red/60 font-bold',
    badgeActiveClass: 'bg-brand-red text-white'
  },
  {
    value: 'Not Confirmed',
    label: t('filters.not_confirmed'),
    count: props.counts['Not Confirmed'] ?? 0,
    activeClass: 'bg-neutral-800 text-neutral-200 border-neutral-600 font-bold',
    badgeActiveClass: 'bg-neutral-700 text-white'
  }
])
</script>
