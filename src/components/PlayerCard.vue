<template>
  <article
    class="bg-brand-charcoal hover:bg-brand-charcoal-light border border-white/10 hover:border-brand-gold/30 rounded-xl px-3 py-2.5 transition-all shadow-card flex items-center justify-between gap-2.5 select-none"
    :aria-label="`Player ${player.name}, Number ${formattedNumber}, Size ${player.jersey_size || player.size}, Status ${statusConfig.label}`"
  >
    <!-- Left: Number Container + Info -->
    <div class="flex items-center gap-2.5 min-w-0">
      <!-- Shirt Number Container (Charcoal Squircle with Gold Accent) -->
      <div class="w-9 h-9 rounded-lg bg-[#202020] border border-brand-gold/40 text-brand-gold flex items-center justify-center font-mono font-bold text-sm tracking-tight flex-shrink-0 shadow-sm">
        {{ formattedNumber }}
      </div>

      <!-- Player Name & Size Info -->
      <div class="min-w-0">
        <!-- Player Name (Bold White, Preserved exactly as in database) -->
        <h3 class="text-sm font-bold text-white tracking-tight truncate leading-tight">
          {{ player.name }}
        </h3>

        <!-- Subtitle: Jersey Size & Optional Notes -->
        <div class="flex items-center gap-1.5 mt-0.5 text-[11px] text-brand-muted font-medium">
          <span>{{ t('player.jersey_size') }}: <strong class="text-white font-semibold">{{ player.jersey_size || player.size }}</strong></span>

          <!-- Optional GK or Khmer Note Tag -->
          <template v-if="player.notes">
            <span class="text-white/20">•</span>
            <span
              v-if="player.notes === 'GK'"
              class="inline-flex items-center px-1.5 py-0.2 rounded bg-brand-gold/15 text-brand-gold border border-brand-gold/30 text-[10px] font-bold tracking-wider font-mono"
            >
              {{ t('player.gk') }}
            </span>
            <span
              v-else
              class="inline-flex items-center px-1.5 py-0.2 rounded bg-[#242424] text-brand-muted border border-white/10 text-[10px] truncate max-w-[120px]"
              :title="player.notes"
            >
              {{ player.notes }}
            </span>
          </template>
        </div>
      </div>
    </div>

    <!-- Right: Payment Status Badge -->
    <div class="flex-shrink-0">
      <span
        :class="[
          'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border shadow-xs',
          statusConfig.badgeClass
        ]"
      >
        <span class="w-1.5 h-1.5 rounded-full" :class="statusConfig.dotClass"></span>
        {{ statusConfig.label }}
      </span>
    </div>
  </article>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  player: {
    type: Object,
    required: true
  }
})

// Format number with leading zero for single digits (e.g. 8 -> '08', 0 -> '00')
const formattedNumber = computed(() => {
  const num = props.player.shirt_number ?? props.player.number
  if (typeof num === 'number' && num < 10) {
    return `0${num}`
  }
  return String(num ?? '')
})

const statusConfig = computed(() => {
  const s = props.player.payment_status || props.player.status
  const normalized = String(s).toLowerCase().replace(' ', '_')

  switch (normalized) {
    case 'paid':
      return {
        label: t('filters.paid'),
        badgeClass: 'bg-brand-gold/15 text-brand-gold border-brand-gold/40',
        dotClass: 'bg-brand-gold'
      }
    case 'unpaid':
      return {
        label: t('filters.unpaid'),
        badgeClass: 'bg-brand-red/15 text-brand-red border-brand-red/40',
        dotClass: 'bg-brand-red'
      }
    case 'not_confirmed':
    default:
      return {
        label: t('filters.not_confirmed'),
        badgeClass: 'bg-neutral-800 text-neutral-300 border-neutral-700',
        dotClass: 'bg-neutral-400'
      }
  }
})
</script>
