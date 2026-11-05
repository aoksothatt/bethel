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
      <!-- Premium Jersey Card Visual (Bethel High School Black & Gold Identity) -->
      <div class="relative bg-gradient-to-b from-[#1c1c1c] to-[#121212] text-white rounded-2xl p-5 border border-brand-gold/40 shadow-card overflow-hidden text-center">
        <!-- Subtle Background Glow & Watermark -->
        <div class="absolute -top-10 -right-10 w-40 h-40 opacity-[0.08] pointer-events-none select-none">
          <img src="/images/bethel-logo.jpg" alt="" class="w-full h-full object-contain filter grayscale contrast-125" />
        </div>

        <!-- Top Row: Team Emblem & Season -->
        <div class="flex items-center justify-between text-xs text-white/70 font-mono mb-4 relative z-10">
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-full overflow-hidden ring-1 ring-brand-gold/50 bg-black flex-shrink-0">
              <img src="/images/bethel-logo.jpg" alt="Bethel High School" class="w-full h-full object-contain" />
            </div>
            <span class="font-bold tracking-wider text-white">BETHEL HIGH SCHOOL</span>
          </div>
          <span class="px-2 py-0.5 rounded-full bg-[#242424] border border-brand-gold/30 text-[10px] text-brand-gold font-bold">
            {{ t('my_kit.official_kit') }}
          </span>
        </div>

        <!-- Jersey Graphic Display -->
        <div class="relative z-10 my-3">
          <!-- Big Athletic Number in Gold -->
          <div class="font-mono font-black text-6xl sm:text-7xl text-brand-gold tracking-tight leading-none drop-shadow-md">
            {{ formattedNumber }}
          </div>
          <!-- Player Name on Jersey Back -->
          <div class="mt-2 text-lg sm:text-xl font-black text-white uppercase tracking-wider font-display">
            {{ selectedPlayer.name }}
          </div>
        </div>

        <!-- Status Details -->
        <div class="mt-5 pt-3.5 border-t border-white/10 flex items-center justify-around text-xs relative z-10 font-mono">
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
                isPaid ? 'bg-brand-gold/15 text-brand-gold border-brand-gold/40' : isUnpaid ? 'bg-brand-red/15 text-brand-red border-brand-red/40' : 'bg-neutral-800 text-neutral-300 border-neutral-700'
              ]"
            >
              {{ statusLabel }}
            </span>
          </div>

          <div v-if="selectedPlayer.notes" class="w-px h-6 bg-white/10"></div>

          <div v-if="selectedPlayer.notes">
            <span class="block text-[10px] text-brand-muted uppercase tracking-wider font-sans">{{ t('my_kit.role') }}</span>
            <span class="font-bold text-white text-xs mt-0.5 block">
              {{ selectedPlayer.notes === 'GK' ? t('player.gk') : selectedPlayer.notes }}
            </span>
          </div>
        </div>
      </div>

      <!-- Action & Instructions Card -->
      <div class="bg-brand-charcoal rounded-xl p-3.5 border border-white/10 shadow-card text-xs">
        <h4 class="font-bold text-white mb-1 flex items-center gap-1.5">
          <span v-if="isPaid" class="text-brand-gold font-bold">✓</span>
          <span v-else class="text-brand-red font-bold">ℹ</span>
          <span>{{ t('my_kit.note_title') }}</span>
        </h4>
        <p v-if="isPaid" class="text-brand-muted leading-relaxed">
          {{ t('my_kit.paid_msg', { size: selectedPlayer.jersey_size || selectedPlayer.size, number: selectedPlayer.shirt_number ?? selectedPlayer.number }) }}
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
            <span class="font-mono font-bold text-brand-gold text-xs w-6">
              #{{ player.shirt_number ?? player.number }}
            </span>
            <span class="text-xs font-bold text-white">
              {{ player.name }}
            </span>
            <span class="text-[10px] text-brand-muted">
              ({{ t('player.jersey_size') }} {{ player.jersey_size || player.size }})
            </span>
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
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  players: {
    type: Array,
    required: true
  }
})

const selectedPlayerId = ref(null)
const searchQuery = ref('')

onMounted(() => {
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

const formattedNumber = computed(() => {
  if (!selectedPlayer.value) return ''
  const num = selectedPlayer.value.shirt_number ?? selectedPlayer.value.number
  return num < 10 ? `0${num}` : String(num)
})

const isPaid = computed(() => {
  if (!selectedPlayer.value) return false
  const s = selectedPlayer.value.payment_status || selectedPlayer.value.status
  return s === 'paid' || s === 'Paid'
})

const isUnpaid = computed(() => {
  if (!selectedPlayer.value) return false
  const s = selectedPlayer.value.payment_status || selectedPlayer.value.status
  return s === 'unpaid' || s === 'Unpaid'
})

const statusLabel = computed(() => {
  if (isPaid.value) return t('filters.paid')
  if (isUnpaid.value) return t('filters.unpaid')
  return t('filters.not_confirmed')
})

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

const filteredPlayers = computed(() => {
  const q = searchQuery.value.trim().toLowerCase()
  if (!q) return props.players
  return props.players.filter(
    (p) =>
      p.name.toLowerCase().includes(q) ||
      String(p.shirt_number ?? p.number).includes(q)
  )
})
</script>
