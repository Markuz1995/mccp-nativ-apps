import { useState, useEffect } from 'react'
import { fetchMessages } from '../api'

const STATUS_STYLES = {
  pending: { bg: '#1c1917', color: 'var(--color-warning)', border: 'var(--color-warning)' },
  processing: { bg: '#172554', color: '#93c5fd', border: '#3b82f6' },
  completed: { bg: '#052e16', color: '#86efac', border: 'var(--color-success)' },
  failed: { bg: '#3b0a0a', color: '#fca5a5', border: 'var(--color-error)' },
}

function History() {
  const [messages, setMessages] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)

  useEffect(() => {
    let cancelled = false

    const load = async () => {
      setLoading(true)
      setError(null)
      try {
        const data = await fetchMessages()
        if (!cancelled) setMessages(data.data ?? data)
      } catch (err) {
        if (!cancelled) {
          setError(
            err.response?.data?.message ||
              err.message ||
              'Failed to load messages',
          )
        }
      } finally {
        if (!cancelled) setLoading(false)
      }
    }

    load()
    return () => { cancelled = true }
  }, [])

  if (loading) {
    return (
      <div style={styles.container}>
        <h1 style={styles.title}>History Dashboard</h1>
        <p style={{ color: 'var(--color-muted)' }}>Loading messages...</p>
      </div>
    )
  }

  if (error) {
    return (
      <div style={styles.container}>
        <h1 style={styles.title}>History Dashboard</h1>
        <div style={styles.alertError}>
          <span>✕</span> {error}
        </div>
      </div>
    )
  }

  return (
    <div style={styles.container}>
      <h1 style={styles.title}>History Dashboard</h1>

      {messages.length === 0 ? (
        <p style={{ color: 'var(--color-muted)' }}>No messages yet. Send your first message!</p>
      ) : (
        <table style={styles.table}>
          <thead>
            <tr>
              {['Date', 'Title', 'Summary', 'Status'].map((h) => (
                <th key={h} style={styles.th}>{h}</th>
              ))}
            </tr>
          </thead>
          <tbody>
            {messages.map((msg) => {
              const st = STATUS_STYLES[msg.status] || STATUS_STYLES.pending
              return (
                <tr key={msg.id} style={styles.tr}>
                  <td style={styles.td}>
                    {msg.created_at
                      ? new Date(msg.created_at).toLocaleDateString()
                      : '—'}
                  </td>
                  <td style={styles.td}>{msg.title}</td>
                  <td style={{ ...styles.td, color: 'var(--color-muted)' }}>
                    {msg.summary || '—'}
                  </td>
                  <td style={styles.td}>
                    <span
                      style={{
                        ...styles.badge,
                        background: st.bg,
                        color: st.color,
                        border: `1px solid ${st.border}`,
                      }}
                    >
                      {msg.status}
                    </span>
                  </td>
                </tr>
              )
            })}
          </tbody>
        </table>
      )}

      {messages.length > 0 && (
        <p style={{ color: 'var(--color-muted)', fontSize: '0.8rem', marginTop: '1rem' }}>
          Showing {messages.length} message{messages.length !== 1 ? 's' : ''}
        </p>
      )}
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

export default History
