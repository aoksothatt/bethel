<template>
  <!-- Desktop Wrapper: Dark stadium atmosphere around centered native mobile frame -->
  <div class="min-h-screen bg-[#050505] text-white flex justify-center selection:bg-brand-red selection:text-white">
    <div class="w-full max-w-[440px] min-h-screen bg-[#090909] border-x border-white/10 flex flex-col justify-between relative shadow-2xl">
      <!-- Top Section -->
      <div class="pb-24">
        <!-- Compact Bethel High School Header -->
        <Header>
          <template #right-action>
            <router-link
              to="/admin"
              class="w-7 h-7 rounded-full bg-brand-charcoal border border-white/10 flex items-center justify-center text-brand-muted hover:text-brand-gold hover:border-brand-gold/40 transition-colors ml-1"
              title="Admin Dashboard"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
            </router-link>
          </template>
        </Header>

        <!-- 1. Squad Tab (Default view) -->
        <main v-if="currentTab === 'squad'" id="squad-view">
          <!-- Bethel High School Hero & Summary Statistics -->
          <SummaryStats
            :total="stats.total"
            :paid="stats.paid"
            :unpaid="stats.unpaid"
            :not-confirmed="stats.notConfirmed"
            :sizes="stats.sizes"
          />

          <!-- Sticky Search & Filter Bar -->
          <div class="sticky top-[52px] z-20 bg-[#090909]/95 backdrop-blur-md pt-1 pb-1 border-b border-white/10 shadow-xs">
            <SearchBar v-model="searchQuery" />
            <PaymentFilter
              v-model="activeFilter"
              :counts="filterCounts"
            />
          </div>

          <!-- Player Cards Stack (All 28 players in focus) -->
          <PlayerList
            :players="filteredPlayers"
            :total-players="stats.total"
            :active-filter="activeFilter"
            :search-query="searchQuery"
            @reset-filters="resetFilters"
          />
        </main>

        <!-- 2. My Kit Tab -->
        <section v-else-if="currentTab === 'my-kit'" id="my-kit-view">
          <MyKitView :players="players" />
        </section>

        <!-- 3. Payments Tab -->
        <section v-else-if="currentTab === 'payments'" id="payments-view">
          <PaymentsView :players="players" />
        </section>
      </div>

      <!-- Fixed Mobile Bottom Navigation Bar -->
      <BottomNav
        v-model:current-tab="currentTab"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { api } from '../services/api'
import { PLAYERS_DATA } from '../data/players'
import Header from '../components/Header.vue'
import SummaryStats from '../components/SummaryStats.vue'
import SearchBar from '../components/SearchBar.vue'
import PaymentFilter from '../components/PaymentFilter.vue'
import PlayerList from '../components/PlayerList.vue'
import BottomNav from '../components/BottomNav.vue'
import MyKitView from '../components/MyKitView.vue'
import PaymentsView from '../components/PaymentsView.vue'

// Active tab in bottom navigation ('squad' | 'my-kit' | 'payments')
const currentTab = ref('squad')

// Search & Filter state
const searchQuery = ref('')
const activeFilter = ref('All')

// Database players & stats
const players = ref([...PLAYERS_DATA])
const backendStats = ref(null)
const isSyncing = ref(false)
let pollingTimer = null

// Normalize status helper
const normalizeStatus = (player) => {
  const s = player.payment_status || player.status
  if (s === 'paid' || s === 'Paid') return 'Paid'
  if (s === 'not_confirmed' || s === 'Not Confirmed') return 'Not Confirmed'
  return 'Unpaid'
}

// Fetch latest data from database
const fetchSquadData = async () => {
  try {
    isSyncing.value = true
    const res = await api.getPlayers()
    if (res && res.players) {
      players.value = res.players
      if (res.stats) {
        backendStats.value = res.stats
      }
    }
  } catch (err) {
    console.warn('Live database sync offline, using local cache:', err.message)
  } finally {
    isSyncing.value = false
  }
}

// Polling interval (every 6 seconds) for automatic public synchronization
onMounted(() => {
  fetchSquadData()
  pollingTimer = setInterval(fetchSquadData, 6000)
})

onUnmounted(() => {
  if (pollingTimer) {
    clearInterval(pollingTimer)
  }
})

// Dynamic statistics computed from current player dataset
const stats = computed(() => {
  if (backendStats.value) {
    return {
      total: backendStats.value.total ?? players.value.length,
      paid: backendStats.value.paid ?? 0,
      unpaid: backendStats.value.unpaid ?? 0,
      notConfirmed: backendStats.value.not_confirmed ?? backendStats.value.notConfirmed ?? 0,
      sizes: backendStats.value.sizes ?? { M: 0, L: 0, XL: 0 }
    }
  }

  const total = players.value.length
  let paid = 0
  let unpaid = 0
  let notConfirmed = 0
  const sizes = { M: 0, L: 0, XL: 0 }

  for (const player of players.value) {
    const s = normalizeStatus(player)
    if (s === 'Paid') paid++
    else if (s === 'Unpaid') unpaid++
    else if (s === 'Not Confirmed') notConfirmed++

    const size = player.jersey_size || player.size
    if (size in sizes) {
      sizes[size]++
    }
  }

  return { total, paid, unpaid, notConfirmed, sizes }
})

// Filter counts for the payment filter buttons
const filterCounts = computed(() => ({
  All: stats.value.total,
  Paid: stats.value.paid,
  Unpaid: stats.value.unpaid,
  'Not Confirmed': stats.value.notConfirmed
}))

// Filtered players list
const filteredPlayers = computed(() => {
  const query = searchQuery.value.trim().toLowerCase()
  const filter = activeFilter.value

  return players.value.filter((player) => {
    // 1. Payment status match
    const pStatus = normalizeStatus(player)
    if (filter !== 'All' && pStatus !== filter) {
      return false
    }

    // 2. Search query match
    if (query) {
      const matchName = player.name.toLowerCase().includes(query)
      const num = player.shirt_number ?? player.number
      const formattedNum = num < 10 ? `0${num}` : String(num)
      const matchNumber = String(num).includes(query) || formattedNum.includes(query)
      const matchNotes = player.notes ? player.notes.toLowerCase().includes(query) : false
      const size = (player.jersey_size || player.size || '').toLowerCase()
      const matchSize = size === query

      if (!matchName && !matchNumber && !matchNotes && !matchSize) {
        return false
      }
    }

    return true
  })
})

// Reset filters handler
const resetFilters = () => {
  searchQuery.value = ''
  activeFilter.value = 'All'
}
</script>
