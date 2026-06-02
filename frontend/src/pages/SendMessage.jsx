/**
 * SendMessage — FASE 1 Placeholder
 * Esta pantalla se desarrollará completamente en FASE 3.
 * Por ahora confirma que React + routing funcionan.
 */
function SendMessage() {
  return (
    <div style={styles.container}>
      <div style={styles.badge}>FASE 3</div>
      <h1 style={styles.title}>Send Message</h1>
      <p style={styles.desc}>
        Esta pantalla incluirá el formulario para enviar mensajes con campos
        <strong> title</strong>, <strong>content</strong> y selección de canales
        (<strong>Email</strong>, <strong>Slack</strong>, <strong>SMS</strong>).
      </p>
      <div style={styles.fields}>
        <div style={styles.field}>
          <span style={styles.label}>title</span>
          <div style={styles.mockInput} />
        </div>
        <div style={styles.field}>
          <span style={styles.label}>content</span>
          <div style={{ ...styles.mockInput, height: '80px' }} />
        </div>
        <div style={styles.field}>
          <span style={styles.label}>channels</span>
          <div style={styles.channels}>
            {['Email', 'Slack', 'SMS'].map((ch) => (
              <div key={ch} style={styles.chip}>{ch}</div>
            ))}
          </div>
        </div>
      </div>
      <div style={styles.mockBtn}>Send →</div>
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
  fields: {
    display: 'flex',
    flexDirection: 'column',
    gap: '1rem',
    marginBottom: '1.5rem',
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
  mockInput: {
    height: '38px',
    background: '#1a2035',
    borderRadius: 'var(--radius)',
    border: '1px solid var(--color-border)',
    opacity: 0.6,
  },
  channels: {
    display: 'flex',
    gap: '0.5rem',
  },
  chip: {
    background: '#1e1b4b',
    color: 'var(--color-primary-h)',
    border: '1px dashed var(--color-primary)',
    borderRadius: '6px',
    padding: '4px 14px',
    fontSize: '0.85rem',
    opacity: 0.6,
  },
  mockBtn: {
    display: 'inline-block',
    background: 'var(--color-primary)',
    color: '#fff',
    borderRadius: 'var(--radius)',
    padding: '0.6rem 1.8rem',
    fontWeight: 600,
    opacity: 0.5,
    cursor: 'not-allowed',
  },
}

export default SendMessage
