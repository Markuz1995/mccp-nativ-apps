import { useState } from 'react'


const PAGES = {
  send: 'send',
  history: 'history',
}

function App() {
  const [page, setPage] = useState(PAGES.send)

  return (
    <div style={{ minHeight: '100vh', display: 'flex', flexDirection: 'column' }}>
      {/* ── Nav ── */}
      <header style={styles.header}>
        <div style={styles.logo}>
          <span style={styles.logoIcon}>⚡</span>
          <span>MCCP</span>
        </div>
        <nav style={styles.nav}>
          <button
            style={page === PAGES.send ? styles.navBtnActive : styles.navBtn}
            onClick={() => setPage(PAGES.send)}
          >
            Send Message
          </button>
          <button
            style={page === PAGES.history ? styles.navBtnActive : styles.navBtn}
            onClick={() => setPage(PAGES.history)}
          >
            History
          </button>
        </nav>
      </header>

      {/* ── Content ── */}
      <main style={styles.main}>
        {page === PAGES.send && <SendMessage />}
        {page === PAGES.history && <History />}
      </main>

      {/* ── Footer ── */}
      <footer style={styles.footer}>
        <span>MCCP — Multi-Channel Content Processor</span>
        <span style={{ color: 'var(--color-muted)' }}>Laravel 12 · React 19 · PostgreSQL</span>
      </footer>
    </div>
  )
}

const styles = {
  header: {
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'space-between',
    padding: '0 2rem',
    height: '64px',
    background: 'var(--color-surface)',
    borderBottom: '1px solid var(--color-border)',
    position: 'sticky',
    top: 0,
    zIndex: 100,
  },
  logo: {
    display: 'flex',
    alignItems: 'center',
    gap: '0.5rem',
    fontSize: '1.2rem',
    fontWeight: 700,
    color: 'var(--color-primary-h)',
    letterSpacing: '-0.5px',
  },
  logoIcon: {
    fontSize: '1.4rem',
  },
  nav: {
    display: 'flex',
    gap: '0.5rem',
  },
  navBtn: {
    padding: '0.4rem 1.2rem',
    borderRadius: '6px',
    border: '1px solid var(--color-border)',
    background: 'transparent',
    color: 'var(--color-muted)',
    cursor: 'pointer',
    fontSize: '0.9rem',
    transition: 'all 0.2s',
  },
  navBtnActive: {
    padding: '0.4rem 1.2rem',
    borderRadius: '6px',
    border: '1px solid var(--color-primary)',
    background: 'var(--color-primary)',
    color: '#fff',
    cursor: 'pointer',
    fontSize: '0.9rem',
    fontWeight: 600,
    transition: 'all 0.2s',
  },
  main: {
    flex: 1,
    padding: '2rem',
    maxWidth: '860px',
    width: '100%',
    margin: '0 auto',
  },
  footer: {
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: '1rem 2rem',
    fontSize: '0.8rem',
    color: 'var(--color-muted)',
    borderTop: '1px solid var(--color-border)',
  },
}

export default App
