<template>
  <div
    :class="[
      'relative select-none flex items-center justify-center',
      compact ? 'w-10 h-11 flex-shrink-0' : 'w-full max-w-[280px] sm:max-w-[320px] mx-auto'
    ]"
  >
    <svg
      viewBox="0 0 200 220"
      class="w-full h-full drop-shadow-md overflow-visible"
      xmlns="http://www.w3.org/2000/svg"
      xmlns:xlink="http://www.w3.org/1999/xlink"
    >
      <defs>
        <!-- Circular clip for team crest -->
        <clipPath :id="`crest-clip-${instanceId}`">
          <circle cx="132" cy="74" r="13" />
        </clipPath>

        <!-- Vertical pinstripe pattern (authentic matchday jersey stripes) -->
        <pattern
          :id="`stripes-${instanceId}`"
          width="12"
          height="20"
          patternUnits="userSpaceOnUse"
        >
          <line
            x1="0"
            y1="0"
            x2="0"
            y2="20"
            :stroke="isGk ? 'rgba(0,0,0,0.1)' : 'rgba(255,255,255,0.07)'"
            stroke-width="1.8"
          />
        </pattern>

        <!-- Torso Body Gradient (Rich contrast for dark backgrounds) -->
        <linearGradient :id="`body-grad-${instanceId}`" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" :stop-color="isGk ? '#e5e7eb' : '#2a2b30'" />
          <stop offset="35%" :stop-color="isGk ? '#d1d4db' : '#1e1f23'" />
          <stop offset="100%" :stop-color="isGk ? '#b5b8c0' : '#121215'" />
        </linearGradient>

        <!-- Left sleeve gradient -->
        <linearGradient :id="`sleeve-grad-l-${instanceId}`" x1="0" y1="0" x2="1" y2="1">
          <stop offset="0%" :stop-color="isGk ? '#eceef2' : '#32333a'" />
          <stop offset="100%" :stop-color="isGk ? '#bcc0c8' : '#161619'" />
        </linearGradient>

        <!-- Right sleeve gradient -->
        <linearGradient :id="`sleeve-grad-r-${instanceId}`" x1="1" y1="0" x2="0" y2="1">
          <stop offset="0%" :stop-color="isGk ? '#eceef2' : '#32333a'" />
          <stop offset="100%" :stop-color="isGk ? '#bcc0c8' : '#161619'" />
        </linearGradient>

        <!-- Drop shadow filter for realism -->
        <filter :id="`jersey-shadow-${instanceId}`" x="-15%" y="-15%" width="130%" height="130%">
          <feDropShadow dx="0" dy="5" stdDeviation="5" flood-color="#000000" flood-opacity="0.45" />
        </filter>
      </defs>

      <!-- JERSEY BASE SILHOUETTE GROUP -->
      <g :filter="compact ? 'none' : `url(#jersey-shadow-${instanceId})`">
        <!-- Left Sleeve -->
        <path
          d="M 62 38 L 14 64 C 11 66 9 70 11 74 L 22 99 C 24 103 28 104 32 102 L 50 82 Z"
          :fill="`url(#sleeve-grad-l-${instanceId})`"
          :stroke="isGk ? '#8e9198' : 'rgba(255,255,255,0.22)'"
          :stroke-width="compact ? '1.5' : '1.2'"
        />
        <!-- Left Sleeve Contrast Ribbed Cuff -->
        <path
          d="M 14 64 L 22 99 L 29 96 L 20 61 Z"
          :fill="isGk ? '#141416' : '#ffffff'"
          :stroke="isGk ? '#222226' : 'none'"
          stroke-width="0.5"
        />

        <!-- Right Sleeve -->
        <path
          d="M 138 38 L 186 64 C 189 66 191 70 189 74 L 178 99 C 176 103 172 104 168 102 L 150 82 Z"
          :fill="`url(#sleeve-grad-r-${instanceId})`"
          :stroke="isGk ? '#8e9198' : 'rgba(255,255,255,0.22)'"
          :stroke-width="compact ? '1.5' : '1.2'"
        />
        <!-- Right Sleeve Contrast Ribbed Cuff -->
        <path
          d="M 186 64 L 178 99 L 171 96 L 180 61 Z"
          :fill="isGk ? '#141416' : '#ffffff'"
          :stroke="isGk ? '#222226' : 'none'"
          stroke-width="0.5"
        />

        <!-- Main Torso Body (Athletic Taper & Curved Hem) -->
        <path
          d="M 62 38 C 74 46 126 46 138 38 L 150 80 C 147 115 149 155 147 200 C 120 205 80 205 53 200 C 51 155 53 115 50 80 Z"
          :fill="`url(#body-grad-${instanceId})`"
          :stroke="isGk ? '#8e9198' : 'rgba(255,255,255,0.22)'"
          :stroke-width="compact ? '1.5' : '1.2'"
        />

        <!-- Fabric Vertical Pinstripe Texture -->
        <path
          d="M 62 38 C 74 46 126 46 138 38 L 150 80 C 147 115 149 155 147 200 C 120 205 80 205 53 200 C 51 155 53 115 50 80 Z"
          :fill="`url(#stripes-${instanceId})`"
        />

        <!-- Hem bottom stitch accent -->
        <path
          d="M 54 196 C 80 201 120 201 146 196"
          fill="none"
          :stroke="isGk ? 'rgba(0,0,0,0.15)' : 'rgba(255,255,255,0.12)'"
          stroke-width="1"
          stroke-dasharray="3,2"
        />

        <!-- COLLAR RENDERING (Distinguish Front V-Neck vs Back High Collar) -->
        <!-- Front View Collar -->
        <template v-if="view === 'front' && !compact">
          <!-- Inner Neck Back Hole -->
          <path
            d="M 74 37 C 85 45 115 45 126 37 C 120 32 80 32 74 37 Z"
            :fill="isGk ? '#25262a' : '#08080a'"
          />
          <!-- Front Contrast Ribbed Collar Band -->
          <path
            d="M 64 37 C 78 54 122 54 136 37 C 128 31 72 31 64 37 Z"
            :fill="isGk ? '#141416' : '#ffffff'"
          />
          <!-- Inner Collar V Notch cutout -->
          <path
            d="M 74 38 C 84 48 116 48 126 38 C 120 34 80 34 74 38 Z"
            :fill="isGk ? '#2d2e33' : '#141417'"
          />
        </template>

        <!-- Back View Collar (Gentle high curve neckline) -->
        <template v-else>
          <!-- Outer Contrast Ribbed Collar Trim across back of neck -->
          <path
            d="M 62 37 C 82 43 118 43 138 37 C 134 32 66 32 62 37 Z"
            :fill="isGk ? '#141416' : '#ffffff'"
            :stroke="isGk ? '#222226' : 'rgba(0,0,0,0.15)'"
            stroke-width="0.5"
          />
        </template>
      </g>

      <!-- FRONT VIEW GRAPHICS -->
      <template v-if="view === 'front' && !compact">
        <!-- Bethel High School Official Crest on Left Chest (Viewer's Right) -->
        <g>
          <circle
            cx="132"
            cy="74"
            r="14"
            fill="#08080a"
            :stroke="isGk ? '#1e1e22' : '#cfa144'"
            stroke-width="1.2"
          />
          <image
            href="/images/bethel-logo.jpg"
            xlink:href="/images/bethel-logo.jpg"
            x="119"
            y="61"
            width="26"
            height="26"
            preserveAspectRatio="xMidYMid slice"
            :clip-path="`url(#crest-clip-${instanceId})`"
          />
        </g>

        <!-- ACE Sponsor on Right Chest (Viewer's Left) -->
        <g>
          <text
            x="68"
            y="78"
            text-anchor="middle"
            :fill="isGk ? '#141416' : '#ffffff'"
            font-family="'Space Grotesk', 'Inter', sans-serif"
            font-weight="900"
            font-size="11"
            letter-spacing="1"
          >
            ACE
          </text>
        </g>

        <!-- Center Chest Squad Number -->
        <g>
          <text
            x="100"
            y="85"
            text-anchor="middle"
            :fill="isGk ? '#141416' : '#ffffff'"
            font-family="'Space Grotesk', 'JetBrains Mono', sans-serif"
            font-weight="900"
            font-size="17"
            letter-spacing="-0.5"
          >
            {{ formattedNumber }}
          </text>

          <!-- Authentic Stylized DE Sport Brand Mark below chest number -->
          <g transform="translate(93, 91) scale(0.65)">
            <path
              d="M 0 0 L 5 0 C 8 0 10 2 10 5 C 10 8 8 10 5 10 L 0 10 Z M 2.2 2 L 2.2 8 L 5 8 C 6.5 8 7.5 7 7.5 5 C 7.5 3 6.5 2 5 2 Z"
              :fill="isGk ? '#141416' : '#ffffff'"
            />
            <path
              d="M 12 0 L 20 0 L 20 2.2 L 14.5 2.2 L 14.5 4 L 19 4 L 19 6.2 L 14.5 6.2 L 14.5 8 L 20 8 L 20 10 L 12 10 Z"
              :fill="isGk ? '#141416' : '#ffffff'"
            />
          </g>
        </g>
      </template>

      <!-- BACK VIEW GRAPHICS (Default & Compact) -->
      <template v-else>
        <!-- Player Name on Upper Back (Full view only) -->
        <text
          v-if="!compact && playerName"
          x="100"
          y="74"
          text-anchor="middle"
          :fill="isGk ? '#141416' : '#ffffff'"
          font-family="'Space Grotesk', 'Inter', sans-serif"
          font-weight="800"
          :font-size="nameFontSize"
          letter-spacing="2.5"
          class="uppercase"
        >
          {{ playerName }}
        </text>

        <!-- Squad Number on Back (Large, Centered, Crisp Athletic Typography) -->
        <text
          x="100"
          :y="compact ? 134 : 140"
          text-anchor="middle"
          dominant-baseline="central"
          :fill="isGk ? '#111113' : '#ffffff'"
          font-family="'Space Grotesk', 'JetBrains Mono', sans-serif"
          font-weight="900"
          :font-size="formattedNumber === '—' ? (compact ? '60' : '54') : (compact ? '84' : '74')"
          letter-spacing="-2"
          :stroke="compact && isGk ? 'none' : compact ? 'rgba(0,0,0,0.6)' : 'none'"
          :stroke-width="compact && !isGk ? '1.5' : '0'"
        >
          {{ formattedNumber }}
        </text>
      </template>
    </svg>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { formatJerseyNumber } from '../utils/formatters'

const props = defineProps({
  number: {
    type: [Number, String],
    default: null
  },
  name: {
    type: String,
    default: ''
  },
  view: {
    type: String,
    default: 'back',
    validator: (v) => ['back', 'front'].includes(v)
  },
  isGk: {
    type: Boolean,
    default: false
  },
  compact: {
    type: Boolean,
    default: false
  }
})

// Unique ID for SVG definitions/clipPaths
const instanceId = Math.random().toString(36).substring(2, 9)

const formattedNumber = computed(() => {
  return formatJerseyNumber(props.number)
})

const playerName = computed(() => {
  if (!props.name) return ''
  return props.name.trim()
})

// Dynamically scale font size for long player names
const nameFontSize = computed(() => {
  const len = playerName.value.length
  if (len > 15) return '8.5'
  if (len > 12) return '10'
  if (len > 8) return '11.5'
  return '13'
})
</script>
