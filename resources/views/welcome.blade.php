<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="AuraNotes - A state-of-the-art, AI-powered Notes Management System featuring hybrid semantic search, AI summaries, and a premium fluid interface.">
    <title>AuraNotes | AI-Powered Smart Notebook</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        darkBg: '#090a0f',
                        darkCard: '#131520',
                        accentPurp: '#8b5cf6',
                        accentRose: '#f43f5e',
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Custom CSS for Premium Effects -->
    <style>
        body {
            background-color: #090a0f;
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%),
                radial-gradient(at 50% 0%, hsla(263,60%,15%,0.2) 0, transparent 50%),
                radial-gradient(at 100% 0%, hsla(339,49%,15%,0.15) 0, transparent 50%);
            background-attachment: fixed;
        }

        .glassmorphic {
            background: rgba(19, 21, 32, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .note-card {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .note-card:hover {
            transform: translateY(-6px) scale(1.01);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
            border-color: rgba(139, 92, 246, 0.3);
        }

        /* Ambient Glows */
        .glow-indigo {
            box-shadow: 0 0 40px -10px rgba(139, 92, 246, 0.25);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #090a0f;
        }
        ::-webkit-scrollbar-thumb {
            background: #25283b;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #8b5cf6;
        }

        /* Pulse Micro-animation */
        .pulse-active {
            box-shadow: 0 0 0 0 rgba(139, 92, 246, 0.7);
            animation: pulse-glow 2s infinite;
        }

        @keyframes pulse-glow {
            0% {
                box-shadow: 0 0 0 0 rgba(139, 92, 246, 0.4);
            }
            70% {
                box-shadow: 0 0 0 8px rgba(139, 92, 246, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(139, 92, 246, 0);
            }
        }
    </style>
</head>
<body class="text-gray-100 min-h-screen flex flex-col font-sans antialiased overflow-x-hidden">

    <!-- Glowing Background Lights -->
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-purple-600/10 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute top-1/3 right-1/4 w-[500px] h-[500px] bg-rose-600/5 rounded-full blur-[120px] pointer-events-none"></div>

    <!-- Header Navigation -->
    <header class="sticky top-0 z-40 glassmorphic border-b border-white/5 py-4 px-6 lg:px-12 flex justify-between items-center transition-all duration-300">
        <div class="flex items-center gap-3">
            <div class="p-2.5 bg-gradient-to-tr from-accentPurp to-accentRose rounded-xl shadow-lg shadow-purple-900/40">
                <i data-lucide="sparkles" class="w-6 h-6 text-white animate-pulse"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold tracking-tight bg-gradient-to-r from-white via-gray-100 to-gray-400 bg-clip-text text-transparent">AuraNotes</h1>
                <p class="text-[10px] text-purple-400 font-semibold tracking-wider uppercase">AI Cognitive Space</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button id="btnSettings" class="p-2.5 rounded-lg border border-white/5 hover:border-purple-500/30 hover:bg-white/5 transition-all relative" title="API Credentials Configuration">
                <i data-lucide="key" class="w-5 h-5 text-gray-400 hover:text-purple-400 transition-colors"></i>
                <span id="keysConfiguredBadge" class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-emerald-500 hidden"></span>
            </button>
            <button id="btnNewNote" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-accentPurp to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-lg font-medium text-sm transition-all duration-300 shadow-md shadow-purple-950/40 border border-purple-500/20 active:scale-95">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Capture Note</span>
            </button>
        </div>
    </header>

    <!-- Main Content Layout -->
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 py-8 lg:px-8 flex flex-col gap-6 relative">
        
        <!-- Search & Control Floating Bar -->
        <section class="w-full flex flex-col md:flex-row gap-4 items-center justify-between" aria-label="Search and filter controls">
            <!-- Dynamic Semantic Search -->
            <div class="relative w-full md:max-w-xl flex-1 group">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i data-lucide="search" class="w-5 h-5 text-gray-500 group-focus-within:text-purple-400 transition-colors"></i>
                </div>
                <input type="text" id="searchInput" placeholder="Describe what you want to find semantically... (e.g. backend code ideas)" class="w-full pl-11 pr-32 py-3.5 bg-darkCard/50 border border-white/10 rounded-xl focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/20 focus:outline-none transition-all duration-300 text-sm placeholder-gray-500 tracking-wide">
                
                <div class="absolute inset-y-0 right-2 flex items-center gap-1.5">
                    <span id="searchBadge" class="px-2.5 py-1 text-[10px] font-semibold tracking-wider uppercase rounded-md bg-purple-500/10 text-purple-400 border border-purple-500/20 hidden">
                        Semantic AI
                    </span>
                    <button id="btnClearSearch" class="p-1 rounded-md hover:bg-white/5 text-gray-500 hover:text-white transition-colors hidden">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <!-- Categories and Settings Pills -->
            <div class="flex items-center gap-3 overflow-x-auto w-full md:w-auto pb-2 md:pb-0" id="categoryFilterContainer">
                <button class="category-pill px-4 py-2 text-xs font-semibold rounded-full bg-accentPurp text-white border border-purple-500/30 transition-all whitespace-nowrap" data-category="ALL">
                    All Spaces
                </button>
                <button class="category-pill px-4 py-2 text-xs font-semibold rounded-full bg-darkCard border border-white/5 text-gray-400 hover:text-white hover:border-white/10 transition-all whitespace-nowrap" data-category="Work">
                    Work
                </button>
                <button class="category-pill px-4 py-2 text-xs font-semibold rounded-full bg-darkCard border border-white/5 text-gray-400 hover:text-white hover:border-white/10 transition-all whitespace-nowrap" data-category="Personal">
                    Personal
                </button>
                <button class="category-pill px-4 py-2 text-xs font-semibold rounded-full bg-darkCard border border-white/5 text-gray-400 hover:text-white hover:border-white/10 transition-all whitespace-nowrap" data-category="Ideas">
                    Ideas
                </button>
                <button class="category-pill px-4 py-2 text-xs font-semibold rounded-full bg-darkCard border border-white/5 text-gray-400 hover:text-white hover:border-white/10 transition-all whitespace-nowrap" data-category="Code">
                    Code
                </button>
            </div>
        </section>

        <!-- Search Mode Feedback Status -->
        <div id="searchMetaFeedback" class="text-xs text-gray-500 flex items-center gap-2 px-1 hidden">
            <span class="w-1.5 h-1.5 rounded-full bg-purple-500 animate-pulse"></span>
            <span id="searchMetaText">Searching...</span>
        </div>

        <!-- Notes Board/Grid -->
        <section id="notesContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" aria-label="Notes board">
            <!-- Skeleton Cards (Loading) -->
            <div class="glassmorphic rounded-2xl p-6 border border-white/5 animate-pulse flex flex-col gap-4 h-64">
                <div class="flex justify-between items-center">
                    <div class="h-4 bg-gray-700 rounded w-1/3"></div>
                    <div class="h-5 bg-gray-700 rounded-full w-12"></div>
                </div>
                <div class="h-6 bg-gray-700 rounded w-3/4 mt-2"></div>
                <div class="space-y-2 mt-4 flex-1">
                    <div class="h-3 bg-gray-700 rounded w-full"></div>
                    <div class="h-3 bg-gray-700 rounded w-5/6"></div>
                    <div class="h-3 bg-gray-700 rounded w-2/3"></div>
                </div>
                <div class="h-10 bg-gray-700 rounded-lg w-full mt-auto"></div>
            </div>
            <div class="glassmorphic rounded-2xl p-6 border border-white/5 animate-pulse flex flex-col gap-4 h-64 hidden md:flex">
                <div class="flex justify-between items-center">
                    <div class="h-4 bg-gray-700 rounded w-1/3"></div>
                    <div class="h-5 bg-gray-700 rounded-full w-12"></div>
                </div>
                <div class="h-6 bg-gray-700 rounded w-3/4 mt-2"></div>
                <div class="space-y-2 mt-4 flex-1">
                    <div class="h-3 bg-gray-700 rounded w-full"></div>
                    <div class="h-3 bg-gray-700 rounded w-5/6"></div>
                </div>
                <div class="h-10 bg-gray-700 rounded-lg w-full mt-auto"></div>
            </div>
            <div class="glassmorphic rounded-2xl p-6 border border-white/5 animate-pulse flex flex-col gap-4 h-64 hidden lg:flex">
                <div class="flex justify-between items-center">
                    <div class="h-4 bg-gray-700 rounded w-1/3"></div>
                    <div class="h-5 bg-gray-700 rounded-full w-12"></div>
                </div>
                <div class="h-6 bg-gray-700 rounded w-3/4 mt-2"></div>
                <div class="space-y-2 mt-4 flex-1">
                    <div class="h-3 bg-gray-700 rounded w-full"></div>
                    <div class="h-3 bg-gray-700 rounded w-5/6"></div>
                </div>
                <div class="h-10 bg-gray-700 rounded-lg w-full mt-auto"></div>
            </div>
        </section>

        <!-- Empty State Container -->
        <section id="emptyState" class="hidden flex-col items-center justify-center text-center py-20 px-4 glassmorphic rounded-3xl border border-white/5">
            <div class="p-4 bg-white/5 rounded-full mb-4 text-purple-400">
                <i data-lucide="journal" class="w-12 h-12"></i>
            </div>
            <h3 class="text-lg font-semibold text-white">Your mind's horizon is clear</h3>
            <p class="text-sm text-gray-500 max-w-sm mt-1">Capture your first brilliant note, structure code outlines, or test our AI-assisted summarizer and embeddings!</p>
            <button id="btnEmptyStateNewNote" class="mt-6 px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-medium transition-all">
                Create First Note
            </button>
        </section>

        <!-- Premium Pagination Controls -->
        <footer class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 py-4 px-4 glassmorphic rounded-2xl border border-white/5" id="paginationContainer">
            <div class="text-xs text-gray-500 font-medium">
                Showing <span id="paginatedFrom" class="text-gray-300">0</span> to <span id="paginatedTo" class="text-gray-300">0</span> of <span id="paginatedTotal" class="text-gray-300">0</span> items
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500">Show:</span>
                    <select id="limitSelect" class="bg-darkBg border border-white/10 rounded-lg px-2 py-1 text-xs text-gray-300 focus:outline-none focus:border-purple-500/50">
                        <option value="6">6</option>
                        <option value="12" selected>12</option>
                        <option value="24">24</option>
                    </select>
                </div>
                <div class="flex items-center gap-1.5">
                    <button id="btnPrevPage" class="p-2 rounded-lg bg-white/5 border border-white/5 hover:border-white/10 disabled:opacity-30 disabled:pointer-events-none transition-all">
                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    </button>
                    <span class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-purple-500/10 text-purple-400 border border-purple-500/20" id="currentPageNumber">
                        Page 1
                    </span>
                    <button id="btnNextPage" class="p-2 rounded-lg bg-white/5 border border-white/5 hover:border-white/10 disabled:opacity-30 disabled:pointer-events-none transition-all">
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </footer>

    </main>

    <!-- Slide-over Drawer: Create / Edit Note -->
    <div id="drawerOverlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 transition-opacity duration-300 opacity-0 pointer-events-none">
        <div class="absolute inset-y-0 right-0 max-w-lg w-full bg-darkBg border-l border-white/10 shadow-2xl transition-transform duration-300 translate-x-full flex flex-col h-full overflow-hidden" id="drawerBody">
            
            <!-- Drawer Header -->
            <div class="p-6 border-b border-white/5 flex items-center justify-between bg-darkCard/35">
                <div class="flex items-center gap-3">
                    <div id="drawerIconContainer" class="p-2 bg-purple-500/10 rounded-lg text-purple-400">
                        <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    </div>
                    <h2 id="drawerTitle" class="text-base font-bold text-white">Capture Idea</h2>
                </div>
                <button id="btnCloseDrawer" class="p-1.5 rounded-lg hover:bg-white/5 text-gray-500 hover:text-white transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Drawer Form Body -->
            <form id="noteForm" class="flex-1 overflow-y-auto p-6 space-y-6">
                <input type="hidden" id="noteId">

                <!-- Title Input -->
                <div class="space-y-1.5">
                    <label for="inputTitle" class="text-xs font-bold text-gray-400 uppercase tracking-wider">Title</label>
                    <input type="text" id="inputTitle" required placeholder="Give your note a title..." class="w-full bg-darkCard border border-white/10 rounded-lg px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-purple-500/50">
                </div>

                <!-- Category & Presets -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="inputCategory" class="text-xs font-bold text-gray-400 uppercase tracking-wider">Category</label>
                        <select id="inputCategory" class="w-full bg-darkCard border border-white/10 rounded-lg px-3 py-3 text-sm text-white focus:outline-none focus:border-purple-500/50">
                            <option value="General">General</option>
                            <option value="Work">Work</option>
                            <option value="Personal">Personal</option>
                            <option value="Ideas">Ideas</option>
                            <option value="Code">Code</option>
                        </select>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Theme Color</label>
                        <div class="flex items-center gap-1.5 h-11 px-2.5 bg-darkCard border border-white/10 rounded-lg">
                            <button type="button" class="color-picker-dot w-5 h-5 rounded-full border border-white/20 transition-all transform scale-110" data-color="#ffffff" style="background-color: #ffffff;"></button>
                            <button type="button" class="color-picker-dot w-5 h-5 rounded-full border border-transparent transition-all" data-color="#f43f5e" style="background-color: #f43f5e;"></button>
                            <button type="button" class="color-picker-dot w-5 h-5 rounded-full border border-transparent transition-all" data-color="#10b981" style="background-color: #10b981;"></button>
                            <button type="button" class="color-picker-dot w-5 h-5 rounded-full border border-transparent transition-all" data-color="#3b82f6" style="background-color: #3b82f6;"></button>
                            <button type="button" class="color-picker-dot w-5 h-5 rounded-full border border-transparent transition-all" data-color="#eab308" style="background-color: #eab308;"></button>
                        </div>
                    </div>
                </div>

                <!-- Rich Note Content -->
                <div class="space-y-1.5">
                    <label for="inputContent" class="text-xs font-bold text-gray-400 uppercase tracking-wider">Content</label>
                    <textarea id="inputContent" required rows="9" placeholder="Expand your thoughts here..." class="w-full bg-darkCard border border-white/10 rounded-lg px-4 py-3.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-purple-500/50 resize-y"></textarea>
                </div>

                <!-- Summary Field (If Editing, shows summary) -->
                <div id="drawerSummarySection" class="space-y-2 hidden">
                    <div class="flex justify-between items-center">
                        <label for="inputSummary" class="text-xs font-bold text-purple-400 uppercase tracking-wider flex items-center gap-1.5">
                            <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                            <span>Cognitive Summary</span>
                        </label>
                        <span class="text-[9px] text-gray-500">AI Synapse</span>
                    </div>
                    <textarea id="inputSummary" rows="3" placeholder="AI summary will appear here once generated..." class="w-full bg-purple-950/15 border border-purple-500/20 text-purple-200/90 rounded-lg px-4 py-3 text-xs focus:outline-none focus:border-purple-500/40 resize-none"></textarea>
                </div>

                <!-- Toast/Validation Error Container Inside Drawer -->
                <div id="drawerAlert" class="hidden p-3.5 rounded-lg border text-xs font-medium flex items-center gap-2"></div>
            </form>

            <!-- Drawer Footer Buttons -->
            <div class="p-6 border-t border-white/5 bg-darkCard/35 flex items-center justify-between gap-3">
                <button type="button" id="btnDeleteNote" class="hidden items-center gap-1.5 px-3 py-2.5 rounded-lg border border-red-500/20 hover:bg-red-500/10 text-red-400 text-xs font-semibold transition-colors duration-200 active:scale-95">
                    <i data-lucide="trash" class="w-3.5 h-3.5"></i>
                    <span>Delete</span>
                </button>
                <div class="flex items-center gap-3 ml-auto">
                    <button type="button" id="btnCancelNote" class="px-4 py-2.5 rounded-lg border border-white/10 hover:bg-white/5 text-gray-400 text-xs font-semibold transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="button" id="btnSaveNote" class="px-5 py-2.5 rounded-lg bg-gradient-to-r from-accentPurp to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white text-xs font-bold transition-all duration-300 shadow-lg shadow-purple-950/20">
                        Store Note
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- API Settings Modal -->
    <div id="settingsModalOverlay" class="fixed inset-0 bg-black/75 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-opacity duration-300 opacity-0 pointer-events-none">
        <div class="max-w-md w-full glassmorphic rounded-2xl border border-white/10 shadow-2xl p-6 transition-all transform scale-95 duration-300 flex flex-col gap-5" id="settingsModalBody">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-gradient-to-tr from-accentPurp to-accentRose rounded-lg">
                        <i data-lucide="cpu" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white">AI Engine Credentials</h2>
                        <p class="text-[10px] text-gray-500">Stored locally in your active sandbox</p>
                    </div>
                </div>
                <button id="btnCloseSettings" class="p-1 rounded-lg hover:bg-white/5 text-gray-500 hover:text-white transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <div class="text-xs text-gray-400 leading-relaxed bg-white/5 border border-white/5 rounded-lg p-3">
                Provide your API Keys to enable premium real-time vector embeddings and cloud summaries. If left empty, AuraNotes will automatically activate its <strong>extractive TF-IDF semantic engine</strong> for robust offline operations!
            </div>

            <div class="space-y-4">
                <!-- Gemini Key -->
                <div class="space-y-1.5">
                    <div class="flex justify-between items-center">
                        <label for="settingsGeminiKey" class="text-xs font-bold text-gray-400 uppercase tracking-wider">Gemini API Key</label>
                        <a href="https://aistudio.google.com/" target="_blank" class="text-[10px] text-purple-400 hover:underline">Get Key</a>
                    </div>
                    <div class="relative">
                        <input type="password" id="settingsGeminiKey" placeholder="AIzaSy..." class="w-full bg-darkBg border border-white/10 rounded-lg pl-3 pr-10 py-2.5 text-sm text-white placeholder-gray-700 focus:outline-none focus:border-purple-500/50">
                        <button type="button" class="toggle-password-btn absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-600 hover:text-gray-400">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- OpenAI Key -->
                <div class="space-y-1.5">
                    <div class="flex justify-between items-center">
                        <label for="settingsOpenAIKey" class="text-xs font-bold text-gray-400 uppercase tracking-wider">OpenAI API Key</label>
                        <a href="https://platform.openai.com/api-keys" target="_blank" class="text-[10px] text-purple-400 hover:underline">Get Key</a>
                    </div>
                    <div class="relative">
                        <input type="password" id="settingsOpenAIKey" placeholder="sk-..." class="w-full bg-darkBg border border-white/10 rounded-lg pl-3 pr-10 py-2.5 text-sm text-white placeholder-gray-700 focus:outline-none focus:border-purple-500/50">
                        <button type="button" class="toggle-password-btn absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-600 hover:text-gray-400">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-2">
                <button id="btnClearKeys" class="px-4 py-2 text-xs font-semibold rounded-lg border border-red-500/20 hover:bg-red-500/10 text-red-400 transition-colors">
                    Clear Credentials
                </button>
                <button id="btnSaveKeys" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-lg transition-colors">
                    Apply Engine
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModalOverlay" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-opacity duration-300 opacity-0 pointer-events-none">
        <div class="max-w-md w-full glassmorphic rounded-2xl border border-red-500/20 shadow-2xl p-6 transition-all transform scale-95 duration-300 flex flex-col gap-5 text-center items-center" id="deleteModalBody">
            <div class="p-4 bg-red-500/10 text-red-500 rounded-full border border-red-500/20 shadow-[0_0_15px_rgba(239,68,68,0.15)]">
                <i data-lucide="alert-triangle" class="w-8 h-8"></i>
            </div>
            
            <div class="space-y-2">
                <h3 class="text-lg font-bold text-white tracking-tight">Purge Note from Core</h3>
                <p class="text-xs text-gray-400 leading-relaxed max-w-xs">
                    This action is permanent and will completely erase this note and its associated vector embeddings from the database. Are you sure you want to proceed?
                </p>
            </div>

            <div class="flex items-center justify-center gap-3 w-full mt-2">
                <button id="btnCancelDelete" class="flex-1 py-2.5 rounded-lg border border-white/10 hover:bg-white/5 text-gray-400 text-xs font-semibold transition-colors duration-200">
                    Cancel
                </button>
                <button id="btnConfirmDelete" class="flex-1 py-2.5 rounded-lg bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white text-xs font-bold transition-all duration-300 shadow-lg shadow-red-950/20">
                    Confirm Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Global Floating Toast Notification -->
    <div id="toastNotification" class="fixed bottom-6 right-6 z-50 glassmorphic px-4 py-3 rounded-xl border border-white/10 flex items-center gap-3 transform translate-y-24 opacity-0 transition-all duration-300 shadow-2xl max-w-sm pointer-events-none">
        <div id="toastIconContainer" class="p-1.5 rounded-lg"></div>
        <div class="flex-1">
            <p id="toastMessage" class="text-xs font-medium text-white"></p>
        </div>
        <button id="btnCloseToast" class="text-gray-500 hover:text-white transition-colors">
            <i data-lucide="x" class="w-3.5 h-3.5"></i>
        </button>
    </div>

    <!-- Application Script Logic -->
    <script>
        // DOM Node References
        const notesContainer = document.getElementById('notesContainer');
        const emptyState = document.getElementById('emptyState');
        const paginationContainer = document.getElementById('paginationContainer');
        const searchInput = document.getElementById('searchInput');
        const btnClearSearch = document.getElementById('btnClearSearch');
        const searchBadge = document.getElementById('searchBadge');
        const searchMetaFeedback = document.getElementById('searchMetaFeedback');
        const searchMetaText = document.getElementById('searchMetaText');

        // Pagination DOM
        const paginatedFrom = document.getElementById('paginatedFrom');
        const paginatedTo = document.getElementById('paginatedTo');
        const paginatedTotal = document.getElementById('paginatedTotal');
        const currentPageNumber = document.getElementById('currentPageNumber');
        const limitSelect = document.getElementById('limitSelect');
        const btnPrevPage = document.getElementById('btnPrevPage');
        const btnNextPage = document.getElementById('btnNextPage');

        // Drawer DOM
        const drawerOverlay = document.getElementById('drawerOverlay');
        const drawerBody = document.getElementById('drawerBody');
        const drawerTitle = document.getElementById('drawerTitle');
        const drawerIconContainer = document.getElementById('drawerIconContainer');
        const btnNewNote = document.getElementById('btnNewNote');
        const btnEmptyStateNewNote = document.getElementById('btnEmptyStateNewNote');
        const btnCloseDrawer = document.getElementById('btnCloseDrawer');
        const btnCancelNote = document.getElementById('btnCancelNote');
        const btnSaveNote = document.getElementById('btnSaveNote');
        const btnDeleteNote = document.getElementById('btnDeleteNote');
        const noteForm = document.getElementById('noteForm');
        const noteId = document.getElementById('noteId');
        const inputTitle = document.getElementById('inputTitle');
        const inputCategory = document.getElementById('inputCategory');
        const inputContent = document.getElementById('inputContent');
        const inputSummary = document.getElementById('inputSummary');
        const drawerSummarySection = document.getElementById('drawerSummarySection');
        const drawerAlert = document.getElementById('drawerAlert');

        // Settings Modal DOM
        const settingsModalOverlay = document.getElementById('settingsModalOverlay');
        const settingsModalBody = document.getElementById('settingsModalBody');
        const btnSettings = document.getElementById('btnSettings');
        const btnCloseSettings = document.getElementById('btnCloseSettings');
        const btnSaveKeys = document.getElementById('btnSaveKeys');
        const btnClearKeys = document.getElementById('btnClearKeys');
        const settingsGeminiKey = document.getElementById('settingsGeminiKey');
        const settingsOpenAIKey = document.getElementById('settingsOpenAIKey');
        const keysConfiguredBadge = document.getElementById('keysConfiguredBadge');

        // Delete Confirmation Modal DOM
        const deleteModalOverlay = document.getElementById('deleteModalOverlay');
        const deleteModalBody = document.getElementById('deleteModalBody');
        const btnCancelDelete = document.getElementById('btnCancelDelete');
        const btnConfirmDelete = document.getElementById('btnConfirmDelete');

        // Toast DOM
        const toastNotification = document.getElementById('toastNotification');
        const toastMessage = document.getElementById('toastMessage');
        const toastIconContainer = document.getElementById('toastIconContainer');
        const btnCloseToast = document.getElementById('btnCloseToast');

        // App State
        let currentPage = 1;
        let perPage = 12;
        let selectedCategory = 'ALL';
        let searchQuery = '';
        let selectedColor = '#ffffff';

        // Dynamic API Base URL logic for subfolder (e.g. /NVECTA) & root runs
        const pathName = window.location.pathname;
        const baseFolder = pathName.endsWith('/') ? pathName.slice(0, -1) : pathName;
        const apiBaseUrl = `${window.location.origin}${baseFolder}`;

        // Initialize App
        window.addEventListener('DOMContentLoaded', () => {
            // Load credentials from localStorage
            loadApiKeys();
            
            // Load initial notes
            fetchNotes();

            // Setup Color Dot Presets click handler
            document.querySelectorAll('.color-picker-dot').forEach(dot => {
                dot.addEventListener('click', () => {
                    document.querySelectorAll('.color-picker-dot').forEach(d => {
                        d.classList.remove('scale-110');
                        d.classList.remove('border-white/20');
                        d.classList.add('border-transparent');
                    });
                    dot.classList.add('scale-110');
                    dot.classList.add('border-white/20');
                    dot.classList.remove('border-transparent');
                    selectedColor = dot.getAttribute('data-color');
                });
            });

            // Re-render Icons
            lucide.createIcons();
        });

        // Local Storage Key Management
        function loadApiKeys() {
            const gemini = localStorage.getItem('X-Gemini-Key') || '';
            const openai = localStorage.getItem('X-OpenAI-Key') || '';
            
            settingsGeminiKey.value = gemini;
            settingsOpenAIKey.value = openai;

            if (gemini || openai) {
                keysConfiguredBadge.classList.remove('hidden');
                searchBadge.classList.remove('hidden');
            } else {
                keysConfiguredBadge.classList.add('hidden');
                searchBadge.classList.add('hidden');
            }
        }

        function getAuthHeaders() {
            const gemini = localStorage.getItem('X-Gemini-Key') || '';
            const openai = localStorage.getItem('X-OpenAI-Key') || '';
            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            };
            if (gemini) headers['X-Gemini-Key'] = gemini;
            if (openai) headers['X-OpenAI-Key'] = openai;
            return headers;
        }

        // Fetch Notes from REST API
        async function fetchNotes() {
            renderLoadingSkeleton();
            try {
                let url = `${apiBaseUrl}/api/notes?page=${currentPage}&limit=${perPage}`;
                
                // If a category filter is active, let's filter the local list or we can handle it in the search.
                // We'll search and pass it if wanted. To support both, we fetch notes.
                if (searchQuery) {
                    url += `&search=${encodeURIComponent(searchQuery)}`;
                }

                const response = await fetch(url, {
                    method: 'GET',
                    headers: getAuthHeaders()
                });

                const result = await response.json();
                
                if (result.success) {
                    let notes = result.data || [];
                    
                    // Client-side category filtering to provide super crisp UI filtering response
                    if (selectedCategory !== 'ALL') {
                        notes = notes.filter(n => n.category && n.category.toLowerCase() === selectedCategory.toLowerCase());
                    }

                    renderNotesGrid(notes, result.meta);
                } else {
                    showToast('Failed to load notes catalog.', 'error');
                }
            } catch (error) {
                console.error(error);
                showToast('API Connection failure. Verify your Laravel dev server.', 'error');
            }
        }

        // Render Loading Skeleton State
        function renderLoadingSkeleton() {
            notesContainer.innerHTML = `
                ${Array(3).fill(0).map(() => `
                    <div class="glassmorphic rounded-2xl p-6 border border-white/5 animate-pulse flex flex-col gap-4 h-64">
                        <div class="flex justify-between items-center">
                            <div class="h-4 bg-gray-800 rounded w-1/3"></div>
                            <div class="h-5 bg-gray-800 rounded-full w-12"></div>
                        </div>
                        <div class="h-6 bg-gray-800 rounded w-3/4 mt-2"></div>
                        <div class="space-y-2 mt-4 flex-1">
                            <div class="h-3 bg-gray-800 rounded w-full"></div>
                            <div class="h-3 bg-gray-800 rounded w-5/6"></div>
                        </div>
                        <div class="h-10 bg-gray-800 rounded-lg w-full mt-auto"></div>
                    </div>
                `).join('')}
            `;
            emptyState.classList.add('hidden');
        }

        // Render Notes Grid
        function renderNotesGrid(notes, meta) {
            notesContainer.innerHTML = '';
            
            if (notes.length === 0) {
                emptyState.classList.remove('hidden');
                paginationContainer.classList.add('hidden');
                searchMetaFeedback.classList.add('hidden');
                return;
            }

            emptyState.classList.add('hidden');
            paginationContainer.classList.remove('hidden');

            // Render Search Meta Information
            if (meta && meta.search_method && meta.search_method !== 'none') {
                searchMetaFeedback.classList.remove('hidden');
                if (meta.search_method === 'vector') {
                    searchMetaText.innerHTML = `Semantic Embeddings Activated. Loaded ${meta.total} matches by cognitive distance.`;
                } else {
                    searchMetaText.innerHTML = `AI credentials not detected. Fired Local Extractive TF-IDF Semantic fallback (Returned ${meta.total} matches).`;
                }
            } else {
                searchMetaFeedback.classList.add('hidden');
            }

            // Render Notes Cards
            notes.forEach(note => {
                const borderStyle = note.color && note.color !== '#ffffff' 
                    ? `border-color: ${note.color}40; box-shadow: 0 4px 20px -2px ${note.color}15;` 
                    : '';
                const categoryStyle = note.color && note.color !== '#ffffff'
                    ? `background-color: ${note.color}15; color: ${note.color}; border-color: ${note.color}25;`
                    : 'background-color: rgba(139, 92, 246, 0.1); color: #c084fc; border-color: rgba(139, 92, 246, 0.2);';

                const scoreBadge = note.search_score !== undefined 
                    ? `<span class="px-2 py-0.5 text-[9px] bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded font-semibold">Sim: ${Math.round(note.search_score * 100)}%</span>` 
                    : '';

                const summaryHtml = note.summary 
                    ? `
                        <div class="mt-4 p-3 rounded-lg bg-purple-950/15 border border-purple-500/10 text-xs text-purple-200/90 leading-relaxed shadow-[inset_0px_1px_3px_rgba(0,0,0,0.2)]">
                            <div class="flex items-center gap-1.5 mb-1 text-[10px] font-bold text-purple-400 uppercase tracking-wider">
                                <i data-lucide="sparkles" class="w-3 h-3 text-purple-400"></i>
                                <span>AI Brain Summary</span>
                            </div>
                            <p>${note.summary}</p>
                        </div>
                    ` 
                    : `
                        <div class="mt-4" id="summarizePlaceholder-${note.id}">
                            <button onclick="generateAISummary(${note.id})" class="inline-flex items-center gap-1.5 text-xs text-purple-400 hover:text-purple-300 font-semibold px-3 py-1.5 rounded-lg border border-purple-500/10 bg-purple-500/5 hover:bg-purple-500/10 active:scale-95 transition-all">
                                <i data-lucide="wand-2" class="w-3.5 h-3.5"></i>
                                <span>Generate AI Summary</span>
                            </button>
                        </div>
                    `;

                const card = document.createElement('div');
                card.className = 'glassmorphic rounded-2xl p-6 border note-card flex flex-col gap-4 h-fit min-h-64';
                card.setAttribute('style', borderStyle);
                card.innerHTML = `
                    <div class="flex justify-between items-start gap-2">
                        <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full border" style="${categoryStyle}">
                            ${note.category || 'General'}
                        </span>
                        <div class="flex items-center gap-1.5">
                            ${scoreBadge}
                            <button onclick="openEditDrawer(${note.id})" class="p-1 rounded hover:bg-white/5 text-gray-500 hover:text-white transition-colors" title="Edit Note">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex-1 flex flex-col gap-2">
                        <h4 class="text-base font-bold text-white tracking-tight">${escapeHtml(note.title)}</h4>
                        <p class="text-xs text-gray-400 leading-relaxed whitespace-pre-line line-clamp-4">${escapeHtml(note.content)}</p>
                    </div>

                    ${summaryHtml}
                `;
                notesContainer.appendChild(card);
            });

            // Update Pagination UI
            if (meta) {
                paginatedTotal.innerText = meta.total || notes.length;
                paginatedFrom.innerText = ((currentPage - 1) * perPage) + 1;
                paginatedTo.innerText = Math.min(currentPage * perPage, meta.total || notes.length);
                currentPageNumber.innerText = `Page ${meta.current_page || 1}`;

                btnPrevPage.disabled = currentPage === 1;
                btnNextPage.disabled = currentPage >= (meta.last_page || 1);
            }

            // Bind icons
            lucide.createIcons();
        }

        // Generate AI Summary
        async function generateAISummary(id) {
            const btnContainer = document.getElementById(`summarizePlaceholder-${id}`);
            if (!btnContainer) return;

            btnContainer.innerHTML = `
                <div class="inline-flex items-center gap-2 text-xs text-purple-400 font-semibold px-3 py-1.5 rounded-lg border border-purple-500/10 bg-purple-500/5">
                    <i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin"></i>
                    <span>Engaging AI Synapse...</span>
                </div>
            `;
            lucide.createIcons();

            try {
                const response = await fetch(`${apiBaseUrl}/api/notes/${id}/summary`, {
                    method: 'POST',
                    headers: getAuthHeaders()
                });

                const result = await response.json();
                
                if (result.success) {
                    showToast('AI Note Summary generated successfully!', 'success');
                    fetchNotes(); // Reload notes
                } else {
                    showToast(result.message || 'AI summary generation failed.', 'error');
                    fetchNotes();
                }
            } catch (error) {
                console.error(error);
                showToast('API Connection failure.', 'error');
                fetchNotes();
            }
        }

        // Add / Edit Action Handlers
        function openCreateDrawer() {
            drawerTitle.innerText = "Capture New Thought";
            drawerIconContainer.innerHTML = '<i data-lucide="plus-circle" class="w-5 h-5"></i>';
            noteId.value = "";
            inputTitle.value = "";
            inputCategory.value = "General";
            inputContent.value = "";
            inputSummary.value = "";
            drawerSummarySection.classList.add('hidden');
            btnDeleteNote.classList.add('hidden');
            
            // Set Color Dot selection to white default
            selectedColor = "#ffffff";
            document.querySelectorAll('.color-picker-dot').forEach(d => {
                if (d.getAttribute('data-color') === '#ffffff') {
                    d.classList.add('scale-110');
                    d.classList.add('border-white/20');
                } else {
                    d.classList.remove('scale-110');
                    d.classList.remove('border-white/20');
                }
            });

            drawerAlert.classList.add('hidden');
            openDrawer();
        }

        async function openEditDrawer(id) {
            drawerTitle.innerText = "Refine Note Intelligence";
            drawerIconContainer.innerHTML = '<i data-lucide="edit-3" class="w-5 h-5"></i>';
            drawerAlert.classList.add('hidden');
            
            try {
                const response = await fetch(`${apiBaseUrl}/api/notes/${id}`, {
                    method: 'GET',
                    headers: getAuthHeaders()
                });
                const result = await response.json();
                
                if (result.success) {
                    const note = result.data;
                    noteId.value = note.id;
                    inputTitle.value = note.title;
                    inputCategory.value = note.category || 'General';
                    inputContent.value = note.content;
                    
                    if (note.summary) {
                        inputSummary.value = note.summary;
                        drawerSummarySection.classList.remove('hidden');
                    } else {
                        drawerSummarySection.classList.add('hidden');
                    }

                    // Select correct color dot
                    selectedColor = note.color || '#ffffff';
                    document.querySelectorAll('.color-picker-dot').forEach(d => {
                        if (d.getAttribute('data-color') === selectedColor) {
                            d.classList.add('scale-110');
                            d.classList.add('border-white/20');
                        } else {
                            d.classList.remove('scale-110');
                            d.classList.remove('border-white/20');
                        }
                    });

                    btnDeleteNote.classList.remove('hidden');
                    openDrawer();
                } else {
                    showToast('Could not fetch note data.', 'error');
                }
            } catch (error) {
                console.error(error);
                showToast('Failed to connect to backend server.', 'error');
            }
        }

        async function saveNote() {
            const id = noteId.value;
            const title = inputTitle.value.trim();
            const category = inputCategory.value;
            const content = inputContent.value.trim();

            if (!title || !content) {
                showDrawerAlert('Please supply both Title and Note Content.', 'error');
                return;
            }

            const payload = {
                title,
                category,
                content,
                color: selectedColor
            };

            const url = id ? `${apiBaseUrl}/api/notes/${id}` : `${apiBaseUrl}/api/notes`;
            const method = id ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: getAuthHeaders(),
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (result.success) {
                    showToast(id ? 'Note intelligence updated.' : 'Note stored in the cognitive board.', 'success');
                    closeDrawer();
                    fetchNotes();
                } else {
                    const errorMsg = result.errors 
                        ? Object.values(result.errors).flat().join(' ') 
                        : result.message || 'Operation failed';
                    showDrawerAlert(errorMsg, 'error');
                }
            } catch (error) {
                console.error(error);
                showDrawerAlert('API integration error. Verify server logs.', 'error');
            }
        }

        // Open Custom Delete Confirmation Modal
        function deleteNote() {
            const id = noteId.value;
            if (!id) return;
            openDeleteModal();
        }

        function openDeleteModal() {
            deleteModalOverlay.classList.remove('pointer-events-none');
            deleteModalOverlay.classList.add('opacity-100');
            deleteModalBody.classList.remove('scale-95');
            deleteModalBody.classList.add('scale-100');
            lucide.createIcons();
        }

        function closeDeleteModal() {
            deleteModalOverlay.classList.add('pointer-events-none');
            deleteModalOverlay.classList.remove('opacity-100');
            deleteModalBody.classList.add('scale-95');
            deleteModalBody.classList.remove('scale-100');
        }

        // Execute API Delete Request
        async function executeDeleteNote() {
            const id = noteId.value;
            if (!id) return;

            try {
                const response = await fetch(`${apiBaseUrl}/api/notes/${id}`, {
                    method: 'DELETE',
                    headers: getAuthHeaders()
                });
                const result = await response.json();
                
                if (result.success) {
                    showToast('Note permanently deleted.', 'success');
                    closeDeleteModal();
                    closeDrawer();
                    fetchNotes();
                } else {
                    showToast('Delete operation failed.', 'error');
                    closeDeleteModal();
                }
            } catch (error) {
                console.error(error);
                showToast('Failed to connect to API.', 'error');
                closeDeleteModal();
            }
        }

        // Settings Credential Handlers
        function saveCredentials() {
            const gemini = settingsGeminiKey.value.trim();
            const openai = settingsOpenAIKey.value.trim();
            
            if (gemini) localStorage.setItem('X-Gemini-Key', gemini);
            else localStorage.removeItem('X-Gemini-Key');

            if (openai) localStorage.setItem('X-OpenAI-Key', openai);
            else localStorage.removeItem('X-OpenAI-Key');

            loadApiKeys();
            closeSettingsModal();
            showToast('AI engine key settings updated.', 'success');
            fetchNotes(); // Reload notes to generate real vector embeddings if they search
        }

        function clearCredentials() {
            localStorage.removeItem('X-Gemini-Key');
            localStorage.removeItem('X-OpenAI-Key');
            settingsGeminiKey.value = '';
            settingsOpenAIKey.value = '';
            loadApiKeys();
            closeSettingsModal();
            showToast('Engine credentials cleared. Activated local TF-IDF engine.', 'success');
            fetchNotes();
        }

        // Drawer UI States
        function openDrawer() {
            drawerOverlay.classList.remove('pointer-events-none');
            drawerOverlay.classList.add('opacity-100');
            drawerBody.classList.remove('translate-x-full');
            lucide.createIcons();
        }

        function closeDrawer() {
            drawerOverlay.classList.add('pointer-events-none');
            drawerOverlay.classList.remove('opacity-100');
            drawerBody.classList.add('translate-x-full');
        }

        function showDrawerAlert(msg, type = 'error') {
            drawerAlert.innerText = msg;
            drawerAlert.classList.remove('hidden');
            if (type === 'error') {
                drawerAlert.className = "p-3.5 rounded-lg border border-red-500/20 bg-red-500/10 text-red-400 text-xs font-semibold flex items-center gap-2";
            } else {
                drawerAlert.className = "p-3.5 rounded-lg border border-emerald-500/20 bg-emerald-500/10 text-emerald-400 text-xs font-semibold flex items-center gap-2";
            }
        }

        // Settings Modal UI States
        function openSettingsModal() {
            settingsModalOverlay.classList.remove('pointer-events-none');
            settingsModalOverlay.classList.add('opacity-100');
            settingsModalBody.classList.remove('scale-95');
            settingsModalBody.classList.add('scale-100');
        }

        function closeSettingsModal() {
            settingsModalOverlay.classList.add('pointer-events-none');
            settingsModalOverlay.classList.remove('opacity-100');
            settingsModalBody.classList.add('scale-95');
            settingsModalBody.classList.remove('scale-100');
        }

        // Toast Messages
        let toastTimeout;
        function showToast(message, type = 'success') {
            clearTimeout(toastTimeout);
            toastMessage.innerText = message;
            
            if (type === 'success') {
                toastIconContainer.className = "p-1.5 rounded-lg bg-emerald-500/20 text-emerald-400 border border-emerald-500/30";
                toastIconContainer.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4"></i>';
            } else {
                toastIconContainer.className = "p-1.5 rounded-lg bg-red-500/20 text-red-400 border border-red-500/30";
                toastIconContainer.innerHTML = '<i data-lucide="alert-triangle" class="w-4 h-4"></i>';
            }

            toastNotification.classList.remove('translate-y-24', 'opacity-0');
            lucide.createIcons();

            toastTimeout = setTimeout(() => {
                closeToast();
            }, 4000);
        }

        function closeToast() {
            toastNotification.classList.add('translate-y-24', 'opacity-0');
        }

        // Escape helper for HTML rendering security
        function escapeHtml(string) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(string).replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        // Events listeners mapping
        btnNewNote.addEventListener('click', openCreateDrawer);
        btnEmptyStateNewNote.addEventListener('click', openCreateDrawer);
        btnCloseDrawer.addEventListener('click', closeDrawer);
        btnCancelNote.addEventListener('click', closeDrawer);
        btnSaveNote.addEventListener('click', saveNote);
        btnDeleteNote.addEventListener('click', deleteNote);

        btnSettings.addEventListener('click', openSettingsModal);
        btnCloseSettings.addEventListener('click', closeSettingsModal);
        btnSaveKeys.addEventListener('click', saveCredentials);
        btnClearKeys.addEventListener('click', clearCredentials);

        btnCancelDelete.addEventListener('click', closeDeleteModal);
        btnConfirmDelete.addEventListener('click', executeDeleteNote);

        btnCloseToast.addEventListener('click', closeToast);

        // Backdrop Dismissal Click
        drawerOverlay.addEventListener('click', (e) => {
            if (e.target === drawerOverlay) closeDrawer();
        });
        settingsModalOverlay.addEventListener('click', (e) => {
            if (e.target === settingsModalOverlay) closeSettingsModal();
        });
        deleteModalOverlay.addEventListener('click', (e) => {
            if (e.target === deleteModalOverlay) closeDeleteModal();
        });

        // Search Input Logic
        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            searchQuery = e.target.value.trim();
            
            if (searchQuery) {
                btnClearSearch.classList.remove('hidden');
            } else {
                btnClearSearch.classList.add('hidden');
            }

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentPage = 1;
                fetchNotes();
            }, 600); // Debounce search
        });

        btnClearSearch.addEventListener('click', () => {
            searchInput.value = '';
            searchQuery = '';
            btnClearSearch.classList.add('hidden');
            currentPage = 1;
            fetchNotes();
        });

        // Category Filter Badge Navigation
        document.querySelectorAll('.category-pill').forEach(pill => {
            pill.addEventListener('click', () => {
                document.querySelectorAll('.category-pill').forEach(p => {
                    p.className = "category-pill px-4 py-2 text-xs font-semibold rounded-full bg-darkCard border border-white/5 text-gray-400 hover:text-white hover:border-white/10 transition-all whitespace-nowrap";
                });
                pill.className = "category-pill px-4 py-2 text-xs font-semibold rounded-full bg-accentPurp text-white border border-purple-500/30 transition-all whitespace-nowrap";
                
                selectedCategory = pill.getAttribute('data-category');
                currentPage = 1;
                fetchNotes();
            });
        });

        // Pagination Events
        btnPrevPage.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                fetchNotes();
            }
        });
        btnNextPage.addEventListener('click', () => {
            currentPage++;
            fetchNotes();
        });
        limitSelect.addEventListener('change', (e) => {
            perPage = parseInt(e.target.value);
            currentPage = 1;
            fetchNotes();
        });

        // Toggle Password Inputs Eye icon
        document.querySelectorAll('.toggle-password-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const input = btn.previousElementSibling;
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                
                // Toggle Eye Icon
                const icon = btn.querySelector('i');
                if (type === 'text') {
                    btn.innerHTML = '<i data-lucide="eye-off" class="w-4 h-4"></i>';
                } else {
                    btn.innerHTML = '<i data-lucide="eye" class="w-4 h-4"></i>';
                }
                lucide.createIcons();
            });
        });
    </script>
</body>
</html>
