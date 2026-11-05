/**
 * Format player jersey numbers consistently for display:
 * - 0 -> '0'
 * - 1 -> '1'
 * - 7 -> '7'
 * - 10 -> '10'
 * - null -> '—'
 * - undefined -> '—'
 * - Empty string "" -> '—'
 * - Whitespace-only string -> '—'
 *
 * Rules:
 * - Never display '00', '01', or '07'.
 * - Do not treat the valid number 0 as empty.
 * - Do not display 'NaN' or 'undefined'.
 * - Do not invent a jersey number for players without one.
 * - Format numbers for display only. Never modify database records.
 */
export function formatJerseyNumber(val) {
  if (val === null || val === undefined) {
    return '—'
  }

  if (typeof val === 'number') {
    if (Number.isNaN(val)) return '—'
    return String(val)
  }

  const str = String(val).trim()
  if (str === '' || str === 'null' || str === 'undefined' || str === 'NaN') {
    return '—'
  }

  // Parse pure digit strings to strip any leading zeros (e.g. "01" -> 1 -> "1", "00" -> 0 -> "0")
  if (/^\d+$/.test(str)) {
    const num = parseInt(str, 10)
    return Number.isNaN(num) ? '—' : String(num)
  }

  return str
}
