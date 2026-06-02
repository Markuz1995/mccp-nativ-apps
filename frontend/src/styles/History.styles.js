const styles = {
  container: {
    background: 'var(--color-surface)',
    border: '1px solid var(--color-border)',
    borderRadius: 'var(--radius-lg)',
    padding: '2rem',
  },
  title: {
    fontSize: '1.6rem',
    fontWeight: 700,
    marginBottom: '1.5rem',
    color: 'var(--color-text)',
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
  badge: {
    borderRadius: '4px',
    padding: '2px 8px',
    fontSize: '0.75rem',
    fontWeight: 600,
    display: 'inline-block',
  },
  alertError: {
    display: 'flex',
    alignItems: 'center',
    gap: '0.5rem',
    padding: '10px 14px',
    background: '#3b0a0a',
    border: '1px solid var(--color-error)',
    borderRadius: 'var(--radius)',
    color: '#fca5a5',
    fontSize: '0.9rem',
  },
}

export default styles
