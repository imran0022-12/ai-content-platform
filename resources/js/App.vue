<template>
    <div id="app">
        <!-- Animated Particles Background - Light Theme -->
        <div class="particles-bg" ref="particlesBg"></div>

        <!-- Navbar - Light Theme -->
        <nav class="navbar navbar-expand-lg fixed-top" :class="{ scrolled: isScrolled }">
            <div class="container">
                <router-link to="/" class="navbar-brand hover-lift">
                    <i class="fas fa-brain brand-icon pulse-glow"></i>
                    <span class="brand-text gradient-text">AI Content Platform</span>
                    <span class="brand-glow"></span>
                </router-link>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <div class="navbar-nav ms-auto">
                        <template v-if="isAuthenticated">
                            <router-link to="/dashboard" class="nav-link hover-glow">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                                <div class="nav-indicator"></div>
                            </router-link>
                            <router-link to="/history" class="nav-link hover-glow">
                                <i class="fas fa-history"></i>
                                <span>History</span>
                                <div class="nav-indicator"></div>
                            </router-link>
                            <button @click="logout" class="btn-logout hover-shake">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </button>
                        </template>
                        <template v-else>
                            <router-link to="/login" class="nav-link hover-glow">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Login</span>
                                <div class="nav-indicator"></div>
                            </router-link>
                            <router-link to="/register" class="nav-link btn-register hover-lift">
                                <i class="fas fa-user-plus"></i>
                                <span>Get Started</span>
                                <span class="btn-glow"></span>
                            </router-link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <main class="main-content">
            <router-view v-slot="{ Component }">
                <transition name="page-fade" mode="out-in">
                    <component :is="Component" />
                </transition>
            </router-view>
        </main>

        <!-- Back to Top Button -->
        <button v-show="showBackToTop" @click="scrollToTop" class="back-to-top glow-pulse">
            <i class="fas fa-arrow-up"></i>
        </button>
    </div>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from './stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const isAuthenticated = computed(() => authStore.isAuthenticated);
const showBackToTop = ref(false);
const isScrolled = ref(false);
const particlesBg = ref(null);

const logout = async () => {
    await authStore.logout();
    router.push('/login');
};

const handleScroll = () => {
    showBackToTop.value = window.scrollY > 300;
    isScrolled.value = window.scrollY > 50;
};

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

// Create floating particles - Light theme
const createParticles = () => {
    if (!particlesBg.value) return;
    particlesBg.value.innerHTML = '';

    for (let i = 0; i < 60; i++) {
        const particle = document.createElement('div');
        particle.classList.add('particle');
        particle.style.left = Math.random() * 100 + '%';
        particle.style.top = Math.random() * 100 + '%';
        particle.style.width = (Math.random() * 3 + 1) + 'px';
        particle.style.height = particle.style.width;
        particle.style.animationDelay = Math.random() * 5 + 's';
        particle.style.animationDuration = (Math.random() * 4 + 2) + 's';
        particle.style.opacity = Math.random() * 0.3 + 0.1;
        particlesBg.value.appendChild(particle);
    }
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    createParticles();
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<style scoped>
/* ========== VARIABLES - MATCHING LANDING PAGE LIGHT THEME ========== */
:root {
    --primary-purple: #6366f1;
    --primary-indigo: #8b5cf6;
    --primary-pink: #d946ef;
    --primary-gradient: linear-gradient(135deg, #6366f1, #8b5cf6, #d946ef);
    --primary-light: rgba(99, 102, 241, 0.1);
}

/* ========== PARTICLES BACKGROUND - LIGHT THEME ========== */
.particles-bg {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
    pointer-events: none;
    overflow: hidden;
    background: linear-gradient(135deg, #f5f7ff 0%, #f0f2ff 50%, #faf5ff 100%);
}

.particle {
    position: absolute;
    background: radial-gradient(circle, #6366f1, #8b5cf6);
    border-radius: 50%;
    animation: floatParticle linear infinite;
    pointer-events: none;
}

@keyframes floatParticle {
    0% {
        transform: translateY(0) rotate(0deg);
        opacity: 0;
    }
    20% {
        opacity: 0.3;
    }
    80% {
        opacity: 0.2;
    }
    100% {
        transform: translateY(-100vh) rotate(360deg);
        opacity: 0;
    }
}

/* ========== NAVBAR STYLES - LIGHT THEME ========== */
.navbar {
    background: rgba(255, 255, 255, 0.85) !important;
    backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(99, 102, 241, 0.15);
    padding: 0.8rem 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    animation: slideDown 0.5s ease forwards;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-100%);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.navbar.scrolled {
    padding: 0.5rem 0;
    background: rgba(255, 255, 255, 0.95) !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    border-bottom-color: rgba(99, 102, 241, 0.25);
}

/* Brand Logo */
.navbar-brand {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.5rem;
    font-weight: 700;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    text-decoration: none;
}

.brand-icon {
    font-size: 1.6rem;
    display: inline-block;
    transition: transform 0.3s ease;
    color: #6366f1;
}

.pulse-glow {
    animation: iconPulse 2s ease-in-out infinite;
}

@keyframes iconPulse {
    0%, 100% {
        filter: drop-shadow(0 0 5px rgba(99, 102, 241, 0.3));
        transform: scale(1);
    }
    50% {
        filter: drop-shadow(0 0 12px rgba(139, 92, 246, 0.5));
        transform: scale(1.05);
    }
}

.navbar-brand:hover .brand-icon {
    transform: scale(1.1) rotate(5deg);
}

.gradient-text {
    background: linear-gradient(135deg, #6366f1, #8b5cf6, #d946ef);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.brand-text {
    transition: all 0.3s ease;
    font-weight: 700;
}

.navbar-brand:hover .brand-text {
    letter-spacing: 0.5px;
}

.brand-glow {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(99, 102, 241, 0.15), transparent);
    transition: left 0.5s ease;
}

.navbar-brand:hover .brand-glow {
    left: 100%;
}

/* Navbar Toggler - Light Theme */
.navbar-toggler {
    border: 1px solid rgba(99, 102, 241, 0.3);
    background: transparent;
    transition: all 0.3s ease;
}

.navbar-toggler:hover {
    border-color: #6366f1;
    transform: scale(1.05);
    background: rgba(99, 102, 241, 0.05);
}

.navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(99, 102, 241, 0.8)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
}

/* Nav Links - Light Theme */
.nav-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    color: #4b5563 !important;
    font-weight: 500;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 10px;
    position: relative;
    margin-left: 1rem;
    overflow: hidden;
}

.nav-link i {
    font-size: 1rem;
    transition: transform 0.3s ease;
}

.nav-link:hover {
    color: #6366f1 !important;
    background: rgba(99, 102, 241, 0.08);
    transform: translateY(-2px) translateX(3px);
}

.nav-link:hover i {
    transform: scale(1.1) rotate(5deg);
}

/* Active Link Indicator */
.router-link-active {
    color: #6366f1 !important;
   
}

.router-link-active .nav-indicator {
    position: absolute;
    bottom: -2px;
    left: 50%;
    transform: translateX(-50%);
    width: 30px;
    height: 2px;
    background: linear-gradient(90deg, #6366f1, #d946ef);
    border-radius: 2px;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        width: 0;
        opacity: 0;
    }
    to {
        width: 30px;
        opacity: 1;
    }
}

/* Logout Button - Light Theme */
.btn-logout {
    background: rgba(99, 102, 241, 0.08);
    border: 1px solid rgba(99, 102, 241, 0.2);
    padding: 0.5rem 1.2rem;
    border-radius: 10px;
    color: #6366f1;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    cursor: pointer;
    margin-left: 1rem;
}

.btn-logout:hover {
    background: rgba(239, 68, 68, 0.1);
    border-color: #ef4444;
    color: #ef4444;
    transform: translateY(-2px);
}

.btn-logout i {
    transition: transform 0.3s ease;
}

.btn-logout:hover i {
    transform: translateX(3px);
}

/* Register Button - Light Theme */
.btn-register {
    background: linear-gradient(135deg, #6366f1, #8b5cf6, #d946ef);
    border-radius: 10px;
    color: white !important;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-register::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
}

.btn-register:hover::before {
    left: 100%;
}

.btn-register:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3);
}

.btn-glow {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.2), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.btn-register:hover .btn-glow {
    opacity: 1;
}

/* Main Content */
.main-content {
    margin-top: 76px;
    min-height: calc(100vh - 76px);
    animation: fadeIn 0.5s ease;
    background: transparent;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Page Transition */
.page-fade-enter-active,
.page-fade-leave-active {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.page-fade-enter-from {
    opacity: 0;
    transform: translateY(20px);
}

.page-fade-leave-to {
    opacity: 0;
    transform: translateY(-20px);
}

/* Back to Top Button - Light Theme */
.back-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border: none;
    border-radius: 12px;
    color: white;
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 999;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.back-to-top[v-show="true"] {
    opacity: 1;
    visibility: visible;
}

.back-to-top:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
}

.back-to-top i {
    transition: transform 0.3s ease;
}

.back-to-top:hover i {
    transform: translateY(-2px);
}

/* Animation Classes */
.hover-lift {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.hover-lift:hover {
    transform: translateY(-2px);
}

.hover-glow {
    transition: all 0.3s ease;
}

.hover-glow:hover {
    filter: drop-shadow(0 0 6px rgba(99, 102, 241, 0.3));
}

.hover-shake:hover {
    animation: shake 0.5s ease;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-3px); }
    75% { transform: translateX(3px); }
}

.glow-pulse {
    animation: glowPulse 2s ease-in-out infinite;
}

@keyframes glowPulse {
    0%, 100% { box-shadow: 0 0 5px rgba(99, 102, 241, 0.2); }
    50% { box-shadow: 0 0 15px rgba(139, 92, 246, 0.4); }
}

/* Responsive Design */
@media (max-width: 991px) {
    .navbar-nav {
        padding: 1rem 0;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        margin-top: 0.5rem;
    }

    .nav-link, .btn-logout {
        justify-content: center;
        margin-left: 0;
    }

    .main-content {
        margin-top: 66px;
        padding: 1rem 0;
    }
}

@media (max-width: 768px) {
    .brand-text {
        font-size: 1.2rem;
    }

    .brand-icon {
        font-size: 1.3rem;
    }

    .back-to-top {
        bottom: 20px;
        right: 20px;
        width: 40px;
        height: 40px;
    }
}
</style>
