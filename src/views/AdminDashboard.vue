<template>
  <div class="min-h-screen bg-[#090909] text-white selection:bg-brand-red selection:text-white">
    <!-- Admin Top Navigation Bar -->
    <header class="bg-[#090909]/95 border-b border-white/10 sticky top-0 z-30 px-3.5 sm:px-6 py-2.5 sm:py-3 backdrop-blur-md shadow-xs">
      <div class="max-w-7xl mx-auto flex items-center justify-between gap-2 sm:gap-3">
        <!-- Logo & Admin Title -->
        <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
          <div class="w-9 h-9 rounded-full overflow-hidden flex items-center justify-center flex-shrink-0 ring-1 ring-brand-gold/40 bg-black shadow-sm">
            <img
              src="/images/bethel-logo.jpg"
              alt="Bethel High School Crest"
              class="w-full h-full object-contain"
            />
          </div>
          <div class="min-w-0">
            <div class="flex items-center gap-2">
              <h1 class="text-xs sm:text-base font-bold text-white tracking-tight truncate">
                {{ t('admin.dashboard') }}
              </h1>
              <span class="px-2 py-0.5 rounded-full bg-brand-charcoal text-brand-gold border border-brand-gold/30 text-[10px] font-mono font-bold uppercase tracking-wider hidden xs:inline-block">
                {{ t('admin.manager') }}
              </span>
            </div>
            <p class="text-[11px] text-brand-muted hidden md:block">
              Bethel High School • {{ t('admin.portal_subtitle') }}
            </p>
          </div>
        </div>

        <!-- Right: Language Switcher, Public View & Logout -->
        <div class="flex items-center gap-1.5 sm:gap-2.5 flex-shrink-0">
          <!-- Language Switcher in Admin -->
          <LanguageSwitcher />

          <router-link
            to="/"
            target="_blank"
            class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-xl bg-brand-charcoal hover:bg-brand-charcoal-light border border-white/10 text-xs font-semibold text-brand-muted hover:text-white transition-colors shadow-xs"
          >
            <svg class="w-3.5 h-3.5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
            <span class="hidden sm:inline">{{ t('admin.view_public') }}</span>
          </router-link>

          <button
            type="button"
            @click="handleLogout"
            class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-xl bg-brand-red/15 hover:bg-brand-red/25 border border-brand-red/40 text-xs font-semibold text-brand-red transition-colors cursor-pointer"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span>{{ t('admin.logout') }}</span>
          </button>
        </div>
      </div>
    </header>

    <!-- Main Admin Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-5 sm:py-6 space-y-5">
      <!-- 1. KPI Overview Cards -->
      <section aria-label="Admin KPI Overview">
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
          <!-- Total Players -->
          <div class="bg-brand-charcoal rounded-2xl p-4 border border-white/10 shadow-card">
            <span class="text-[11px] font-semibold text-brand-muted uppercase tracking-wider block">
              {{ t('admin.total_squad') }}
            </span>
            <div class="mt-1.5 flex items-baseline gap-2">
              <span class="text-2xl sm:text-3xl font-bold text-white font-mono">{{ stats.total }}</span>
              <span class="text-xs text-brand-muted">{{ t('stats.players') }}</span>
            </div>
          </div>

          <!-- Paid (Green) -->
          <div class="bg-brand-charcoal rounded-2xl p-4 border border-emerald-500/30 shadow-card relative overflow-hidden">
            <div class="flex items-center justify-between">
              <span class="text-[11px] font-semibold text-emerald-400 uppercase tracking-wider block">
                {{ t('filters.paid') }}
              </span>
              <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            </div>
            <div class="mt-1.5 flex items-baseline gap-2">
              <span class="text-2xl sm:text-3xl font-bold text-emerald-400 font-mono">{{ stats.paid }}</span>
              <span class="text-xs text-brand-muted">{{ t('admin.confirmed') }}</span>
            </div>
          </div>

          <!-- 50% Paid (Amber) -->
          <div class="bg-brand-charcoal rounded-2xl p-4 border border-amber-500/30 shadow-card relative overflow-hidden">
            <div class="flex items-center justify-between">
              <span class="text-[11px] font-semibold text-amber-400 uppercase tracking-wider block">
                {{ t('filters.half_paid') }}
              </span>
              <span class="w-2 h-2 rounded-full bg-amber-400"></span>
            </div>
            <div class="mt-1.5 flex items-baseline gap-2">
              <span class="text-2xl sm:text-3xl font-bold text-amber-400 font-mono">{{ stats.halfPaid }}</span>
              <span class="text-xs text-brand-muted">50%</span>
            </div>
          </div>

          <!-- Unpaid (Red) -->
          <div class="bg-brand-charcoal rounded-2xl p-4 border border-brand-red/30 shadow-card">
            <div class="flex items-center justify-between">
              <span class="text-[11px] font-semibold text-brand-red uppercase tracking-wider block">
                {{ t('filters.unpaid') }}
              </span>
              <span class="w-2 h-2 rounded-full bg-brand-red"></span>
            </div>
            <div class="mt-1.5 flex items-baseline gap-2">
              <span class="text-2xl sm:text-3xl font-bold text-brand-red font-mono">{{ stats.unpaid }}</span>
              <span class="text-xs text-brand-muted">{{ t('admin.pending_fee') }}</span>
            </div>
          </div>

          <!-- Not Confirmed (Grey) -->
          <div class="bg-brand-charcoal rounded-2xl p-4 border border-neutral-700 shadow-card">
            <div class="flex items-center justify-between">
              <span class="text-[11px] font-semibold text-neutral-300 uppercase tracking-wider block">
                {{ t('filters.not_confirmed') }}
              </span>
              <span class="w-2 h-2 rounded-full bg-neutral-400"></span>
            </div>
            <div class="mt-1.5 flex items-baseline gap-2">
              <span class="text-2xl sm:text-3xl font-bold text-neutral-300 font-mono">{{ stats.notConfirmed }}</span>
              <span class="text-xs text-brand-muted">{{ t('admin.needs_review') }}</span>
            </div>
          </div>
        </div>

        <!-- Kit Sizing Bar in Admin -->
        <div class="mt-3 bg-brand-charcoal rounded-xl px-4 py-2.5 border border-white/10 shadow-card flex flex-wrap items-center justify-between gap-3 text-xs">
          <span class="font-semibold text-brand-muted uppercase tracking-wider text-[11px] flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-brand-gold"></span>
            {{ t('admin.sizing_breakdown') }}
          </span>
          <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 font-mono">
            <span class="px-2.5 py-0.5 rounded-lg bg-[#222222] border border-white/10">
              <strong class="text-brand-muted font-sans font-medium">S:</strong> <span class="text-neutral-300 font-bold">{{ stats.sizes.S || 0 }}</span>
            </span>
            <span class="px-2.5 py-0.5 rounded-lg bg-[#222222] border border-white/10">
              <strong class="text-brand-muted font-sans font-medium">M:</strong> <span class="text-white font-bold">{{ stats.sizes.M || 0 }}</span>
            </span>
            <span class="px-2.5 py-0.5 rounded-lg bg-[#222222] border border-white/10">
              <strong class="text-brand-muted font-sans font-medium">L:</strong> <span class="text-brand-gold font-bold">{{ stats.sizes.L || 0 }}</span>
            </span>
            <span class="px-2.5 py-0.5 rounded-lg bg-[#222222] border border-white/10">
              <strong class="text-brand-muted font-sans font-medium">XL:</strong> <span class="text-brand-red font-bold">{{ stats.sizes.XL || 0 }}</span>
            </span>
            <span class="px-2.5 py-0.5 rounded-lg bg-[#222222] border border-white/10">
              <strong class="text-brand-muted font-sans font-medium">XXL:</strong> <span class="text-brand-gold-light font-bold">{{ stats.sizes.XXL || 0 }}</span>
            </span>
          </div>
        </div>
      </section>

      <!-- 2. Management Toolbar -->
      <section class="bg-brand-charcoal rounded-2xl p-3.5 border border-white/10 shadow-card flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
        <!-- Search & Filter Controls -->
        <div class="flex flex-wrap items-center gap-2.5 flex-1 min-w-0">
          <!-- Search -->
          <div class="relative flex-1 min-w-[200px]">
            <input
              v-model="searchQuery"
              type="search"
              :placeholder="t('admin.search_placeholder')"
              class="w-full h-10 pl-9 pr-3 text-xs bg-[#222222] text-white placeholder-brand-dark-muted rounded-xl border border-white/10 focus:outline-none focus:border-brand-red focus:ring-1 focus:ring-brand-red"
            />
            <svg class="w-4 h-4 text-brand-dark-muted absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>

          <!-- Payment Status Filter -->
          <select
            v-model="filterStatus"
            class="h-10 px-3 text-xs bg-[#222222] text-white rounded-xl border border-white/10 focus:outline-none focus:border-brand-red"
          >
            <option value="All">{{ t('filters.all') }}</option>
            <option value="Paid">{{ t('filters.paid') }}</option>
            <option value="50% Paid">{{ t('filters.half_paid') }}</option>
            <option value="Unpaid">{{ t('filters.unpaid') }}</option>
            <option value="Not Confirmed">{{ t('filters.not_confirmed') }}</option>
          </select>

          <!-- Jersey Size Filter -->
          <select
            v-model="filterSize"
            class="h-10 px-3 text-xs bg-[#222222] text-white rounded-xl border border-white/10 focus:outline-none focus:border-brand-red"
          >
            <option value="All">{{ t('filters.all_short') }} ({{ t('player.jersey_size') }})</option>
            <option v-for="s in JERSEY_SIZES" :key="s" :value="s">{{ s }}</option>
          </select>
        </div>

        <!-- Add Player Action Button -->
        <button
          type="button"
          @click="openAddModal"
          class="h-10 px-4 rounded-xl bg-brand-red text-white font-bold text-xs uppercase tracking-wider hover:bg-brand-red-hover transition-colors flex items-center justify-center gap-2 shadow-sm cursor-pointer flex-shrink-0"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
          </svg>
          <span>{{ t('admin.add_player') }}</span>
        </button>
      </section>

      <!-- 3. Players Management Table -->
      <section class="bg-brand-charcoal rounded-2xl border border-white/10 shadow-card overflow-hidden">
        <!-- Table Header Info -->
        <div class="px-4 py-3 border-b border-white/10 flex items-center justify-between text-xs text-brand-muted font-medium bg-[#1d1d1d]">
          <div>
            <span>{{ t('player.roster') }}: </span>
            <strong class="text-white">{{ filteredPlayers.length }}</strong>
            <span v-if="filteredPlayers.length !== players.length"> of {{ players.length }}</span>
            <span> {{ t('stats.players') }}</span>
          </div>
          <span class="text-[11px] text-brand-muted hidden sm:inline">
            {{ t('admin.tip_dropdown') }}
          </span>
        </div>

        <!-- Desktop Table View -->
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-[#1f1f1f] text-brand-muted font-semibold uppercase tracking-wider text-[11px] border-b border-white/10">
              <tr>
                <th class="py-3 px-4 w-16">#</th>
                <th class="py-3 px-4">{{ t('admin.player_name') }}</th>
                <th class="py-3 px-4 w-24">{{ t('player.jersey_size') }}</th>
                <th class="py-3 px-4 w-44">{{ t('admin.payment_status') }}</th>
                <th class="py-3 px-4">{{ t('player.notes') }}</th>
                <th class="py-3 px-4 text-right w-28">{{ t('admin.actions') }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
              <tr
                v-for="player in filteredPlayers"
                :key="player.id"
                class="hover:bg-[#202020] transition-colors"
              >
                <!-- Number -->
                <td class="py-3.5 px-4 font-mono font-bold text-sm">
                  <div class="w-8 h-8 rounded-lg bg-[#222222] border border-brand-gold/30 text-brand-gold flex items-center justify-center font-bold">
                    {{ formatJerseyNumber(player.shirt_number ?? player.number) !== '—' ? `#${formatJerseyNumber(player.shirt_number ?? player.number)}` : '—' }}
                  </div>
                </td>

                <!-- Name (Preserved exactly) -->
                <td class="py-3.5 px-4 font-bold text-white text-sm">
                  {{ player.name }}
                </td>

                <!-- Size -->
                <td class="py-3.5 px-4">
                  <span class="px-2 py-0.5 rounded bg-[#222222] border border-white/10 font-mono font-bold text-white text-[11px]">
                    {{ player.jersey_size ?? player.size }}
                  </span>
                </td>

                <!-- Payment Status Control -->
                <td class="py-3.5 px-4">
                  <select
                    :value="normalizeStatus(player)"
                    @change="quickChangePayment(player, $event.target.value)"
                    :disabled="isUpdatingId === player.id"
                    :class="[
                      'h-8 px-2.5 rounded-lg text-xs font-mono font-bold uppercase tracking-wider cursor-pointer border transition-colors outline-none shadow-xs',
                      normalizeStatus(player) === 'Paid'
                        ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/40'
                        : normalizeStatus(player) === '50% Paid'
                        ? 'bg-amber-500/15 text-amber-400 border-amber-500/40'
                        : normalizeStatus(player) === 'Unpaid'
                        ? 'bg-brand-red/15 text-brand-red border-brand-red/40'
                        : 'bg-neutral-800 text-neutral-300 border-neutral-700'
                    ]"
                  >
                    <option value="Paid" class="bg-[#171717] text-emerald-400 font-bold">{{ t('filters.paid') }}</option>
                    <option value="50% Paid" class="bg-[#171717] text-amber-400 font-bold">{{ t('filters.half_paid') }}</option>
                    <option value="Unpaid" class="bg-[#171717] text-brand-red font-bold">{{ t('filters.unpaid') }}</option>
                    <option value="Not Confirmed" class="bg-[#171717] text-neutral-300 font-bold">{{ t('filters.not_confirmed') }}</option>
                  </select>
                </td>

                <!-- Notes / Role -->
                <td class="py-3.5 px-4">
                  <span
                    v-if="player.notes === 'GK'"
                    class="px-2 py-0.5 rounded bg-brand-gold/15 text-brand-gold border border-brand-gold/30 font-bold text-[10px] uppercase font-mono"
                  >
                    {{ t('player.gk') }}
                  </span>
                  <span
                    v-else-if="player.notes"
                    class="text-xs text-brand-muted px-2 py-0.5 rounded bg-[#222222] border border-white/10"
                  >
                    {{ player.notes }}
                  </span>
                  <span v-else class="text-white/20">—</span>
                </td>

                <!-- Actions -->
                <td class="py-3.5 px-4 text-right">
                  <div class="inline-flex items-center gap-1.5">
                    <button
                      type="button"
                      @click="openEditModal(player)"
                      class="w-7 h-7 rounded-lg bg-[#222222] hover:bg-[#2b2b2b] border border-white/10 hover:border-brand-gold/40 text-brand-muted hover:text-brand-gold flex items-center justify-center transition-colors shadow-xs cursor-pointer"
                      :title="t('admin.edit_player')"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                      </svg>
                    </button>

                    <button
                      type="button"
                      @click="openDeleteModal(player)"
                      class="w-7 h-7 rounded-lg bg-[#222222] hover:bg-brand-red/20 border border-white/10 hover:border-brand-red/40 text-brand-muted hover:text-brand-red flex items-center justify-center transition-colors shadow-xs cursor-pointer"
                      :title="t('admin.delete_player')"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="filteredPlayers.length === 0">
                <td colspan="6" class="py-8 text-center text-brand-muted">
                  {{ t('search.no_players') }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </main>

    <!-- Modal: Add / Edit Player -->
    <div
      v-if="showFormModal"
      class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
    >
      <div class="bg-brand-charcoal border border-white/10 rounded-2xl p-6 w-full max-w-md shadow-2xl relative text-white">
        <h3 class="text-base font-bold text-white mb-0.5">
          {{ isEditing ? t('admin.edit_player') : t('admin.add_player') }}
        </h3>
        <p class="text-xs text-brand-muted mb-4">
          {{ isEditing ? t('admin.edit_desc') : t('admin.add_desc') }}
        </p>

        <form @submit.prevent="submitPlayerForm" class="space-y-3.5">
          <!-- Player Name -->
          <div>
            <label class="block text-xs font-semibold text-brand-muted uppercase tracking-wider mb-1">
              {{ t('admin.player_name') }} *
            </label>
            <input
              v-model="form.name"
              type="text"
              required
              placeholder="e.g. MESSI"
              class="w-full h-10 px-3 text-xs bg-[#222222] text-white rounded-xl border border-white/10 focus:outline-none focus:border-brand-red"
            />
          </div>

          <!-- Shirt Number & Jersey Size (Grid) -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-brand-muted uppercase tracking-wider mb-1">
                {{ t('admin.shirt_number') }} *
              </label>
              <input
                v-model.number="form.shirt_number"
                type="number"
                min="0"
                max="999"
                required
                placeholder="0"
                class="w-full h-10 px-3 text-xs font-mono font-bold bg-[#222222] text-white rounded-xl border border-white/10 focus:outline-none focus:border-brand-red"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-brand-muted uppercase tracking-wider mb-1">
                {{ t('admin.jersey_size') }} *
              </label>
              <select
                v-model="form.jersey_size"
                required
                class="w-full h-10 px-3 text-xs font-mono font-bold bg-[#222222] text-white rounded-xl border border-white/10 focus:outline-none focus:border-brand-red"
              >
                <option v-for="s in JERSEY_SIZES" :key="s" :value="s" class="bg-[#171717]">{{ s }}</option>
              </select>
            </div>
          </div>

          <!-- Payment Status -->
          <div>
            <label class="block text-xs font-semibold text-brand-muted uppercase tracking-wider mb-1">
              {{ t('admin.payment_status') }} *
            </label>
            <select
              v-model="form.payment_status"
              required
              class="w-full h-10 px-3 text-xs font-mono font-bold bg-[#222222] text-white rounded-xl border border-white/10 focus:outline-none focus:border-brand-red uppercase"
            >
              <option value="paid" class="bg-[#171717] text-emerald-400 font-bold">{{ t('filters.paid') }}</option>
              <option value="half_paid" class="bg-[#171717] text-amber-400 font-bold">{{ t('filters.half_paid') }}</option>
              <option value="unpaid" class="bg-[#171717] text-brand-red font-bold">{{ t('filters.unpaid') }}</option>
              <option value="not_confirmed" class="bg-[#171717] text-neutral-300 font-bold">{{ t('filters.not_confirmed') }}</option>
            </select>
          </div>

          <!-- Notes (Supports Khmer text & English) -->
          <div>
            <label class="block text-xs font-semibold text-brand-muted uppercase tracking-wider mb-1">
              {{ t('admin.notes_role') }}
            </label>
            <input
              v-model="form.notes"
              type="text"
              placeholder="e.g. GK or អាវគេចូលរួម"
              class="w-full h-10 px-3 text-xs bg-[#222222] text-white rounded-xl border border-white/10 focus:outline-none focus:border-brand-red"
            />
          </div>

          <!-- Actions -->
          <div class="mt-5 pt-3 border-t border-white/10 flex items-center justify-end gap-2">
            <button
              type="button"
              @click="closeFormModal"
              class="px-4 py-2 rounded-xl bg-[#222222] hover:bg-[#2c2c2c] text-xs font-semibold text-brand-muted hover:text-white transition-colors cursor-pointer"
            >
              {{ t('admin.cancel') }}
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="px-4 py-2 rounded-xl bg-brand-red text-white font-bold text-xs uppercase tracking-wider hover:bg-brand-red-hover transition-colors shadow-sm disabled:opacity-60 cursor-pointer"
            >
              {{ isSubmitting ? t('admin.saving') : (isEditing ? t('admin.save_changes') : t('admin.create_player')) }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal: Delete Confirmation Dialog -->
    <div
      v-if="showDeleteModal && deletingPlayer"
      class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
    >
      <div class="bg-brand-charcoal border border-brand-red/40 rounded-2xl p-6 w-full max-w-sm shadow-2xl text-white">
        <div class="w-10 h-10 rounded-full bg-brand-red/15 flex items-center justify-center text-brand-red mb-3 border border-brand-red/30">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>
        <h3 class="text-sm font-bold text-white mb-1">
          {{ t('admin.delete_confirm_title', { number: formatJerseyNumber(deletingPlayer.shirt_number ?? deletingPlayer.number), name: deletingPlayer.name }) }}
        </h3>
        <p class="text-xs text-brand-muted leading-relaxed mb-4">
          {{ t('admin.delete_confirm_desc') }}
        </p>

        <div class="flex items-center justify-end gap-2">
          <button
            type="button"
            @click="closeDeleteModal"
            class="px-3.5 py-2 rounded-xl bg-[#222222] hover:bg-[#2c2c2c] text-xs font-semibold text-brand-muted hover:text-white transition-colors cursor-pointer"
          >
            {{ t('admin.cancel') }}
          </button>
          <button
            type="button"
            @click="confirmDeletePlayer"
            :disabled="isDeleting"
            class="px-4 py-2 rounded-xl bg-brand-red hover:bg-brand-red-hover text-white font-bold text-xs uppercase tracking-wider transition-colors shadow-sm disabled:opacity-60 cursor-pointer"
          >
            {{ isDeleting ? t('admin.deleting') : t('admin.delete_player') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Toast Notification Banner -->
    <div
      v-if="toastMessage"
      class="fixed bottom-5 right-5 z-50 bg-brand-charcoal text-white text-xs px-4 py-3 rounded-xl shadow-2xl flex items-center gap-2.5 animate-slide-up border border-brand-gold/40"
    >
      <span class="w-2 h-2 rounded-full bg-brand-gold animate-pulse"></span>
      <span class="font-medium">{{ toastMessage }}</span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { api, removeAdminToken } from '../services/api'
import LanguageSwitcher from '../components/LanguageSwitcher.vue'
import { formatJerseyNumber, JERSEY_SIZES } from '../utils/formatters'

const router = useRouter()
const { t } = useI18n()

// Data State
const players = ref([])
const backendStats = ref(null)
const isUpdatingId = ref(null)

// Search & Filter
const searchQuery = ref('')
const filterStatus = ref('All')
const filterSize = ref('All')

// Modals State
const showFormModal = ref(false)
const isEditing = ref(false)
const isSubmitting = ref(false)
const form = ref({
  id: null,
  name: '',
  shirt_number: 0,
  jersey_size: 'L',
  payment_status: 'unpaid',
  notes: ''
})

const showDeleteModal = ref(false)
const deletingPlayer = ref(null)
const isDeleting = ref(false)

// Toast State
const toastMessage = ref('')
let toastTimer = null

const showToast = (msg) => {
  toastMessage.value = msg
  if (toastTimer) clearTimeout(toastTimer)
  toastTimer = setTimeout(() => {
    toastMessage.value = ''
  }, 3500)
}

// Fetch all squad data
const fetchPlayers = async () => {
  try {
    const res = await api.getPlayers()
    if (res && res.players) {
      players.value = res.players
      if (res.stats) {
        backendStats.value = res.stats
      }
    }
  } catch (err) {
    showToast(`Error: ${err.message}`)
  }
}

onMounted(() => {
  fetchPlayers()
})

// Normalizer for status display
const normalizeStatus = (player) => {
  const s = String(player.payment_status || player.status || '').toLowerCase().replace(/[\s%]+/g, '_')
  if (s === 'paid') return 'Paid'
  if (s === 'half_paid' || s === '50_paid' || s === '50_percent_paid') return '50% Paid'
  if (s === 'not_confirmed') return 'Not Confirmed'
  return 'Unpaid'
}

// Dynamic statistics
const stats = computed(() => {
  if (backendStats.value) {
    return {
      total: backendStats.value.total ?? players.value.length,
      paid: backendStats.value.paid ?? 0,
      halfPaid: backendStats.value.half_paid ?? backendStats.value.halfPaid ?? 0,
      unpaid: backendStats.value.unpaid ?? 0,
      notConfirmed: backendStats.value.not_confirmed ?? backendStats.value.notConfirmed ?? 0,
      sizes: backendStats.value.sizes ?? { S: 0, M: 0, L: 0, XL: 0, XXL: 0 }
    }
  }

  const total = players.value.length
  let paid = 0
  let halfPaid = 0
  let unpaid = 0
  let notConfirmed = 0
  const sizes = { S: 0, M: 0, L: 0, XL: 0, XXL: 0 }

  for (const player of players.value) {
    const s = normalizeStatus(player)
    if (s === 'Paid') paid++
    else if (s === '50% Paid') halfPaid++
    else if (s === 'Unpaid') unpaid++
    else if (s === 'Not Confirmed') notConfirmed++

    const size = player.jersey_size || player.size
    if (size in sizes) {
      sizes[size]++
    }
  }

  return { total, paid, halfPaid, unpaid, notConfirmed, sizes }
})

// Filtered players list
const filteredPlayers = computed(() => {
  const query = searchQuery.value.trim().toLowerCase()
  const status = filterStatus.value
  const size = filterSize.value

  return players.value.filter((player) => {
    const pStatus = normalizeStatus(player)
    const pSize = player.jersey_size || player.size

    if (status !== 'All' && pStatus !== status) return false
    if (size !== 'All' && pSize !== size) return false

    if (query) {
      const matchName = player.name.toLowerCase().includes(query)
      const numStr = formatJerseyNumber(player.shirt_number ?? player.number)
      const matchNum = numStr !== '—' && numStr.includes(query)
      const matchNotes = player.notes ? player.notes.toLowerCase().includes(query) : false
      if (!matchName && !matchNum && !matchNotes) return false
    }

    return true
  })
})

// 1-Tap Quick Change Payment Status
const quickChangePayment = async (player, newStatus) => {
  isUpdatingId.value = player.id
  let apiStatus = 'unpaid'
  if (newStatus === 'Paid') apiStatus = 'paid'
  else if (newStatus === '50% Paid') apiStatus = 'half_paid'
  else if (newStatus === 'Not Confirmed') apiStatus = 'not_confirmed'

  try {
    const res = await api.updatePaymentStatus(player.id, apiStatus)
    player.payment_status = apiStatus
    player.status = newStatus
    if (res.stats) {
      backendStats.value = res.stats
    }
    const localizedStatus =
      newStatus === 'Paid'
        ? t('filters.paid')
        : newStatus === '50% Paid'
        ? t('filters.half_paid')
        : newStatus === 'Not Confirmed'
        ? t('filters.not_confirmed')
        : t('filters.unpaid')
    showToast(t('admin.updated_status', { name: player.name, status: localizedStatus }))
  } catch (err) {
    showToast(`Error: ${err.message}`)
  } finally {
    isUpdatingId.value = null
  }
}

// Modal open/close handlers
const openAddModal = () => {
  isEditing.value = false
  form.value = {
    id: null,
    name: '',
    shirt_number: 0,
    jersey_size: 'L',
    payment_status: 'unpaid',
    notes: ''
  }
  showFormModal.value = true
}

const openEditModal = (player) => {
  isEditing.value = true
  const rawStatus = String(player.payment_status || player.status || '').toLowerCase().replace(/[\s%]+/g, '_')
  let status = 'unpaid'
  if (rawStatus === 'paid') status = 'paid'
  else if (rawStatus === 'half_paid' || rawStatus === '50_paid' || rawStatus === '50_percent_paid') status = 'half_paid'
  else if (rawStatus === 'not_confirmed') status = 'not_confirmed'

  form.value = {
    id: player.id,
    name: player.name,
    shirt_number: player.shirt_number ?? player.number,
    jersey_size: player.jersey_size ?? player.size,
    payment_status: status,
    notes: player.notes || ''
  }
  showFormModal.value = true
}

const closeFormModal = () => {
  showFormModal.value = false
}

// Submit Add / Edit Form
const submitPlayerForm = async () => {
  isSubmitting.value = true
  try {
    if (isEditing.value) {
      const res = await api.updatePlayer(form.value.id, form.value)
      const idx = players.value.findIndex((p) => p.id === form.value.id)
      if (idx !== -1 && res.player) {
        players.value[idx] = res.player
      }
      if (res.stats) {
        backendStats.value = res.stats
      }
      showToast(t('admin.updated_status', { name: form.value.name, status: form.value.payment_status }))
    } else {
      const res = await api.createPlayer(form.value)
      if (res.player) {
        players.value.push(res.player)
      }
      if (res.stats) {
        backendStats.value = res.stats
      }
      showToast(t('admin.added_player', { name: form.value.name }))
    }
    closeFormModal()
  } catch (err) {
    showToast(`Error: ${err.message}`)
  } finally {
    isSubmitting.value = false
  }
}

// Delete Confirmation
const openDeleteModal = (player) => {
  deletingPlayer.value = player
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  deletingPlayer.value = null
}

const confirmDeletePlayer = async () => {
  if (!deletingPlayer.value) return
  isDeleting.value = true
  const id = deletingPlayer.value.id
  const name = deletingPlayer.value.name

  try {
    const res = await api.deletePlayer(id)
    players.value = players.value.filter((p) => p.id !== id)
    if (res.stats) {
      backendStats.value = res.stats
    }
    showToast(t('admin.deleted_player', { name }))
    closeDeleteModal()
  } catch (err) {
    showToast(`Error: ${err.message}`)
  } finally {
    isDeleting.value = false
  }
}

// Logout
const handleLogout = async () => {
  try {
    await api.logout()
  } catch (e) {
    // Ignore error
  } finally {
    removeAdminToken()
    router.push('/admin/login')
  }
}
</script>
