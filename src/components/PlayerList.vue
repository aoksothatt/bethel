<template>
  <div class="px-4 py-2" aria-label="Squad Player List">
    <!-- List Subheader / Counter -->
    <div class="flex items-center justify-between mb-2.5 px-0.5 text-xs text-brand-muted font-medium">
      <div class="flex items-center gap-1.5">
        <span class="text-[11px] uppercase tracking-wider font-semibold text-white">
          {{ t('player.roster') }}
        </span>
        <span class="text-white/20">•</span>
        <span class="font-mono text-brand-gold font-bold text-[11px]">{{ players.length }}</span>
        <span v-if="players.length !== totalPlayers"> of {{ totalPlayers }}</span>
        <span>{{ t('stats.players') }}</span>
      </div>

      <button
        v-if="activeFilter !== 'All' || searchQuery"
        type="button"
        @click="$emit('reset-filters')"
        class="text-[11px] text-brand-red font-semibold hover:underline transition-colors cursor-pointer"
      >
        {{ t('filters.clear') }}
      </button>
    </div>

    <!-- Compact Cards Stack -->
    <div v-if="players.length > 0" class="space-y-2">
      <PlayerCard
        v-for="player in players"
        :key="player.id || player.no"
        :player="player"
      />
    </div>

    <!-- Empty State -->
    <div
      v-else
      class="bg-brand-charcoal border border-white/10 rounded-2xl p-8 text-center my-4 shadow-card"
    >
      <div class="w-10 h-10 mx-auto rounded-full bg-[#202020] border border-white/10 flex items-center justify-center text-brand-muted mb-3">
        <svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div>
      <h4 class="text-sm font-bold text-white mb-1">
        {{ t('search.no_players') }}
      </h4>
      <p class="text-xs text-brand-muted mb-4 max-w-xs mx-auto leading-relaxed">
        {{ t('search.no_players_desc') }}
      </p>
      <button
        type="button"
        @click="$emit('reset-filters')"
        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-brand-red text-white font-bold text-xs uppercase tracking-wider hover:bg-brand-red-hover transition-colors shadow-sm cursor-pointer"
      >
        {{ t('search.reset') }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
import PlayerCard from './PlayerCard.vue'

const { t } = useI18n()

defineProps({
  players: {
    type: Array,
    required: true
  },
  totalPlayers: {
    type: Number,
    required: true
  },
  activeFilter: {
    type: String,
    default: 'All'
  },
  searchQuery: {
    type: String,
    default: ''
  }
})

defineEmits(['reset-filters'])
</script>
