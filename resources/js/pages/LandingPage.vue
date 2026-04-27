<template>
  <div class="landing-page" ref="landingPage">
    <!-- Animated Background -->
    <div class="animated-bg">
      <div class="gradient-sphere sphere-1"></div>
      <div class="gradient-sphere sphere-2"></div>
      <div class="gradient-sphere sphere-3"></div>
      <div class="grid-overlay"></div>
    </div>

    <!-- Hero Section -->
    <section class="hero-section">
      <div class="container">
        <div class="hero-wrapper">
          <div class="hero-content">
            <div class="floating-badge" ref="heroBadge">
              <span class="pulse-dot"></span>
              <i class="fas fa-brain me-2"></i>
              Introducing NeuroGen 2.0
            </div>
            <h1 class="hero-title" ref="heroTitle">
              Create <span class="gradient-text">Epic Content</span> <br />With
              <span class="gradient-text">Neural AI</span>
              <div class="hero-accent"></div>
            </h1>
            <p class="hero-description" ref="heroSubtitle">
              Generate human-quality blog posts, viral social media content,
              email sequences, brand taglines, and SEO-optimized meta tags using
              advanced LLMs.
            </p>
            <div class="hero-actions" ref="heroButtons">
              <router-link to="/register" class="btn-primary-glow">
                <span class="btn-text">Start Creating Free</span>
                <i class="fas fa-arrow-right ms-2"></i>
                <span class="btn-glow"></span>
              </router-link>
              <router-link to="/demo" class="btn-outline-modern">
                <i class="fas fa-play me-2"></i>
                Watch Demo
              </router-link>
            </div>
            <div class="hero-stats" ref="heroStats">
              <div class="stat-item" v-for="(stat, idx) in stats" :key="idx">
                <div class="stat-number">
                  <span class="counter-val" :data-target="stat.value">0</span>
                  <span>{{ stat.suffix }}</span>
                </div>
                <div class="stat-label">{{ stat.label }}</div>
              </div>
            </div>
          </div>
          <div class="hero-visual" ref="heroVisual">
            <div class="visual-card">
              <div class="visual-header">
                <div class="visual-dots">
                  <span></span><span></span><span></span>
                </div>
                <span>AI Generated</span>
              </div>
              <div class="visual-content">
                <div class="typing-animation" ref="typingText"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="scroll-hint" ref="scrollHint">
        <div class="scroll-wheel"></div>
        <span>Scroll to explore</span>
      </div>
    </section>

    <!-- Features Grid Section -->
    <section class="features-section" ref="featuresSection">
      <div class="container">
        <div class="section-label">
          <span class="label-badge">✨ Features</span>
        </div>
        <h2 class="section-title">
          Everything you need in <span class="gradient-text">one platform</span>
        </h2>
        <div class="features-grid">
          <div
            v-for="(feature, idx) in features"
            :key="idx"
            class="feature-card"
            :style="{ transitionDelay: `${idx * 50}ms` }"
          >
            <div class="feature-icon-wrap">
              <i :class="feature.icon"></i>
            </div>
            <h3>{{ feature.title }}</h3>
            <p>{{ feature.description }}</p>
            <div class="feature-more">
              <span>Learn more</span>
              <i class="fas fa-arrow-right"></i>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Dashboard Section -->
    <section class="stats-dashboard" ref="statsSection">
      <div class="container">
        <div class="stats-wrapper">
          <div class="stats-card-large">
            <div class="stats-header">
              <span class="live-badge">
                <span class="live-dot"></span>
                Live Analytics
              </span>
            </div>
            <div class="stats-grid">
              <div
                v-for="(stat, idx) in dashboardStats"
                :key="idx"
                class="stat-block"
              >
                <div class="stat-icon">
                  <i :class="stat.icon"></i>
                </div>
                <div class="stat-data">
                  <div class="stat-number" :ref="(el) => setDashStat(el, idx)">
                    0
                  </div>
                  <div class="stat-name">{{ stat.name }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- How It Works - Timeline Style -->
    <section class="timeline-section" ref="timelineSection">
      <div class="container">
        <div class="section-label center">
          <span class="label-badge">⚡ Simple Process</span>
        </div>
        <h2 class="section-title center">
          Get started in <span class="gradient-text">3 easy steps</span>
        </h2>
        <div class="timeline">
          <div v-for="(step, idx) in steps" :key="idx" class="timeline-item">
            <div class="timeline-marker">
              <div class="marker-number">{{ idx + 1 }}</div>
              <div v-if="idx < steps.length - 1" class="marker-line"></div>
            </div>
            <div
              class="timeline-content"
              :style="{ transitionDelay: `${idx * 150}ms` }"
            >
              <div class="timeline-icon">
                <i :class="step.icon"></i>
              </div>
              <h3>{{ step.title }}</h3>
              <p>{{ step.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing Section - Modern Cards -->
    <section class="pricing-modern" ref="pricingSection">
      <div class="container">
        <div class="section-label center">
          <span class="label-badge">💰 Pricing</span>
        </div>
        <h2 class="section-title center">
          Simple, <span class="gradient-text">transparent pricing</span>
        </h2>
        <div class="pricing-cards">
          <div
            v-for="(plan, idx) in pricingPlans"
            :key="idx"
            class="pricing-card-modern"
            :class="{ popular: plan.popular }"
          >
            <div v-if="plan.popular" class="popular-tag">🔥 Most Popular</div>
            <h3>{{ plan.name }}</h3>
            <div class="price-display">
              <span class="currency">$</span>
              <span class="amount">{{ plan.price }}</span>
              <span class="period">/month</span>
            </div>
            <p class="plan-desc">{{ plan.description }}</p>
            <ul class="feature-list-modern">
              <li v-for="(feature, fIdx) in plan.features" :key="fIdx">
                <i class="fas fa-check-circle"></i>
                <span>{{ feature }}</span>
              </li>
            </ul>
            <router-link
              :to="plan.buttonLink"
              class="plan-btn"
              :class="plan.popular ? 'btn-primary' : 'btn-outline'"
            >
              {{ plan.buttonText }}
              <i class="fas fa-arrow-right ms-2"></i>
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials - Carousel Style -->
    <section class="testimonials-modern" ref="testimonialsSection">
      <div class="container">
        <div class="section-label center">
          <span class="label-badge">💬 Testimonials</span>
        </div>
        <h2 class="section-title center">
          Trusted by <span class="gradient-text">creators worldwide</span>
        </h2>
        <div class="testimonial-carousel">
          <div class="testimonial-track">
            <div
              v-for="(testimonial, idx) in testimonials"
              :key="idx"
              class="testimonial-slide"
            >
              <div class="quote-mark">"</div>
              <p class="testimonial-quote">{{ testimonial.text }}</p>
              <div class="testimonial-author-modern">
                <div class="author-avatar-modern">
                  <img
                    :src="`https://ui-avatars.com/api/?background=6d28d9&color=fff&bold=true&name=${testimonial.name}`"
                    alt=""
                  />
                </div>
                <div class="author-details">
                  <h4>{{ testimonial.name }}</h4>
                  <span>{{ testimonial.role }}</span>
                  <div class="stars">
                    <i
                      v-for="star in 5"
                      :key="star"
                      class="fas fa-star"
                      :class="{ filled: star <= testimonial.rating }"
                    ></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Banner -->
    <section class="cta-banner">
      <div class="container">
        <div class="cta-wrapper">
          <div class="cta-content">
            <h2>
              Ready to transform your
              <span class="gradient-text">content creation?</span>
            </h2>
            <p>Join 100,000+ creators using the most advanced AI platform</p>
            <router-link to="/register" class="cta-btn">
              Start your free trial
              <i class="fas fa-arrow-right ms-2"></i>
              <div class="btn-particles"></div>
            </router-link>
            <p class="cta-note">✨ No credit card required • Cancel anytime</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer-modern">
      <div class="container">
        <div class="footer-grid-modern">
          <div class="footer-brand-modern">
            <h3>AI Content <span class="gradient-text">Platform</span></h3>
            <p>The most advanced AI content generation platform of 2026.</p>
            <div class="social-links">
              <a href="#"><i class="fab fa-twitter"></i></a>
              <a href="#"><i class="fab fa-linkedin-in"></i></a>
              <a href="#"><i class="fab fa-github"></i></a>
              <a href="#"><i class="fab fa-discord"></i></a>
            </div>
          </div>
          <div class="footer-links-modern">
            <h4>Product</h4>
            <ul>
              <li><a href="#">Features</a></li>
              <li><a href="#">Pricing</a></li>
              <li><a href="#">API</a></li>
              <li><a href="#">Integrations</a></li>
            </ul>
          </div>
          <div class="footer-links-modern">
            <h4>Company</h4>
            <ul>
              <li><a href="#">About</a></li>
              <li><a href="#">Blog</a></li>
              <li><a href="#">Careers</a></li>
              <li><a href="#">Press</a></li>
            </ul>
          </div>
          <div class="footer-links-modern">
            <h4>Resources</h4>
            <ul>
              <li><a href="#">Documentation</a></li>
              <li><a href="#">Help Center</a></li>
              <li><a href="#">Community</a></li>
              <li><a href="#">Status</a></li>
            </ul>
          </div>
        </div>
        <div class="footer-bottom-modern">
          <p>
            © 2026 NeuroWriter. All rights reserved. Made with
            <i class="fas fa-heart" style="color: #6d28d9"></i> for creators
          </p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

// Data
const stats = ref([
  { label: "Active Users", value: 100000, suffix: "+" },
  { label: "Content Generated", value: 5000000, suffix: "+" },
  { label: "Time Saved", value: 85, suffix: "%" },
]);

const dashboardStats = ref([
  { icon: "fas fa-users", name: "Happy Creators", target: 50000 },
  { icon: "fas fa-file-alt", name: "Content Created", target: 2500000 },
  { icon: "fas fa-star", name: "5-Star Reviews", target: 15000 },
  { icon: "fas fa-globe", name: "Countries", target: 120 },
]);

const features = ref([
  {
    icon: "fas fa-blog",
    title: "GPT-5 Blog Generation",
    description:
      "Generate human-quality blog posts that rank #1 on Google with our advanced AI.",
  },
  {
    icon: "fab fa-tiktok",
    title: "Viral Social Media",
    description:
      "Create engaging content optimized for TikTok, Instagram Reels, and YouTube Shorts.",
  },
  {
    icon: "fas fa-envelope",
    title: "Smart Email Sequences",
    description:
      "Write personalized email sequences with 45% higher open rates.",
  },
  {
    icon: "fas fa-chart-line",
    title: "SEO First Content",
    description:
      "Real-time SEO optimization following Google's latest EEAT guidelines.",
  },
  {
    icon: "fas fa-microphone-alt",
    title: "Voice-to-Content",
    description:
      "Speak your ideas - AI turns voice notes into polished content instantly.",
  },
  {
    icon: "fas fa-palette",
    title: "Brand Voice Cloning",
    description:
      "Upload your content and AI learns your unique brand voice perfectly.",
  },
]);

const steps = ref([
  {
    icon: "fas fa-pen-alt",
    title: "Write or Speak",
    description:
      "Type your ideas or use voice input - our AI understands natural language.",
  },
  {
    icon: "fas fa-magic",
    title: "AI Processing",
    description:
      "Neural networks analyze and generate unique content in seconds.",
  },
  {
    icon: "fas fa-rocket",
    title: "Publish & Scale",
    description:
      "Export, schedule, or publish directly to your favorite platforms.",
  },
]);

const pricingPlans = ref([
  {
    name: "Starter",
    price: "19",
    description: "Perfect for individuals",
    popular: false,
    buttonText: "Start Free Trial",
    buttonLink: "/register",
    features: [
      "20,000 AI words/month",
      "6 content types",
      "GPT-5 Access",
      "Email support",
    ],
  },
  {
    name: "Pro",
    price: "49",
    description: "Best for professionals",
    popular: true,
    buttonText: "Start Pro Trial",
    buttonLink: "/register",
    features: [
      "100,000 AI words/month",
      "All content types",
      "Priority GPT-5 Access",
      "24/7 Priority support",
      "Brand voice cloning",
    ],
  },
  {
    name: "Enterprise",
    price: "199",
    description: "For large organizations",
    popular: false,
    buttonText: "Contact Sales",
    buttonLink: "/contact",
    features: [
      "Unlimited AI words",
      "Custom AI training",
      "Dedicated instance",
      "SLA guarantee",
      "Team collaboration",
    ],
  },
]);

const testimonials = ref([
  {
    name: "Sarah Johnson",
    role: "Content Director",
    text: "This platform has completely transformed our content workflow. We're producing 5x more content with better quality.",
    rating: 5,
  },
  {
    name: "Michael Chen",
    role: "SEO Specialist",
    text: "The AI-generated content ranks incredibly well. Our organic traffic increased by 200% in just 3 months.",
    rating: 5,
  },
  {
    name: "Emily Rodriguez",
    role: "Marketing Manager",
    text: "Finally, an AI that understands brand voice. The content sounds like our team wrote it!",
    rating: 5,
  },
]);

// Refs for animations
const landingPage = ref(null);
const heroBadge = ref(null);
const heroTitle = ref(null);
const heroSubtitle = ref(null);
const heroButtons = ref(null);
const heroStats = ref(null);
const heroVisual = ref(null);
const scrollHint = ref(null);
const featuresSection = ref(null);
const statsSection = ref(null);
const timelineSection = ref(null);
const pricingSection = ref(null);
const testimonialsSection = ref(null);
const typingText = ref(null);
const dashStats = ref([]);

const setDashStat = (el, index) => {
  if (el) dashStats.value[index] = el;
};

// Typing animation text
const typingStrings = [
  "Write a blog post about AI trends...",
  "Create viral Twitter thread...",
  "Generate email subject lines...",
  "Write product descriptions...",
];

onMounted(() => {
  // Hero animations
  const tl = anime.timeline({ easing: "easeOutExpo" });

  tl.add({
    targets: heroBadge.value,
    scale: [0, 1],
    opacity: [0, 1],
    duration: 800,
    delay: 200,
  })
    .add(
      {
        targets: heroTitle.value,
        translateY: [50, 0],
        opacity: [0, 1],
        duration: 800,
      },
      "-=600"
    )
    .add(
      {
        targets: heroSubtitle.value,
        translateY: [30, 0],
        opacity: [0, 1],
        duration: 800,
      },
      "-=600"
    )
    .add(
      {
        targets: heroButtons.value.children,
        scale: [0.9, 1],
        opacity: [0, 1],
        delay: anime.stagger(100),
        duration: 600,
      },
      "-=600"
    )
    .add(
      {
        targets: heroStats.value.children,
        translateY: [20, 0],
        opacity: [0, 1],
        delay: anime.stagger(80),
        duration: 600,
      },
      "-=400"
    )
    .add(
      {
        targets: heroVisual.value,
        translateX: [50, 0],
        opacity: [0, 1],
        duration: 1000,
        easing: "easeOutBack",
      },
      "-=800"
    );

  // Typing animation
  let charIndex = 0;
  let textIndex = 0;
  let isDeleting = false;

  const typeText = () => {
    if (!typingText.value) return;
    const currentText = typingStrings[textIndex];

    if (isDeleting) {
      charIndex--;
      typingText.value.textContent = currentText.substring(0, charIndex);
    } else {
      charIndex++;
      typingText.value.textContent = currentText.substring(0, charIndex);
    }

    if (!isDeleting && charIndex === currentText.length) {
      isDeleting = true;
      setTimeout(typeText, 2000);
    } else if (isDeleting && charIndex === 0) {
      isDeleting = false;
      textIndex = (textIndex + 1) % typingStrings.length;
      setTimeout(typeText, 500);
    } else {
      setTimeout(typeText, isDeleting ? 50 : 100);
    }
  };

  setTimeout(typeText, 1000);

  // Scroll hint animation
  anime({
    targets: scrollHint.value,
    translateY: [-10, 10],
    direction: "alternate",
    loop: true,
    duration: 1000,
    easing: "easeInOutSine",
  });

  // Intersection observers for scroll animations
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = "1";
          entry.target.style.transform = "translateY(0)";
        }
      });
    },
    { threshold: 0.1, rootMargin: "0px 0px -100px 0px" }
  );

  // Observe sections
  const sections = [
    featuresSection.value,
    statsSection.value,
    timelineSection.value,
    pricingSection.value,
    testimonialsSection.value,
  ];
  sections.forEach((section) => {
    if (section) {
      section.style.opacity = "0";
      section.style.transform = "translateY(50px)";
      section.style.transition = "all 0.8s ease";
      observer.observe(section);
    }
  });

  // Animate stats on view
  const statObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          dashboardStats.value.forEach((stat, i) => {
            anime({
              targets: dashStats.value[i],
              innerHTML: [0, stat.target],
              round: 1,
              duration: 2000,
              delay: i * 200,
              easing: "easeOutQuad",
              update: function (anim) {
                if (dashStats.value[i]) {
                  dashStats.value[i].innerHTML = Math.floor(
                    anim.animations[0].currentValue
                  ).toLocaleString();
                }
              },
            });
          });
          statObserver.disconnect();
        }
      });
    },
    { threshold: 0.5 }
  );

  if (statsSection.value) statObserver.observe(statsSection.value);

  // Hero stats counter
  setTimeout(() => {
    document.querySelectorAll(".counter-val").forEach((counter) => {
      const target = parseInt(counter.dataset.target);
      anime({
        targets: counter,
        innerHTML: [0, target],
        round: 1,
        duration: 2000,
        easing: "easeOutQuad",
        update: function (anim) {
          counter.innerHTML = Math.floor(
            anim.animations[0].currentValue
          ).toLocaleString();
        },
      });
    });
  }, 1500);
});
</script>

<style scoped>
/* Light Gradient Theme - 2026 Modern */
.landing-page {
  overflow-x: hidden;
  background: linear-gradient(135deg, #f5f7ff 0%, #f0f2ff 50%, #faf5ff 100%);
  position: relative;
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
  opacity: 0.4;
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
  0%,
  100% {
    transform: translate(0, 0) scale(1);
  }
  33% {
    transform: translate(50px, -50px) scale(1.1);
  }
  66% {
    transform: translate(-50px, 50px) scale(0.9);
  }
}

.grid-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-image: linear-gradient(
      rgba(99, 102, 241, 0.03) 1px,
      transparent 1px
    ),
    linear-gradient(90deg, rgba(99, 102, 241, 0.03) 1px, transparent 1px);
  background-size: 50px 50px;
}

/* Hero Section */
.hero-section {
  min-height: 100vh;
  position: relative;
  display: flex;
  align-items: center;
  padding: 120px 0 80px;
  z-index: 2;
}

.hero-wrapper {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
}

.hero-content {
  position: relative;
}

.floating-badge {
  display: inline-flex;
  align-items: center;
  background: rgba(99, 102, 241, 0.1);
  border: 1px solid rgba(99, 102, 241, 0.2);
  padding: 8px 20px;
  border-radius: 50px;
  font-size: 0.85rem;
  color: #6366f1;
  margin-bottom: 2rem;
  backdrop-filter: blur(10px);
}

.pulse-dot {
  width: 8px;
  height: 8px;
  background: #10b981;
  border-radius: 50%;
  margin-right: 8px;
  animation: pulse 1.5s infinite;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.5;
    transform: scale(1.2);
  }
}

.hero-title {
  font-size: 4rem;
  font-weight: 800;
  line-height: 1.2;
  margin-bottom: 1.5rem;
  background: linear-gradient(135deg, #1e1b4b 0%, #3730a3 100%);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}

.gradient-text {
  background: linear-gradient(135deg, #6366f1, #8b5cf6, #d946ef);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}

.hero-description {
  font-size: 1.1rem;
  color: #4b5563;
  line-height: 1.6;
  margin-bottom: 2rem;
}

.hero-actions {
  display: flex;
  gap: 1rem;
  margin-bottom: 3rem;
  flex-wrap: wrap;
}

.btn-primary-glow {
  position: relative;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  padding: 12px 28px;
  border-radius: 12px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  overflow: hidden;
  color: white;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-primary-glow:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
}

.btn-glow {
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.2),
    transparent
  );
  transition: left 0.5s ease;
}

.btn-primary-glow:hover .btn-glow {
  left: 100%;
}

.btn-outline-modern {
  background: rgba(99, 102, 241, 0.1);
  border: 1px solid rgba(99, 102, 241, 0.3);
  padding: 12px 28px;
  border-radius: 12px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  color: #6366f1;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-outline-modern:hover {
  background: rgba(99, 102, 241, 0.2);
  transform: translateY(-2px);
}

.hero-stats {
  display: flex;
  gap: 3rem;
}

.stat-item {
  text-align: center;
}

.stat-number {
  font-size: 1.8rem;
  font-weight: 700;
  color: #1e1b4b;
}

.stat-label {
  font-size: 0.8rem;
  color: #6b7280;
}

/* Hero Visual Card */
.hero-visual {
  position: relative;
}

.visual-card {
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(20px);
  border-radius: 24px;
  border: 1px solid rgba(255, 255, 255, 0.8);
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
  overflow: hidden;
}

.visual-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: rgba(255, 255, 255, 0.5);
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.visual-dots {
  display: flex;
  gap: 6px;
}

.visual-dots span {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #cbd5e1;
}

.visual-dots span:first-child {
  background: #ef4444;
}
.visual-dots span:nth-child(2) {
  background: #eab308;
}
.visual-dots span:nth-child(3) {
  background: #10b981;
}

.visual-header span {
  font-size: 0.8rem;
  color: #64748b;
}

.visual-content {
  padding: 2rem;
  min-height: 150px;
}

.typing-animation {
  font-size: 1rem;
  color: #334155;
  font-family: monospace;
}

.typing-animation::before {
  content: ">";
  color: #6366f1;
  margin-right: 8px;
}

/* Scroll Hint */
.scroll-hint {
  position: absolute;
  bottom: 30px;
  left: 50%;
  transform: translateX(-50%);
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.scroll-wheel {
  width: 24px;
  height: 38px;
  border: 2px solid #6366f1;
  border-radius: 20px;
  position: relative;
}

.scroll-wheel::before {
  content: "";
  position: absolute;
  top: 5px;
  left: 50%;
  transform: translateX(-50%);
  width: 4px;
  height: 10px;
  background: #6366f1;
  border-radius: 2px;
  animation: scrollAnim 1.5s infinite;
}

@keyframes scrollAnim {
  0% {
    opacity: 1;
    top: 5px;
  }
  100% {
    opacity: 0;
    top: 20px;
  }
}

.scroll-hint span {
  font-size: 0.7rem;
  color: #94a3b8;
}

/* Features Section */
.features-section {
  padding: 80px 0;
  position: relative;
  z-index: 2;
}

.section-label {
  text-align: center;
  margin-bottom: 1rem;
}

.section-label.center {
  text-align: center;
}

.label-badge {
  display: inline-block;
  background: rgba(99, 102, 241, 0.1);
  padding: 4px 12px;
  border-radius: 50px;
  font-size: 0.7rem;
  color: #6366f1;
}

.section-title {
  text-align: center;
  font-size: 2.2rem;
  font-weight: 700;
  margin-bottom: 3rem;
  color: #1e1b4b;
}

.section-title.center {
  text-align: center;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
  gap: 1.5rem;
}

.feature-card {
  background: rgba(255, 255, 255, 0.6);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.8);
  border-radius: 20px;
  padding: 1.5rem;
  transition: all 0.3s ease;
  opacity: 0;
  animation: fadeInUp 0.6s ease forwards;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.feature-card:hover {
  transform: translateY(-5px);
  border-color: rgba(99, 102, 241, 0.3);
  box-shadow: 0 20px 35px -10px rgba(99, 102, 241, 0.1);
}

.feature-icon-wrap {
  width: 50px;
  height: 50px;
  background: linear-gradient(
    135deg,
    rgba(99, 102, 241, 0.1),
    rgba(139, 92, 246, 0.1)
  );
  border-radius: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
}

.feature-icon-wrap i {
  font-size: 1.5rem;
  color: #6366f1;
}

.feature-card h3 {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #1e1b4b;
}

.feature-card p {
  font-size: 0.85rem;
  color: #6b7280;
  line-height: 1.5;
  margin-bottom: 1rem;
}

.feature-more {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.8rem;
  color: #6366f1;
  cursor: pointer;
}

.feature-more i {
  transition: transform 0.3s ease;
}

.feature-more:hover i {
  transform: translateX(5px);
}

/* Stats Dashboard */
.stats-dashboard {
  padding: 60px 0;
  position: relative;
  z-index: 2;
}

.stats-wrapper {
  max-width: 900px;
  margin: 0 auto;
}

.stats-card-large {
  background: rgba(255, 255, 255, 0.6);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.8);
  border-radius: 30px;
  padding: 2rem;
}

.stats-header {
  margin-bottom: 2rem;
}

.live-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(16, 185, 129, 0.1);
  padding: 4px 12px;
  border-radius: 50px;
  font-size: 0.7rem;
  color: #10b981;
}

.live-dot {
  width: 8px;
  height: 8px;
  background: #10b981;
  border-radius: 50%;
  animation: livePulse 1s infinite;
}

@keyframes livePulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 1.5rem;
}

.stat-block {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: rgba(255, 255, 255, 0.4);
  border-radius: 16px;
}

.stat-icon {
  width: 45px;
  height: 45px;
  background: rgba(99, 102, 241, 0.1);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-icon i {
  font-size: 1.3rem;
  color: #6366f1;
}

.stat-number {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e1b4b;
}

.stat-name {
  font-size: 0.75rem;
  color: #6b7280;
}

/* Timeline Section */
.timeline-section {
  padding: 80px 0;
  position: relative;
  z-index: 2;
}

.timeline {
  max-width: 800px;
  margin: 0 auto;
  position: relative;
}

.timeline-item {
  display: flex;
  gap: 2rem;
  margin-bottom: 2rem;
  position: relative;
}

.timeline-marker {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.marker-number {
  width: 45px;
  height: 45px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  z-index: 2;
  position: relative;
}

.marker-line {
  position: absolute;
  top: 45px;
  left: 50%;
  transform: translateX(-50%);
  width: 2px;
  height: calc(100% + 2rem);
  background: linear-gradient(180deg, #6366f1, transparent);
}

.timeline-item:last-child .marker-line {
  display: none;
}

.timeline-content {
  flex: 1;
  background: rgba(255, 255, 255, 0.6);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.8);
  border-radius: 20px;
  padding: 1.5rem;
  transition: all 0.3s ease;
  opacity: 0;
  animation: slideInRight 0.6s ease forwards;
}

@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(-30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.timeline-content:hover {
  transform: translateX(5px);
  border-color: rgba(99, 102, 241, 0.3);
}

.timeline-icon {
  width: 40px;
  height: 40px;
  background: rgba(99, 102, 241, 0.1);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
}

.timeline-icon i {
  font-size: 1.2rem;
  color: #6366f1;
}

.timeline-content h3 {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #1e1b4b;
}

.timeline-content p {
  font-size: 0.85rem;
  color: #6b7280;
  line-height: 1.5;
  margin: 0;
}

/* Pricing Modern */
.pricing-modern {
  padding: 80px 0;
  position: relative;
  z-index: 2;
}

.pricing-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.pricing-card-modern {
  background: rgba(255, 255, 255, 0.6);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.8);
  border-radius: 24px;
  padding: 2rem;
  transition: all 0.3s ease;
  position: relative;
}

.pricing-card-modern.popular {
  border: 2px solid #6366f1;
  transform: scale(1.02);
}

.pricing-card-modern:hover {
  transform: translateY(-5px);
}

.popular-tag {
  position: absolute;
  top: -12px;
  left: 50%;
  transform: translateX(-50%);
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: white;
  padding: 4px 16px;
  border-radius: 20px;
  font-size: 0.7rem;
  font-weight: 600;
  white-space: nowrap;
}

.pricing-card-modern h3 {
  font-size: 1.3rem;
  font-weight: 700;
  text-align: center;
  margin-bottom: 1rem;
  color: #1e1b4b;
}

.price-display {
  text-align: center;
  margin-bottom: 0.5rem;
}

.price-display .currency {
  font-size: 1rem;
  vertical-align: top;
  color: #6366f1;
}

.price-display .amount {
  font-size: 3rem;
  font-weight: 800;
  color: #6366f1;
}

.price-display .period {
  font-size: 0.8rem;
  color: #94a3b8;
}

.plan-desc {
  text-align: center;
  font-size: 0.8rem;
  color: #6b7280;
  margin-bottom: 1.5rem;
}

.feature-list-modern {
  list-style: none;
  padding: 0;
  margin: 1.5rem 0;
}

.feature-list-modern li {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0;
  font-size: 0.85rem;
  color: #4b5563;
}

.feature-list-modern li i {
  color: #10b981;
  font-size: 0.8rem;
}

.plan-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 12px;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
  width: 100%;
}

.plan-btn.btn-primary {
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: white;
}

.plan-btn.btn-outline {
  background: transparent;
  border: 1px solid rgba(99, 102, 241, 0.3);
  color: #6366f1;
}

.plan-btn:hover {
  transform: translateY(-2px);
}

/* Testimonials Modern */
.testimonials-modern {
  padding: 80px 0;
  position: relative;
  z-index: 2;
}

.testimonial-carousel {
  overflow: hidden;
  padding: 1rem;
}

.testimonial-track {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
  gap: 2rem;
}

.testimonial-slide {
  background: rgba(255, 255, 255, 0.6);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.8);
  border-radius: 24px;
  padding: 2rem;
  position: relative;
  transition: all 0.3s ease;
}

.testimonial-slide:hover {
  transform: translateY(-5px);
}

.quote-mark {
  font-size: 4rem;
  color: rgba(99, 102, 241, 0.2);
  position: absolute;
  top: 1rem;
  right: 1.5rem;
  font-family: serif;
}

.testimonial-quote {
  font-size: 0.9rem;
  color: #4b5563;
  line-height: 1.6;
  margin-bottom: 1.5rem;
  font-style: italic;
}

.testimonial-author-modern {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.author-avatar-modern img {
  width: 50px;
  height: 50px;
  border-radius: 50%;
}

.author-details h4 {
  font-size: 1rem;
  font-weight: 600;
  margin: 0 0 2px;
  color: #1e1b4b;
}

.author-details span {
  font-size: 0.7rem;
  color: #6b7280;
}

.stars {
  margin-top: 5px;
}

.stars i {
  font-size: 0.7rem;
  color: #cbd5e1;
}

.stars i.filled {
  color: #fbbf24;
}

/* CTA Banner */
.cta-banner {
  padding: 80px 0;
  position: relative;
  z-index: 2;
}

.cta-wrapper {
  background: linear-gradient(
    135deg,
    rgba(99, 102, 241, 0.1),
    rgba(139, 92, 246, 0.1)
  );
  border-radius: 30px;
  padding: 4rem 2rem;
  text-align: center;
  backdrop-filter: blur(10px);
}

.cta-content h2 {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 1rem;
  color: #1e1b4b;
}

.cta-content p {
  font-size: 1rem;
  color: #4b5563;
  margin-bottom: 2rem;
}

.cta-btn {
  display: inline-flex;
  align-items: center;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  padding: 14px 32px;
  border-radius: 12px;
  text-decoration: none;
  color: white;
  font-weight: 600;
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
}

.cta-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
}

.cta-note {
  font-size: 0.7rem;
  color: #94a3b8;
  margin-top: 1rem;
  margin-bottom: 0;
}

/* Footer Modern */
.footer-modern {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  border-top: 1px solid rgba(0, 0, 0, 0.05);
  padding: 60px 0 30px;
  position: relative;
  z-index: 2;
}

.footer-grid-modern {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
}

.footer-brand-modern h3 {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.footer-brand-modern p {
  font-size: 0.8rem;
  color: #6b7280;
  margin-bottom: 1rem;
}

.social-links {
  display: flex;
  gap: 0.8rem;
}

.social-links a {
  width: 35px;
  height: 35px;
  background: rgba(99, 102, 241, 0.1);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6366f1;
  transition: all 0.3s ease;
}

.social-links a:hover {
  background: #6366f1;
  color: white;
  transform: translateY(-3px);
}

.footer-links-modern h4 {
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 1rem;
  color: #1e1b4b;
}

.footer-links-modern ul {
  list-style: none;
  padding: 0;
}

.footer-links-modern li {
  margin-bottom: 0.5rem;
}

.footer-links-modern a {
  color: #6b7280;
  text-decoration: none;
  font-size: 0.8rem;
  transition: color 0.3s ease;
}

.footer-links-modern a:hover {
  color: #6366f1;
}

.footer-bottom-modern {
  text-align: center;
  padding-top: 2rem;
  border-top: 1px solid rgba(0, 0, 0, 0.05);
}

.footer-bottom-modern p {
  font-size: 0.7rem;
  color: #94a3b8;
  margin: 0;
}

/* Responsive */
@media (max-width: 992px) {
  .hero-wrapper {
    grid-template-columns: 1fr;
    gap: 2rem;
    text-align: center;
  }

  .hero-stats {
    justify-content: center;
  }

  .hero-actions {
    justify-content: center;
  }

  .hero-title {
    font-size: 2.5rem;
  }

  .section-title {
    font-size: 1.8rem;
  }

  .pricing-card-modern.popular {
    transform: scale(1);
  }

  .timeline-item {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .marker-line {
    display: none;
  }

  .timeline-content {
    text-align: center;
  }

  .timeline-icon {
    margin: 0 auto 1rem;
  }
}

@media (max-width: 768px) {
  .features-grid,
  .pricing-cards,
  .testimonial-track {
    grid-template-columns: 1fr;
  }

  .footer-grid-modern {
    grid-template-columns: 1fr;
    text-align: center;
  }

  .social-links {
    justify-content: center;
  }

  .hero-stats {
    flex-wrap: wrap;
    gap: 1rem;
    justify-content: center;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }

  .cta-content h2 {
    font-size: 1.5rem;
  }
}
</style>
