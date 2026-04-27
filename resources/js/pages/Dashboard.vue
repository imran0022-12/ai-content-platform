<template>
  <div class="dashboard-container" ref="dashboardContainer">
    <!-- Animated Background - Matching Landing Page -->
    <div class="animated-bg">
      <div class="gradient-sphere sphere-1"></div>
      <div class="gradient-sphere sphere-2"></div>
      <div class="gradient-sphere sphere-3"></div>
      <div class="grid-overlay"></div>
    </div>

    <div class="container-fluid px-4">
      <!-- Welcome Section - Matching Landing Page -->
      <div class="welcome-section mb-4 animate-slide-down">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="floating-badge animate-pulse">
              <span class="pulse-dot"></span>
              <i class="fas fa-crown me-2"></i>
              {{ getTimeGreeting() }}, {{ userName }}!
            </div>
            <h1 class="hero-title">
              Ready to Create <span class="gradient-text">Magic</span>?
            </h1>
            <p class="hero-description">
              Your AI-powered content studio is waiting for you
            </p>
          </div>
          <div class="col-md-4 text-md-end">
            <div class="date-badge animate-float">
              <i class="fas fa-calendar-alt me-2"></i>
              {{ currentDate }}
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Cards - Matching Landing Page -->
      <div class="row mb-4 g-4">
        <div
          v-for="(stat, index) in statsData"
          :key="index"
          class="col-xl-3 col-md-6"
        >
          <div
            class="stat-block-dashboard animate-fade-up"
            :style="{ animationDelay: index * 0.1 + 's' }"
          >
            <div class="stat-icon-dashboard">
              <i :class="stat.icon"></i>
            </div>
            <div class="stat-info-dashboard">
              <div class="stat-value-dashboard">
                {{ stat.displayValue }}
                <span v-if="stat.unit" class="stat-unit-dashboard">{{ stat.unit }}</span>
              </div>
              <div class="stat-label-dashboard">{{ stat.title }}</div>
              <div v-if="stat.progress" class="progress-bar-container">
                <div class="progress-fill" :style="{ width: stat.progress + '%' }"></div>
              </div>
              <small class="stat-hint">
                <i :class="stat.labelIcon"></i> {{ stat.label }}
              </small>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="quick-actions-wrapper animate-fade-up" style="animation-delay: 0.2s">
        <div class="quick-actions">
          <div class="action-chip" @click="scrollToGenerator">
            <i class="fas fa-magic"></i>
            <span>New Generation</span>
          </div>
          <div class="action-chip" @click="$router.push('/history')">
            <i class="fas fa-history"></i>
            <span>View History</span>
          </div>
          <div class="action-chip" @click="exportAllData">
            <i class="fas fa-download"></i>
            <span>Export Data</span>
          </div>
          <div class="action-chip" @click="refreshStats">
            <i class="fas fa-sync-alt"></i>
            <span>Refresh</span>
          </div>
        </div>
      </div>

      <!-- Main Generator - Matching Landing Page Card Style -->
      <div class="row">
        <div class="col-lg-7">
          <div class="feature-card-dashboard animate-slide-right" style="animation-delay: 0.3s">
            <div class="feature-card-header">
              <div class="feature-icon-wrap">
                <i class="fas fa-magic"></i>
              </div>
              <h3>AI Content Generator</h3>
              <span class="label-badge">Powered by AI</span>
            </div>

            <div class="feature-card-body">
              <form @submit.prevent="generateContent">
                <!-- Content Type Selector -->
                <div class="form-group mb-4">
                  <label class="form-label-dashboard">
                    <i class="fas fa-tag me-2"></i>Select Content Type
                  </label>
                  <div class="content-type-grid">
                    <div
                      v-for="type in contentTypes"
                      :key="type.value"
                      @click="selectType(type.value)"
                      class="type-option-dashboard"
                      :class="{ active: form.type === type.value }"
                    >
                      <i :class="type.icon"></i>
                      <span>{{ type.label }}</span>
                    </div>
                  </div>
                </div>

                <!-- Prompt Input -->
                <div class="form-group mb-4">
                  <label class="form-label-dashboard">
                    <i class="fas fa-pen-alt me-2"></i>What do you want to create?
                  </label>
                  <textarea
                    v-model="form.prompt"
                    class="form-control-dashboard"
                    rows="5"
                    :class="{ 'is-invalid': error }"
                    placeholder="Example: Write a blog post about the benefits of meditation for busy professionals..."
                  ></textarea>
                  <div class="input-hint">
                    <i class="fas fa-lightbulb me-1"></i>
                    Be specific for 10x better results!
                  </div>
                  <div v-if="error" class="invalid-feedback d-block">
                    <i class="fas fa-exclamation-circle me-1"></i>{{ error }}
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                  <button type="submit" class="btn-primary-glow w-100" :disabled="loading">
                    <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                    <i v-else class="fas fa-play me-2"></i>
                    {{ loading ? "Generating..." : "Generate Content" }}
                    <span class="btn-glow"></span>
                  </button>
                  <button type="button" class="btn-outline-modern" @click="clearForm">
                    <i class="fas fa-eraser me-2"></i>Clear
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="col-lg-5">
          <!-- Tips Card - Matching Landing Page -->
          <div class="feature-card-dashboard animate-slide-left" style="animation-delay: 0.4s">
            <div class="feature-card-header">
              <div class="feature-icon-wrap">
                <i class="fas fa-lightbulb"></i>
              </div>
              <h3>Pro Tips</h3>
            </div>
            <div class="feature-card-body">
              <div class="tip-carousel">
                <div class="tip-item-dashboard" :key="currentTip">
                  <div class="tip-icon-dashboard">
                    <i class="fas fa-check-circle"></i>
                  </div>
                  <div class="tip-content-dashboard">
                    <strong>{{ tips[currentTip].title }}</strong>
                    <p>{{ tips[currentTip].description }}</p>
                  </div>
                </div>
                <div class="tip-navigation">
                  <button @click="prevTip" class="tip-nav-btn">
                    <i class="fas fa-chevron-left"></i>
                  </button>
                  <div class="tip-dots">
                    <span v-for="(tip, idx) in tips" :key="idx"
                          class="tip-dot"
                          :class="{ active: idx === currentTip }"
                          @click="currentTip = idx"></span>
                  </div>
                  <button @click="nextTip" class="tip-nav-btn">
                    <i class="fas fa-chevron-right"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Activity -->
          <div class="feature-card-dashboard mt-4 animate-slide-left" style="animation-delay: 0.5s">
            <div class="feature-card-header">
              <div class="feature-icon-wrap">
                <i class="fas fa-clock"></i>
              </div>
              <h3>Recent Activity</h3>
              <router-link to="/history" class="feature-more">View All →</router-link>
            </div>
            <div class="feature-card-body">
              <div v-if="recentGenerations.length === 0" class="empty-activity">
                <i class="fas fa-inbox"></i>
                <p>No recent activity. Start creating!</p>
              </div>
              <div v-else class="activity-list">
                <div
                  v-for="item in recentGenerations"
                  :key="item.id"
                  class="activity-item-dashboard"
                >
                  <div class="activity-icon-dashboard" :class="getTypeClass(item.type)">
                    <i :class="getTypeIcon(item.type)"></i>
                  </div>
                  <div class="activity-details-dashboard">
                    <p class="activity-prompt">{{ truncate(item.prompt, 50) }}</p>
                    <span class="activity-time">{{ timeAgo(item.created_at) }}</span>
                  </div>
                  <button @click="viewRecentItem(item)" class="activity-action-dashboard">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Generated Result - Matching Landing Page -->
      <transition name="result-slide">
        <div v-if="result" class="row mt-4">
          <div class="col-12">
            <div class="result-card-dashboard animate-zoom">
              <div class="result-header-dashboard">
                <div class="result-header-left">
                  <i class="fas fa-check-circle success-icon"></i>
                  <h4 class="mb-0">Content Generated Successfully!</h4>
                </div>
                <div class="result-actions">
                  <button @click="copyToClipboard" class="btn-icon" title="Copy">
                    <i class="fas fa-copy"></i>
                  </button>
                  <button @click="downloadResult" class="btn-icon" title="Download">
                    <i class="fas fa-download"></i>
                  </button>
                  <button @click="shareResult" class="btn-icon" title="Share">
                    <i class="fas fa-share-alt"></i>
                  </button>
                  <button @click="regenerateContent" class="btn-icon" title="Regenerate">
                    <i class="fas fa-sync-alt"></i>
                  </button>
                </div>
              </div>
              <div class="result-stats-dashboard" v-if="stats">
                <span class="stat-badge-dashboard">
                  <i class="fas fa-microchip"></i> Tokens: {{ stats.tokens }}
                </span>
                <span class="stat-badge-dashboard">
                  <i class="fas fa-dollar-sign"></i> Cost: ${{ stats.cost }}
                </span>
              </div>
              <div class="result-content-dashboard">
                <div class="content-preview">{{ result }}</div>
              </div>
              <div class="result-footer-dashboard">
                <button class="btn-outline-modern" @click="copyToClipboard">
                  <i class="fas fa-copy me-2"></i>Copy to Clipboard
                </button>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import anime from "animejs";

const router = useRouter();

// State
const form = ref({ prompt: "", type: "blog" });
const loading = ref(false);
const result = ref("");
const error = ref("");
const stats = ref(null);
const todayCount = ref(0);
const totalCount = ref(0);
const totalTokens = ref(0);
const totalCost = ref(0);
const userName = ref("User");
const recentGenerations = ref([]);
const currentTip = ref(0);
let tipInterval = null;

// Stats Data
const statsData = computed(() => [
  {
    title: "Today's Usage",
    displayValue: `${todayCount.value}/10`,
    icon: "fas fa-chart-line",
    progress: (todayCount.value / 10) * 100,
    label: `${10 - todayCount.value} generations remaining`,
    labelIcon: "fas fa-clock",
  },
  {
    title: "Total Generated",
    displayValue: formatNumber(totalCount.value),
    icon: "fas fa-file-alt",
    label: "All time content",
    labelIcon: "fas fa-trophy",
  },
  {
    title: "Tokens Used",
    displayValue: formatNumber(totalTokens.value),
    icon: "fas fa-microchip",
    label: "Total AI tokens",
    labelIcon: "fas fa-chart-simple",
  },
  {
    title: "Est. Cost",
    displayValue: `$${totalCost.value}`,
    icon: "fas fa-dollar-sign",
    label: "Total spent",
    labelIcon: "fas fa-charging-station",
  },
]);

const contentTypes = [
  { value: "blog", label: "Blog Post", icon: "fas fa-blog" },
  { value: "social", label: "Social Media", icon: "fab fa-twitter" },
  { value: "email", label: "Email", icon: "fas fa-envelope" },
  { value: "tagline", label: "Tagline", icon: "fas fa-tag" },
  { value: "product", label: "Product", icon: "fas fa-box" },
  { value: "seo", label: "SEO Meta", icon: "fas fa-chart-line" },
];

const tips = ref([
  { title: "Be Specific", description: "Detailed prompts produce 5x better results." },
  { title: "Use Keywords", description: "Include relevant keywords for SEO optimization." },
  { title: "Iterate & Improve", description: "Try different phrasings for perfect output." },
  { title: "Save Your Best", description: "All generations are saved in history." },
]);

const currentDate = computed(() => {
  return new Date().toLocaleDateString("en-US", {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  });
});

const getTimeGreeting = () => {
  const hour = new Date().getHours();
  if (hour < 12) return "Good Morning";
  if (hour < 18) return "Good Afternoon";
  return "Good Evening";
};

const formatNumber = (num) => {
  if (num >= 1000000) return (num / 1000000).toFixed(1) + "M";
  if (num >= 1000) return (num / 1000).toFixed(1) + "K";
  return num.toString();
};

const timeAgo = (date) => {
  const seconds = Math.floor((new Date() - new Date(date)) / 1000);
  if (seconds < 60) return "just now";
  const minutes = Math.floor(seconds / 60);
  if (minutes < 60) return `${minutes} min ago`;
  const hours = Math.floor(minutes / 60);
  if (hours < 24) return `${hours} hour ago`;
  return `${Math.floor(hours / 24)} days ago`;
};

const truncate = (text, length) => {
  if (!text) return "";
  return text.length > length ? text.substring(0, length) + "..." : text;
};

const getTypeIcon = (type) => {
  const icons = {
    blog: "fas fa-blog",
    social: "fab fa-twitter",
    email: "fas fa-envelope",
    tagline: "fas fa-tag",
    product: "fas fa-box",
    seo: "fas fa-chart-line",
  };
  return icons[type] || "fas fa-file";
};

const getTypeClass = (type) => {
  const classes = { blog: "blog", social: "social", email: "email", tagline: "tagline", product: "product", seo: "seo" };
  return classes[type] || "default";
};

const selectType = (type) => { form.value.type = type; };
const scrollToGenerator = () => { document.querySelector(".feature-card-dashboard")?.scrollIntoView({ behavior: "smooth" }); };
const prevTip = () => { currentTip.value = (currentTip.value - 1 + tips.value.length) % tips.value.length; };
const nextTip = () => { currentTip.value = (currentTip.value + 1) % tips.value.length; };

const generateContent = async () => {
  if (!form.value.prompt.trim()) {
    error.value = "Please enter a prompt";
    setTimeout(() => (error.value = ""), 3000);
    return;
  }

  loading.value = true;
  error.value = "";
  result.value = "";

  try {
    const response = await axios.post("/api/generate", form.value);
    if (response.data.success) {
      result.value = response.data.data.result;
      stats.value = { tokens: response.data.data.tokens_used, cost: response.data.data.cost_usd };
      fetchStats();
      fetchRecentActivity();
    }
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to generate content";
  } finally {
    loading.value = false;
  }
};

const regenerateContent = () => { generateContent(); };
const copyToClipboard = async () => {
  try {
    await navigator.clipboard.writeText(result.value);
    showToast("Content copied to clipboard!", "success");
  } catch (err) { showToast("Failed to copy", "error"); }
};
const downloadResult = () => {
  const blob = new Blob([result.value], { type: "text/plain" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = `${form.value.type}_${Date.now()}.txt`;
  a.click();
  URL.revokeObjectURL(url);
  showToast("File downloaded!", "success");
};
const shareResult = async () => {
  if (navigator.share) {
    try {
      await navigator.share({ title: "AI Generated Content", text: result.value.substring(0, 500) });
    } catch (err) { copyToClipboard(); }
  } else { copyToClipboard(); }
};
const exportAllData = async () => {
  try {
    const response = await axios.get("/api/history");
    const data = response.data.data;
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: "application/json" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `ai_content_export_${Date.now()}.json`;
    a.click();
    URL.revokeObjectURL(url);
    showToast("Data exported successfully!", "success");
  } catch (err) { showToast("Failed to export data", "error"); }
};
const viewRecentItem = (item) => {
  result.value = item.result;
  stats.value = { tokens: item.tokens_used, cost: item.cost_usd };
  scrollToGenerator();
  showToast("Loaded from history!", "success");
};
const clearForm = () => {
  form.value.prompt = "";
  result.value = "";
  stats.value = null;
  error.value = "";
};
const refreshStats = () => {
  fetchStats();
  fetchRecentActivity();
  showToast("Stats refreshed!", "success");
};
const showToast = (message, type = "info") => {
  const toast = document.createElement("div");
  toast.className = `toast-notification ${type}`;
  toast.innerHTML = `<i class="fas ${type === "success" ? "fa-check-circle" : "fa-info-circle"} me-2"></i>${message}`;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 3000);
};
const fetchStats = async () => {
  try {
    const response = await axios.get("/api/stats");
    if (response.data.success) {
      todayCount.value = response.data.data.today_count;
      totalCount.value = response.data.data.total_count;
      totalTokens.value = response.data.data.total_tokens;
      totalCost.value = response.data.data.total_cost;
    }
  } catch (err) { console.error("Failed to fetch stats"); }
};
const fetchRecentActivity = async () => {
  try {
    const response = await axios.get("/api/history");
    recentGenerations.value = response.data.data.data?.slice(0, 3) || response.data.data?.slice(0, 3) || [];
  } catch (err) { console.error("Failed to fetch recent activity"); }
};
const fetchUser = async () => {
  try {
    const response = await axios.get("/api/user");
    if (response.data.name) userName.value = response.data.name.split(" ")[0];
  } catch (err) { console.error("Failed to fetch user"); }
};

// Page refresh animation
const pageRefreshAnimation = () => {
  anime({
    targets: '.dashboard-container',
    opacity: [0, 1],
    translateY: [20, 0],
    duration: 600,
    easing: 'easeOutCubic'
  });
};

// Keyboard shortcut
const handleKeyPress = (e) => {
  if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
    e.preventDefault();
    generateContent();
  }
};

onMounted(() => {
  fetchStats();
  fetchRecentActivity();
  fetchUser();

  // Add event listeners
  document.addEventListener('keydown', handleKeyPress);

  // Auto-rotate tips
  tipInterval = setInterval(() => { nextTip(); }, 8000);

  // Page refresh animation
  pageRefreshAnimation();

  // Animate stats cards
  anime({
    targets: '.stat-block-dashboard',
    translateY: [30, 0],
    opacity: [0, 1],
    delay: anime.stagger(100),
    duration: 600,
    easing: 'easeOutCubic'
  });
});

onUnmounted(() => {
  if (tipInterval) clearInterval(tipInterval);
  document.removeEventListener('keydown', handleKeyPress);
});
</script>

<style scoped>
/* ========== DASHBOARD STYLES - MATCHING LANDING PAGE ========== */
.dashboard-container {
  padding: 1rem 0;
  min-height: 100vh;
}

/* Animated Background - Matching Landing Page */
.animated-bg {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: -1;
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

/* Welcome Section - Matching Landing Page */
.welcome-section {
  background: rgba(255, 255, 255, 0.6);
  backdrop-filter: blur(10px);
  border-radius: 24px;
  padding: 1.5rem 2rem;
  border: 1px solid rgba(255, 255, 255, 0.8);
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
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
  margin-bottom: 1rem;
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
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.5; transform: scale(1.2); }
}

.hero-title {
  font-size: 2.5rem;
  font-weight: 800;
  margin-bottom: 0.5rem;
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
  font-size: 1rem;
  color: #6b7280;
}

.date-badge {
  background: rgba(99, 102, 241, 0.1);
  padding: 0.5rem 1rem;
  border-radius: 12px;
  display: inline-block;
  color: #6366f1;
}

/* Stats Cards - Matching Landing Page */
.stat-block-dashboard {
  background: rgba(255, 255, 255, 0.6);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.8);
  border-radius: 20px;
  padding: 1.2rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: all 0.3s ease;
}

.stat-block-dashboard:hover {
  transform: translateY(-5px);
  border-color: rgba(99, 102, 241, 0.3);
  box-shadow: 0 20px 35px -10px rgba(99, 102, 241, 0.1);
}

.stat-icon-dashboard {
  width: 50px;
  height: 50px;
  background: rgba(99, 102, 241, 0.1);
  border-radius: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-icon-dashboard i {
  font-size: 1.5rem;
  color: #6366f1;
}

.stat-info-dashboard {
  flex: 1;
}

.stat-value-dashboard {
  font-size: 1.8rem;
  font-weight: 700;
  color: #1e1b4b;
}

.stat-unit-dashboard {
  font-size: 0.8rem;
  color: #6b7280;
}

.stat-label-dashboard {
  font-size: 0.75rem;
  color: #6b7280;
  margin-bottom: 0.3rem;
}

.progress-bar-container {
  height: 4px;
  background: #e5e7eb;
  border-radius: 2px;
  overflow: hidden;
  margin: 0.3rem 0;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #6366f1, #d946ef);
  border-radius: 2px;
  transition: width 0.6s ease;
}

.stat-hint {
  font-size: 0.7rem;
  color: #94a3b8;
}

/* Quick Actions */
.quick-actions-wrapper {
  background: rgba(255, 255, 255, 0.4);
  backdrop-filter: blur(10px);
  border-radius: 60px;
  padding: 0.5rem;
}

.quick-actions {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  justify-content: center;
}

.action-chip {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1.2rem;
  background: rgba(255, 255, 255, 0.8);
  border-radius: 40px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.85rem;
  color: #4b5563;
}

.action-chip:hover {
  background: white;
  transform: translateY(-2px);
  color: #6366f1;
  box-shadow: 0 5px 15px rgba(99, 102, 241, 0.15);
}

/* Feature Cards - Matching Landing Page */
.feature-card-dashboard {
  background: rgba(255, 255, 255, 0.6);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.8);
  border-radius: 20px;
  overflow: hidden;
  transition: all 0.3s ease;
}

.feature-card-dashboard:hover {
  transform: translateY(-5px);
  border-color: rgba(99, 102, 241, 0.3);
  box-shadow: 0 20px 35px -10px rgba(99, 102, 241, 0.1);
}

.feature-card-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 1.2rem;
  background: rgba(255, 255, 255, 0.4);
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.feature-icon-wrap {
  width: 40px;
  height: 40px;
  background: rgba(99, 102, 241, 0.1);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.feature-icon-wrap i {
  font-size: 1.2rem;
  color: #6366f1;
}

.feature-card-header h3 {
  font-size: 1.1rem;
  font-weight: 600;
  margin: 0;
  color: #1e1b4b;
}

.label-badge {
  margin-left: auto;
  background: rgba(99, 102, 241, 0.1);
  padding: 0.2rem 0.7rem;
  border-radius: 20px;
  font-size: 0.65rem;
  color: #6366f1;
}

.feature-more {
  margin-left: auto;
  font-size: 0.7rem;
  color: #6366f1;
  text-decoration: none;
  transition: all 0.3s ease;
}

.feature-more:hover {
  transform: translateX(3px);
}

.feature-card-body {
  padding: 1.2rem;
}

/* Form Styles */
.form-label-dashboard {
  display: block;
  margin-bottom: 0.5rem;
  color: #374151;
  font-weight: 500;
  font-size: 0.85rem;
}

.form-control-dashboard {
  width: 100%;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 0.8rem;
  color: #1f2937;
  transition: all 0.3s ease;
}

.form-control-dashboard:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.content-type-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
  gap: 0.5rem;
}

.type-option-dashboard {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  padding: 0.6rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.type-option-dashboard i {
  font-size: 1.1rem;
  display: block;
  margin-bottom: 0.2rem;
  color: #6b7280;
}

.type-option-dashboard span {
  font-size: 0.7rem;
  color: #6b7280;
}

.type-option-dashboard:hover,
.type-option-dashboard.active {
  background: rgba(99, 102, 241, 0.05);
  border-color: #6366f1;
  transform: translateY(-2px);
}

.type-option-dashboard.active i,
.type-option-dashboard.active span {
  color: #6366f1;
}

.input-hint {
  margin-top: 0.3rem;
  font-size: 0.7rem;
  color: #94a3b8;
}

.action-buttons {
  display: flex;
  gap: 1rem;
}

.btn-primary-glow {
  position: relative;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  padding: 10px 20px;
  border-radius: 12px;
  border: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  color: white;
  font-weight: 600;
  transition: all 0.3s ease;
  cursor: pointer;
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

.btn-outline-modern {
  background: white;
  border: 1px solid #e5e7eb;
  padding: 10px 20px;
  border-radius: 12px;
  color: #4b5563;
  font-weight: 600;
  transition: all 0.3s ease;
  cursor: pointer;
}

.btn-outline-modern:hover {
  border-color: #6366f1;
  color: #6366f1;
  transform: translateY(-2px);
}

/* Tips */
.tip-carousel {
  min-height: 120px;
}

.tip-item-dashboard {
  display: flex;
  gap: 0.8rem;
  padding: 0.6rem;
  background: rgba(99, 102, 241, 0.05);
  border-radius: 12px;
  animation: fadeSlide 0.5s ease;
}

@keyframes fadeSlide {
  from { opacity: 0; transform: translateX(20px); }
  to { opacity: 1; transform: translateX(0); }
}

.tip-icon-dashboard {
  width: 35px;
  height: 35px;
  background: rgba(99, 102, 241, 0.1);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.tip-icon-dashboard i {
  color: #10b981;
  font-size: 1rem;
}

.tip-content-dashboard p {
  margin: 0;
  font-size: 0.75rem;
  color: #6b7280;
}

.tip-content-dashboard strong {
  font-size: 0.8rem;
  color: #1e1b4b;
}

.tip-navigation {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  margin-top: 0.8rem;
}

.tip-nav-btn {
  background: rgba(99, 102, 241, 0.1);
  border: none;
  width: 30px;
  height: 30px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  color: #6366f1;
}

.tip-nav-btn:hover {
  background: rgba(99, 102, 241, 0.2);
  transform: scale(1.05);
}

.tip-dots {
  display: flex;
  gap: 0.5rem;
}

.tip-dot {
  width: 8px;
  height: 8px;
  background: #cbd5e1;
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.3s ease;
}

.tip-dot.active {
  width: 24px;
  background: linear-gradient(135deg, #6366f1, #d946ef);
  border-radius: 4px;
}

/* Recent Activity */
.activity-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.activity-item-dashboard {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.5rem;
  background: rgba(0, 0, 0, 0.02);
  border-radius: 10px;
  transition: all 0.3s ease;
}

.activity-item-dashboard:hover {
  
  transform: translateX(3px);
}

.activity-icon-dashboard {
  width: 35px;
  height: 35px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.activity-icon-dashboard.blog { background: rgba(99, 102, 241, 0.1); }
.activity-icon-dashboard i { font-size: 0.9rem; color: #6366f1; }

.activity-details-dashboard { flex: 1; }
.activity-prompt { font-size: 0.75rem; margin: 0 0 0.2rem; color: #374151; }
.activity-time { font-size: 0.6rem; color: #94a3b8; }

.activity-action-dashboard {
  background: transparent;
  border: none;
  width: 28px;
  height: 28px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
  color: #6b7280;
}

.activity-action-dashboard:hover {
  background: rgba(99, 102, 241, 0.1);
  color: #6366f1;
}

.empty-activity {
  text-align: center;
  padding: 1.5rem;
  color: #94a3b8;
}

.empty-activity i {
  font-size: 2rem;
  margin-bottom: 0.5rem;
  display: block;
}

/* Result Card */
.result-card-dashboard {
  background: white;
  border-radius: 20px;
  border: 1px solid #e5e7eb;
  overflow: hidden;
  box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.1);
}

.result-header-dashboard {
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(139, 92, 246, 0.02));
  padding: 1rem 1.2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 0.5rem;
  border-bottom: 1px solid #f0f0f0;
}

.result-header-left {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.success-icon {
  font-size: 1.2rem;
  color: #10b981;
}

.result-actions {
  display: flex;
  gap: 0.5rem;
}

.btn-icon {
  background: rgba(0, 0, 0, 0.05);
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-icon:hover {
  background: #6366f1;
  color: white;
  transform: translateY(-2px);
}

.result-stats-dashboard {
  padding: 0.5rem 1.2rem;
  background: #f9fafb;
  display: flex;
  gap: 1rem;
  border-bottom: 1px solid #f0f0f0;
}

.stat-badge-dashboard {
  font-size: 0.7rem;
  color: #6366f1;
}

.result-content-dashboard {
  padding: 1.2rem;
  max-height: 400px;
  overflow-y: auto;
}

.content-preview {
  color: #374151;
  line-height: 1.6;
  font-size: 0.9rem;
  white-space: pre-wrap;
}

.result-footer-dashboard {
  padding: 0.8rem 1.2rem;
  border-top: 1px solid #f0f0f0;
  text-align: center;
}

/* Toast Notification */
.toast-notification {
  position: fixed;
  bottom: 30px;
  right: 30px;
  background: white;
  border-left: 3px solid #6366f1;
  padding: 10px 18px;
  border-radius: 10px;
  color: #374151;
  z-index: 10000;
  animation: toastSlide 0.3s ease;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

@keyframes toastSlide {
  from { transform: translateX(100%); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

/* Animations */
.animate-slide-down {
  animation: slideDown 0.5s ease forwards;
}
@keyframes slideDown {
  from { opacity: 0; transform: translateY(-30px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-up {
  animation: fadeUp 0.5s ease forwards;
  opacity: 0;
}
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-slide-right {
  animation: slideRight 0.5s ease forwards;
  opacity: 0;
}
@keyframes slideRight {
  from { opacity: 0; transform: translateX(-30px); }
  to { opacity: 1; transform: translateX(0); }
}

.animate-slide-left {
  animation: slideLeft 0.5s ease forwards;
  opacity: 0;
}
@keyframes slideLeft {
  from { opacity: 0; transform: translateX(30px); }
  to { opacity: 1; transform: translateX(0); }
}

.animate-zoom {
  animation: zoomIn 0.4s ease forwards;
}
@keyframes zoomIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}

.animate-float {
  animation: floatAnim 3s ease-in-out infinite;
}
@keyframes floatAnim {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-3px); }
}

.animate-pulse {
  animation: pulseAnim 2s ease-in-out infinite;
}
@keyframes pulseAnim {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}

.result-slide-enter-active,
.result-slide-leave-active {
  transition: all 0.4s ease;
}
.result-slide-enter-from {
  opacity: 0;
  transform: translateY(30px);
}
.result-slide-leave-to {
  opacity: 0;
  transform: translateY(-30px);
}

/* Responsive */
@media (max-width: 768px) {
  .stat-value-dashboard {
    font-size: 1.3rem;
  }
  .content-type-grid {
    grid-template-columns: repeat(3, 1fr);
  }
  .action-buttons {
    flex-direction: column;
  }
  .result-header-dashboard {
    flex-direction: column;
    text-align: center;
  }
  .welcome-section {
    text-align: center;
  }
  .date-badge {
    margin-top: 0.5rem;
    display: inline-block;
  }
}
</style>
