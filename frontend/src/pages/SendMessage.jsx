import { useState } from 'react'
import { createMessage } from '../api'
import styles from '../styles/SendMessage.styles'

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

export default SendMessage
