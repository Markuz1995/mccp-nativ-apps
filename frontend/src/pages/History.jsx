/**
 * History — FASE 1 Placeholder
 * This screen will be fully implemented in FASE 3.
 */
function History() {
  const mockRows = [
    { date: '2026-06-01', title: 'Test message', summary: '—', status: 'pending' },
  ]

  return (
    <div style={styles.container}>
      <div style={styles.badge}>FASE 3</div>
      <h1 style={styles.title}>History Dashboard</h1>
      <p style={styles.desc}>
        Table with sent messages history: date, title, AI summary and status.
      </p>
      <table style={styles.table}>
        <thead>
          <tr>
            {['Date', 'Title', 'Summary', 'Status'].map((h) => (
              <th key={h} style={styles.th}>{h}</th>
            ))}
          </tr>
        </thead>
        <tbody>
          {mockRows.map((row, i) => (
            <tr key={i} style={styles.tr}>
              <td style={styles.td}>{row.date}</td>
              <td style={styles.td}>{row.title}</td>
              <td style={{ ...styles.td, color: 'var(--color-muted)' }}>{row.summary}</td>
              <td style={styles.td}>
                <span style={styles.statusBadge}>{row.status}</span>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  )
}

const styles = {
  container: {
    background: 'var(--color-surface)',
    border: '1px solid var(--color-border)',
    borderRadius: 'var(--radius-lg)',
    padding: '2rem',
  },
  badge: {
    display: 'inline-block',
    background: '#1e1b4b',
    color: 'var(--color-primary-h)',
    border: '1px solid var(--color-primary)',
    borderRadius: '20px',
    padding: '2px 12px',
    fontSize: '0.75rem',
    fontWeight: 700,
    marginBottom: '1rem',
    letterSpacing: '0.5px',
  },
  title: {
    fontSize: '1.6rem',
    fontWeight: 700,
    marginBottom: '0.5rem',
    color: 'var(--color-text)',
  },
  desc: {
    color: 'var(--color-muted)',
    marginBottom: '1.5rem',
    fontSize: '0.95rem',
  },
  table: {
    width: '100%',
    borderCollapse: 'collapse',
    fontSize: '0.9rem',
  },
  th: {
    textAlign: 'left',
    padding: '10px 14px',
    borderBottom: '1px solid var(--color-border)',
    color: 'var(--color-muted)',
    fontWeight: 600,
    fontSize: '0.8rem',
    textTransform: 'uppercase',
    letterSpacing: '0.5px',
  },
  tr: {
    borderBottom: '1px solid var(--color-border)',
  },
  td: {
    padding: '12px 14px',
    color: 'var(--color-text)',
  },
  statusBadge: {
    background: '#1c1917',
    color: 'var(--color-warning)',
    border: '1px solid var(--color-warning)',
    borderRadius: '4px',
    padding: '2px 8px',
    fontSize: '0.75rem',
    fontWeight: 600,
  },
}

export default History
