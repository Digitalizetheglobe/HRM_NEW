import axios from 'axios';

const api = axios.create({
    baseURL: '', // Empty uses current origin (proxy)
    withCredentials: true,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    }
});

export default api;
