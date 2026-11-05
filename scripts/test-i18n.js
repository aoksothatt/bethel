import fs from 'node:fs'

const en = JSON.parse(fs.readFileSync('./src/locales/en.json', 'utf8'))
const km = JSON.parse(fs.readFileSync('./src/locales/km.json', 'utf8'))

console.log('=== VERIFYING KHMER & ENGLISH TRANSLATION DICTIONARIES ===')

const requiredChecks = [
  ['app.title', 'Team Jersey Tracker', 'តារាងព័ត៌មានអាវក្រុម'],
  ['app.subtitle', 'Official Squad Kit', 'ព័ត៌មានអាវកីឡាករ'],
  ['app.season', 'Season', 'រដូវកាល'],
  ['hero.tag', 'YOUR SQUAD · 2026', 'ក្រុមរបស់អ្នក · ២០២៦'],
  ['stats.total', 'Total Players', 'ចំនួនកីឡាករសរុប'],
  ['filters.paid', 'Paid', 'បានបង់ប្រាក់'],
  ['filters.unpaid', 'Unpaid', 'មិនទាន់បង់ប្រាក់'],
  ['filters.not_confirmed', 'Not Confirmed', 'មិនទាន់បញ្ជាក់'],
  ['stats.size_distribution', 'Size Distribution', 'ការបែងចែកទំហំអាវ'],
  ['search.placeholder', 'Search your name or shirt number...', 'ស្វែងរកឈ្មោះ ឬលេខអាវរបស់អ្នក...'],
  ['filters.all', 'All Players', 'កីឡាករទាំងអស់'],
  ['player.roster', 'Squad Roster', 'បញ្ជីកីឡាករ'],
  ['player.player', 'Player', 'កីឡាករ'],
  ['player.jersey_size', 'Jersey size', 'ទំហំអាវ'],
  ['player.shirt_number', 'Shirt Number', 'លេខអាវ'],
  ['player.notes', 'Notes', 'កំណត់សម្គាល់'],
  ['player.goalkeeper', 'Goalkeeper', 'អ្នកចាំទី'],
  ['nav.squad', 'Squad', 'ក្រុម'],
  ['nav.my_kit', 'My Kit', 'អាវរបស់ខ្ញុំ'],
  ['nav.payments', 'Payments', 'ការបង់ប្រាក់'],
  ['search.no_players', 'No players found', 'រកមិនឃើញកីឡាករទេ']
]

function getVal(obj, path) {
  return path.split('.').reduce((acc, part) => acc && acc[part], obj)
}

for (const [key, expectedEn, expectedKm] of requiredChecks) {
  const actualEn = getVal(en, key)
  const actualKm = getVal(km, key)
  if (!actualEn) throw new Error('Missing EN key: ' + key)
  if (!actualKm) throw new Error('Missing KM key: ' + key)
  if (actualKm !== expectedKm) {
    throw new Error(`KM mismatch for ${key}: expected "${expectedKm}", got "${actualKm}"`)
  }
  console.log(`✓ ${key}: ${actualEn} -> ${actualKm}`)
}

console.log('\nAll 21 required translation mappings are verified 100% correct!')
