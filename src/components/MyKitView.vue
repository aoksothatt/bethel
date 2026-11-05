<template>
  <div class="px-4 py-4" aria-label="My Kit View">
    <!-- Header -->
    <div class="mb-4">
      <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-brand-charcoal border border-brand-gold/30 text-[10px] font-bold tracking-wider text-brand-gold uppercase mb-1.5">
        <span class="w-1.5 h-1.5 rounded-full bg-brand-red"></span>
        {{ t('my_kit.badge') }}
      </div>
      <h2 class="text-xl sm:text-2xl font-black tracking-tight text-white leading-tight uppercase font-display">
        {{ t('my_kit.title') }}
      </h2>
      <p class="text-xs text-brand-muted mt-0.5 font-medium leading-relaxed">
        {{ t('my_kit.subtitle') }}
      </p>
    </div>

    <!-- Active Selected Player View -->
    <div v-if="selectedPlayer" class="space-y-3.5">
      <!-- Premium Jersey Showcase Card -->
      <div class="relative bg-gradient-to-b from-[#1c1c1f] via-[#141416] to-[#0d0d0f] text-white rounded-2xl p-4 sm:p-5 border border-brand-gold/40 shadow-card overflow-hidden">
        <!-- Subtle Background Glow & Crest Watermark -->
        <div class="absolute -top-10 -right-10 w-44 h-44 opacity-[0.06] pointer-events-none select-none">
          <img src="/images/bethel-logo.jpg" alt="" class="w-full h-full object-contain filter grayscale contrast-125" />
        </div>

        <!-- Top Row: Team Emblem & Kit Type Badge -->
        <div class="flex items-center justify-between text-xs text-white/70 font-mono mb-3 relative z-10">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-full overflow-hidden ring-1 ring-brand-gold/50 bg-black flex-shrink-0">
              <img src="/images/bethel-logo.jpg" alt="Bethel High School" class="w-full h-full object-contain" />
            </div>
            <div>
              <span class="font-bold tracking-wider text-white text-[11px] sm:text-xs block leading-tight">BETHEL HIGH SCHOOL</span>
              <span class="text-[9px] text-brand-muted tracking-widest uppercase">FC BETHEL SOUL</span>
            </div>
          </div>
          <span
            :class="[
              'px-2 py-0.5 rounded-full text-[10px] font-bold border tracking-wider',
              isGoalkeeper
                ? 'bg-neutral-800 text-neutral-200 border-neutral-600'
                : 'bg-brand-charcoal text-brand-gold border-brand-gold/30'
            ]"
          >
            {{ isGoalkeeper ? t('my_kit.gk_kit') : t('my_kit.outfield_kit') }}
          </span>
        </div>

        <!-- View Controls (Back vs Front Toggle) -->
        <div class="flex items-center justify-center relative z-10 my-2">
          <div class="inline-flex p-1 rounded-xl bg-black/60 border border-white/10 backdrop-blur-sm shadow-inner">
            <button
              type="button"
              @click="activeJerseyView = 'back'"
              :class="[
                'px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer',
                activeJerseyView === 'back'
                  ? 'bg-brand-gold text-black shadow-sm font-black'
                  : 'text-brand-muted hover:text-white'
              ]"
            >
              <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
              {{ t('my_kit.back_view') }}
            </button>
            <button
              type="button"
              @click="activeJerseyView = 'front'"
              :class="[
                'px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer',
                activeJerseyView === 'front'
                  ? 'bg-brand-gold text-black shadow-sm font-black'
                  : 'text-brand-muted hover:text-white'
              ]"
            >
              <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
              </svg>
              {{ t('my_kit.front_view') }}
            </button>
          </div>
        </div>

        <!-- Realistic Football Jersey Display -->
        <div class="relative z-10 my-3 py-1">
          <TeamJersey
            :number="selectedPlayer.shirt_number ?? selectedPlayer.number"
            :name="selectedPlayer.name"
            :view="activeJerseyView"
            :is-gk="isGoalkeeper"
          />
        </div>

        <!-- Design Sheet Toggle Link -->
        <div class="text-center relative z-10 pt-1">
          <button
            type="button"
            @click="showDesignSheet = !showDesignSheet"
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 text-[11px] font-semibold text-brand-muted hover:text-white transition-colors cursor-pointer"
          >
            <svg class="w-3.5 h-3.5 text-brand-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
              <circle cx="8.5" cy="8.5" r="1.5"></circle>
              <polyline points="21 15 16 10 5 21"></polyline>
            </svg>
            <span>{{ showDesignSheet ? t('my_kit.hide_sheet') : t('my_kit.view_sheet') }}</span>
          </button>

          <!-- Expandable Design Sheet Preview -->
          <div v-if="showDesignSheet" class="mt-3 p-2 rounded-xl bg-black/80 border border-brand-gold/30 text-left">
            <img
              src="/images/team-jersey-reference.jpg"
              alt="Official Bethel High School Jersey Design Reference"
              class="w-full h-auto rounded-lg object-contain max-h-[440px] mx-auto border border-white/10"
              loading="lazy"
            />
            <p class="text-[10px] text-brand-muted text-center mt-2 font-mono">
              Bethel High School Official Kit Production Sheet · DE Sport AL5582
            </p>
          </div>
        </div>

        <!-- Status & Sizing Details Row -->
        <div class="mt-4 pt-3.5 border-t border-white/10 flex items-center justify-around text-xs relative z-10 font-mono">
          <div>
            <span class="block text-[10px] text-brand-muted uppercase tracking-wider font-sans">{{ t('my_kit.kit_size') }}</span>
            <span class="font-bold text-white text-base">{{ selectedPlayer.jersey_size || selectedPlayer.size }}</span>
          </div>

          <div class="w-px h-6 bg-white/10"></div>

          <div>
            <span class="block text-[10px] text-brand-muted uppercase tracking-wider font-sans">{{ t('my_kit.payment') }}</span>
            <span
              :class="[
                'font-bold text-xs uppercase px-2 py-0.5 rounded-full inline-block mt-0.5 border',
                paymentBadgeConfig.badgeClass
              ]"
            >
              {{ statusLabel }}
            </span>
          </div>

          <div v-if="selectedPlayer.notes" class="w-px h-6 bg-white/10"></div>

          <div v-if="selectedPlayer.notes">
            <span class="block text-[10px] text-brand-muted uppercase tracking-wider font-sans">{{ t('my_kit.role') }}</span>
            <span class="font-bold text-white text-xs mt-0.5 block">
              {{ isGoalkeeper ? t('player.gk') : selectedPlayer.notes }}
            </span>
          </div>
        </div>
      </div>

      <!-- Action & Instructions Card -->
      <div class="bg-brand-charcoal rounded-xl p-3.5 border border-white/10 shadow-card text-xs">
        <h4 class="font-bold text-white mb-1 flex items-center gap-1.5">
          <span v-if="isPaid" class="text-emerald-400 font-bold">✓</span>
          <span v-else-if="isHalfPaid" class="text-amber-400 font-bold">◐</span>
          <span v-else class="text-brand-red font-bold">ℹ</span>
          <span>{{ t('my_kit.note_title') }}</span>
        </h4>
        <p v-if="isPaid" class="text-brand-muted leading-relaxed">
          {{ t('my_kit.paid_msg', { size: selectedPlayer.jersey_size || selectedPlayer.size, number: formatJerseyNumber(selectedPlayer.shirt_number ?? selectedPlayer.number) }) }}
        </p>
        <p v-else-if="isHalfPaid" class="text-brand-muted leading-relaxed">
          {{ t('my_kit.half_paid_msg', { size: selectedPlayer.jersey_size || selectedPlayer.size, number: formatJerseyNumber(selectedPlayer.shirt_number ?? selectedPlayer.number) }) }}
        </p>
        <p v-else-if="isUnpaid" class="text-brand-muted leading-relaxed">
          {{ t('my_kit.unpaid_msg') }}
        </p>
        <p v-else class="text-brand-muted leading-relaxed">
          {{ t('my_kit.unconfirmed_msg') }}
        </p>
      </div>

      <!-- Switch Player Button -->
      <button
        type="button"
        @click="clearSelectedPlayer"
        class="w-full py-2.5 rounded-xl bg-brand-charcoal hover:bg-brand-charcoal-light border border-white/10 text-xs text-brand-muted hover:text-white font-semibold transition-colors flex items-center justify-center gap-2 shadow-card cursor-pointer"
      >
        <span>{{ t('my_kit.switch_player') }}</span>
      </button>
    </div>

    <!-- Player Picker View (If no player is selected yet) -->
    <div v-else class="space-y-3.5">
      <div class="bg-brand-charcoal rounded-xl p-4 border border-white/10 shadow-card text-center">
        <div class="w-10 h-10 mx-auto rounded-full bg-[#222222] border border-brand-gold/30 flex items-center justify-center text-brand-gold mb-2.5">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"></path>
          </svg>
        </div>
        <h3 class="text-sm font-bold text-white mb-0.5">{{ t('my_kit.find_title') }}</h3>
        <p class="text-xs text-brand-muted leading-relaxed">
          {{ t('my_kit.find_desc') }}
        </p>
      </div>

      <!-- Quick Search in My Kit -->
      <div class="relative">
        <input
          v-model="searchQuery"
          type="search"
          :placeholder="t('search.quick_placeholder')"
          class="w-full h-10 pl-9 pr-3 text-xs bg-brand-charcoal text-white placeholder-brand-dark-muted rounded-xl border border-white/10 focus:outline-none focus:border-brand-red focus:ring-1 focus:ring-brand-red shadow-sm"
        />
        <svg class="w-3.5 h-3.5 text-brand-dark-muted absolute left-3 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div>

      <!-- List of Players to Select -->
      <div class="space-y-1.5 max-h-[360px] overflow-y-auto no-scrollbar pr-0.5">
        <button
          v-for="player in filteredPlayers"
          :key="player.id || player.no"
          type="button"
          @click="selectPlayer(player.id || player.no)"
          class="w-full bg-brand-charcoal hover:bg-brand-charcoal-light border border-white/10 hover:border-brand-gold/30 rounded-xl px-3 py-2 flex items-center justify-between text-left transition-colors shadow-xs cursor-pointer"
        >
          <div class="flex items-center gap-2.5">
            <span class="font-mono font-bold text-brand-gold text-xs min-w-[28px]">
              {{ formatJerseyNumber(player.shirt_number ?? player.number) !== '—' ? `#${formatJerseyNumber(player.shirt_number ?? player.number)}` : '—' }}
            </span>
            <span class="text-xs font-bold text-white">
              {{ player.name }}
            </span>
            <span class="text-[10px] text-brand-muted">
              ({{ t('player.jersey_size') }} {{ player.jersey_size || player.size }})
            </span>
            <span
              v-if="player.notes === 'GK'"
              class="text-[9px] px-1 py-0.2 rounded bg-brand-gold/20 text-brand-gold font-bold font-mono"
            >
              GK
            </span>
          </div>

          <span
            :class="[
              'text-[10px] font-mono font-bold px-2 py-0.5 rounded-full border',
              normalizeStatus(player) === 'Paid'
                ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/40'
                : normalizeStatus(player) === '50% Paid'
                ? 'bg-amber-500/15 text-amber-400 border-amber-500/40'
                : normalizeStatus(player) === 'Unpaid'
                ? 'bg-brand-red/15 text-brand-red border-brand-red/40'
                : 'bg-neutral-800 text-neutral-300 border-neutral-700'
            ]"
          >
            {{ normalizeStatusLabel(player) }}
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import TeamJersey from './TeamJersey.vue'
import { formatJerseyNumber } from '../utils/formatters'

const { t } = useI18n()

const props = defineProps({
  players: {
    type: Array,
    required: true
  }
})

const selectedPlayerId = ref(null)
const searchQuery = ref('')
const initialView = new URLSearchParams(window.location.search).get('view')
const activeJerseyView = ref(initialView && ['back', 'front'].includes(initialView) ? initialView : 'back') // 'back' | 'front'
const showDesignSheet = ref(false)

onMounted(() => {
  const urlPlayer = new URLSearchParams(window.location.search).get('player')
  if (urlPlayer) {
    const numId = Number(urlPlayer)
    if (!isNaN(numId)) {
      selectedPlayerId.value = numId
      localStorage.setItem('my_kit_player_id', String(numId))
      return
    }
  }
  const saved = localStorage.getItem('my_kit_player_id')
  if (saved) {
    selectedPlayerId.value = Number(saved)
  }
})

const selectPlayer = (id) => {
  selectedPlayerId.value = id
  localStorage.setItem('my_kit_player_id', String(id))
}

const clearSelectedPlayer = () => {
  selectedPlayerId.value = null
  localStorage.removeItem('my_kit_player_id')
}

const selectedPlayer = computed(() => {
  if (!selectedPlayerId.value) return null
  return props.players.find((p) => (p.id || p.no) === selectedPlayerId.value) || null
})

// Reliable Goalkeeper Detection (strictly when notes === 'GK')
const isGoalkeeper = computed(() => {
  return selectedPlayer.value?.notes?.trim().toUpperCase() === 'GK'
})

const isPaid = computed(() => {
  if (!selectedPlayer.value) return false
  const s = String(selectedPlayer.value.payment_status || selectedPlayer.value.status || '').toLowerCase().replace(/[\s%]+/g, '_')
  return s === 'paid'
})

const isHalfPaid = computed(() => {
  if (!selectedPlayer.value) return false
  const s = String(selectedPlayer.value.payment_status || selectedPlayer.value.status || '').toLowerCase().replace(/[\s%]+/g, '_')
  return s === 'half_paid' || s === '50_paid' || s === '50_percent_paid'
})

const isUnpaid = computed(() => {
  if (!selectedPlayer.value) return false
  const s = String(selectedPlayer.value.payment_status || selectedPlayer.value.status || '').toLowerCase().replace(/[\s%]+/g, '_')
  return s === 'unpaid'
})

const statusLabel = computed(() => {
  if (isPaid.value) return t('filters.paid')
  if (isHalfPaid.value) return t('filters.half_paid')
  if (isUnpaid.value) return t('filters.unpaid')
  return t('filters.not_confirmed')
})

const paymentBadgeConfig = computed(() => {
  if (isPaid.value) {
    return {
      badgeClass: 'bg-emerald-500/15 text-emerald-400 border-emerald-500/40'
    }
  }
  if (isHalfPaid.value) {
    return {
      badgeClass: 'bg-amber-500/15 text-amber-400 border-amber-500/40'
    }
  }
  if (isUnpaid.value) {
    return {
      badgeClass: 'bg-brand-red/15 text-brand-red border-brand-red/40'
    }
  }
  return {
    badgeClass: 'bg-neutral-800 text-neutral-300 border-neutral-700'
  }
})

const normalizeStatus = (player) => {
  const s = String(player.payment_status || player.status || '').toLowerCase().replace(/[\s%]+/g, '_')
  if (s === 'paid') return 'Paid'
  if (s === 'half_paid' || s === '50_paid' || s === '50_percent_paid') return '50% Paid'
  if (s === 'not_confirmed') return 'Not Confirmed'
  return 'Unpaid'
}

const normalizeStatusLabel = (player) => {
  const s = normalizeStatus(player)
  if (s === 'Paid') return t('filters.paid')
  if (s === '50% Paid') return t('filters.half_paid')
  if (s === 'Unpaid') return t('filters.unpaid')
  return t('filters.not_confirmed')
}

const filteredPlayers = computed(() => {
  const q = searchQuery.value.trim().toLowerCase()
  if (!q) return props.players
  return props.players.filter((p) => {
    const nameMatch = p.name.toLowerCase().includes(q)
    const numStr = formatJerseyNumber(p.shirt_number ?? p.number)
    const numMatch = numStr !== '—' && numStr.includes(q)
    return nameMatch || numMatch
  })
})
</script>
