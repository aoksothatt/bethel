<template>
  <section class="relative px-4 pt-4 pb-2 overflow-hidden" aria-label="Bethel High School Overview and Kit Summary">
    <!-- Stadium Radial Lighting Ambient Glow -->
    <div
      class="absolute inset-0 pointer-events-none select-none"
      style="background: radial-gradient(circle at 60% 0%, rgba(213, 46, 56, 0.10) 0%, transparent 60%), radial-gradient(circle at 90% 20%, rgba(213, 172, 80, 0.08) 0%, transparent 50%);"
    ></div>

    <!-- Subtle Team Emblem Watermark in Hero Header -->
    <div class="absolute top-2 right-2 w-28 h-28 sm:w-32 sm:h-32 opacity-[0.14] pointer-events-none select-none">
      <img
        src="/images/bethel-logo.jpg"
        alt=""
        class="w-full h-full object-contain filter contrast-125"
      />
    </div>

    <!-- Compact Team Tag with ample room so the status bar comes down below the crest -->
    <div class="relative z-10 pt-1.5 pb-20 sm:pb-24">
      <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-brand-charcoal border border-brand-gold/30 text-[10px] font-bold tracking-wider text-brand-gold uppercase shadow-xs">
        <span class="w-1.5 h-1.5 rounded-full bg-brand-red animate-pulse"></span>
        <span>{{ t('hero.tag') }}</span>
      </div>
    </div>

    <!-- Unified Statistics Card (Charcoal #171717) -->
    <div class="relative z-10 bg-brand-charcoal rounded-2xl p-3 sm:p-3.5 border border-white/10 shadow-card">
      <!-- 4 Compact Stat Pills in a Clean Row -->
      <div class="grid grid-cols-4 gap-1 sm:gap-2 text-center divide-x divide-white/10">
        <!-- Total Players -->
        <div class="px-0.5 sm:px-1">
          <span class="block text-[10px] font-semibold text-brand-muted uppercase tracking-wider truncate">
            {{ t('stats.players') }}
          </span>
          <span class="text-lg sm:text-xl font-bold text-white font-mono leading-tight block mt-0.5">{{ total }}</span>
        </div>

        <!-- Paid (Gold Success Indicator) -->
        <div class="px-0.5 sm:px-1">
          <div class="flex items-center justify-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-brand-gold"></span>
            <span class="text-[10px] font-semibold text-brand-gold uppercase tracking-wider truncate">
              {{ t('stats.paid') }}
            </span>
          </div>
          <span class="text-lg sm:text-xl font-bold text-brand-gold font-mono leading-tight block mt-0.5">{{ paid }}</span>
        </div>

        <!-- Unpaid (Football Red Accent) -->
        <div class="px-0.5 sm:px-1">
          <div class="flex items-center justify-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-brand-red"></span>
            <span class="text-[10px] font-semibold text-brand-red uppercase tracking-wider truncate">
              {{ t('stats.unpaid') }}
            </span>
          </div>
          <span class="text-lg sm:text-xl font-bold text-brand-red font-mono leading-tight block mt-0.5">{{ unpaid }}</span>
        </div>

        <!-- Not Confirmed (Neutral Gray) -->
        <div class="px-0.5 sm:px-1">
          <div class="flex items-center justify-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-neutral-400"></span>
            <span class="text-[10px] font-semibold text-neutral-300 uppercase tracking-wider truncate">
              {{ t('stats.review') }}
            </span>
          </div>
          <span class="text-lg sm:text-xl font-bold text-neutral-300 font-mono leading-tight block mt-0.5">{{ notConfirmed }}</span>
        </div>
      </div>

      <!-- Size Distribution Segmented Bar -->
      <div class="mt-3 pt-2.5 border-t border-white/10 flex flex-col gap-1.5">
        <div class="flex items-center justify-between text-[11px] text-brand-muted font-medium">
          <span class="text-[10px] uppercase tracking-wider font-semibold text-brand-muted">
            {{ t('stats.sizes') }}
          </span>
          <div class="flex items-center gap-3 font-mono text-[11px] text-white">
            <span>M: <strong class="text-white">{{ sizes.M || 0 }}</strong></span>
            <span class="text-white/20">|</span>
            <span>L: <strong class="text-brand-gold">{{ sizes.L || 0 }}</strong></span>
            <span class="text-white/20">|</span>
            <span>XL: <strong class="text-brand-red">{{ sizes.XL || 0 }}</strong></span>
          </div>
        </div>

        <!-- Thin Segmented Indicator with Bethel Colors -->
        <div class="w-full h-1.5 rounded-full bg-[#262626] flex overflow-hidden">
          <div
            class="h-full bg-white/70 rounded-l-full transition-all duration-500"
            :style="{ width: `${total ? (sizes.M / total) * 100 : 0}%` }"
            :title="`Size M: ${sizes.M}`"
          ></div>
          <div
            class="h-full bg-brand-gold transition-all duration-500"
            :style="{ width: `${total ? (sizes.L / total) * 100 : 0}%` }"
            :title="`Size L: ${sizes.L}`"
          ></div>
          <div
            class="h-full bg-brand-red rounded-r-full transition-all duration-500"
            :style="{ width: `${total ? (sizes.XL / total) * 100 : 0}%` }"
            :title="`Size XL: ${sizes.XL}`"
          ></div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps({
  total: {
    type: Number,
    required: true
  },
  paid: {
    type: Number,
    required: true
  },
  unpaid: {
    type: Number,
    required: true
  },
  notConfirmed: {
    type: Number,
    required: true
  },
  sizes: {
    type: Object,
    default: () => ({ M: 0, L: 0, XL: 0 })
  }
})
</script>
