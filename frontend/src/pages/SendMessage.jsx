import { useState } from 'react'
import { createMessage } from '../api'

const CHANNELS = ['email', 'slack', 'sms']

function SendMessage() {
  const [title, setTitle] = useState('')
  const [content, setContent] = useState('')
  const [channels, setChannels] = useState([])
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState(null)
  const [success, setSuccess] = useState(null)

  const toggleChannel = (ch) => {
    setChannels((prev) =>
      prev.includes(ch) ? prev.filter((c) => c !== ch) : [...prev, ch],
    )
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setError(null)
    setSuccess(null)
    setLoading(true)

    try {
      const result = await createMessage({ title, content, channels })
      setSuccess(`Message sent! ID: ${result.data.id}`)
      setTitle('')
      setContent('')
      setChannels([])
    } catch (err) {
      const msg =
        err.response?.data?.error ||
        err.response?.data?.message ||
        err.message ||
        'An unexpected error occurred'
      setError(msg)
    } finally {
      setLoading(false)
    }
  }

  return (
    <div style={styles.container}>
      <h1 style={styles.title}>Send Message</h1>

      <form onSubmit={handleSubmit} style={styles.form}>
        <div style={styles.field}>
          <label style={styles.label}>Title</label>
          <input
            style={styles.input}
            value={title}
            onChange={(e) => setTitle(e.target.value)}
            placeholder="Message title"
            required
          />
        </div>

        <div style={styles.field}>
          <label style={styles.label}>Content</label>
          <textarea
            style={{ ...styles.input, minHeight: '120px', resize: 'vertical' }}
            value={content}
            onChange={(e) => setContent(e.target.value)}
            placeholder="Write your message content here..."
            required
          />
        </div>

        <div style={styles.field}>
          <label style={styles.label}>Channels</label>
          <div style={styles.channels}>
            {CHANNELS.map((ch) => (
              <label key={ch} style={styles.chip}>
                <input
                  type="checkbox"
                  checked={channels.includes(ch)}
                  onChange={() => toggleChannel(ch)}
                  style={{ display: 'none' }}
                />
                <span
                  style={{
                    ...styles.chipLabel,
                    ...(channels.includes(ch) ? styles.chipActive : {}),
                  }}
                >
                  {ch === 'email' ? '📧' : ch === 'slack' ? '💬' : '📱'} {ch}
                </span>
              </label>
            ))}
          </div>
        </div>

        {error && (
          <div style={styles.alertError}>
            <span>✕</span> {error}
          </div>
        )}

        {success && (
          <div style={styles.alertSuccess}>
            <span>✓</span> {success}
          </div>
        )}

        <button
          type="submit"
          disabled={loading || channels.length === 0}
          style={{
            ...styles.btn,
            ...(loading || channels.length === 0 ? styles.btnDisabled : {}),
          }}
        >
          {loading ? 'Sending...' : 'Send →'}
        </button>
      </form>
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
  form: {
    display: 'flex',
    flexDirection: 'column',
    gap: '1.25rem',
  },
  field: {
    display: 'flex',
    flexDirection: 'column',
    gap: '6px',
  },
  label: {
    fontSize: '0.8rem',
    color: 'var(--color-muted)',
    fontWeight: 600,
    textTransform: 'uppercase',
    letterSpacing: '0.5px',
  },
  input: {
    padding: '10px 14px',
    background: '#1a2035',
    border: '1px solid var(--color-border)',
    borderRadius: 'var(--radius)',
    color: 'var(--color-text)',
    fontSize: '0.95rem',
    outline: 'none',
    fontFamily: 'var(--font-sans)',
  },
  channels: {
    display: 'flex',
    gap: '0.5rem',
    flexWrap: 'wrap',
  },
  chip: {
    cursor: 'pointer',
  },
  chipLabel: {
    display: 'inline-block',
    padding: '8px 16px',
    borderRadius: '8px',
    border: '1px solid var(--color-border)',
    background: '#1a2035',
    color: 'var(--color-muted)',
    fontSize: '0.9rem',
    fontWeight: 500,
    transition: 'all 0.15s',
  },
  chipActive: {
    background: '#1e1b4b',
    border: '1px solid var(--color-primary)',
    color: 'var(--color-primary-h)',
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
  alertSuccess: {
    display: 'flex',
    alignItems: 'center',
    gap: '0.5rem',
    padding: '10px 14px',
    background: '#052e16',
    border: '1px solid var(--color-success)',
    borderRadius: 'var(--radius)',
    color: '#86efac',
    fontSize: '0.9rem',
  },
  btn: {
    padding: '0.7rem 2rem',
    background: 'var(--color-primary)',
    color: '#fff',
    border: 'none',
    borderRadius: 'var(--radius)',
    fontWeight: 600,
    fontSize: '1rem',
    cursor: 'pointer',
    alignSelf: 'flex-start',
    transition: 'all 0.15s',
  },
  btnDisabled: {
    opacity: 0.5,
    cursor: 'not-allowed',
  },
}

export default SendMessage
