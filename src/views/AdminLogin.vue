<template>
  <div class="min-h-screen bg-[#090909] text-white flex flex-col justify-center items-center px-4 py-12 selection:bg-brand-red selection:text-white">
    <!-- Top Right Language Switcher -->
    <div class="w-full max-w-sm flex justify-end mb-3">
      <LanguageSwitcher />
    </div>

    <div class="w-full max-w-sm">
      <!-- Brand Logo & Header -->
      <div class="text-center mb-6">
        <div class="w-16 h-16 rounded-full overflow-hidden mx-auto mb-3 ring-2 ring-brand-gold/50 bg-black shadow-lg shadow-black/60 flex items-center justify-center">
          <img
            src="/images/bethel-logo.jpg"
            alt="Bethel High School Crest"
            class="w-full h-full object-contain"
          />
        </div>
        <h1 class="text-xl font-black tracking-tight text-white uppercase font-display">
          {{ t('admin.portal') }}
        </h1>
        <p class="text-xs text-brand-muted mt-0.5 font-medium">
          {{ t('admin.portal_subtitle') }}
        </p>
      </div>

      <!-- Login Card -->
      <div class="bg-brand-charcoal border border-white/10 rounded-2xl p-6 shadow-2xl">
        <form @submit.prevent="handleLogin" class="space-y-4">
          <!-- Error Alert -->
          <div
            v-if="errorMessage"
            class="p-3 rounded-xl bg-brand-red/15 border border-brand-red/40 text-brand-red text-xs flex items-center gap-2"
          >
            <svg class="w-4 h-4 flex-shrink-0 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ errorMessage }}</span>
          </div>

          <!-- Email Input -->
          <div>
            <label for="admin-email" class="block text-xs font-semibold text-brand-muted uppercase tracking-wider mb-1.5">
              {{ t('admin.email') }}
            </label>
            <input
              id="admin-email"
              v-model="email"
              type="email"
              required
              autocomplete="email"
              placeholder="admin@teamjersey.local"
              class="w-full h-11 px-3.5 text-xs sm:text-sm bg-[#222222] text-white placeholder-brand-dark-muted rounded-xl border border-white/10 focus:outline-none focus:border-brand-red focus:ring-1 focus:ring-brand-red transition-colors"
            />
          </div>

          <!-- Password Input -->
          <div>
            <label for="admin-password" class="block text-xs font-semibold text-brand-muted uppercase tracking-wider mb-1.5">
              {{ t('admin.password') }}
            </label>
            <input
              id="admin-password"
              v-model="password"
              type="password"
              required
              autocomplete="current-password"
              placeholder="••••••••"
              class="w-full h-11 px-3.5 text-xs sm:text-sm bg-[#222222] text-white placeholder-brand-dark-muted rounded-xl border border-white/10 focus:outline-none focus:border-brand-red focus:ring-1 focus:ring-brand-red transition-colors"
            />
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="isLoading"
            class="w-full h-11 rounded-xl bg-brand-red text-white font-bold text-xs sm:text-sm uppercase tracking-wider hover:bg-brand-red-hover active:scale-[0.99] transition-all flex items-center justify-center gap-2 shadow-sm disabled:opacity-60 cursor-pointer"
          >
            <svg v-if="isLoading" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span>{{ isLoading ? t('admin.signing_in') : t('admin.sign_in') }}</span>
          </button>
        </form>

        <div class="mt-4 pt-3.5 border-t border-white/10 text-center">
          <p class="text-[11px] text-brand-muted leading-relaxed">
            Deployment credentials: <br />
            <code class="text-brand-gold font-mono font-bold text-[11px]">admin@teamjersey.local</code> / <code class="text-brand-gold font-mono font-bold text-[11px]">admin12345</code>
          </p>
        </div>
      </div>

      <!-- Back to Public Site -->
      <div class="mt-5 text-center">
        <router-link
          to="/"
          class="inline-flex items-center gap-1.5 text-xs text-brand-muted hover:text-white transition-colors font-medium"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          <span>{{ t('admin.return_public') }}</span>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { api, setAdminToken } from '../services/api'
import LanguageSwitcher from '../components/LanguageSwitcher.vue'

const router = useRouter()
const route = useRoute()
const { t } = useI18n()

const email = ref('admin@teamjersey.local')
const password = ref('admin12345')
const errorMessage = ref('')
const isLoading = ref(false)

const handleLogin = async () => {
  errorMessage.value = ''
  isLoading.value = true

  try {
    const res = await api.login(email.value, password.value)
    if (res && res.token) {
      setAdminToken(res.token)
      const redirectPath = route.query.redirect || '/admin'
      router.push(redirectPath)
    } else {
      errorMessage.value = 'Failed to retrieve access token.'
    }
  } catch (err) {
    errorMessage.value = err.message || 'Invalid email or password.'
  } finally {
    isLoading.value = false
  }
}
</script>
