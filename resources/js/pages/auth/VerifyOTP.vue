<template>
    <div class="verify-container">
        <!-- Animated Background -->
        <div class="animated-bg">
            <div class="gradient-sphere sphere-1"></div>
            <div class="gradient-sphere sphere-2"></div>
            <div class="gradient-sphere sphere-3"></div>
            <div class="grid-overlay"></div>
        </div>

        <div class="container-fluid min-vh-100 d-flex align-items-center">
            <div class="row w-100">
                <!-- Left Side - OTP Form -->
                <div class="col-lg-6 d-flex align-items-center">
                    <div class="verify-card animate-slide-right">
                        <div class="verify-header">
                            <div class="header-badge animate-pulse">
                                <i class="fas fa-envelope me-2"></i>
                                Verification Required
                            </div>
                            <h2 class="verify-title">
                                Check Your <span class="gradient-text">Inbox</span>
                            </h2>
                            <p class="verify-subtitle">
                                We've sent a verification code to your email address
                            </p>
                        </div>

                        <div class="email-display">
                            <i class="fas fa-envelope-open-text"></i>
                            <div class="email-details">
                                <span>Verification code sent to</span>
                                <strong>{{ tempData.email }}</strong>
                            </div>
                        </div>

                        <form @submit.prevent="verifyOTP" class="otp-form">
                            <div class="otp-input-group">
                                <label class="form-label">
                                    <i class="fas fa-key me-2"></i>Enter 6-Digit Code
                                </label>
                                <div class="otp-inputs">
                                    <input
                                        v-for="(digit, idx) in 6"
                                        :key="idx"
                                        type="text"
                                        maxlength="1"
                                        class="otp-digit"
                                        :class="{ 'has-value': otpArray[idx], 'is-invalid': error }"
                                        v-model="otpArray[idx]"
                                        @input="handleOtpInput(idx, $event)"
                                        @keyup="moveToNext(idx, $event)"
                                        @paste="handlePaste"
                                        ref="otpInputs"
                                    >
                                </div>
                                <div v-if="error" class="invalid-feedback d-block text-center">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ error }}
                                </div>
                                <div class="otp-hint">
                                    <i class="fas fa-clock me-1"></i>
                                    Code expires in 10 minutes
                                </div>
                            </div>

                            <button type="submit" class="btn-primary-glow w-100" :disabled="loading">
                                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                                <i v-else class="fas fa-check-circle me-2"></i>
                                {{ loading ? 'Verifying...' : 'Verify & Login' }}
                                <span class="btn-glow"></span>
                            </button>
                        </form>

                        <div class="verify-footer">
                            <div class="resend-section">
                                <button
                                    @click="resendOTP"
                                    class="resend-btn"
                                    :disabled="resendLoading || countdown > 0"
                                >
                                    <i class="fas fa-redo-alt me-1"></i>
                                    {{ resendLoading ? 'Sending...' : 'Resend Code' }}
                                </button>
                                <div v-if="countdown > 0" class="countdown-timer">
                                    <i class="fas fa-hourglass-half me-1"></i>
                                    Resend available in {{ countdown }}s
                                </div>
                            </div>
                            <router-link to="/login" class="back-link">
                                <i class="fas fa-arrow-left me-1"></i>
                                Back to Login
                            </router-link>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Help Content -->
                <div class="col-lg-6 d-none d-lg-flex align-items-center">
                    <div class="help-content-card animate-slide-right">
                        <div class="help-icon">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <h2>Having Trouble?</h2>
                        <p>Follow these steps to get your verification code:</p>

                        <div class="help-steps">
                            <div class="step-item">
                                <div class="step-number">1</div>
                                <div class="step-content">
                                    <strong>Check your email inbox</strong>
                                    <p>Look for an email from our system</p>
                                </div>
                            </div>
                            <div class="step-item">
                                <div class="step-number">2</div>
                                <div class="step-content">
                                    <strong>Check spam/junk folder</strong>
                                    <p>Sometimes emails might be filtered</p>
                                </div>
                            </div>
                            <div class="step-item">
                                <div class="step-number">3</div>
                                <div class="step-content">
                                    <strong>Wait a few minutes</strong>
                                    <p>Email delivery may take a moment</p>
                                </div>
                            </div>
                            <div class="step-item">
                                <div class="step-number">4</div>
                                <div class="step-content">
                                    <strong>Request a new code</strong>
                                    <p>Use the resend button if needed</p>
                                </div>
                            </div>
                        </div>

                        <div class="contact-support">
                            <i class="fas fa-headset"></i>
                            <div>
                                <span>Still need help?</span>
                                <a href="#">Contact Support</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import axios from 'axios';

const router = useRouter();
const authStore = useAuthStore();
const loading = ref(false);
const resendLoading = ref(false);
const error = ref('');
const countdown = ref(0);
const otpArray = ref(['', '', '', '', '', '']);
const otpInputs = ref([]);
let timer = null;

const tempData = computed(() => authStore.tempData);
const otp = computed(() => otpArray.value.join(''));

// Auto-focus first input on mount
onMounted(() => {
    if (!tempData.value.email) {
        router.push('/login');
    }
    nextTick(() => {
        if (otpInputs.value[0]) {
            otpInputs.value[0].focus();
        }
    });
});

// Watch OTP for auto-submit
watch(otp, (newVal) => {
    if (newVal.length === 6 && !loading.value) {
        verifyOTP();
    }
});

const handleOtpInput = (index, event) => {
    const value = event.target.value;
    // Only allow digits
    if (value && !/^\d+$/.test(value)) {
        otpArray.value[index] = '';
        return;
    }

    // Auto move to next input
    if (value && index < 5) {
        otpInputs.value[index + 1].focus();
    }
};

const moveToNext = (index, event) => {
    // Handle backspace to go to previous input
    if (event.key === 'Backspace' && !otpArray.value[index] && index > 0) {
        otpInputs.value[index - 1].focus();
    }
};

const handlePaste = (event) => {
    event.preventDefault();
    const pastedData = event.clipboardData.getData('text');
    const digits = pastedData.replace(/\D/g, '').slice(0, 6);
    const digitsArray = digits.split('');

    for (let i = 0; i < 6; i++) {
        otpArray.value[i] = digitsArray[i] || '';
    }

    // Focus last filled or first empty
    const lastFilledIndex = otpArray.value.findIndex(d => !d);
    const focusIndex = lastFilledIndex === -1 ? 5 : lastFilledIndex;
    if (otpInputs.value[focusIndex]) {
        otpInputs.value[focusIndex].focus();
    }
};

const verifyOTP = async () => {
    if (otp.value.length !== 6) {
        error.value = 'Please enter the 6-digit verification code';
        return;
    }

    loading.value = true;
    error.value = '';

    try {
        await authStore.verifyOTP(otp.value);
        router.push('/dashboard');
    } catch (err) {
        error.value = err.message || 'Invalid OTP. Please try again.';
        // Clear OTP on error
        otpArray.value = ['', '', '', '', '', ''];
        if (otpInputs.value[0]) {
            otpInputs.value[0].focus();
        }
    } finally {
        loading.value = false;
    }
};

const resendOTP = async () => {
    if (countdown.value > 0) return;

    resendLoading.value = true;

    try {
        await axios.post('/api/resend-otp', {
            email: tempData.value.email
        });

        countdown.value = 60;
        startCountdown();

        // Clear previous errors
        error.value = '';

        // Show success indicator
        const resendBtn = document.querySelector('.resend-btn');
        if (resendBtn) {
            resendBtn.classList.add('success');
            setTimeout(() => {
                resendBtn.classList.remove('success');
            }, 2000);
        }
    } catch (err) {
        error.value = 'Failed to resend OTP. Please try again.';
    } finally {
        resendLoading.value = false;
    }
};

const startCountdown = () => {
    if (timer) clearInterval(timer);

    timer = setInterval(() => {
        if (countdown.value > 0) {
            countdown.value--;
        } else {
            clearInterval(timer);
        }
    }, 1000);
};

onUnmounted(() => {
    if (timer) clearInterval(timer);
});
</script>

<style scoped>
.verify-container {
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

/* Verify Card */
.verify-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(15px);
    border-radius: 32px;
    padding: 2.5rem;
    max-width: 500px;
    margin: 0 auto;
    border: 1px solid rgba(255, 255, 255, 0.8);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
}

.verify-header {
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

.verify-title {
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

.verify-subtitle {
    font-size: 0.9rem;
    color: #6b7280;
}

/* Email Display */
.email-display {
    background: rgba(99, 102, 241, 0.08);
    border-radius: 16px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.email-display i {
    font-size: 1.5rem;
    color: #6366f1;
}

.email-details span {
    display: block;
    font-size: 0.7rem;
    color: #6b7280;
}

.email-details strong {
    font-size: 0.9rem;
    color: #1e1b4b;
}

/* OTP Form */
.otp-form {
    margin: 1.5rem 0;
}

.form-label {
    display: block;
    margin-bottom: 0.75rem;
    color: #374151;
    font-weight: 500;
    font-size: 0.85rem;
}

.otp-inputs {
    display: flex;
    gap: 0.75rem;
    justify-content: center;
    margin-bottom: 0.75rem;
}

.otp-digit {
    width: 55px;
    height: 65px;
    text-align: center;
    font-size: 1.8rem;
    font-weight: 600;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 14px;
    color: #1f2937;
    transition: all 0.3s ease;
}

.otp-digit:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    transform: scale(1.02);
}

.otp-digit.has-value {
    border-color: #6366f1;
    background: rgba(99, 102, 241, 0.05);
}

.otp-digit.is-invalid {
    border-color: #ef4444;
    animation: shake 0.5s ease;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.otp-hint {
    text-align: center;
    font-size: 0.7rem;
    color: #9ca3af;
    margin-top: 0.5rem;
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
    margin-top: 0.5rem;
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

/* Verify Footer */
.verify-footer {
    margin-top: 1.5rem;
    text-align: center;
}

.resend-section {
    margin-bottom: 1rem;
}

.resend-btn {
    background: transparent;
    border: none;
    color: #6366f1;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}

.resend-btn:hover:not(:disabled) {
    color: #8b5cf6;
    text-decoration: underline;
}

.resend-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.resend-btn.success {
    color: #10b981;
}

.countdown-timer {
    font-size: 0.7rem;
    color: #9ca3af;
    margin-top: 0.25rem;
}

.back-link {
    font-size: 0.8rem;
    color: #6b7280;
    text-decoration: none;
    transition: color 0.3s ease;
    display: inline-block;
}

.back-link:hover {
    color: #6366f1;
}

/* Right Side Help Content */
.help-content-card {
    background: rgba(255, 255, 255, 0.5);
    backdrop-filter: blur(15px);
    border-radius: 32px;
    padding: 2.5rem;
    border: 1px solid rgba(255, 255, 255, 0.8);
    max-width: 500px;
    margin: 0 auto;
}

.help-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
}

.help-icon i {
    font-size: 2rem;
    color: #6366f1;
}

.help-content-card h2 {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: #1e1b4b;
}

.help-content-card > p {
    font-size: 0.9rem;
    color: #6b7280;
    margin-bottom: 1.5rem;
}

.help-steps {
    margin-bottom: 1.5rem;
}

.step-item {
    display: flex;
    gap: 1rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.step-item:last-child {
    border-bottom: none;
}

.step-number {
    width: 30px;
    height: 30px;
    background: rgba(99, 102, 241, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: #6366f1;
}

.step-content strong {
    display: block;
    font-size: 0.85rem;
    color: #1f2937;
}

.step-content p {
    font-size: 0.7rem;
    color: #6b7280;
    margin: 0;
}

.contact-support {
    background: rgba(99, 102, 241, 0.05);
    border-radius: 16px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.contact-support i {
    font-size: 1.2rem;
    color: #6366f1;
}

.contact-support span {
    display: block;
    font-size: 0.7rem;
    color: #6b7280;
}

.contact-support a {
    font-size: 0.85rem;
    color: #6366f1;
    text-decoration: none;
    font-weight: 500;
}

.contact-support a:hover {
    text-decoration: underline;
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
    50% { opacity: 0.7;
}
}

/* Responsive */
@media (max-width: 992px) {
    .verify-card {
        margin: 2rem auto;
        max-width: 450px;
    }
}

@media (max-width: 768px) {
    .verify-card {
        padding: 1.5rem;
    }

    .verify-title {
        font-size: 1.5rem;
    }

    .otp-digit {
        width: 45px;
        height: 55px;
        font-size: 1.5rem;
    }

    .otp-inputs {
        gap: 0.5rem;
    }

    .help-content-card {
        display: none;
    }
}
</style>
