/**
 * API Service for Team Jersey Tracker
 * Handles communication with the Laravel backend.
 */

const TOKEN_KEY = 'team_jersey_admin_token'
const API_BASE = (import.meta.env.VITE_API_URL || '').replace(/\/+$/, '')

export function getAdminToken() {
  return localStorage.getItem(TOKEN_KEY) || ''
}

export function setAdminToken(token) {
  if (token) {
    localStorage.setItem(TOKEN_KEY, token)
  } else {
    localStorage.removeItem(TOKEN_KEY)
  }
}

export function removeAdminToken() {
  localStorage.removeItem(TOKEN_KEY)
}

async function request(endpoint, options = {}) {
  const headers = {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    ...(options.headers || {})
  }

  const token = getAdminToken()
  if (token && !headers['Authorization']) {
    headers['Authorization'] = `Bearer ${token}`
  }

  const config = {
    ...options,
    headers
  }

  const targetUrl = endpoint.startsWith('http') ? endpoint : `${API_BASE}${endpoint}`
  const response = await fetch(targetUrl, config)

  if (response.status === 401 && endpoint.startsWith('/api/admin') && !endpoint.includes('/login')) {
    removeAdminToken()
    if (window.location.pathname.startsWith('/admin')) {
      window.location.href = '/admin/login'
    }
  }

  const data = await response.json().catch(() => ({}))

  if (!response.ok) {
    const error = new Error(data.message || `Request failed with status ${response.status}`)
    error.status = response.status
    error.data = data
    throw error
  }

  return data
}

export const api = {
  // Public Endpoints
  getPlayers: () => request('/api/players'),
  getPlayer: (id) => request(`/api/players/${id}`),

  // Admin Authentication
  login: (email, password) =>
    request('/api/admin/login', {
      method: 'POST',
      body: JSON.stringify({ email, password })
    }),

  logout: () =>
    request('/api/admin/logout', {
      method: 'POST'
    }),

  getMe: () => request('/api/admin/me'),

  // Admin Player Management
  createPlayer: (playerData) =>
    request('/api/admin/players', {
      method: 'POST',
      body: JSON.stringify(playerData)
    }),

  updatePlayer: (id, playerData) =>
    request(`/api/admin/players/${id}`, {
      method: 'PUT',
      body: JSON.stringify(playerData)
    }),

  updatePaymentStatus: (id, payment_status) =>
    request(`/api/admin/players/${id}/payment`, {
      method: 'PATCH',
      body: JSON.stringify({ payment_status })
    }),

  deletePlayer: (id) =>
    request(`/api/admin/players/${id}`, {
      method: 'DELETE'
    })
}
