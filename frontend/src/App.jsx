import { useState } from 'react'
import styles from './styles/App.styles'

const PAGES = {
  send: 'send',
  history: 'history',
}

function App() {
  const [page, setPage] = useState(PAGES.send)

  return (
    <div style={{ minHeight: '100vh', display: 'flex', flexDirection: 'column' }}>
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

      <main style={styles.main}>
        {page === PAGES.send && <SendMessage />}
        {page === PAGES.history && <History />}
      </main>

      <footer style={styles.footer}>
        <span>MCCP — Multi-Channel Content Processor</span>
        <span style={{ color: 'var(--color-muted)' }}>Laravel 12 · React 19 · PostgreSQL</span>
      </footer>
    </div>
  )
}

export default App
