<template>
    <div class="register-container">
        <!-- Animated Background -->
        <div class="animated-bg">
            <div class="gradient-sphere sphere-1"></div>
            <div class="gradient-sphere sphere-2"></div>
            <div class="gradient-sphere sphere-3"></div>
            <div class="grid-overlay"></div>
        </div>

        <div class="container-fluid min-vh-100 d-flex align-items-center">
            <div class="row w-100">
                <!-- Left Side - Hero Content -->
                <div class="col-lg-6 d-none d-lg-flex align-items-center">
                    <div class="hero-content-card animate-slide-right">
                        <div class="hero-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h2>Start Your AI Journey Today</h2>
                        <p>Join thousands of creators who are already generating amazing content with our advanced AI platform.</p>

                        <div class="benefits-list">
                            <div class="benefit-item">
                                <div class="benefit-icon">
                                    <i class="fas fa-infinity"></i>
                                </div>
                                <div>
                                    <strong>10 Free Generations Daily</strong>
                                    <p>Try our AI completely free</p>
                                </div>
                            </div>
                            <div class="benefit-item">
                                <div class="benefit-icon">
                                    <i class="fas fa-bolt"></i>
                                </div>
                                <div>
                                    <strong>Fast & Reliable</strong>
                                    <p>Get results in seconds</p>
                                </div>
                            </div>
                            <div class="benefit-item">
                                <div class="benefit-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div>
                                    <strong>Secure & Private</strong>
                                    <p>Your data is always protected</p>
                                </div>
                            </div>
                        </div>

                        <div class="pricing-preview">
                            <div class="price-card">
                                <div class="price-header">
                                    <i class="fas fa-gem"></i>
                                    <span>Pro Plan</span>
                                </div>
                                <div class="price">$29<span>/month</span></div>
                                <div class="price-features">
                                    <span>Unlimited generations</span>
                                    <span>Priority support</span>
                                    <span>API access</span>
                                </div>
                            </div>
                            <div class="price-note">
                                <i class="fas fa-check-circle"></i> No credit card required for free trial
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Register Form -->
                <div class="col-lg-6 d-flex align-items-center">
                    <div class="register-card animate-slide-right">
                        <div class="register-header">
                            <div class="header-badge animate-pulse">
                                <i class="fas fa-user-plus me-2"></i>
                                Join Now
                            </div>
                            <h2 class="register-title">
                                Create <span class="gradient-text">Account</span>
                            </h2>
                            <p class="register-subtitle">
                                Get started with your free account today
                            </p>
                        </div>

                        <form @submit.prevent="register" class="register-form">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user me-2"></i>Full Name
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-user input-icon"></i>
                                    <input
                                        type="text"
                                        v-model="form.name"
                                        class="form-control-modern"
                                        :class="{ 'is-invalid': errors.name }"
                                        placeholder="Enter your full name"
                                        required
                                    >
                                </div>
                                <div v-if="errors.name" class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ errors.name }}
                                </div>
                            </div>

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
                                        placeholder="+1 234 567 8900"
                                    >
                                </div>
                                <small class="form-hint">
                                    <i class="fas fa-info-circle me-1"></i>Used for SMS OTP verification
                                </small>
                            </div>

                            <button type="submit" class="btn-primary-glow w-100" :disabled="loading">
                                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                                <i v-else class="fas fa-rocket me-2"></i>
                                {{ loading ? 'Creating Account...' : 'Create Account & Send OTP' }}
                                <span class="btn-glow"></span>
                            </button>
                        </form>

                        <div class="register-footer">
                            <p class="terms-text">
                                <i class="fas fa-check-circle me-1"></i>
                                By signing up, you agree to our <a href="#">Terms</a> and <a href="#">Privacy Policy</a>
                            </p>
                            <router-link to="/login" class="login-link">
                                Already have an account? <span>Sign in</span>
                            </router-link>
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
    name: '',
    email: '',
    phone: ''
});

const register = async () => {
    loading.value = true;
    errors.value = {};

    try {
        await authStore.sendOTP(form.value);
        router.push('/verify-otp');
    } catch (error) {
        errors.value = error.errors || { email: error.message || 'Failed to create account' };
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.register-container {
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

/* Register Card */
.register-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(15px);
    border-radius: 32px;
    padding: 2.5rem;
    max-width: 500px;
    margin: 0 auto;
    border: 1px solid rgba(255, 255, 255, 0.8);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
}

.register-header {
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

.register-title {
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

.register-subtitle {
    font-size: 0.9rem;
    color: #6b7280;
}

/* Form Styles */
.register-form {
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

/* Register Footer */
.register-footer {
    margin-top: 1.5rem;
    text-align: center;
}

.terms-text {
    font-size: 0.7rem;
    color: #94a3b8;
    margin-bottom: 1rem;
}

.terms-text a {
    color: #6366f1;
    text-decoration: none;
}

.terms-text a:hover {
    text-decoration: underline;
}

.login-link {
    font-size: 0.85rem;
    color: #6b7280;
    text-decoration: none;
    transition: color 0.3s ease;
}

.login-link span {
    color: #6366f1;
    font-weight: 600;
}

.login-link:hover span {
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

.benefits-list {
    margin-bottom: 2rem;
}

.benefit-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.benefit-item:last-child {
    border-bottom: none;
}

.benefit-icon {
    width: 45px;
    height: 45px;
    background: rgba(99, 102, 241, 0.1);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.benefit-icon i {
    font-size: 1.2rem;
    color: #6366f1;
}

.benefit-item strong {
    display: block;
    font-size: 0.9rem;
    color: #1f2937;
}

.benefit-item p {
    font-size: 0.75rem;
    color: #6b7280;
    margin: 0;
}

/* Pricing Preview */
.pricing-preview {
    background: rgba(99, 102, 241, 0.05);
    border-radius: 20px;
    padding: 1.5rem;
    margin-top: 1rem;
}

.price-card {
    text-align: center;
    margin-bottom: 1rem;
}

.price-header {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(99, 102, 241, 0.1);
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.7rem;
    color: #6366f1;
    margin-bottom: 0.75rem;
}

.price-card .price {
    font-size: 2rem;
    font-weight: 800;
    color: #1e1b4b;
}

.price-card .price span {
    font-size: 0.8rem;
    font-weight: normal;
    color: #6b7280;
}

.price-features {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 0.5rem;
}

.price-features span {
    font-size: 0.7rem;
    color: #6b7280;
}

.price-note {
    text-align: center;
    font-size: 0.7rem;
    color: #10b981;
    margin-top: 0.5rem;
}

.price-note i {
    margin-right: 0.25rem;
}

/* Animations */
.animate-slide-right {
    animation: slideRight 0.6s ease forwards;
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

@keyframes pulseAnim {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

/* Responsive */
@media (max-width: 992px) {
    .register-card {
        margin: 2rem auto;
        max-width: 450px;
    }
}

@media (max-width: 768px) {
    .register-card {
        padding: 1.5rem;
    }

    .register-title {
        font-size: 1.5rem;
    }

    .hero-content-card {
        display: none;
    }
}
</style>
