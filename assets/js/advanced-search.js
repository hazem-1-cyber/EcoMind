// assets/js/advanced-search.js - Recherche et Filtrage Avancé

class AdvancedSearch {
    constructor(config) {
        this.config = {
            apiUrl: config.apiUrl || '/api/search.php',
            baseUrl: config.baseUrl || '',
            container: config.container,
            type: config.type, // 'events', 'inscriptions', 'propositions'
            ...config
        };
        
        this.currentFilters = {};
        this.currentPage = 1;
        this.itemsPerPage = 20;
        this.autocompleteTimeout = null;
        
        this.init();
    }
    
    init() {
        this.createSearchInterface();
        this.bindEvents();
        this.loadInitialData();
    }
    
    createSearchInterface() {
        const container = document.querySelector(this.config.container);
        if (!container) return;
        
        container.innerHTML = `
            <div class="advanced-search-container">
                <!-- Search Header -->
                <div class="search-header">
                    <div class="search-title">
                        <i class="fas fa-search"></i>
                        <h3>Recherche Avancée</h3>
                    </div>
                    <button class="toggle-filters-btn" id="toggleFilters">
                        <i class="fas fa-filter"></i>
                        Filtres
                    </button>
                </div>
                
                <!-- Main Search Bar -->
                <div class="main-search-bar">
                    <div class="search-input-container">
                        <input type="text" id="mainSearchInput" placeholder="${this.getSearchPlaceholder()}" autocomplete="off">
                        <div class="search-suggestions" id="searchSuggestions"></div>
                    </div>
                    <button class="search-btn" id="searchBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                
                <!-- Advanced Filters Panel -->
                <div class="filters-panel" id="filtersPanel">
                    ${this.createFiltersHTML()}
                </div>
                
                <!-- Results Header -->
                <div class="results-header">
                    <div class="results-info">
                        <span id="resultsCount">0 résultats</span>
                    </div>
                    <div class="sort-controls">
                        <label>Trier par:</label>
                        <select id="sortSelect">
                            ${this.createSortOptions()}
                        </select>
                    </div>
                </div>
                
                <!-- Results Container -->
                <div class="search-results" id="searchResults">
                    <div class="loading-spinner" id="loadingSpinner">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>Recherche en cours...</span>
                    </div>
                </div>
                
                <!-- Pagination -->
                <div class="pagination-container" id="paginationContainer"></div>
            </div>
        `;
    }
    
    getSearchPlaceholder() {
        switch (this.config.type) {
            case 'events':
                return 'Rechercher par titre, description, type...';
            case 'inscriptions':
                return 'Rechercher par nom, prénom, email...';
            case 'propositions':
                return 'Rechercher par association, description...';
            default:
                return 'Rechercher...';
        }
    }
    
    createFiltersHTML() {
        switch (this.config.type) {
            case 'events':
                return `
                    <div class="filter-row">
                        <div class="filter-group">
                            <label>Type d'événement:</label>
                            <select id="typeFilter">
                                <option value="">Tous les types</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Date de création (du):</label>
                            <input type="date" id="dateFromFilter">
                        </div>
                        <div class="filter-group">
                            <label>Date de création (au):</label>
                            <input type="date" id="dateToFilter">
                        </div>
                    </div>
                `;
                
            case 'inscriptions':
                return `
                    <div class="filter-row">
                        <div class="filter-group">
                            <label>Événement:</label>
                            <select id="eventFilter">
                                <option value="">Tous les événements</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Âge minimum:</label>
                            <input type="number" id="ageMinFilter" min="0" max="100">
                        </div>
                        <div class="filter-group">
                            <label>Âge maximum:</label>
                            <input type="number" id="ageMaxFilter" min="0" max="100">
                        </div>
                    </div>
                    <div class="filter-row">
                        <div class="filter-group">
                            <label>Date d'inscription (du):</label>
                            <input type="date" id="dateFromFilter">
                        </div>
                        <div class="filter-group">
                            <label>Date d'inscription (au):</label>
                            <input type="date" id="dateToFilter">
                        </div>
                    </div>
                `;
                
            case 'propositions':
                return `
                    <div class="filter-row">
                        <div class="filter-group">
                            <label>Type de proposition:</label>
                            <select id="typeFilter">
                                <option value="">Tous les types</option>
                                <option value="Nettoyage">Nettoyage</option>
                                <option value="Reboisement">Reboisement</option>
                                <option value="Atelier">Atelier</option>
                                <option value="Sensibilisation">Sensibilisation</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Date de proposition (du):</label>
                            <input type="date" id="dateFromFilter">
                        </div>
                        <div class="filter-group">
                            <label>Date de proposition (au):</label>
                            <input type="date" id="dateToFilter">
                        </div>
                    </div>
                `;
                
            default:
                return '';
        }
    }
    
    createSortOptions() {
        switch (this.config.type) {
            case 'events':
                return `
                    <option value="date_desc">Plus récents</option>
                    <option value="date_asc">Plus anciens</option>
                    <option value="title_asc">Titre A-Z</option>
                    <option value="title_desc">Titre Z-A</option>
                    <option value="popularity_desc">Plus populaires</option>
                    <option value="popularity_asc">Moins populaires</option>
                    <option value="type_asc">Type A-Z</option>
                `;
                
            case 'inscriptions':
                return `
                    <option value="date_desc">Plus récentes</option>
                    <option value="date_asc">Plus anciennes</option>
                    <option value="name_asc">Nom A-Z</option>
                    <option value="name_desc">Nom Z-A</option>
                    <option value="age_asc">Âge croissant</option>
                    <option value="age_desc">Âge décroissant</option>
                    <option value="event_asc">Événement A-Z</option>
                `;
                
            case 'propositions':
                return `
                    <option value="date_desc">Plus récentes</option>
                    <option value="date_asc">Plus anciennes</option>
                    <option value="association_asc">Association A-Z</option>
                    <option value="association_desc">Association Z-A</option>
                    <option value="type_asc">Type A-Z</option>
                `;
                
            default:
                return '<option value="date_desc">Plus récents</option>';
        }
    }
    
    bindEvents() {
        // Toggle filters panel
        const toggleFilters = document.getElementById('toggleFilters');
        if (toggleFilters) {
            toggleFilters.addEventListener('click', () => {
                const panel = document.getElementById('filtersPanel');
                if (panel) panel.classList.toggle('active');
            });
        }
        
        // Main search input with real-time search
        const searchInput = document.getElementById('mainSearchInput');
        if (searchInput) {
            let searchTimeout;
            
            searchInput.addEventListener('input', (e) => {
                const value = e.target.value.trim();
                console.log('🔍 Search input changed:', value);
                
                // Real-time search with optimized debounce
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    console.log('🚀 Triggering search for:', value);
                    this.performSearch();
                }, 250);
            });
            
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    clearTimeout(searchTimeout);
                    this.performSearch();
                }
            });
        } else {
            console.error('❌ Search input not found');
        }
        
        // Search button
        const searchBtn = document.getElementById('searchBtn');
        if (searchBtn) {
            searchBtn.addEventListener('click', () => {
                this.performSearch();
            });
        }
        
        // Sort change
        const sortSelect = document.getElementById('sortSelect');
        if (sortSelect) {
            sortSelect.addEventListener('change', (e) => {
                console.log('🔄 Sort changed to:', e.target.value);
                this.performSearch();
            });
        } else {
            console.error('❌ Sort select not found');
        }
        
        // Filter changes
        this.bindFilterEvents();
        
        // Click outside to close suggestions
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-input-container')) {
                document.getElementById('searchSuggestions').style.display = 'none';
            }
        });
    }
    
    bindFilterEvents() {
        const filterInputs = document.querySelectorAll('#filtersPanel input, #filtersPanel select');
        filterInputs.forEach(input => {
            input.addEventListener('change', () => {
                console.log('Filter changed:', input.id, input.value);
                this.performSearch();
            });
            
            // Also trigger on input for text fields
            if (input.type === 'text' || input.type === 'number' || input.type === 'date') {
                input.addEventListener('input', () => {
                    clearTimeout(this.filterTimeout);
                    this.filterTimeout = setTimeout(() => {
                        console.log('Filter input changed:', input.id, input.value);
                        this.performSearch();
                    }, 300);
                });
            }
        });
    }
    
    handleAutocomplete(query) {
        clearTimeout(this.autocompleteTimeout);
        
        if (query.length < 2) {
            document.getElementById('searchSuggestions').style.display = 'none';
            return;
        }
        
        this.autocompleteTimeout = setTimeout(() => {
            this.fetchAutocomplete(query);
        }, 300);
    }
    
    async fetchAutocomplete(query) {
        try {
            // For simple API, we'll skip autocomplete for now
            // or implement a basic version
            console.log('Autocomplete disabled for simple API');
            document.getElementById('searchSuggestions').style.display = 'none';
        } catch (error) {
            console.error('Erreur autocomplete:', error);
        }
    }
    
    displaySuggestions(suggestions) {
        const container = document.getElementById('searchSuggestions');
        
        if (suggestions.length === 0) {
            container.style.display = 'none';
            return;
        }
        
        container.innerHTML = suggestions.map(suggestion => `
            <div class="suggestion-item" data-value="${suggestion.suggestion}">
                <i class="fas fa-${suggestion.type === 'titre' ? 'calendar' : suggestion.type === 'type' ? 'tag' : 'user'}"></i>
                <span>${suggestion.suggestion}</span>
                <small>${suggestion.type}</small>
            </div>
        `).join('');
        
        container.style.display = 'block';
        
        // Bind click events to suggestions
        container.querySelectorAll('.suggestion-item').forEach(item => {
            item.addEventListener('click', () => {
                document.getElementById('mainSearchInput').value = item.dataset.value;
                container.style.display = 'none';
                this.performSearch();
            });
        });
    }
    
    async loadInitialData() {
        // Load filter options
        await this.loadFilterOptions();
        // Perform initial search
        this.performSearch();
    }
    
    async loadFilterOptions() {
        try {
            if (this.config.type === 'events') {
                const typeSelect = document.getElementById('typeFilter');
                if (typeSelect) {
                    // Add common event types
                    const commonTypes = ['Nettoyage', 'Reboisement', 'Atelier', 'Sensibilisation'];
                    commonTypes.forEach(type => {
                        const option = document.createElement('option');
                        option.value = type;
                        option.textContent = type;
                        typeSelect.appendChild(option);
                    });
                }
            } else if (this.config.type === 'inscriptions') {
                // Load events for the event filter dropdown
                const eventSelect = document.getElementById('eventFilter');
                if (eventSelect) {
                    try {
                        const response = await fetch(`${this.config.apiUrl}?action=events&keyword=`);
                        const data = await response.json();
                        
                        if (data.success && data.data) {
                            data.data.forEach(event => {
                                const option = document.createElement('option');
                                option.value = event.id;
                                option.textContent = event.titre;
                                eventSelect.appendChild(option);
                            });
                            console.log('✅ Loaded', data.data.length, 'events for filter');
                        }
                    } catch (error) {
                        console.error('❌ Failed to load events for filter:', error);
                    }
                }
            }
        } catch (error) {
            console.error('Erreur chargement filtres:', error);
        }
    }
    
    collectFilters() {
        const searchInput = document.getElementById('mainSearchInput');
        const sortSelect = document.getElementById('sortSelect');
        
        const keyword = searchInput ? searchInput.value.trim() : '';
        const sortValue = sortSelect ? sortSelect.value : 'date_desc';
        
        const filters = {
            keyword: keyword,
            sort: sortValue
        };
        
        // Debug logging
        console.log('📋 Collecting filters:', filters);
        console.log('📋 Search input found:', !!searchInput);
        console.log('📋 Sort select found:', !!sortSelect);
        console.log('📋 Keyword:', keyword);
        console.log('📋 Sort value:', sortValue);
        
        // Type-specific filters
        if (this.config.type === 'events') {
            filters.type = document.getElementById('typeFilter')?.value || '';
            filters.date_from = document.getElementById('dateFromFilter')?.value || '';
            filters.date_to = document.getElementById('dateToFilter')?.value || '';
        } else if (this.config.type === 'inscriptions') {
            filters.evenement_id = document.getElementById('eventFilter')?.value || '';
            filters.age_min = document.getElementById('ageMinFilter')?.value || '';
            filters.age_max = document.getElementById('ageMaxFilter')?.value || '';
            filters.date_from = document.getElementById('dateFromFilter')?.value || '';
            filters.date_to = document.getElementById('dateToFilter')?.value || '';
        } else if (this.config.type === 'propositions') {
            filters.type = document.getElementById('typeFilter')?.value || '';
            filters.date_from = document.getElementById('dateFromFilter')?.value || '';
            filters.date_to = document.getElementById('dateToFilter')?.value || '';
        }
        
        return filters;
    }
    
    async performSearch(page = 1) {
        this.currentPage = page;
        this.currentFilters = this.collectFilters();
        
        // Show loading
        this.showLoading(true);
        
        try {
            // Build URL with proper parameters
            const params = new URLSearchParams();
            params.append('action', this.config.type);
            
            // Add all filters - always send keyword and sort, others only if they have values
            Object.keys(this.currentFilters).forEach(key => {
                const value = this.currentFilters[key];
                if (key === 'keyword' || key === 'sort') {
                    // Always send keyword and sort, even if empty
                    params.append(key, value || '');
                } else if (value !== null && value !== undefined && value !== '') {
                    // Only send other filters if they have values
                    params.append(key, value);
                }
            });
            
            // Add pagination
            params.append('limit', this.itemsPerPage);
            params.append('offset', (page - 1) * this.itemsPerPage);
            
            const url = `${this.config.apiUrl}?${params}`;
            console.log('🔍 Search URL:', url);
            console.log('🔍 Search filters:', this.currentFilters);
            console.log('🔍 URL params:', params.toString());
            
            const response = await fetch(url);
            console.log('🔍 Response status:', response.status);
            console.log('🔍 Response headers:', response.headers.get('content-type'));
            
            // Get raw response text first
            const responseText = await response.text();
            console.log('🔍 Raw response:', responseText);
            
            // Try to parse as JSON
            let data;
            try {
                data = JSON.parse(responseText);
            } catch (parseError) {
                console.error('❌ JSON Parse Error:', parseError);
                throw new Error('Réponse invalide du serveur: ' + responseText.substring(0, 100));
            }
            
            console.log('🔍 Parsed response:', data);
            
            if (data.success) {
                console.log('✅ Search successful!');
                console.log('📊 Total results:', data.total);
                console.log('🔤 Keyword used:', data.keyword);
                console.log('🔄 Sort used:', data.sort);
                console.log('📋 Debug info:', data.debug);
                
                this.displayResults(data.data || []);
                this.updateResultsCount(data.total || 0);
                this.createPagination(data.total || 0);
            } else {
                console.error('❌ Search failed:', data.error);
                this.showError(data.error || 'Erreur inconnue');
            }
        } catch (error) {
            console.error('❌ Erreur recherche:', error);
            this.showError('Erreur lors de la recherche: ' + error.message);
        } finally {
            this.showLoading(false);
        }
    }
    
    displayResults(results) {
        const container = document.getElementById('searchResults');
        
        console.log('🎨 Displaying results:', results.length, 'items');
        console.log('🎨 Results data:', results);
        console.log('🎨 Current filters:', this.currentFilters);
        
        if (results.length === 0) {
            const keyword = this.currentFilters.keyword || '';
            container.innerHTML = `
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3>Aucun résultat trouvé</h3>
                    <p>${keyword ? `Aucun résultat pour "${keyword}"` : 'Aucune donnée disponible'}</p>
                    <p><small>Essayez de modifier vos critères de recherche</small></p>
                </div>
            `;
            return;
        }
        
        const resultHTML = results.map(item => this.createResultItem(item)).join('');
        console.log('🎨 Generated HTML length:', resultHTML.length);
        container.innerHTML = resultHTML;
        console.log('🎨 Results displayed successfully');
    }
    
    createResultItem(item) {
        switch (this.config.type) {
            case 'events':
                // Highlight search terms if keyword exists
                let titre = item.titre;
                let description = item.description ? item.description.substring(0, 150) + '...' : '';
                let type = item.type;
                
                if (this.currentFilters.keyword && this.currentFilters.keyword.trim() !== '') {
                    const keyword = this.currentFilters.keyword.trim();
                    const regex = new RegExp(`(${keyword})`, 'gi');
                    titre = titre.replace(regex, '<mark style="background: #A8E6CF; padding: 2px 4px; border-radius: 3px;">$1</mark>');
                    description = description.replace(regex, '<mark style="background: #A8E6CF; padding: 2px 4px; border-radius: 3px;">$1</mark>');
                    type = type.replace(regex, '<mark style="background: #A8E6CF; padding: 2px 4px; border-radius: 3px;">$1</mark>');
                }
                
                return `
                    <div class="result-item event-item" onclick="goToEventsPage(${item.id})" style="cursor: pointer;" title="Cliquer pour aller à la page des événements">
                        <div class="item-header">
                            <h4>${titre}</h4>
                            <span class="item-type">${type}</span>
                        </div>
                        <div class="item-content">
                            <p>${description}</p>
                            <div class="item-meta">
                                <span><i class="fas fa-calendar"></i> ${new Date(item.date_creation).toLocaleDateString('fr-FR')}</span>
                                <span><i class="fas fa-users"></i> ${item.nb_inscriptions} inscriptions</span>
                            </div>
                        </div>
                        <div class="item-actions">
                            <button onclick="event.stopPropagation(); editEvent(${item.id})" class="btn-edit">
                                <i class="fas fa-edit"></i> Modifier
                            </button>
                        </div>
                    </div>
                `;
                
            case 'inscriptions':
                return `
                    <div class="result-item inscription-item" onclick="goToInscriptionsPage()" style="cursor: pointer;" title="Cliquer pour aller à la page des inscriptions">
                        <div class="item-header">
                            <h4>${item.prenom} ${item.nom}</h4>
                            <span class="item-age">${item.age} ans</span>
                        </div>
                        <div class="item-content">
                            <div class="item-meta">
                                <span><i class="fas fa-envelope"></i> ${item.email}</span>
                                <span><i class="fas fa-phone"></i> ${item.tel}</span>
                                <span><i class="fas fa-calendar-alt"></i> ${item.evenement_titre}</span>
                                <span><i class="fas fa-clock"></i> ${new Date(item.date_inscription).toLocaleDateString('fr-FR')}</span>
                            </div>
                        </div>
                        <div class="item-actions">
                            <button onclick="event.stopPropagation(); editInscription(${item.id})" class="btn-edit">
                                <i class="fas fa-edit"></i> Modifier
                            </button>
                        </div>
                    </div>
                `;
                
            case 'propositions':
                return `
                    <div class="result-item proposition-item" onclick="goToPropositionsPage()" style="cursor: pointer;" title="Cliquer pour aller à la page des propositions">
                        <div class="item-header">
                            <h4>${item.association_nom}</h4>
                            <span class="item-type">${item.type}</span>
                        </div>
                        <div class="item-content">
                            <p>${item.description ? item.description.substring(0, 150) + '...' : ''}</p>
                            <div class="item-meta">
                                <span><i class="fas fa-envelope"></i> ${item.email_contact}</span>
                                <span><i class="fas fa-phone"></i> ${item.tel}</span>
                                <span><i class="fas fa-calendar"></i> ${new Date(item.date_proposition).toLocaleDateString('fr-FR')}</span>
                            </div>
                        </div>
                        <div class="item-actions">
                            <button onclick="event.stopPropagation(); editProposition(${item.id})" class="btn-edit">
                                <i class="fas fa-edit"></i> Modifier
                            </button>
                        </div>
                    </div>
                `;
                
            default:
                return '';
        }
    }
    
    updateResultsCount(total) {
        const count = total || 0;
        const resultsElement = document.getElementById('resultsCount');
        if (resultsElement) {
            resultsElement.textContent = `${count} résultat${count > 1 ? 's' : ''}`;
        }
    }
    
    createPagination(total) {
        const container = document.getElementById('paginationContainer');
        const totalPages = Math.ceil(total / this.itemsPerPage);
        
        if (totalPages <= 1) {
            container.innerHTML = '';
            return;
        }
        
        let paginationHTML = '<div class="pagination">';
        
        // Previous button
        if (this.currentPage > 1) {
            paginationHTML += `<button onclick="advancedSearch.performSearch(${this.currentPage - 1})" class="page-btn">
                <i class="fas fa-chevron-left"></i>
            </button>`;
        }
        
        // Page numbers
        const startPage = Math.max(1, this.currentPage - 2);
        const endPage = Math.min(totalPages, this.currentPage + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `<button onclick="advancedSearch.performSearch(${i})" 
                class="page-btn ${i === this.currentPage ? 'active' : ''}">${i}</button>`;
        }
        
        // Next button
        if (this.currentPage < totalPages) {
            paginationHTML += `<button onclick="advancedSearch.performSearch(${this.currentPage + 1})" class="page-btn">
                <i class="fas fa-chevron-right"></i>
            </button>`;
        }
        
        paginationHTML += '</div>';
        container.innerHTML = paginationHTML;
    }
    
    showLoading(show) {
        const spinner = document.getElementById('loadingSpinner');
        const results = document.getElementById('searchResults');
        
        if (spinner && results) {
            if (show) {
                spinner.style.display = 'flex';
                results.style.opacity = '0.5';
            } else {
                spinner.style.display = 'none';
                results.style.opacity = '1';
            }
        } else {
            console.warn('⚠️ Loading elements not found:', { spinner: !!spinner, results: !!results });
        }
    }
    
    showError(message) {
        const container = document.getElementById('searchResults');
        if (container) {
            container.innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>Erreur</h3>
                    <p>${message}</p>
                </div>
            `;
        } else {
            console.error('❌ Search results container not found');
            alert('Erreur: ' + message);
        }
    }
}

// Global functions for edit actions (to be implemented by the parent page)
function editEvent(id) {
    window.location.href = `${BASE_URL}/index.php?page=admin_events&edit=${id}`;
}

function editInscription(id) {
    window.location.href = `${BASE_URL}/index.php?page=admin_inscriptions&edit=${id}`;
}

function editProposition(id) {
    window.location.href = `${BASE_URL}/index.php?page=admin_propositions&edit=${id}`;
}

// Global functions for navigation to admin pages
function goToEventsPage() {
    window.location.href = `${BASE_URL}/index.php?page=admin_dashboard#events`;
}

function goToInscriptionsPage() {
    window.location.href = `${BASE_URL}/index.php?page=admin_dashboard#inscriptions`;
}

function goToPropositionsPage() {
    window.location.href = `${BASE_URL}/index.php?page=admin_dashboard#propositions`;
}