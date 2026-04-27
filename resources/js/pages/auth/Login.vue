<template>
    <div class="login-container">
        <!-- Animated Background -->
        <div class="animated-bg">
            <div class="gradient-sphere sphere-1"></div>
            <div class="gradient-sphere sphere-2"></div>
            <div class="gradient-sphere sphere-3"></div>
            <div class="grid-overlay"></div>
        </div>

        <div class="container-fluid min-vh-100 d-flex align-items-center">
            <div class="row w-100">
                <!-- Left Side - Login Form -->
                <div class="col-lg-6 d-flex align-items-center">
                    <div class="login-card animate-slide-right">
                        <div class="login-header">
                            <div class="header-badge animate-pulse">
                                <i class="fas fa-lock me-2"></i>
                                Secure Login
                            </div>
                            <h2 class="login-title">
                                Welcome <span class="gradient-text">Back</span>
                            </h2>
                            <p class="login-subtitle">
                                Sign in to your account to continue creating amazing content
                            </p>
                        </div>

                        <form @submit.prevent="sendOTP" class="login-form">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email Address
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input
                                        type="email"
                                        v-model="form.email"
                                        class="form-control-modern"
                                        :class="{ 'is-invalid': errors.email }"
                                        placeholder="Enter your email"
                                        required
                                    >
                                </div>
                                <div v-if="errors.email" class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ errors.email }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-phone-alt me-2"></i>Phone Number <span class="optional">(Optional)</span>
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-phone input-icon"></i>
                                    <input
                                        type="tel"
                                        v-model="form.phone"
                                        class="form-control-modern"
                                        placeholder="+1234567890"
                                    >
                                </div>
                                <small class="form-hint">
                                    <i class="fas fa-info-circle me-1"></i>Only needed if you want SMS OTP
                                </small>
                            </div>

                            <button type="submit" class="btn-primary-glow w-100" :disabled="loading">
                                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                                <i v-else class="fas fa-paper-plane me-2"></i>
                                {{ loading ? 'Sending OTP...' : 'Send OTP' }}
                                <span class="btn-glow"></span>
                            </button>
                        </form>

                        <div class="login-footer">
                            <p class="text-center">
                                <i class="fas fa-shield-alt me-1"></i>
                                We'll send a 6-digit code to your email
                            </p>
                            <router-link to="/register" class="register-link">
                                Don't have an account? <span>Create one</span>
                            </router-link>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Hero Content -->
                <div class="col-lg-6 d-none d-lg-flex align-items-center">
                    <div class="hero-content-card animate-slide-left">
                        <div class="hero-icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <h2>AI-Powered Content Creation</h2>
                        <p>Join thousands of creators using our advanced AI platform to generate high-quality content in seconds.</p>

                        <div class="features-list">
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>GPT-5 Powered Generation</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>6+ Content Types Available</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>SEO Optimized Content</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Save & Export Your Work</span>
                            </div>
                        </div>

                        <div class="testimonial-quote">
                            <i class="fas fa-quote-left"></i>
                            <p>This platform has completely transformed my content creation workflow. I save hours every day!</p>
                            <div class="testimonial-author">
                                <img src="https://ui-avatars.com/api/?background=6366f1&color=fff&name=Sarah" alt="Sarah">
                                <div>
                                    <strong>Sarah Johnson</strong>
                                    <span>Content Creator</span>
                                </div>
                            </div>
                        </div>

                        <div class="stats-badge">
                            <div class="stat">
                                <span class="stat-number">10K+</span>
                                <span class="stat-label">Active Users</span>
                            </div>
                            <div class="stat">
                                <span class="stat-number">1M+</span>
                                <span class="stat-label">Content Generated</span>
                            </div>
                            <div class="stat">
                                <span class="stat-number">4.9</span>
                                <span class="stat-label">User Rating</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const loading = ref(false);
const errors = ref({});

const form = ref({
    email: '',
    phone: ''
});

const sendOTP = async () => {
    loading.value = true;
    errors.value = {};

    try {
        await authStore.sendOTP(form.value);
        router.push('/verify-otp');
    } catch (error) {
        errors.value = error.errors || { email: error.message || 'Failed to send OTP' };
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.login-container {
    min-height: 100vh;
    position: relative;
    background: linear-gradient(135deg, #f5f7ff 0%, #f0f2ff 50%, #faf5ff 100%);
}

/* Animated Background */
.animated-bg {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    overflow: hidden;
}

.gradient-sphere {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.3;
    animation: floatSphere 25s infinite ease-in-out;
}

.sphere-1 {
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, #6366f1, #8b5cf6);
    top: -200px;
    left: -200px;
}

.sphere-2 {
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, #06b6d4, #3b82f6);
    bottom: -200px;
    right: -200px;
    animation-delay: 5s;
}

.sphere-3 {
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, #ec4899, #f43f5e);
    top: 50%;
    left: 50%;
    animation-delay: 10s;
}

@keyframes floatSphere {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(50px, -50px) scale(1.1); }
    66% { transform: translate(-50px, 50px) scale(0.9); }
}

.grid-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: linear-gradient(rgba(99, 102, 241, 0.03) 1px, transparent 1px),
                      linear-gradient(90deg, rgba(99, 102, 241, 0.03) 1px, transparent 1px);
    background-size: 50px 50px;
}

/* Login Card */
.login-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(15px);
    border-radius: 32px;
    padding: 2.5rem;
    max-width: 500px;
    margin: 0 auto;
    border: 1px solid rgba(255, 255, 255, 0.8);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
}

.login-header {
    text-align: center;
    margin-bottom: 2rem;
}

.header-badge {
    display: inline-block;
    background: rgba(99, 102, 241, 0.1);
    border: 1px solid rgba(99, 102, 241, 0.2);
    padding: 6px 16px;
    border-radius: 50px;
    font-size: 0.75rem;
    color: #6366f1;
    margin-bottom: 1rem;
}

.login-title {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
    color: #1e1b4b;
}

.gradient-text {
    background: linear-gradient(135deg, #6366f1, #8b5cf6, #d946ef);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.login-subtitle {
    font-size: 0.9rem;
    color: #6b7280;
}

/* Form Styles */
.login-form {
    margin-top: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    color: #374151;
    font-weight: 500;
    font-size: 0.85rem;
}

.optional {
    font-size: 0.7rem;
    color: #9ca3af;
    font-weight: normal;
}

.input-wrapper {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 1rem;
    transition: color 0.3s ease;
    pointer-events: none;
}

.form-control-modern {
    width: 100%;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 0.9rem 1rem 0.9rem 45px;
    font-size: 0.95rem;
    color: #1f2937;
    transition: all 0.3s ease;
}

.form-control-modern:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-control-modern:focus + .input-icon,
.input-wrapper:focus-within .input-icon {
    color: #6366f1;
}

.form-control-modern.is-invalid {
    border-color: #ef4444;
}

.invalid-feedback {
    font-size: 0.75rem;
    color: #ef4444;
    margin-top: 0.25rem;
}

.form-hint {
    font-size: 0.7rem;
    color: #9ca3af;
    display: block;
    margin-top: 0.25rem;
}

/* Button */
.btn-primary-glow {
    position: relative;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    padding: 12px 24px;
    border-radius: 14px;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    color: white;
    font-weight: 600;
    transition: all 0.3s ease;
    cursor: pointer;
    font-size: 0.95rem;
}

.btn-primary-glow:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
}

.btn-primary-glow:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-glow {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s ease;
}

.btn-primary-glow:hover .btn-glow {
    left: 100%;
}

/* Login Footer */
.login-footer {
    margin-top: 1.5rem;
    text-align: center;
}

.login-footer p {
    font-size: 0.75rem;
    color: #94a3b8;
    margin-bottom: 1rem;
}

.register-link {
    font-size: 0.85rem;
    color: #6b7280;
    text-decoration: none;
    transition: color 0.3s ease;
}

.register-link span {
    color: #6366f1;
    font-weight: 600;
}

.register-link:hover span {
    text-decoration: underline;
}

/* Right Side Hero Content */
.hero-content-card {
    background: rgba(255, 255, 255, 0.5);
    backdrop-filter: blur(15px);
    border-radius: 32px;
    padding: 2.5rem;
    border: 1px solid rgba(255, 255, 255, 0.8);
    max-width: 500px;
    margin: 0 auto;
}

.hero-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
}

.hero-icon i {
    font-size: 2rem;
    color: #6366f1;
}

.hero-content-card h2 {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: #1e1b4b;
}

.hero-content-card > p {
    font-size: 0.95rem;
    color: #6b7280;
    line-height: 1.6;
    margin-bottom: 2rem;
}

.features-list {
    margin-bottom: 2rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0;
}

.feature-item i {
    color: #10b981;
    font-size: 1rem;
}

.feature-item span {
    color: #374151;
    font-size: 0.9rem;
}

.testimonial-quote {
    background: rgba(99, 102, 241, 0.05);
    border-radius: 20px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.testimonial-quote i {
    font-size: 2rem;
    color: rgba(99, 102, 241, 0.3);
    margin-bottom: 0.5rem;
    display: block;
}

.testimonial-quote p {
    font-size: 0.9rem;
    color: #4b5563;
    font-style: italic;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.testimonial-author {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.testimonial-author img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

.testimonial-author strong {
    display: block;
    font-size: 0.85rem;
    color: #1f2937;
}

.testimonial-author span {
    font-size: 0.7rem;
    color: #6b7280;
}

.stats-badge {
    display: flex;
    justify-content: space-between;
    padding: 1rem 0;
    border-top: 1px solid rgba(0, 0, 0, 0.05);
}

.stat {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 1.2rem;
    font-weight: 700;
    color: #6366f1;
}

.stat-label {
    font-size: 0.7rem;
    color: #6b7280;
}

/* Animations */
.animate-slide-right {
    animation: slideRight 0.6s ease forwards;
    opacity: 0;
}

.animate-slide-left {
    animation: slideLeft 0.6s ease forwards;
    opacity: 0;
}

.animate-pulse {
    animation: pulseAnim 2s ease-in-out infinite;
}

@keyframes slideRight {
    from {
        opacity: 0;
        transform: translateX(-50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideLeft {
    from {
        opacity: 0;
        transform: translateX(50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes pulseAnim {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

/* Responsive */
@media (max-width: 992px) {
    .login-card {
        margin: 2rem auto;
        max-width: 450px;
    }
}

@media (max-width: 768px) {
    .login-card {
        padding: 1.5rem;
    }

    .login-title {
        font-size: 1.5rem;
    }

    .hero-content-card {
        display: none;
    }
}
</style>
