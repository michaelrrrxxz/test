import axios from 'axios'

const apiClient = axios.create({
  baseURL: '127.0.0.1:8000/api/v1', 
  headers: {
    'Content-Type': 'application/json',
  },
  timeout: 10000,
})