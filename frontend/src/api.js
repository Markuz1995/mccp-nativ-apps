import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || '/api',
  headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
})

export const createMessage = (data) => api.post('/messages', data).then((r) => r.data)

export const fetchMessages = () => api.get('/messages').then((r) => r.data)

export default api
