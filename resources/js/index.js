import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from './stores/auth';

// Import Pages
import LandingPage from './pages/LandingPage.vue';
import Login from './pages/auth/Login.vue';
import Register from './pages/auth/Register.vue';
import VerifyOTP from './pages/auth/VerifyOTP.vue';
import Dashboard from './pages/Dashboard.vue';
import History from './pages/History.vue';
import Demo from './pages/Demo.vue';

const routes = [
    { path: '/', name: 'landing', component: LandingPage, meta: { guest: true } },
    { path: '/demo', name: 'demo', component: Demo, meta: { public: true } }, // Changed to public
    { path: '/login', name: 'login', component: Login, meta: { guest: true } },
    { path: '/register', name: 'register', component: Register, meta: { guest: true } },
    { path: '/verify-otp', name: 'verify-otp', component: VerifyOTP, meta: { guest: true } },
    { path: '/dashboard', name: 'dashboard', component: Dashboard, meta: { requiresAuth: true } },
    { path: '/history', name: 'history', component: History, meta: { requiresAuth: true } }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// Navigation Guard
router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();
    const isAuthenticated = authStore.isAuthenticated;

    // Allow public routes (like demo) for everyone
    if (to.meta.public) {
        next();
        return;
    }

    // Require auth for protected routes
    if (to.meta.requiresAuth && !isAuthenticated) {
        next('/login');
        return;
    }

    // Redirect authenticated users from guest routes to dashboard
    if (to.meta.guest && isAuthenticated && to.name !== 'landing') {
        next('/dashboard');
        return;
    }

    next();
});

export default router;
