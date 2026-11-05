<template>
  <div class="px-4 py-4" aria-label="Payments Overview">
    <!-- Header -->
    <div class="mb-4">
      <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-brand-charcoal border border-brand-gold/30 text-[10px] font-bold tracking-wider text-brand-gold uppercase mb-1.5">
        <span class="w-1.5 h-1.5 rounded-full bg-brand-red"></span>
        {{ t('payments.badge') }}
      </div>
      <h2 class="text-xl sm:text-2xl font-black tracking-tight text-white leading-tight uppercase font-display">
        {{ t('payments.title') }}
      </h2>
      <p class="text-xs text-brand-muted mt-0.5 font-medium leading-relaxed">
        {{ t('payments.subtitle') }}
      </p>
    </div>

    <!-- Collection Progress Card -->
    <div class="bg-brand-charcoal rounded-2xl p-4 border border-white/10 shadow-card mb-4">
      <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-semibold text-brand-muted uppercase tracking-wider">
          {{ t('payments.progress') }}
        </span>
        <span class="text-sm font-mono font-bold text-brand-gold">
          {{ collectionPercentage }}%
        </span>
      </div>

      <!-- Segmented Bar -->
      <div class="w-full h-2 rounded-full bg-[#262626] flex overflow-hidden p-0.5 gap-0.5 mb-3">
        <div
          class="h-full bg-brand-gold rounded-l-full transition-all duration-500"
          :style="{ width: `${collectionPercentage}%` }"
        ></div>
        <div
          class="h-full bg-brand-red transition-all duration-500"
          :style="{ width: `${total ? (unpaidCount / total) * 100 : 0}%` }"
        ></div>
        <div
          class="h-full bg-neutral-600 rounded-r-full transition-all duration-500"
          :style="{ width: `${total ? (notConfirmedCount / total) * 100 : 0}%` }"
        ></div>
      </div>

      <!-- Breakdown Counts -->
      <div class="grid grid-cols-3 gap-2 text-center pt-2.5 border-t border-white/10 text-xs font-mono">
        <div>
          <span class="block text-[10px] text-brand-gold font-sans uppercase font-semibold">{{ t('filters.paid') }}</span>
          <strong class="text-base text-brand-gold font-bold">{{ paidCount }}</strong>
          <span class="text-[10px] text-brand-muted block font-sans">{{ t('payments.kits') }}</span>
        </div>
        <div>
          <span class="block text-[10px] text-brand-red font-sans uppercase font-semibold">{{ t('filters.unpaid') }}</span>
          <strong class="text-base text-brand-red font-bold">{{ unpaidCount }}</strong>
          <span class="text-[10px] text-brand-muted block font-sans">{{ t('payments.pending') }}</span>
        </div>
        <div>
          <span class="block text-[10px] text-neutral-300 font-sans uppercase font-semibold">{{ t('filters.not_confirmed') }}</span>
          <strong class="text-base text-neutral-200 font-bold">{{ notConfirmedCount }}</strong>
          <span class="text-[10px] text-brand-muted block font-sans">{{ t('payments.review') }}</span>
        </div>
      </div>
    </div>

    <!-- Sub-Filter Pills -->
    <div class="flex items-center gap-1.5 mb-3 overflow-x-auto no-scrollbar pb-1">
      <button
        type="button"
        @click="subFilter = 'Paid'"
        :class="[
          'px-3 py-1.5 rounded-full text-xs font-semibold tracking-wide transition-colors cursor-pointer shadow-xs flex-shrink-0 border',
          subFilter === 'Paid'
            ? 'bg-brand-gold/20 text-brand-gold border-brand-gold/60 font-bold'
            : 'bg-brand-charcoal text-brand-muted hover:text-white border-white/10'
        ]"
      >
        {{ t('filters.paid') }} ({{ paidCount }})
      </button>
      <button
        type="button"
        @click="subFilter = 'Unpaid'"
        :class="[
          'px-3 py-1.5 rounded-full text-xs font-semibold tracking-wide transition-colors cursor-pointer shadow-xs flex-shrink-0 border',
          subFilter === 'Unpaid'
            ? 'bg-brand-red/20 text-brand-red border-brand-red/60 font-bold'
            : 'bg-brand-charcoal text-brand-muted hover:text-white border-white/10'
        ]"
      >
        {{ t('filters.unpaid') }} ({{ unpaidCount }})
      </button>
      <button
        type="button"
        @click="subFilter = 'Not Confirmed'"
        :class="[
          'px-3 py-1.5 rounded-full text-xs font-semibold tracking-wide transition-colors cursor-pointer shadow-xs flex-shrink-0 border',
          subFilter === 'Not Confirmed'
            ? 'bg-neutral-800 text-neutral-200 border-neutral-600 font-bold'
            : 'bg-brand-charcoal text-brand-muted hover:text-white border-white/10'
        ]"
      >
        {{ t('filters.not_confirmed') }} ({{ notConfirmedCount }})
      </button>
    </div>

    <!-- Players in this payment filter -->
    <div class="space-y-1.5">
      <div
        v-for="player in filteredList"
        :key="player.id || player.no"
        class="bg-brand-charcoal hover:bg-brand-charcoal-light border border-white/10 hover:border-brand-gold/30 rounded-xl px-3 py-2 flex items-center justify-between shadow-xs transition-colors"
      >
        <div class="flex items-center gap-2.5">
          <span class="w-7 h-7 rounded-lg bg-[#222222] border border-brand-gold/30 text-brand-gold flex items-center justify-center font-mono font-bold text-xs">
            #{{ player.shirt_number ?? player.number }}
          </span>
          <div>
            <h4 class="text-xs font-bold text-white">{{ player.name }}</h4>
            <span class="text-[10px] text-brand-muted">{{ t('player.jersey_size') }} {{ player.jersey_size || player.size }}</span>
          </div>
        </div>

        <span
          :class="[
            'text-[10px] font-mono font-bold px-2 py-0.5 rounded-full border',
            normalizeStatus(player) === 'Paid'
              ? 'bg-brand-gold/15 text-brand-gold border-brand-gold/40'
              : normalizeStatus(player) === 'Unpaid'
              ? 'bg-brand-red/15 text-brand-red border-brand-red/40'
              : 'bg-neutral-800 text-neutral-300 border-neutral-700'
          ]"
        >
          {{ normalizeStatusLabel(player) }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  players: {
    type: Array,
    required: true
  }
})

const subFilter = ref('Paid')

const normalizeStatus = (player) => {
  const s = player.payment_status || player.status
  if (s === 'paid' || s === 'Paid') return 'Paid'
  if (s === 'not_confirmed' || s === 'Not Confirmed') return 'Not Confirmed'
  return 'Unpaid'
}

const normalizeStatusLabel = (player) => {
  const s = normalizeStatus(player)
  if (s === 'Paid') return t('filters.paid')
  if (s === 'Unpaid') return t('filters.unpaid')
  return t('filters.not_confirmed')
}

const total = computed(() => props.players.length)
const paidCount = computed(() => props.players.filter((p) => normalizeStatus(p) === 'Paid').length)
const unpaidCount = computed(() => props.players.filter((p) => normalizeStatus(p) === 'Unpaid').length)
const notConfirmedCount = computed(() => props.players.filter((p) => normalizeStatus(p) === 'Not Confirmed').length)

const collectionPercentage = computed(() => {
  if (!total.value) return 0
  return ((paidCount.value / total.value) * 100).toFixed(1)
})

const filteredList = computed(() => {
  return props.players.filter((p) => normalizeStatus(p) === subFilter.value)
})
</script>
