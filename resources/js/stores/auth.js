// resources/js/stores/auth.js
import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token'),
        tempData: JSON.parse(localStorage.getItem('tempData') || '{}')
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        getUser: (state) => state.user
    },

    actions: {
        async sendOTP(data) {
            try {
                const response = await axios.post('/api/send-otp', data);
                this.tempData = { phone: data.phone, email: data.email };
                localStorage.setItem('tempData', JSON.stringify(this.tempData));
                return response.data;
            } catch (error) {
                throw error.response?.data || { message: 'Something went wrong' };
            }
        },

        async verifyOTP(otp) {
            try {
                const response = await axios.post('/api/verify-otp', {
                    otp: otp,
                    ...this.tempData
                });

                this.token = response.data.token;
                this.user = response.data.user;
                localStorage.setItem('token', response.data.token);
                axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;

                // Clear temp data
                localStorage.removeItem('tempData');
                this.tempData = {};

                return response.data;
            } catch (error) {
                throw error.response?.data || { message: 'Invalid OTP' };
            }
        },

        async logout() {
            try {
                await axios.post('/api/logout');
            } catch (error) {
                console.error('Logout error:', error);
            } finally {
                this.token = null;
                this.user = null;
                localStorage.removeItem('token');
                delete axios.defaults.headers.common['Authorization'];
            }
        },

        async fetchUser() {
            if (!this.token) return;

            try {
                const response = await axios.get('/api/user');
                this.user = response.data;
            } catch (error) {
                this.logout();
            }
        }
    }
});

// Set default axios header
const token = localStorage.getItem('token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}
