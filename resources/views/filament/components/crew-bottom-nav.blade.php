@if(auth()->check() && auth()->user()->role === 'crew')
<nav class="crew-bottom-nav-bar" id="crewBottomNavBar">
    @php
        $isHome = request()->path() === '' || request()->path() === 'admin' || request()->is('*/');
        $isAbsensi = request()->is('*absensi-saya*');
    @endphp

    <a href="{{ url('/') }}" class="crew-nav-link {{ $isHome ? 'active' : '' }}">
        <div class="crew-nav-icon-wrap">
            <i class="ph-fill ph-house"></i>
        </div>
        <span class="crew-nav-text">Beranda</span>
        @if($isHome) <div class="crew-nav-indicator"></div> @endif
    </a>

    <a href="{{ url('/absensi-saya') }}" class="crew-nav-link {{ $isAbsensi ? 'active' : '' }}">
        <div class="crew-nav-icon-wrap">
            <i class="ph-bold ph-fingerprint"></i>
        </div>
        <span class="crew-nav-text">Absensi</span>
        @if($isAbsensi) <div class="crew-nav-indicator"></div> @endif
    </a>

    <a href="{{ url('/absensi-saya#tugas') }}" class="crew-nav-link">
        <div class="crew-nav-icon-wrap">
            <i class="ph-bold ph-calendar-check"></i>
        </div>
        <span class="crew-nav-text">Tugas SPK</span>
    </a>

    <form action="{{ filament()->getLogoutUrl() }}" method="POST" style="margin: 0; padding: 0; display: contents;">
        @csrf
        <button type="submit" class="crew-nav-link crew-logout-btn" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
            <div class="crew-nav-icon-wrap">
                <i class="ph-bold ph-sign-out"></i>
            </div>
            <span class="crew-nav-text">Keluar</span>
        </button>
    </form>
</nav>

<style>
    /* ==========================================================
       MOBILE 1ST CREW BOTTOM NAVIGATION BAR
       ========================================================== */
    .crew-bottom-nav-bar {
        position: fixed !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        height: 64px !important;
        background: rgba(15, 16, 22, 0.94) !important;
        backdrop-filter: blur(18px) !important;
        -webkit-backdrop-filter: blur(18px) !important;
        border-top: 1px solid rgba(207, 162, 75, 0.25) !important;
        box-shadow: 0 -4px 24px rgba(0, 0, 0, 0.6) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-around !important;
        z-index: 99999 !important;
        padding: 0 12px !important;
        padding-bottom: env(safe-area-inset-bottom, 0px) !important;
    }

    /* Reserve space at page bottom so footer/buttons aren't obscured */
    body.fi-body {
        padding-bottom: 78px !important;
    }

    .crew-nav-link {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        text-decoration: none !important;
        color: #7d8496 !important;
        flex: 1 !important;
        height: 100% !important;
        padding: 6px 0 !important;
        position: relative !important;
        background: transparent !important;
        border: none !important;
        cursor: pointer !important;
        transition: all 0.15s ease-in-out !important;
        -webkit-tap-highlight-color: transparent !important;
    }

    .crew-nav-icon-wrap {
        font-size: 20px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin-bottom: 2px !important;
        transition: transform 0.15s ease !important;
    }

    .crew-nav-text {
        font-size: 11px !important;
        font-weight: 600 !important;
        letter-spacing: 0.02em !important;
        text-transform: capitalize !important;
        font-family: 'Inter', sans-serif !important;
    }

    .crew-nav-link:hover,
    .crew-nav-link.active {
        color: #cfa24b !important;
    }

    .crew-nav-link.active .crew-nav-icon-wrap {
        transform: translateY(-2px) !important;
        color: #cfa24b !important;
    }

    .crew-nav-indicator {
        position: absolute !important;
        top: 0 !important;
        width: 32px !important;
        height: 3px !important;
        background: #cfa24b !important;
        border-radius: 0 0 3px 3px !important;
        box-shadow: 0 2px 8px rgba(207, 162, 75, 0.6) !important;
    }

    .crew-logout-btn {
        color: #e57373 !important;
    }
    .crew-logout-btn:hover {
        color: #ff5252 !important;
    }

    /* On mobile screens for crew: hide desktop sidebar toggle button to keep UI app-like */
    @media (max-width: 1023px) {
        body.fi-body .fi-topbar-sidebar-toggle-btn {
            display: none !important;
        }
        .fi-sidebar {
            display: none !important;
        }
    }
</style>
@endif
