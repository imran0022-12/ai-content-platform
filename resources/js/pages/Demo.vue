<template>
    <div class="demo-page">
        <!-- Animated Background -->
        <div class="animated-bg">
            <div class="gradient-sphere sphere-1"></div>
            <div class="gradient-sphere sphere-2"></div>
            <div class="gradient-sphere sphere-3"></div>
            <div class="grid-overlay"></div>
        </div>

        <!-- Hero Section -->
        <section class="demo-hero">
            <div class="container">
                <div class="hero-content">
                    <div class="hero-badge animate-pulse">
                        <i class="fas fa-play-circle me-2"></i>
                        Live Demo
                    </div>
                    <h1 class="hero-title">
                        See <span class="gradient-text">AI Content Generator</span> in Action
                    </h1>
                    <p class="hero-subtitle">
                        Watch how our AI creates high-quality content in seconds. Try it yourself below!
                    </p>
                </div>
            </div>
        </section>

        <!-- Interactive Demo Section -->
        <section class="demo-section">
            <div class="container">
                <div class="demo-container">
                    <div class="row g-4">
                        <!-- Left Side - Demo Controls -->
                        <div class="col-lg-5">
                            <div class="demo-card animate-slide-right">
                                <div class="demo-header">
                                    <div class="demo-icon">
                                        <i class="fas fa-magic"></i>
                                    </div>
                                    <h3>Try It Free</h3>
                                    <p>Generate sample content instantly</p>
                                </div>

                                <div class="demo-body">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-tag me-2"></i>Content Type
                                        </label>
                                        <div class="content-type-selector">
                                            <div
                                                v-for="type in contentTypes"
                                                :key="type.value"
                                                @click="selectedType = type.value"
                                                class="type-btn"
                                                :class="{ active: selectedType === type.value }"
                                            >
                                                <i :class="type.icon"></i>
                                                <span>{{ type.label }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-pen-alt me-2"></i>What to Create?
                                        </label>
                                        <textarea
                                            v-model="demoPrompt"
                                            class="demo-textarea"
                                            rows="4"
                                            :placeholder="getPlaceholder()"
                                        ></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-cog me-2"></i>Sample Prompts
                                        </label>
                                        <div class="sample-prompts">
                                            <button
                                                v-for="(sample, idx) in getSamplePrompts()"
                                                :key="idx"
                                                @click="demoPrompt = sample"
                                                class="sample-btn"
                                            >
                                                <i class="fas fa-lightbulb"></i>
                                                {{ sample.substring(0, 40) }}...
                                            </button>
                                        </div>
                                    </div>

                                    <button
                                        @click="generateDemo"
                                        class="demo-btn"
                                        :disabled="demoLoading"
                                    >
                                        <span v-if="demoLoading" class="spinner-border spinner-border-sm me-2"></span>
                                        <i v-else class="fas fa-play me-2"></i>
                                        {{ demoLoading ? 'Generating...' : 'Generate Demo Content' }}
                                    </button>

                                    <div class="demo-note">
                                        <i class="fas fa-info-circle"></i>
                                        <span>Free demo • No login required • Real AI generation</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side - Live Preview -->
                        <div class="col-lg-7">
                            <div class="preview-card animate-slide-left">
                                <div class="preview-header">
                                    <div class="preview-tabs">
                                        <button
                                            class="tab-btn"
                                            :class="{ active: activeTab === 'result' }"
                                            @click="activeTab = 'result'"
                                        >
                                            <i class="fas fa-file-alt"></i> Generated Content
                                        </button>
                                        <button
                                            class="tab-btn"
                                            :class="{ active: activeTab === 'preview' }"
                                            @click="activeTab = 'preview'"
                                        >
                                            <i class="fas fa-eye"></i> Live Preview
                                        </button>
                                        <button
                                            class="tab-btn"
                                            :class="{ active: activeTab === 'code' }"
                                            @click="activeTab = 'code'"
                                        >
                                            <i class="fas fa-code"></i> HTML Code
                                        </button>
                                    </div>
                                    <div class="preview-actions">
                                        <button @click="copyDemoResult" class="action-icon" title="Copy">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        <button @click="downloadDemoResult" class="action-icon" title="Download">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="preview-body">
                                    <!-- Result Tab -->
                                    <div v-show="activeTab === 'result'" class="result-content">
                                        <div v-if="demoResult" class="demo-result">
                                            <div class="result-meta" v-if="demoStats">
                                                <span class="meta-badge">
                                                    <i class="fas fa-microchip"></i> Tokens: {{ demoStats.tokens }}
                                                </span>
                                                <span class="meta-badge">
                                                    <i class="fas fa-clock"></i> Generated just now
                                                </span>
                                            </div>
                                            <div class="result-text">{{ demoResult }}</div>
                                        </div>
                                        <div v-else class="empty-preview">
                                            <i class="fas fa-magic"></i>
                                            <p>Click "Generate Demo Content" to see AI in action!</p>
                                            <small>Try any of the sample prompts above</small>
                                        </div>
                                    </div>

                                    <!-- Live Preview Tab -->
                                    <div v-show="activeTab === 'preview'" class="live-preview">
                                        <div v-if="demoResult" class="preview-frame">
                                            <div class="preview-card-styled" v-html="formatPreview(demoResult)"></div>
                                        </div>
                                        <div v-else class="empty-preview">
                                            <i class="fas fa-eye"></i>
                                            <p>Generate content to see live preview</p>
                                        </div>
                                    </div>

                                    <!-- Code Tab -->
                                    <div v-show="activeTab === 'code'" class="code-content">
                                        <div v-if="demoResult" class="code-wrapper">
                                            <pre><code class="language-html">{{ escapeHtml(demoResult) }}</code></pre>
                                        </div>
                                        <div v-else class="empty-preview">
                                            <i class="fas fa-code"></i>
                                            <p>Generate content to view HTML code</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="preview-footer">
                                    <div class="feature-badges">
                                        <span class="feature-badge"><i class="fas fa-check-circle"></i> SEO Optimized</span>
                                        <span class="feature-badge"><i class="fas fa-check-circle"></i> Plagiarism Free</span>
                                        <span class="feature-badge"><i class="fas fa-check-circle"></i> Human Quality</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Showcase -->
        <section class="features-showcase">
            <div class="container">
                <div class="section-header">
                    <h2>Why Choose <span class="gradient-text">AI Content Platform</span></h2>
                    <p>Join thousands of creators using our advanced AI technology</p>
                </div>
                <div class="features-grid-demo">
                    <div class="feature-item-demo">
                        <div class="feature-icon-demo">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h4>Lightning Fast</h4>
                        <p>Generate content in seconds, not hours</p>
                    </div>
                    <div class="feature-item-demo">
                        <div class="feature-icon-demo">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>SEO Optimized</h4>
                        <p>Content that ranks on Google</p>
                    </div>
                    <div class="feature-item-demo">
                        <div class="feature-icon-demo">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4>100% Original</h4>
                        <p>Unique, plagiarism-free content</p>
                    </div>
                    <div class="feature-item-demo">
                        <div class="feature-icon-demo">
                            <i class="fas fa-language"></i>
                        </div>
                        <h4>Multiple Languages</h4>
                        <p>Support for 50+ languages</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="demo-cta">
            <div class="container">
                <div class="cta-box">
                    <h2>Ready to Create Amazing Content?</h2>
                    <p>Get started with 10 free generations today!</p>
                    <div class="cta-buttons">
                        <router-link to="/register" class="btn-primary-glow">
                            <i class="fas fa-user-plus me-2"></i>Sign Up Free
                            <span class="btn-glow"></span>
                        </router-link>
                        <router-link to="/login" class="btn-outline-modern">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </router-link>
                    </div>
                    <p class="cta-note">✨ No credit card required • Cancel anytime</p>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const contentTypes = [
    { value: 'blog', label: 'Blog Post', icon: 'fas fa-blog' },
    { value: 'social', label: 'Social Media', icon: 'fab fa-twitter' },
    { value: 'email', label: 'Email', icon: 'fas fa-envelope' },
    { value: 'tagline', label: 'Tagline', icon: 'fas fa-tag' },
    { value: 'product', label: 'Product', icon: 'fas fa-box' },
    { value: 'seo', label: 'SEO Meta', icon: 'fas fa-chart-line' }
];

const selectedType = ref('blog');
const demoPrompt = ref('');
const demoResult = ref('');
const demoLoading = ref(false);
const demoStats = ref(null);
const activeTab = ref('result');

const getPlaceholder = () => {
    const placeholders = {
        blog: 'Example: Write a blog post about the benefits of meditation for busy professionals...',
        social: 'Example: Create a viral Twitter thread about AI in marketing...',
        email: 'Example: Write a welcome email for new subscribers to our newsletter...',
        tagline: 'Example: Generate catchy taglines for a eco-friendly coffee shop...',
        product: 'Example: Write a product description for a wireless noise-canceling headphones...',
        seo: 'Example: Create SEO meta tags for an article about healthy meal prep...'
    };
    return placeholders[selectedType.value] || 'Enter your prompt here...';
};

const getSamplePrompts = () => {
    const samples = {
        blog: [
            'The future of artificial intelligence in content creation',
            '10 productivity hacks for remote workers in 2024',
            'How to start a successful podcast on a budget'
        ],
        social: [
            'Exciting news about our new AI platform! Create engaging social media post',
            '5 tips for growing your Instagram following organically',
            'Behind the scenes: How our AI creates content'
        ],
        email: [
            'Welcome to our community! Create a warm welcome email for new users',
            'Special offer announcement for our loyal customers',
            'Weekly newsletter about AI and content marketing'
        ],
        tagline: [
            'Create memorable taglines for a sustainable fashion brand',
            'Generate catchy slogans for a coffee shop chain',
            'Brand taglines for a tech startup focused on innovation'
        ],
        product: [
            'Write a compelling product description for wireless earbuds',
            'Describe a smart home security system with advanced features',
            'Product description for an eco-friendly water bottle'
        ],
        seo: [
            'Create SEO meta tags for an article about digital marketing trends',
            'Generate SEO-optimized titles for a fitness blog',
            'Meta descriptions for a travel website about European destinations'
        ]
    };
    return samples[selectedType.value] || samples.blog;
};

const generateDemo = async () => {
    if (!demoPrompt.value.trim()) {
        alert('Please enter a prompt or select a sample!');
        return;
    }

    demoLoading.value = true;
    demoResult.value = '';
    demoStats.value = null;

    try {
        // Using a demo endpoint - you can adjust this to your actual API
        const response = await axios.post('/api/demo-generate', {
            prompt: demoPrompt.value,
            type: selectedType.value
        });

        if (response.data.success) {
            demoResult.value = response.data.data.result;
            demoStats.value = {
                tokens: response.data.data.tokens_used
            };
            activeTab.value = 'result';
        }
    } catch (error) {
        // Fallback demo response for showcase
        demoResult.value = getDemoResponse(demoPrompt.value, selectedType.value);
        demoStats.value = { tokens: Math.floor(Math.random() * 300) + 100 };
        console.error('Demo API error:', error);
    } finally {
        demoLoading.value = false;
    }
};

const getDemoResponse = (prompt, type) => {
    const responses = {
        blog: `# ${prompt}\n\n## Introduction\n\nWelcome to this comprehensive guide about ${prompt}. In today's fast-paced digital world, understanding ${prompt} has become essential for success.\n\n## Key Benefits\n\n- **Increased Productivity**: Save up to 5 hours per week\n- **Better Results**: Achieve 3x better outcomes\n- **Cost Effective**: Reduce expenses by 40%\n\n## Getting Started\n\n1. Assess your current situation\n2. Set clear, achievable goals\n3. Implement proven strategies\n4. Track and optimize results\n\n## Conclusion\n\n${prompt} is transforming how we work. Start your journey today!\n\n*This is a demo response. Sign up for full access!*`,

        social: `🌟 Just discovered the power of ${prompt}! Here's what you need to know:\n\n✅ Game-changing insights\n✅ Proven strategies that work\n✅ Real results from real users\n\nReady to level up? 🚀 #ContentCreation #AI #Success\n\n*Demo content - Sign up for full access!*`,

        email: `**Subject:** 🚀 Transform Your ${prompt} Journey\n\nDear valued customer,\n\nI hope this email finds you well! Today, I'm excited to share how ${prompt} can transform your results.\n\n**Here's what you'll get:**\n• Exclusive strategies\n• Expert insights\n• Actionable tips\n\n[CLAIM YOUR ACCESS]\n\nBest regards,\nThe AI Team\n\n*Demo content - Sign up for real AI-generated emails!*`,

        tagline: `🎯 **Creative Taglines for ${prompt}**\n\n1. "${prompt} - Excellence Redefined"\n2. "Your ${prompt}, Your Success"\n3. "Experience the ${prompt} Difference"\n4. "${prompt}: Quality You Can Trust"\n5. "Transform Your World with ${prompt}"\n\n*Demo content - Sign up for 10+ unique taglines with explanations!*`,

        product: `**✨ Introducing ${prompt}**\n\n**Premium Quality** - Crafted with the finest materials\n\n**Key Features:**\n✓ Advanced technology\n✓ Sleek, modern design\n✓ Long-lasting durability\n\n**Why Choose Our ${prompt}?**\n✓ 30-day money-back guarantee\n✓ Free shipping worldwide\n✓ 24/7 customer support\n\n*Demo content - Sign up for real product descriptions!*`,

        seo: `**SEO Meta Tags for: ${prompt}**\n\n**Meta Title (52 chars):**\nBest ${prompt} Guide 2024 | Tips & Strategies\n\n**Meta Description (158 chars):**\nDiscover the ultimate guide to ${prompt}. Learn proven strategies, expert tips, and actionable steps to master ${prompt} today!\n\n**Keywords:**\n• ${prompt}\n• Best ${prompt} 2024\n• ${prompt} guide\n\n*Demo content - Sign up for real SEO optimization!*`
    };
    return responses[type] || responses.blog;
};

const copyDemoResult = () => {
    if (demoResult.value) {
        navigator.clipboard.writeText(demoResult.value);
        alert('Content copied to clipboard!');
    }
};

const downloadDemoResult = () => {
    if (demoResult.value) {
        const blob = new Blob([demoResult.value], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${selectedType.value}_demo_${Date.now()}.txt`;
        a.click();
        URL.revokeObjectURL(url);
    }
};

const formatPreview = (text) => {
    return text.replace(/\n/g, '<br>').replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
};

const escapeHtml = (text) => {
    return text.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
};
</script>

<style scoped>
.demo-page {
    min-height: 100vh;
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

/* Hero Section */
.demo-hero {
    position: relative;
    z-index: 1;
    padding: 80px 0 40px;
    text-align: center;
}

.hero-badge {
    display: inline-block;
    background: rgba(99, 102, 241, 0.1);
    border: 1px solid rgba(99, 102, 241, 0.2);
    padding: 8px 20px;
    border-radius: 50px;
    font-size: 0.85rem;
    color: #6366f1;
    margin-bottom: 1.5rem;
}

.hero-title {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 1rem;
    color: #1e1b4b;
}

.gradient-text {
    background: linear-gradient(135deg, #6366f1, #8b5cf6, #d946ef);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.hero-subtitle {
    font-size: 1.1rem;
    color: #6b7280;
    max-width: 600px;
    margin: 0 auto;
}

/* Demo Section */
.demo-section {
    position: relative;
    z-index: 1;
    padding: 40px 0 60px;
}

.demo-container {
    max-width: 1300px;
    margin: 0 auto;
    padding: 0 20px;
}

.demo-card, .preview-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    border: 1px solid rgba(255, 255, 255, 0.8);
    overflow: hidden;
    box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.1);
}

.demo-header {
    padding: 1.5rem;
    text-align: center;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.demo-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}

.demo-icon i {
    font-size: 1.8rem;
    color: #6366f1;
}

.demo-header h3 {
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    color: #1e1b4b;
}

.demo-header p {
    font-size: 0.85rem;
    color: #6b7280;
    margin: 0;
}

.demo-body {
    padding: 1.5rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    color: #374151;
    font-weight: 500;
    font-size: 0.85rem;
}

.content-type-selector {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.5rem;
}

.type-btn {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 0.6rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.type-btn i {
    font-size: 1rem;
    display: block;
    margin-bottom: 0.2rem;
    color: #6b7280;
}

.type-btn span {
    font-size: 0.7rem;
    color: #6b7280;
}

.type-btn:hover, .type-btn.active {
    background: rgba(99, 102, 241, 0.05);
    border-color: #6366f1;
    transform: translateY(-2px);
}

.type-btn.active i,
.type-btn.active span {
    color: #6366f1;
}

.demo-textarea {
    width: 100%;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 0.8rem;
    color: #1f2937;
    font-size: 0.85rem;
    resize: vertical;
}

.demo-textarea:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.sample-prompts {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.sample-btn {
    background: rgba(99, 102, 241, 0.05);
    border: 1px solid rgba(99, 102, 241, 0.15);
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
    font-size: 0.75rem;
    color: #4b5563;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: left;
}

.sample-btn:hover {
    background: rgba(99, 102, 241, 0.1);
    border-color: #6366f1;
    color: #6366f1;
}

.demo-btn {
    width: 100%;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    padding: 12px;
    border-radius: 12px;
    border: none;
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 1rem;
}

.demo-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
}

.demo-note {
    text-align: center;
    margin-top: 1rem;
    font-size: 0.7rem;
    color: #9ca3af;
}

/* Preview Card */
.preview-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    flex-wrap: wrap;
    gap: 1rem;
}

.preview-tabs {
    display: flex;
    gap: 0.5rem;
}

.tab-btn {
    background: transparent;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.8rem;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.3s ease;
}

.tab-btn:hover, .tab-btn.active {
    background: rgba(99, 102, 241, 0.1);
    color: #6366f1;
}

.preview-actions {
    display: flex;
    gap: 0.5rem;
}

.action-icon {
    background: rgba(0, 0, 0, 0.05);
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #6b7280;
}

.action-icon:hover {
    background: #6366f1;
    color: white;
}

.preview-body {
    padding: 1.5rem;
    min-height: 400px;
}

.result-content, .live-preview, .code-content {
    max-height: 400px;
    overflow-y: auto;
}

.result-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.meta-badge {
    font-size: 0.7rem;
    color: #6366f1;
    background: rgba(99, 102, 241, 0.1);
    padding: 0.2rem 0.5rem;
    border-radius: 20px;
}

.result-text {
    white-space: pre-wrap;
    line-height: 1.6;
    color: #374151;
}

.empty-preview {
    text-align: center;
    padding: 3rem;
    color: #9ca3af;
}

.empty-preview i {
    font-size: 3rem;
    margin-bottom: 1rem;
    display: block;
}

.preview-frame {
    background: white;
    border-radius: 12px;
    padding: 1rem;
    border: 1px solid #e5e7eb;
}

.code-wrapper pre {
    background: #1e1e1e;
    color: #d4d4d4;
    padding: 1rem;
    border-radius: 12px;
    overflow-x: auto;
    font-size: 0.8rem;
    margin: 0;
}

.preview-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid rgba(0, 0, 0, 0.05);
}

.feature-badges {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    justify-content: center;
}

.feature-badge {
    font-size: 0.7rem;
    color: #10b981;
}

/* Features Showcase */
.features-showcase {
    position: relative;
    z-index: 1;
    padding: 60px 0;
}

.section-header {
    text-align: center;
    margin-bottom: 3rem;
}

.section-header h2 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: #1e1b4b;
}

.features-grid-demo {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 2rem;
    max-width: 1000px;
    margin: 0 auto;
}

.feature-item-demo {
    text-align: center;
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.5);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    transition: all 0.3s ease;
}

.feature-item-demo:hover {
    transform: translateY(-5px);
    background: rgba(255, 255, 255, 0.7);
}

.feature-icon-demo {
    width: 60px;
    height: 60px;
    background: rgba(99, 102, 241, 0.1);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}

.feature-icon-demo i {
    font-size: 1.8rem;
    color: #6366f1;
}

.feature-item-demo h4 {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #1e1b4b;
}

.feature-item-demo p {
    font-size: 0.8rem;
    color: #6b7280;
    margin: 0;
}

/* CTA Section */
.demo-cta {
    position: relative;
    z-index: 1;
    padding: 60px 0 80px;
}

.cta-box {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
    border-radius: 32px;
    padding: 3rem;
    text-align: center;
    backdrop-filter: blur(10px);
}

.cta-box h2 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: #1e1b4b;
}

.cta-box p {
    font-size: 1rem;
    color: #6b7280;
    margin-bottom: 1.5rem;
}

.cta-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-primary-glow {
    position: relative;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    padding: 12px 28px;
    border-radius: 12px;
    color: white;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    overflow: hidden;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary-glow:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
    color: white;
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

.btn-outline-modern {
    background: white;
    border: 1px solid #e5e7eb;
    padding: 12px 28px;
    border-radius: 12px;
    color: #4b5563;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-outline-modern:hover {
    border-color: #6366f1;
    color: #6366f1;
    transform: translateY(-2px);
}

.cta-note {
    font-size: 0.7rem;
    color: #9ca3af;
    margin-top: 1rem;
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
    from { opacity: 0; transform: translateX(-30px); }
    to { opacity: 1; transform: translateX(0); }
}

@keyframes slideLeft {
    from { opacity: 0; transform: translateX(30px); }
    to { opacity: 1; transform: translateX(0); }
}

@keyframes pulseAnim {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

/* Responsive */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }

    .content-type-selector {
        grid-template-columns: repeat(3, 1fr);
    }

    .preview-header {
        flex-direction: column;
    }

    .cta-buttons {
        flex-direction: column;
    }

    .cta-box {
        padding: 2rem;
    }

    .cta-box h2 {
        font-size: 1.5rem;
    }
}
</style>
