<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500;1,600&display=swap" rel="stylesheet">
<script src="https://unpkg.com/@phosphor-icons/web"></script>

<style>
    /* ==========================================================
       GLOBAL CANVAS & GAP FIXES
       ========================================================== */
    :root {
        --bg-main: #0c0d11;
        --bg-sidebar: #111217;
        --bg-card: #15161d;
        --bg-card-hover: #191b24;
        --bg-input: #191b23;
        --border-subtle: #20232e;
        --border-focus: #cfa24b;
        --accent-gold: #cfa24b;
        --accent-gold-hover: #deb25b;
        --accent-gold-glow: rgba(207, 162, 75, 0.15);
        --text-primary: #f2f3f7;
        --text-secondary: #8b92a4;
        --text-muted: #5e6475;
    }

    html.fi, body.fi-body {
        background-color: var(--bg-main) !important;
        color: var(--text-primary) !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
    }

    /* Outer layout container */
    .fi-layout {
        background-color: var(--bg-main) !important;
        width: 100% !important;
        min-height: 100vh !important;
    }

    /* Main content container - remove any restrictive gap */
    .fi-main-ctn {
        background-color: var(--bg-main) !important;
        width: 100% !important;
        min-width: 0 !important;
        flex: 1 1 0% !important;
    }

    /* Eliminate the giant gap/void between sidebar and content */
    .fi-main {
        background-color: var(--bg-main) !important;
        max-width: 100% !important;
        width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        padding-left: 1.5rem !important;
        padding-right: 1.5rem !important;
        padding-top: 1.25rem !important;
        padding-bottom: 3rem !important;
    }

    @media (min-width: 1024px) {
        .fi-main {
            padding-left: 2rem !important;
            padding-right: 2rem !important;
            padding-top: 1.5rem !important;
        }
    }

    /* Page header & content spacing */
    .fi-page-header-main-ctn {
        padding-top: 0.5rem !important;
        padding-bottom: 1.5rem !important;
        gap: 1.5rem !important;
    }

    .fi-page-content {
        gap: 1.5rem !important;
    }

    /* ==========================================================
       SIDEBAR & TOPBAR LUXURY DARK THEME
       ========================================================== */
    .fi-sidebar {
        background-color: var(--bg-sidebar) !important;
        border-right: 1px solid var(--border-subtle) !important;
        box-shadow: 4px 0 24px rgba(0, 0, 0, 0.3) !important;
    }

    .fi-sidebar-header {
        background-color: var(--bg-sidebar) !important;
        border-bottom: 1px solid var(--border-subtle) !important;
        height: 4.25rem !important;
    }

    .fi-sidebar-header .fi-logo {
        font-family: 'Playfair Display', serif !important;
        letter-spacing: 0.05em !important;
        font-size: 1rem !important;
        color: var(--accent-gold) !important;
        font-weight: 700 !important;
    }

    .fi-sidebar-nav {
        padding-top: 1.25rem !important;
        padding-bottom: 2rem !important;
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }

    .fi-sidebar-group-label {
        color: var(--text-muted) !important;
        font-size: 0.6875rem !important;
        letter-spacing: 0.09em !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        margin-bottom: 0.35rem !important;
    }

    .fi-sidebar-item-btn {
        border-radius: 0.5rem !important;
        padding: 0.55rem 0.75rem !important;
        transition: all 0.15s ease-in-out !important;
    }

    .fi-sidebar-item-btn:hover {
        background-color: rgba(255, 255, 255, 0.04) !important;
    }

    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn,
    .fi-sidebar-item-active > .fi-sidebar-item-btn {
        background: linear-gradient(90deg, rgba(207, 162, 75, 0.16) 0%, rgba(207, 162, 75, 0.04) 100%) !important;
        border-left: 3px solid var(--accent-gold) !important;
    }

    .fi-sidebar-item.fi-active .fi-sidebar-item-label,
    .fi-sidebar-item.fi-active .fi-icon {
        color: var(--accent-gold) !important;
        font-weight: 600 !important;
    }

    .fi-sidebar-item-label {
        color: var(--text-secondary) !important;
        font-size: 0.875rem !important;
        font-weight: 500 !important;
    }

    .fi-topbar {
        background-color: var(--bg-sidebar) !important;
        border-bottom: 1px solid var(--border-subtle) !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25) !important;
    }

    /* ==========================================================
       TYPOGRAPHY & HEADINGS
       ========================================================== */
    .fi-header-heading,
    .c-header-title,
    .font-serif,
    h1.fi-header-heading {
        font-family: 'Playfair Display', Georgia, serif !important;
        color: var(--text-primary) !important;
        font-size: 1.85rem !important;
        font-weight: 600 !important;
        letter-spacing: -0.02em !important;
    }

    .fi-header-subheading {
        color: var(--text-secondary) !important;
        font-size: 0.875rem !important;
    }

    .fi-breadcrumbs {
        margin-bottom: 0.5rem !important;
    }

    .fi-breadcrumbs-item-label {
        font-size: 0.75rem !important;
        letter-spacing: 0.05em !important;
        text-transform: uppercase !important;
        color: var(--accent-gold) !important;
        font-weight: 600 !important;
    }

    /* ==========================================================
       CARDS, SECTIONS & WIDGETS
       ========================================================== */
    .fi-section,
    .fi-ta-ctn,
    .fi-widget {
        background-color: var(--bg-card) !important;
        border: 1px solid var(--border-subtle) !important;
        border-radius: 0.875rem !important;
        box-shadow: 0 4px 24px -2px rgba(0, 0, 0, 0.35) !important;
    }

    .fi-section-header {
        border-bottom: 1px solid var(--border-subtle) !important;
        padding: 1.15rem 1.5rem !important;
    }

    .fi-section-header-heading {
        font-family: 'Playfair Display', serif !important;
        font-size: 1.25rem !important;
        color: var(--text-primary) !important;
        font-weight: 600 !important;
    }

    .fi-section-content {
        padding: 1.5rem !important;
    }

    /* ==========================================================
       TABLES
       ========================================================== */
    .fi-ta-ctn {
        overflow: hidden !important;
    }

    .fi-ta-header-cell {
        background-color: #121318 !important;
        color: var(--text-secondary) !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.06em !important;
        border-bottom: 1px solid var(--border-subtle) !important;
        padding: 0.875rem 1rem !important;
    }

    .fi-ta-row {
        border-bottom: 1px solid #1a1c24 !important;
        transition: background-color 0.15s ease !important;
    }

    .fi-ta-row:hover {
        background-color: rgba(207, 162, 75, 0.035) !important;
    }

    .fi-ta-cell {
        color: var(--text-primary) !important;
        padding: 0.875rem 1rem !important;
    }

    .fi-ta-empty-state {
        background-color: transparent !important;
        padding: 3rem 1.5rem !important;
    }

    .fi-ta-empty-state-heading {
        color: var(--text-primary) !important;
        font-family: 'Playfair Display', serif !important;
    }

    .fi-ta-empty-state-description {
        color: var(--text-secondary) !important;
    }

    /* Search bar & filters */
    .fi-ta-search-input-wrp,
    .fi-input-wrp {
        background-color: var(--bg-input) !important;
        border: 1px solid var(--border-subtle) !important;
        border-radius: 0.5rem !important;
        color: var(--text-primary) !important;
    }

    .fi-input-wrp:focus-within,
    .fi-ta-search-input-wrp:focus-within {
        border-color: var(--border-focus) !important;
        box-shadow: 0 0 0 2px var(--accent-gold-glow) !important;
    }

    .fi-input {
        color: var(--text-primary) !important;
    }

    .fi-input::placeholder {
        color: var(--text-muted) !important;
    }

    .fi-fo-field-wrp-label label {
        color: var(--text-secondary) !important;
        font-size: 0.8125rem !important;
        font-weight: 500 !important;
    }

    /* ==========================================================
       BUTTONS & BADGES
       ========================================================== */
    .fi-btn-color-primary,
    .fi-btn-primary {
        background: linear-gradient(135deg, #d8ab54, #b78a36) !important;
        color: #0c0d10 !important;
        font-weight: 600 !important;
        border: none !important;
        box-shadow: 0 2px 10px rgba(207, 162, 75, 0.3) !important;
        border-radius: 0.5rem !important;
        transition: all 0.2s ease !important;
    }

    .fi-btn-color-primary:hover,
    .fi-btn-primary:hover {
        background: linear-gradient(135deg, #e4b760, #c49642) !important;
        box-shadow: 0 4px 16px rgba(207, 162, 75, 0.45) !important;
        transform: translateY(-1px);
    }

    .fi-badge-color-primary {
        background-color: rgba(207, 162, 75, 0.15) !important;
        color: #e5b960 !important;
        border: 1px solid rgba(207, 162, 75, 0.3) !important;
    }

    .fi-badge-color-success {
        background-color: rgba(62, 224, 138, 0.12) !important;
        color: #3ee08a !important;
        border: 1px solid rgba(62, 224, 138, 0.25) !important;
    }

    .fi-badge-color-gray {
        background-color: rgba(255, 255, 255, 0.06) !important;
        color: #c2c6d4 !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }

    /* ==========================================================
       MODALS, DROPDOWNS & SCROLLBAR
       ========================================================== */
    .fi-modal-window,
    .fi-dropdown-panel {
        background-color: var(--bg-card) !important;
        border: 1px solid var(--border-subtle) !important;
        box-shadow: 0 24px 48px -12px rgba(0, 0, 0, 0.75) !important;
        border-radius: 0.875rem !important;
    }

    .fi-modal-header {
        border-bottom: 1px solid var(--border-subtle) !important;
        padding: 1.25rem 1.5rem !important;
    }

    .fi-modal-heading {
        font-family: 'Playfair Display', serif !important;
        color: var(--text-primary) !important;
        font-size: 1.35rem !important;
    }

    .fi-modal-footer {
        border-top: 1px solid var(--border-subtle) !important;
        padding: 1rem 1.5rem !important;
    }

    /* Custom dark scrollbars */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    ::-webkit-scrollbar-track {
        background: transparent;
    }
    ::-webkit-scrollbar-thumb {
        background: #282b37;
        border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: var(--accent-gold);
    }
</style>
