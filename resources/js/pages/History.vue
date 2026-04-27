<template>
    <div class="history-container">
        <div class="container-fluid px-4">
            <!-- Header Section -->
            <div class="history-header mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="header-badge">
                            <i class="fas fa-database me-2"></i>
                            Content Library
                        </div>
                        <h1 class="display-4 fw-semibold mb-2">
                            <i class="fas fa-history me-3 text-primary"></i>Generation History
                        </h1>
                        <p class="text-muted">View and manage all your AI-generated content</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="header-stats">
                            <div class="stat-chip">
                                <i class="fas fa-file-alt me-1"></i>
                                Total: <strong>{{ totalItems }}</strong>
                            </div>
                            <router-link to="/dashboard" class="btn-gradient">
                                <i class="fas fa-plus me-2"></i>New Generation
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class="filter-bar mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input
                                type="text"
                                v-model="searchQuery"
                                class="form-control"
                                placeholder="Search by prompt or content..."
                                @input="filterGenerations"
                            >
                            <div v-if="searchQuery" class="search-clear" @click="searchQuery = ''">
                                <i class="fas fa-times-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select v-model="filterType" class="form-select" @change="filterGenerations">
                            <option value="">All Types</option>
                            <option value="blog">📝 Blog Post</option>
                            <option value="social">🐦 Social Media</option>
                            <option value="email">✉️ Email</option>
                            <option value="tagline">💡 Tagline</option>
                            <option value="product">🛍️ Product</option>
                            <option value="seo">🔍 SEO Meta</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select v-model="sortBy" class="form-select" @change="filterGenerations">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="most_tokens">Most Tokens</option>
                            <option value="least_tokens">Least Tokens</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button @click="exportData" class="btn-outline w-100">
                            <i class="fas fa-download me-2"></i>Export
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="stats-overview mb-4">
                <div class="row g-3">
                    <div v-for="(stat, index) in statsCards" :key="index" class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon" :class="stat.iconClass">
                                <i :class="stat.icon"></i>
                            </div>
                            <div class="stat-info">
                                <h3 class="stat-number">{{ stat.value }}</h3>
                                <p class="stat-label">{{ stat.label }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="loading-state">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
                <p class="mt-3">Loading your history...</p>
            </div>

            <!-- Empty State -->
            <div v-else-if="filteredGenerations.length === 0" class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3>No generations yet</h3>
                <p>Start creating amazing content with AI</p>
                <router-link to="/dashboard" class="btn-gradient mt-3">
                    <i class="fas fa-magic me-2"></i>Create Your First Content
                </router-link>
            </div>

            <!-- History Cards Grid -->
            <div v-else class="history-grid">
                <div v-for="item in paginatedGenerations" :key="item.id" class="history-card">
                    <div class="card-header">
                        <div class="type-badge" :class="getTypeClass(item.type)">
                            <i :class="getTypeIcon(item.type)"></i>
                            {{ getTypeLabel(item.type) }}
                        </div>
                        <div class="card-actions">
                            <button @click="viewDetails(item)" class="icon-btn" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button @click="copyContent(item.result)" class="icon-btn" title="Copy">
                                <i class="fas fa-copy"></i>
                            </button>
                            <button @click="openDeleteModal(item.id)" class="icon-btn delete" title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="prompt">
                            <i class="fas fa-quote-left"></i>
                            <p>{{ truncate(item.prompt, 120) }}</p>
                        </div>
                        <div class="preview">
                            <div class="preview-content" v-html="highlightSearch(truncate(item.result, 200))"></div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="meta">
                            <span><i class="fas fa-microchip"></i> {{ item.tokens_used }} tokens</span>
                            <span><i class="fas fa-dollar-sign"></i> ${{ parseFloat(item.cost_usd).toFixed(4) }}</span>
                            <span><i class="fas fa-calendar-alt"></i> {{ formatDate(item.created_at) }}</span>
                        </div>
                        <button class="btn-use" @click="quickUse(item)">
                            <i class="fas fa-redo-alt"></i> Use Again
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="filteredGenerations.length > 0" class="pagination-section">
                <nav>
                    <ul class="pagination">
                        <li class="page-item" :class="{ disabled: currentPage === 1 }">
                            <button class="page-link" @click="currentPage--">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                        </li>
                        <li v-for="page in totalPages" :key="page" class="page-item" :class="{ active: currentPage === page }">
                            <button class="page-link" @click="currentPage = page">{{ page }}</button>
                        </li>
                        <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                            <button class="page-link" @click="currentPage++">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </li>
                    </ul>
                </nav>
                <div class="pagination-info">
                    Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to
                    {{ Math.min(currentPage * itemsPerPage, filteredGenerations.length) }} of
                    {{ filteredGenerations.length }} items
                </div>
            </div>
        </div>

        <!-- Details Modal -->
        <div class="modal fade" id="detailsModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-title">
                            <div class="title-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">Content Details</h5>
                                <small class="text-muted">Full content preview</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" v-if="selectedItem">
                        <div class="detail-section">
                            <label>Content Type</label>
                            <div class="detail-value">
                                <span class="type-badge-large" :class="getTypeClass(selectedItem.type)">
                                    <i :class="getTypeIcon(selectedItem.type)"></i>
                                    {{ getTypeLabel(selectedItem.type) }}
                                </span>
                            </div>
                        </div>

                        <div class="detail-section">
                            <label><i class="fas fa-quote-left me-1"></i> Your Prompt</label>
                            <div class="detail-value prompt-box">
                                {{ selectedItem.prompt }}
                            </div>
                        </div>

                        <div class="detail-section">
                            <label><i class="fas fa-magic me-1"></i> Generated Content</label>
                            <div class="detail-value content-box">
                                <div class="full-content" v-html="selectedItem.result.replace(/\n/g, '<br>')"></div>
                            </div>
                        </div>

                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-circle">
                                    <i class="fas fa-microchip"></i>
                                </div>
                                <div>
                                    <span class="stat-label-sm">Tokens Used</span>
                                    <strong class="stat-value">{{ selectedItem.tokens_used }}</strong>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-circle">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <div>
                                    <span class="stat-label-sm">Estimated Cost</span>
                                    <strong class="stat-value">${{ parseFloat(selectedItem.cost_usd).toFixed(4) }}</strong>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-circle">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div>
                                    <span class="stat-label-sm">Created At</span>
                                    <strong class="stat-value">{{ formatDate(selectedItem.created_at) }}</strong>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-circle">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <span class="stat-label-sm">Last Updated</span>
                                    <strong class="stat-value">{{ formatDate(selectedItem.updated_at) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn-outline" @click="copyModalContent">
                            <i class="fas fa-copy me-2"></i>Copy Content
                        </button>
                        <button class="btn-outline" @click="downloadContent">
                            <i class="fas fa-download me-2"></i>Download
                        </button>
                        <router-link :to="`/dashboard?type=${selectedItem?.type}&prompt=${encodeURIComponent(selectedItem?.prompt)}`"
                                     class="btn-gradient" @click="closeModal">
                            <i class="fas fa-redo-alt me-2"></i>Regenerate
                        </router-link>
                        <button class="btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-title">
                            <div class="title-icon warning">
                                <i class="fas fa-trash-alt"></i>
                            </div>
                            <h5 class="mb-0">Delete Content</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="delete-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h4>Are you sure?</h4>
                        <p>This action cannot be undone. This content will be permanently deleted.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button class="btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button class="btn-danger" @click="confirmDelete">
                            Delete Forever
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { Modal } from 'bootstrap';

const router = useRouter();

const generations = ref([]);
const loading = ref(false);
const selectedItem = ref(null);
const deleteId = ref(null);
const searchQuery = ref('');
const filterType = ref('');
const sortBy = ref('newest');
const currentPage = ref(1);
const itemsPerPage = 9;

// Modal instances
let detailsModal = null;
let deleteModal = null;

const statsCards = computed(() => [
    { icon: 'fas fa-file-alt', iconClass: '', value: filteredGenerations.value.length.toLocaleString(), label: 'Total Items' },
    { icon: 'fas fa-microchip', iconClass: 'success', value: totalTokensFormatted.value, label: 'Total Tokens' },
    { icon: 'fas fa-dollar-sign', iconClass: 'warning', value: '$' + totalCostFormatted.value, label: 'Total Cost' },
    { icon: 'fas fa-chart-line', iconClass: 'info', value: averageTokens.value, label: 'Avg Tokens/Item' }
]);

const totalItems = computed(() => generations.value.length);

const totalTokensFormatted = computed(() => {
    const total = generations.value.reduce((sum, item) => sum + item.tokens_used, 0);
    if (total >= 1000000) return (total / 1000000).toFixed(1) + 'M';
    if (total >= 1000) return (total / 1000).toFixed(1) + 'K';
    return total.toLocaleString();
});

const totalCostFormatted = computed(() => {
    const total = generations.value.reduce((sum, item) => sum + parseFloat(item.cost_usd), 0);
    return total.toFixed(4);
});

const averageTokens = computed(() => {
    if (generations.value.length === 0) return 0;
    const total = generations.value.reduce((sum, item) => sum + item.tokens_used, 0);
    return Math.round(total / generations.value.length);
});

const filteredGenerations = computed(() => {
    let filtered = [...generations.value];

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(item =>
            item.prompt.toLowerCase().includes(query) ||
            item.result.toLowerCase().includes(query)
        );
    }

    if (filterType.value) {
        filtered = filtered.filter(item => item.type === filterType.value);
    }

    switch (sortBy.value) {
        case 'newest':
            filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            break;
        case 'oldest':
            filtered.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
            break;
        case 'most_tokens':
            filtered.sort((a, b) => b.tokens_used - a.tokens_used);
            break;
        case 'least_tokens':
            filtered.sort((a, b) => a.tokens_used - b.tokens_used);
            break;
    }

    return filtered;
});

const totalPages = computed(() => Math.ceil(filteredGenerations.value.length / itemsPerPage));
const paginatedGenerations = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredGenerations.value.slice(start, end);
});

const fetchHistory = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/history');
        generations.value = response.data.data.data || response.data.data || [];
    } catch (error) {
        console.error('Failed to fetch history:', error);
        showToast('Failed to load history', 'error');
    } finally {
        loading.value = false;
    }
};

const filterGenerations = () => { currentPage.value = 1; };

const viewDetails = (item) => {
    selectedItem.value = item;
    if (detailsModal) {
        detailsModal.show();
    }
};

const closeModal = () => {
    if (detailsModal) {
        detailsModal.hide();
    }
};

const openDeleteModal = (id) => {
    deleteId.value = id;
    if (deleteModal) {
        deleteModal.show();
    }
};

const confirmDelete = async () => {
    if (deleteId.value) {
        try {
            await axios.delete(`/api/history/${deleteId.value}`);
            await fetchHistory();
            showToast('Content deleted successfully', 'success');
            if (deleteModal) {
                deleteModal.hide();
            }
        } catch (error) {
            showToast('Failed to delete', 'error');
        }
    }
};

const copyContent = async (content) => {
    try {
        await navigator.clipboard.writeText(content);
        showToast('Content copied to clipboard!', 'success');
    } catch (err) {
        showToast('Failed to copy', 'error');
    }
};

const copyModalContent = () => {
    if (selectedItem.value) copyContent(selectedItem.value.result);
};

const downloadContent = () => {
    if (selectedItem.value) {
        const blob = new Blob([selectedItem.value.result], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${selectedItem.value.type}_${Date.now()}.txt`;
        a.click();
        URL.revokeObjectURL(url);
        showToast('File downloaded!', 'success');
    }
};

const quickUse = (item) => {
    router.push(`/dashboard?type=${item.type}&prompt=${encodeURIComponent(item.prompt)}`);
};

const exportData = () => {
    const data = JSON.stringify(filteredGenerations.value, null, 2);
    const blob = new Blob([data], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `history_export_${Date.now()}.json`;
    a.click();
    URL.revokeObjectURL(url);
    showToast(`Exported ${filteredGenerations.value.length} items!`, 'success');
};

const highlightSearch = (text) => {
    if (!searchQuery.value) return text;
    const regex = new RegExp(`(${searchQuery.value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
    return text.replace(regex, '<mark class="search-highlight">$1</mark>');
};

const showToast = (message, type = 'info') => {
    const toast = document.createElement('div');
    toast.className = `toast-notification ${type}`;
    toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-info-circle'} me-2"></i>${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
};

const truncate = (text, length) => {
    if (!text) return '';
    return text.length > length ? text.substring(0, length) + '...' : text;
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric', month: 'short', day: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
};

const getTypeIcon = (type) => {
    const icons = { blog: 'fas fa-blog', social: 'fab fa-twitter', email: 'fas fa-envelope', tagline: 'fas fa-tag', product: 'fas fa-box', seo: 'fas fa-chart-line' };
    return icons[type] || 'fas fa-file';
};

const getTypeLabel = (type) => {
    const labels = { blog: 'Blog Post', social: 'Social Media', email: 'Email', tagline: 'Tagline', product: 'Product', seo: 'SEO Meta' };
    return labels[type] || type;
};

const getTypeClass = (type) => {
    const classes = { blog: 'blog', social: 'social', email: 'email', tagline: 'tagline', product: 'product', seo: 'seo' };
    return classes[type] || 'default';
};

watch([searchQuery, filterType, sortBy], () => { currentPage.value = 1; });

onMounted(() => {
    fetchHistory();

    // Initialize modals after DOM is ready
    nextTick(() => {
        const detailsModalEl = document.getElementById('detailsModal');
        const deleteModalEl = document.getElementById('deleteModal');

        if (detailsModalEl) {
            detailsModal = new Modal(detailsModalEl);
        }
        if (deleteModalEl) {
            deleteModal = new Modal(deleteModalEl);
        }
    });
});
</script>

<style scoped>
/* History Page - Updated to Landing Page Purple/Blue Theme */
.history-container {
    padding: 1.5rem 0;
    min-height: 100vh;
    background: transparent;
}

/* Header */
.history-header {
    background: rgba(255, 255, 255, 0.6);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 1.5rem 2rem;
    border: 1px solid rgba(99, 102, 241, 0.15);
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
}

.header-badge {
    display: inline-block;
    background: rgba(99, 102, 241, 0.1);
    border: 1px solid rgba(99, 102, 241, 0.2);
    padding: 4px 14px;
    border-radius: 50px;
    font-size: 0.75rem;
    margin-bottom: 0.75rem;
    color: #6366f1;
    letter-spacing: 0.5px;
}

.display-4 {
    font-size: 2rem;
    font-weight: 600;
    letter-spacing: -0.5px;
    color: #1e1b4b;
}

.text-primary {
    color: #6366f1 !important;
}

.text-muted {
    color: #6b7280 !important;
}

.header-stats {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    align-items: center;
}

.stat-chip {
    background: rgba(99, 102, 241, 0.08);
    padding: 0.5rem 1rem;
    border-radius: 10px;
    color: #4b5563;
    font-size: 0.9rem;
    border: 1px solid rgba(99, 102, 241, 0.1);
}

.btn-gradient {
    background: linear-gradient(135deg, #6366f1, #8b5cf6, #d946ef);
    padding: 0.5rem 1.2rem;
    border-radius: 10px;
    color: white;
    text-decoration: none;
    transition: all 0.2s ease;
    font-weight: 500;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
}

.btn-gradient:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
    color: white;
}

.btn-outline {
    background: transparent;
    border: 1px solid rgba(99, 102, 241, 0.2);
    padding: 0.5rem 1rem;
    border-radius: 10px;
    color: #4b5563;
    transition: all 0.2s ease;
    cursor: pointer;
    font-size: 0.85rem;
}

.btn-outline:hover {
    border-color: #6366f1;
    color: #6366f1;
}

/* Filter Bar */
.filter-bar {
    background: rgba(255, 255, 255, 0.5);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    padding: 1.2rem;
    border: 1px solid rgba(99, 102, 241, 0.1);
}

.search-box {
    position: relative;
}

.search-box i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 0.85rem;
}

.search-clear {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #9ca3af;
    font-size: 0.85rem;
}

.search-clear:hover {
    color: #6366f1;
}

.form-control, .form-select {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 0.6rem 1rem 0.6rem 2.5rem;
    color: #1f2937;
    font-size: 0.85rem;
}

.form-control:focus, .form-select:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.1);
}

.form-select {
    padding-left: 1rem;
}

/* Stats Cards */
.stat-card {
    background: white;
    border-radius: 16px;
    padding: 1rem 1.2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    border: 1px solid #f0f0f0;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.08);
    border-color: rgba(99, 102, 241, 0.2);
}

.stat-icon {
    width: 48px;
    height: 48px;
    background: rgba(99, 102, 241, 0.08);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-icon i {
    font-size: 1.3rem;
    color: #6366f1;
}

.stat-icon.success i { color: #10b981; }
.stat-icon.warning i { color: #f59e0b; }
.stat-icon.info i { color: #6366f1; }

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #1f2937;
}

.stat-label {
    font-size: 0.7rem;
    color: #6b7280;
    margin: 0;
    letter-spacing: 0.3px;
}

/* Loading & Empty States */
.loading-state, .empty-state {
    text-align: center;
    padding: 3rem;
    background: white;
    border-radius: 20px;
    border: 1px solid #f0f0f0;
}

.empty-icon {
    font-size: 3.5rem;
    color: #d1d5db;
    margin-bottom: 1rem;
}

.empty-state h3 {
    font-size: 1.3rem;
    margin-bottom: 0.5rem;
    color: #1f2937;
}

.empty-state p {
    color: #6b7280;
}

/* History Cards Grid */
.history-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 1.2rem;
    margin-bottom: 2rem;
}

.history-card {
    background: white;
    border-radius: 16px;
    border: 1px solid #f0f0f0;
    transition: all 0.2s ease;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
}

.history-card:hover {
    transform: translateY(-2px);
    border-color: rgba(99, 102, 241, 0.2);
    box-shadow: 0 12px 24px rgba(99, 102, 241, 0.08);
}

.card-header {
    padding: 0.8rem 1rem;
    background: #fafafa;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #f0f0f0;
}

.type-badge {
    padding: 0.2rem 0.7rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 500;
}

.type-badge.blog { background: rgba(99, 102, 241, 0.1); color: #6366f1; }
.type-badge.social { background: rgba(29, 161, 242, 0.1); color: #1da1f2; }
.type-badge.email { background: rgba(52, 152, 219, 0.1); color: #3498db; }
.type-badge.tagline { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.type-badge.product { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
.type-badge.seo { background: rgba(16, 185, 129, 0.1); color: #10b981; }

.card-actions {
    display: flex;
    gap: 0.5rem;
}

.icon-btn {
    background: rgba(0, 0, 0, 0.03);
    border: none;
    width: 30px;
    height: 30px;
    border-radius: 8px;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.8rem;
}

.icon-btn:hover {
    background: #6366f1;
    color: white;
    transform: translateY(-1px);
}

.icon-btn.delete:hover {
    background: #ef4444;
}

.card-body {
    padding: 1rem;
}

.prompt {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 0.8rem;
}

.prompt i {
    color: #6366f1;
    font-size: 0.8rem;
    margin-top: 0.1rem;
}

.prompt p {
    font-size: 0.85rem;
    color: #374151;
    margin: 0;
    line-height: 1.4;
}

.preview {
    background: #f9fafb;
    border-radius: 10px;
    padding: 0.6rem;
    max-height: 80px;
    overflow-y: auto;
}

.preview-content {
    font-size: 0.75rem;
    color: #6b7280;
    line-height: 1.4;
}

.card-footer {
    padding: 0.6rem 1rem;
    border-top: 1px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.meta {
    display: flex;
    gap: 0.8rem;
    flex-wrap: wrap;
}

.meta span {
    font-size: 0.65rem;
    color: #9ca3af;
    display: inline-flex;
    align-items: center;
    gap: 0.2rem;
}

.btn-use {
    background: rgba(99, 102, 241, 0.08);
    border: none;
    padding: 0.2rem 0.6rem;
    border-radius: 6px;
    color: #6366f1;
    font-size: 0.65rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-use:hover {
    background: rgba(99, 102, 241, 0.15);
}

/* Pagination */
.pagination-section {
    margin-top: 2rem;
    text-align: center;
}

.pagination {
    display: inline-flex;
    gap: 0.3rem;
    list-style: none;
    padding: 0;
    margin: 0;
}

.page-item .page-link {
    background: white;
    border: 1px solid #e5e7eb;
    padding: 0.4rem 0.8rem;
    color: #4b5563;
    cursor: pointer;
    transition: all 0.2s ease;
    border-radius: 8px;
}

.page-item.active .page-link {
    background: linear-gradient(135deg, #6366f1, #8b5cf6, #d946ef);
    border-color: transparent;
    color: white;
}

.page-item .page-link:hover:not(.disabled) {
    background: rgba(99, 102, 241, 0.1);
    transform: translateY(-1px);
}

.page-item.disabled .page-link {
    opacity: 0.4;
    cursor: not-allowed;
}

.pagination-info {
    margin-top: 0.8rem;
    font-size: 0.7rem;
    color: #9ca3af;
}

/* Modal Styles */
.modal-content {
    background: white;
    border: 1px solid rgba(99, 102, 241, 0.15);
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.modal-header {
    padding: 1.2rem 1.5rem;
    border-bottom: 1px solid #f0f0f0;
}

.modal-title {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.title-icon {
    width: 40px;
    height: 40px;
    background: rgba(99, 102, 241, 0.08);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.title-icon i {
    font-size: 1.2rem;
    color: #6366f1;
}

.title-icon.warning i {
    color: #f59e0b;
}

.btn-close {
    filter: none;
    opacity: 0.5;
}

.modal-body {
    padding: 1.5rem;
}

.detail-section {
    margin-bottom: 1.5rem;
}

.detail-section label {
    display: block;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6366f1;
    margin-bottom: 0.5rem;
}

.detail-value {
    background: #f9fafb;
    padding: 0.8rem 1rem;
    border-radius: 12px;
    border: 1px solid #f0f0f0;
}

.prompt-box, .content-box {
    line-height: 1.5;
    font-size: 0.9rem;
    color: #374151;
}

.content-box {
    max-height: 350px;
    overflow-y: auto;
}

.full-content {
    white-space: pre-wrap;
    line-height: 1.6;
}

.type-badge-large {
    padding: 0.3rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.8rem;
    margin-top: 1rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    padding: 0.8rem;
    background: #f9fafb;
    border-radius: 12px;
}

.stat-circle {
    width: 40px;
    height: 40px;
    background: rgba(99, 102, 241, 0.08);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-circle i {
    font-size: 1rem;
    color: #6366f1;
}

.stat-label-sm {
    display: block;
    font-size: 0.6rem;
    color: #6b7280;
    margin-bottom: 0.2rem;
}

.stat-value {
    font-size: 0.9rem;
    color: #1f2937;
}

.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #f0f0f0;
    display: flex;
    gap: 0.8rem;
    justify-content: flex-end;
}

.btn-secondary {
    background: #f3f4f6;
    border: none;
    padding: 0.5rem 1.2rem;
    border-radius: 10px;
    color: #4b5563;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.btn-danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    border: none;
    padding: 0.5rem 1.2rem;
    border-radius: 10px;
    color: white;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-danger:hover {
    transform: translateY(-1px);
}

/* Delete Modal */
.delete-icon {
    font-size: 3rem;
    color: #f59e0b;
    margin-bottom: 1rem;
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
    animation: fadeSlide 0.3s ease;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

@keyframes fadeSlide {
    from { opacity: 0; transform: translateX(30px); }
    to { opacity: 1; transform: translateX(0); }
}

/* Search Highlight */
.search-highlight {
    background: rgba(99, 102, 241, 0.15);
    padding: 0 2px;
    border-radius: 3px;
    color: #6366f1;
}

/* Responsive */
@media (max-width: 768px) {
    .history-grid {
        grid-template-columns: 1fr;
    }

    .header-stats {
        flex-direction: column;
        margin-top: 1rem;
    }

    .stat-chip, .btn-gradient {
        width: 100%;
        text-align: center;
        justify-content: center;
    }

    .modal-footer {
        flex-wrap: wrap;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>
