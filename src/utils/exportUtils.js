/**
 * Bethel High School — Team Jersey Tracker
 * Export Utilities: Excel (.xlsx) & PDF (.pdf)
 *
 * Implements client-side document generation without backend dependencies.
 * Respects active admin dashboard filters and calculates dynamic statistics
 * directly from the exported dataset.
 */

import { formatJerseyNumber } from './formatters.js';

/**
 * Calculate dynamic squad statistics from the provided players array.
 * Guaranteed to compute counts for all 5 jersey sizes (S, M, L, XL, XXL)
 * and all 4 payment statuses (paid, half_paid, unpaid, not_confirmed).
 *
 * @param {Array} players List of players being exported
 * @returns {Object} Calculated metrics
 */
export function calculateExportStats(players = []) {
  const total = players.length;
  const sizes = { S: 0, M: 0, L: 0, XL: 0, XXL: 0 };
  const payment = { paid: 0, half_paid: 0, unpaid: 0, not_confirmed: 0 };

  for (const player of players) {
    // 1. Jersey Size breakdown
    const sz = String(player.jersey_size || player.size || '').toUpperCase().trim();
    if (sz in sizes) {
      sizes[sz]++;
    }

    // 2. Payment Status breakdown
    const rawStatus = String(player.payment_status || player.status || '')
      .toLowerCase()
      .replace(/[\s%]+/g, '_');

    if (rawStatus === 'paid') {
      payment.paid++;
    } else if (rawStatus === 'half_paid' || rawStatus === '50_paid' || rawStatus === '50_percent_paid') {
      payment.half_paid++;
    } else if (rawStatus === 'not_confirmed') {
      payment.not_confirmed++;
    } else {
      payment.unpaid++;
    }
  }

  return { total, sizes, payment };
}

/**
 * Return human-readable label for player's payment status.
 *
 * @param {Object} player
 * @returns {string} Human-readable status ('Paid', '50% Paid', 'Unpaid', 'Not Confirmed')
 */
export function getHumanPaymentStatus(player) {
  const rawStatus = String(player.payment_status || player.status || '')
    .toLowerCase()
    .replace(/[\s%]+/g, '_');

  if (rawStatus === 'paid') return 'Paid';
  if (rawStatus === 'half_paid' || rawStatus === '50_paid' || rawStatus === '50_percent_paid') return '50% Paid';
  if (rawStatus === 'not_confirmed') return 'Not Confirmed';
  return 'Unpaid';
}

/**
 * Get current formatted timestamps for reports.
 *
 * @returns {{ dateOnly: string, fullDateTime: string }}
 */
export function getReportTimestamp() {
  const now = new Date();
  const yyyy = now.getFullYear();
  const mm = String(now.getMonth() + 1).padStart(2, '0');
  const dd = String(now.getDate()).padStart(2, '0');
  const hh = String(now.getHours()).padStart(2, '0');
  const min = String(now.getMinutes()).padStart(2, '0');

  return {
    dateOnly: `${yyyy}-${mm}-${dd}`,
    fullDateTime: `${yyyy}-${mm}-${dd} ${hh}:${min}`
  };
}

/**
 * Build a concise description of the active dashboard filters.
 *
 * @param {Object} filterOptions
 * @returns {string}
 */
export function formatFilterDescription(filterOptions = {}) {
  const parts = [];

  if (filterOptions.searchQuery && filterOptions.searchQuery.trim()) {
    parts.push(`Search: "${filterOptions.searchQuery.trim()}"`);
  }
  if (filterOptions.filterStatus && filterOptions.filterStatus !== 'All') {
    parts.push(`Status: ${filterOptions.filterStatus}`);
  }
  if (filterOptions.filterSize && filterOptions.filterSize !== 'All') {
    parts.push(`Size: ${filterOptions.filterSize}`);
  }

  return parts.length > 0 ? parts.join(' | ') : 'All Players (No filters applied)';
}

/**
 * Export filtered player roster and dynamic summary to an Excel (.xlsx) workbook.
 * Creates two worksheets:
 *   - "Squad Roster": Full player list with 5 standard columns.
 *   - "Summary & Stats": Executive summary, size breakdown, and payment status breakdown.
 *
 * @param {Array} players Filtered players list
 * @param {Object} filterOptions Active search and filter parameters
 * @returns {Promise<{ success: boolean, filename: string, count: number }>}
 */
export async function exportSquadToExcel(players = [], filterOptions = {}) {
  if (!players || players.length === 0) {
    throw new Error('NO_PLAYERS');
  }

  // Load XLSX dynamically on-demand to keep initial app bundle light
  const XLSX = await import('xlsx');

  const stats = calculateExportStats(players);
  const { dateOnly, fullDateTime } = getReportTimestamp();
  const activeFilters = formatFilterDescription(filterOptions);

  // 1. Build Worksheet 1: "Squad Roster"
  const rosterRows = players.map((p) => {
    const rawNumber = p.shirt_number ?? p.number;
    const formattedNum = formatJerseyNumber(rawNumber);
    const numericVal = formattedNum !== '—' && /^\d+$/.test(formattedNum) ? Number(formattedNum) : formattedNum;

    return {
      'Shirt Number': numericVal,
      'Player Name': p.name || '',
      'Jersey Size': String(p.jersey_size || p.size || '').toUpperCase(),
      'Payment Status': getHumanPaymentStatus(p),
      'Notes / Role': p.notes || ''
    };
  });

  const wsRoster = XLSX.utils.json_to_sheet(rosterRows, {
    header: ['Shirt Number', 'Player Name', 'Jersey Size', 'Payment Status', 'Notes / Role']
  });

  // Set readable column widths
  wsRoster['!cols'] = [
    { wch: 14 }, // Shirt Number
    { wch: 28 }, // Player Name
    { wch: 14 }, // Jersey Size
    { wch: 20 }, // Payment Status
    { wch: 35 }  // Notes / Role
  ];

  // 2. Build Worksheet 2: "Summary & Stats"
  const summaryRows = [
    ['Bethel High School — Team Jersey Tracker'],
    ['Executive Summary & Audit Statistics'],
    [],
    ['Report Generation Date', fullDateTime],
    ['Total Exported Players', stats.total],
    ['Active Filter Scope', activeFilters],
    [],
    ['Jersey Size Breakdown', 'Count'],
    ['S', stats.sizes.S],
    ['M', stats.sizes.M],
    ['L', stats.sizes.L],
    ['XL', stats.sizes.XL],
    ['XXL', stats.sizes.XXL],
    [],
    ['Payment Status Breakdown', 'Count'],
    ['Paid', stats.payment.paid],
    ['50% Paid', stats.payment.half_paid],
    ['Unpaid', stats.payment.unpaid],
    ['Not Confirmed', stats.payment.not_confirmed]
  ];

  const wsSummary = XLSX.utils.aoa_to_sheet(summaryRows);
  wsSummary['!cols'] = [
    { wch: 28 },
    { wch: 32 }
  ];

  // 3. Assemble workbook
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, wsRoster, 'Squad Roster');
  XLSX.utils.book_append_sheet(wb, wsSummary, 'Summary & Stats');

  const filename = `Bethel_Jersey_Tracker_${dateOnly}.xlsx`;

  // 4. Trigger download
  XLSX.writeFile(wb, filename);

  return {
    success: true,
    filename,
    count: players.length
  };
}

/**
 * Generate a printable, professional A4 portrait PDF report of the filtered squad roster.
 * Styled with Bethel High School branding (dark charcoal, gold accents, red highlights)
 * and renders Khmer notes using Kantumruy Pro with guaranteed font loading.
 *
 * @param {Array} players Filtered players list
 * @param {Object} filterOptions Active search and filter parameters
 * @returns {Promise<{ success: boolean, filename: string, count: number }>}
 */
export async function exportSquadToPdf(players = [], filterOptions = {}) {
  if (!players || players.length === 0) {
    throw new Error('NO_PLAYERS');
  }

  // 1. Wait for document fonts to be fully rendered (essential for Khmer Unicode shaping)
  if (typeof document !== 'undefined' && document.fonts && document.fonts.ready) {
    await document.fonts.ready;
  }

  // Load html2pdf dynamically on-demand to keep initial app bundle light
  const html2pdfModule = await import('html2pdf.js');
  const html2pdf = html2pdfModule.default || html2pdfModule;

  const stats = calculateExportStats(players);
  const { dateOnly, fullDateTime } = getReportTimestamp();
  const activeFilters = formatFilterDescription(filterOptions);

  // 2. Build rows HTML
  const rowsHtml = players.map((p, idx) => {
    const rawNumber = p.shirt_number ?? p.number;
    const formattedNum = formatJerseyNumber(rawNumber);
    const size = String(p.jersey_size || p.size || '').toUpperCase();
    const statusText = getHumanPaymentStatus(p);
    const notes = p.notes || '';

    // Color badges for payment status
    let statusBadgeColor = '#9CA3AF'; // neutral
    let statusBgColor = 'rgba(156, 163, 175, 0.15)';
    if (statusText === 'Paid') {
      statusBadgeColor = '#34D399'; // emerald
      statusBgColor = 'rgba(16, 185, 129, 0.15)';
    } else if (statusText === '50% Paid') {
      statusBadgeColor = '#FBBF24'; // amber
      statusBgColor = 'rgba(245, 158, 11, 0.15)';
    } else if (statusText === 'Unpaid') {
      statusBadgeColor = '#EF4444'; // red
      statusBgColor = 'rgba(239, 68, 68, 0.15)';
    }

    const rowBg = idx % 2 === 0 ? '#141414' : '#181818';

    return `
      <tr style="background-color: ${rowBg}; page-break-inside: avoid; break-inside: avoid; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
        <td style="padding: 9px 12px; font-weight: 700; color: #D5AC50; font-family: 'JetBrains Mono', monospace; font-size: 11px; text-align: center; width: 48px;">
          ${formattedNum !== '—' ? '#' + formattedNum : '—'}
        </td>
        <td style="padding: 9px 12px; font-weight: 600; color: #FFFFFF; font-size: 11px;">
          ${escapeHtml(p.name || '')}
        </td>
        <td style="padding: 9px 12px; text-align: center; width: 64px;">
          <span style="display: inline-block; padding: 2px 8px; border-radius: 6px; background: #222222; border: 1px solid rgba(255,255,255,0.12); color: #FFFFFF; font-weight: 700; font-size: 10px; font-family: 'JetBrains Mono', monospace;">
            ${size || '—'}
          </span>
        </td>
        <td style="padding: 9px 12px; width: 110px;">
          <span style="display: inline-block; padding: 3px 8px; border-radius: 6px; background: ${statusBgColor}; color: ${statusBadgeColor}; font-weight: 700; font-size: 10px; border: 1px solid ${statusBadgeColor}33; text-transform: uppercase;">
            ${statusText}
          </span>
        </td>
        <td style="padding: 9px 12px; color: #D1D5DB; font-size: 11px; font-family: 'Kantumruy Pro', 'Inter', sans-serif; word-break: break-word;">
          ${escapeHtml(notes)}
        </td>
      </tr>
    `;
  }).join('');

  // 3. Construct print wrapper element
  const reportContainer = document.createElement('div');
  reportContainer.id = 'bethel-pdf-export-container';
  reportContainer.style.width = '780px';
  reportContainer.style.backgroundColor = '#0A0A0A';
  reportContainer.style.color = '#FFFFFF';
  reportContainer.style.fontFamily = "'Kantumruy Pro', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif";
  reportContainer.style.padding = '24px 28px';
  reportContainer.style.boxSizing = 'border-box';
  reportContainer.style.margin = '40px auto';
  reportContainer.style.display = 'block';

  reportContainer.innerHTML = `
    <!-- Bethel Header Banner -->
    <div style="border-bottom: 2px solid #D5AC50; padding-bottom: 14px; margin-bottom: 14px; display: flex; justify-content: space-between; align-items: flex-start;">
      <div>
        <div style="font-size: 10px; font-weight: 800; letter-spacing: 2px; color: #D5AC50; text-transform: uppercase; margin-bottom: 2px;">
          BETHEL HIGH SCHOOL · SEASON 2026
        </div>
        <h1 style="margin: 0; font-size: 19px; font-weight: 800; color: #FFFFFF; letter-spacing: -0.5px;">
          Team Jersey Tracker — Squad Roster
        </h1>
        <p style="margin: 3px 0 0 0; font-size: 10px; color: #9CA3AF;">
          Official Kit Allocation & Payment Audit Ledger
        </p>
      </div>
      <div style="text-align: right; font-size: 10px; color: #9CA3AF; line-height: 1.5;">
        <div><strong style="color: #FFFFFF;">Generated:</strong> ${fullDateTime}</div>
        <div><strong style="color: #D5AC50;">Export Scope:</strong> ${stats.total} Players</div>
      </div>
    </div>

    <!-- Filter Scope Pill -->
    <div style="background-color: #171717; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; padding: 7px 12px; margin-bottom: 12px; font-size: 10px; color: #D1D5DB; display: flex; align-items: center; justify-content: space-between;">
      <div>
        <span style="color: #9CA3AF; font-weight: 600; text-transform: uppercase; margin-right: 6px;">Active Filter:</span>
        <strong style="color: #FFFFFF;">${escapeHtml(activeFilters)}</strong>
      </div>
      <div style="color: #D5AC50; font-weight: 700; font-family: 'JetBrains Mono', monospace;">
        ${stats.total} matching
      </div>
    </div>

    <!-- Executive KPI Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; margin-bottom: 12px;">
      <div style="background-color: #171717; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 8px 10px;">
        <div style="font-size: 9px; font-weight: 700; color: #9CA3AF; text-transform: uppercase;">Total</div>
        <div style="font-size: 18px; font-weight: 800; color: #FFFFFF; font-family: 'JetBrains Mono', monospace; margin-top: 2px;">${stats.total}</div>
      </div>
      <div style="background-color: #171717; border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 8px; padding: 8px 10px;">
        <div style="font-size: 9px; font-weight: 700; color: #34D399; text-transform: uppercase;">Paid</div>
        <div style="font-size: 18px; font-weight: 800; color: #34D399; font-family: 'JetBrains Mono', monospace; margin-top: 2px;">${stats.payment.paid}</div>
      </div>
      <div style="background-color: #171717; border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 8px; padding: 8px 10px;">
        <div style="font-size: 9px; font-weight: 700; color: #FBBF24; text-transform: uppercase;">50% Paid</div>
        <div style="font-size: 18px; font-weight: 800; color: #FBBF24; font-family: 'JetBrains Mono', monospace; margin-top: 2px;">${stats.payment.half_paid}</div>
      </div>
      <div style="background-color: #171717; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 8px; padding: 8px 10px;">
        <div style="font-size: 9px; font-weight: 700; color: #EF4444; text-transform: uppercase;">Unpaid</div>
        <div style="font-size: 18px; font-weight: 800; color: #EF4444; font-family: 'JetBrains Mono', monospace; margin-top: 2px;">${stats.payment.unpaid}</div>
      </div>
      <div style="background-color: #171717; border: 1px solid rgba(156, 163, 175, 0.3); border-radius: 8px; padding: 8px 10px;">
        <div style="font-size: 9px; font-weight: 700; color: #9CA3AF; text-transform: uppercase;">Review</div>
        <div style="font-size: 18px; font-weight: 800; color: #D1D5DB; font-family: 'JetBrains Mono', monospace; margin-top: 2px;">${stats.payment.not_confirmed}</div>
      </div>
    </div>

    <!-- Jersey Sizing Breakdown Bar -->
    <div style="background-color: #171717; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 6px 12px; margin-bottom: 14px; display: flex; align-items: center; justify-content: space-between; font-size: 10px;">
      <span style="color: #9CA3AF; font-weight: 700; text-transform: uppercase; display: flex; align-items: center; gap: 6px;">
        <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #D5AC50;"></span>
        Kit Sizing Breakdown:
      </span>
      <div style="display: flex; gap: 8px; font-family: 'JetBrains Mono', monospace; font-size: 10px;">
        <span style="padding: 2px 7px; background: #222222; border-radius: 5px; border: 1px solid rgba(255,255,255,0.08);"><strong style="color: #9CA3AF;">S:</strong> <span style="color: #FFFFFF; font-weight: 700;">${stats.sizes.S}</span></span>
        <span style="padding: 2px 7px; background: #222222; border-radius: 5px; border: 1px solid rgba(255,255,255,0.08);"><strong style="color: #9CA3AF;">M:</strong> <span style="color: #FFFFFF; font-weight: 700;">${stats.sizes.M}</span></span>
        <span style="padding: 2px 7px; background: #222222; border-radius: 5px; border: 1px solid rgba(255,255,255,0.08);"><strong style="color: #9CA3AF;">L:</strong> <span style="color: #D5AC50; font-weight: 700;">${stats.sizes.L}</span></span>
        <span style="padding: 2px 7px; background: #222222; border-radius: 5px; border: 1px solid rgba(255,255,255,0.08);"><strong style="color: #9CA3AF;">XL:</strong> <span style="color: #EF4444; font-weight: 700;">${stats.sizes.XL}</span></span>
        <span style="padding: 2px 7px; background: #222222; border-radius: 5px; border: 1px solid rgba(255,255,255,0.08);"><strong style="color: #9CA3AF;">XXL:</strong> <span style="color: #E5C478; font-weight: 700;">${stats.sizes.XXL}</span></span>
      </div>
    </div>

    <!-- Squad Roster Table -->
    <table style="width: 100%; border-collapse: collapse; text-align: left; margin-bottom: 16px;">
      <thead>
        <tr style="background-color: #1F1F1F; border-bottom: 2px solid #D5AC50; color: #D5AC50; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
          <th style="padding: 9px 12px; text-align: center; width: 48px;">#</th>
          <th style="padding: 9px 12px;">Player Name</th>
          <th style="padding: 9px 12px; text-align: center; width: 64px;">Size</th>
          <th style="padding: 9px 12px; width: 110px;">Payment Status</th>
          <th style="padding: 9px 12px;">Notes / Role</th>
        </tr>
      </thead>
      <tbody>
        ${rowsHtml}
      </tbody>
    </table>

    <!-- Document Footer -->
    <div style="border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 10px; font-size: 9px; color: #6B7280; display: flex; justify-content: space-between; align-items: center;">
      <span>Bethel High School Official Jersey Tracker • Season 2026</span>
      <span>Confidential Team Document</span>
    </div>
  `;

  document.body.appendChild(reportContainer);

  const filename = `Bethel_Jersey_Tracker_${dateOnly}.pdf`;

  const pdfOptions = {
    margin: [8, 8, 8, 8], // mm
    filename,
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: {
      scale: 2,
      useCORS: true,
      logging: true,
      backgroundColor: '#0A0A0A',
      onclone: (clonedDoc) => {
        const overlay = clonedDoc.querySelector('.html2pdf__overlay');
        if (overlay) overlay.style.opacity = '1';
        const el = clonedDoc.getElementById('bethel-pdf-export-container');
        if (el) {
          el.style.position = 'static';
          el.style.height = 'auto';
        }
      }
    },
    jsPDF: {
      unit: 'mm',
      format: 'a4',
      orientation: 'portrait'
    },
    pagebreak: {
      mode: ['avoid-all', 'css', 'legacy']
    }
  };

  try {
    // Wait small tick to ensure browser has painted styles & webfonts
    await new Promise((resolve) => setTimeout(resolve, 80));

    // Explicitly chain toPdf() before save() to ensure html2canvas renders the container
    const generator = typeof html2pdf === 'function' ? html2pdf : (html2pdf.default || html2pdf);
    await generator().set(pdfOptions).from(reportContainer).toPdf().save();

    return {
      success: true,
      filename,
      count: players.length
    };
  } finally {
    // Clean up temporary DOM element
    if (reportContainer && reportContainer.parentNode) {
      reportContainer.parentNode.removeChild(reportContainer);
    }
  }
}

/**
 * Basic HTML sanitizer to prevent XSS during report generation.
 *
 * @param {string} str
 * @returns {string}
 */
function escapeHtml(str) {
  if (typeof str !== 'string') return '';
  return str
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}
