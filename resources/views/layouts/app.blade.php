<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'AttendMS') }}</title>
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar { transition: all 0.3s ease; }
        
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); z-index: 1000; position: fixed; height: 100vh; }
            .sidebar.show { transform: translateX(0); }
        }
        
        @media (min-width: 769px) {
            .sidebar.show { width: 80px; }
            .sidebar.show .nav-item span,
            .sidebar.show .sidebar-logo span,
            .sidebar.show .nav-section,
            .sidebar.show .user-info { display: none; }
            .sidebar.show ~ .main-wrapper { margin-left: 80px; }
        }
    </style>
</head>
<body>

    @if(session('success'))
        <div class="flash-message flash-success" id="flash-message">
            <span>{{ session('success') }}</span>
            <button class="btn-close" onclick="closeFlash()">&times;</button>
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div class="flash-message flash-error" id="flash-message">
            <span>{{ session('error') ?? $errors->first() }}</span>
            <button class="btn-close" onclick="closeFlash()">&times;</button>
        </div>
    @endif

    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <span>AttendMS</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">Main</div>
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
                
                <div class="nav-section">Attendance</div>
                <a href="{{ route('attendance.index') }}" class="nav-item {{ request()->routeIs('attendance.index') ? 'active' : '' }}">
                    My Attendance
                </a>
                <a href="{{ route('attendance.calendar') }}" class="nav-item {{ request()->routeIs('attendance.calendar') ? 'active' : '' }}">
                    Calendar
                </a>
                <a href="{{ route('regularization.index') }}" class="nav-item {{ request()->routeIs('regularization.*') ? 'active' : '' }}">
                    Regularization
                </a>
                @if(auth()->user()->hasAnyRole(['manager', 'admin', 'hr', 'super_admin']))
                    <a href="{{ route('attendance.team') }}" class="nav-item {{ request()->routeIs('attendance.team') ? 'active' : '' }}">
                        Team Attendance
                    </a>
                @endif
                
                <div class="nav-section">Leave</div>
                <a href="{{ route('leaves.index') }}" class="nav-item {{ request()->routeIs('leaves.index') ? 'active' : '' }}">
                    My Leaves
                    @php $empApproved = \App\Models\LeaveRequest::where('user_id', auth()->id())->where('status', 'approved')->where('updated_at', '>', session('last_seen_leaves_at', now()->subDays(3)))->count(); @endphp
                    @if($empApproved > 0) <span style="margin-left:auto; background:#10b981; box-shadow: 0 0 8px #10b981; width:8px; height:8px; border-radius:50%; display:inline-block;" title="Leave successfully approved recently!"></span> @endif
                </a>
                <a href="{{ route('holidays.index') }}" class="nav-item {{ request()->routeIs('holidays.index') ? 'active' : '' }}">
                    Holidays
                </a>

                @if(auth()->user()->hasAnyRole(['manager', 'admin', 'hr', 'super_admin']))
                    <div class="nav-section">Approvals</div>
                    <a href="{{ route('leaves.approvals') }}" class="nav-item {{ request()->routeIs('leaves.approvals') ? 'active' : '' }}">
                        Leave Approvals
                        @php 
                            if(auth()->user()->hasAnyRole(['hr', 'admin', 'super_admin'])) {
                                $pc = \App\Models\LeaveRequest::where('status', 'pending')->count(); 
                            } else {
                                $pc = \App\Models\LeaveRequest::whereIn('user_id', \App\Models\User::where('manager_id', auth()->id())->pluck('id'))->where('status', 'pending')->count();
                            }
                        @endphp
                        @if($pc > 0) <span style="margin-left:auto; background:#10b981; box-shadow: 0 0 8px #10b981; color:white; padding:2px 6px; border-radius:10px; font-size:10px;">{{ $pc }} New</span> @endif
                    </a>
                    <a href="{{ route('regularization.approvals') }}" class="nav-item {{ request()->routeIs('regularization.approvals') ? 'active' : '' }}">
                        Reg. Approvals
                    </a>
                    <a href="{{ route('hr.probations') }}" class="nav-item {{ request()->routeIs('hr.probations') ? 'active' : '' }}">
                        Team Performance
                    </a>
                @endif

                @if(auth()->user()->hasAnyRole(['admin', 'hr', 'super_admin']))
                    <div class="nav-section">Admin</div>
                    <a href="{{ route('org.dashboard') }}" class="nav-item {{ request()->routeIs('org.dashboard') ? 'active' : '' }}">
                        Org Dashboard
                        @if(isset($pc) && $pc > 0) <span style="margin-left:auto; background:#10b981; box-shadow: 0 0 8px #10b981; width:8px; height:8px; border-radius:50%; display:inline-block;" title="Leaves Pending"></span> @endif
                    </a>
                    <a href="{{ route('employees.index') }}" class="nav-item {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                        Employees
                    </a>
                    <a href="{{ route('attendance.report') }}" class="nav-item {{ request()->routeIs('attendance.report') ? 'active' : '' }}">
                        Daily Report
                    </a>
                    <a href="{{ route('leaves.types') }}" class="nav-item {{ request()->routeIs('leaves.types') ? 'active' : '' }}">
                        Leave Types
                    </a>
                    <a href="{{ route('reports.summary') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        Analytics
                    </a>
                @endif
            </nav>

            <div class="sidebar-footer">
                <div class="user-card">
                    <a href="{{ route('profile.show') }}" style="text-decoration:none; display:flex; align-items:center; gap:0.75rem; flex:1; overflow:hidden;">
                        <img src="{{ auth()->user()->avatar_url }}" alt="avatar" class="user-avatar">
                        <div class="user-info">
                            <div class="user-name">{{ auth()->user()->name }}</div>
                            <div class="user-role">{{ str_replace('_', ' ', auth()->user()->role) }}</div>
                        </div>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" style="margin-left:auto;">
                        @csrf
                        <button type="submit" class="btn-logout" title="Logout" style="font-size: 1.25rem;">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-wrapper">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="toggle-sidebar" onclick="toggleSidebar()" style="margin-right: 1rem;">
                        <i class="fas fa-bars"></i>
                    </button>
                    <button onclick="history.back()" style="margin-right: 1rem; border: none; background: transparent; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; width: 35px; height: 35px; border-radius: 50%; transition: all 0.2s;" onmouseover="this.style.background='var(--border)';" onmouseout="this.style.background='transparent';" title="Go Back">
                        <i class="fas fa-arrow-left" style="font-size: 1.1rem;"></i>
                    </button>
                    <h2 class="page-title" style="margin: 0;">@yield('title')</h2>
                </div>
                <div class="topbar-right">
                    <div class="clock-widget" id="live-clock">--:--:--</div>
                    
                    @php
                        $todayAtt = \App\Models\Attendance::where('user_id', auth()->id())->where('date', \Carbon\Carbon::today()->toDateString())->first();
                    @endphp

                    @if(!$todayAtt || !$todayAtt->check_in)
                        <form action="{{ route('attendance.check_in') }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-checkin">Web Check-in</button>
                        </form>
                    @elseif($todayAtt->check_in && !$todayAtt->check_out)
                        <form action="{{ route('attendance.check_out') }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-checkout">Web Check-out</button>
                        </form>
                    @else
                        <button class="btn btn-outline" disabled>Check-in Done</button>
                    @endif
                    
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline" style="border:none; font-size: 1.2rem; cursor: pointer; color: var(--text-muted);" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </header>

            <div class="content-area">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- JS -->
    <script>
        // Live Clock
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' });
            document.getElementById('live-clock').textContent = timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Sidebar Toggle
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        // Flash auto dismiss
        function closeFlash() {
            const flash = document.getElementById('flash-message');
            if (flash) {
                flash.classList.add('fade-out');
                setTimeout(() => flash.remove(), 500);
            }
        }

        setTimeout(closeFlash, 4000);

        // Tab switching
        function switchTab(tabId) {
            document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-link').forEach(el => el.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            event.currentTarget.classList.add('active');
        }
        
        // Modal
        function openModal(id) {
            document.getElementById(id).classList.add('show');
        }
        function closeModal(id) {
            document.getElementById(id).classList.remove('show');
        }

        // Seamless AJAX SPA Engine
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link || !link.getAttribute('href')) return;
            
            const href = link.getAttribute('href');
            if (href.startsWith('#') || href.startsWith('javascript:') || link.target === '_blank' || link.hasAttribute('download')) return;
            
            // Only intercept internal standard links
            if (link.origin !== window.location.origin) return;

            e.preventDefault();
            
            // Update sidebar highlighting instantly if applicable
            if (link.classList.contains('nav-item')) {
                document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
                link.classList.add('active');
                
                // Instantly clear notification dots/badges visually for the clicked item
                const notifSpans = link.querySelectorAll('span');
                notifSpans.forEach(span => {
                    // Only clear the simple notification dots or badges (avoid text/icons if they exist)
                    if (span.style.borderRadius) span.remove();
                });
            }

            // Visual feedback
            const contentArea = document.querySelector('.content-area');
            contentArea.style.opacity = '0.3';
            contentArea.style.pointerEvents = 'none';

            fetch(href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url; // Native redirect intercept
                        return;
                    }
                    if (!response.ok) throw new Error('Navigation failed');
                    return response.text();
                })
                .then(html => {
                    if (!html) return;
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Hot Swap Component
                    const newContent = doc.querySelector('.content-area');
                    if (newContent) {
                        contentArea.innerHTML = newContent.innerHTML;
                    }

                    // Hot Swap Topbar Title
                    const newTitle = doc.querySelector('.page-title');
                    if (newTitle) {
                        document.querySelector('.page-title').innerHTML = newTitle.innerHTML;
                    }
                    document.title = doc.title;

                    // Execute Injected Page Scripts (like Charts and Clocks)
                    doc.querySelectorAll('script:not([src])').forEach(oldScript => {
                        // Skip core layout scripts to prevent duplication overhead
                        if (!oldScript.innerText.includes('function updateClock()') && !oldScript.innerText.includes('Seamless AJAX')) {
                            const newScript = document.createElement('script');
                            newScript.text = oldScript.innerText;
                            document.body.appendChild(newScript);
                        }
                    });

                    // Restore interactions
                    contentArea.style.opacity = '1';
                    contentArea.style.pointerEvents = 'auto';
                    
                    // Push URL to browser history
                    window.history.pushState({}, '', href);
                    
                    // Ensure the sidebar stays positioned correctly
                    const activeNav = document.querySelector('.nav-item.active');
                    if (activeNav) {
                        sessionStorage.setItem('sidebarScroll', document.querySelector('.sidebar-nav').scrollTop);
                    }
                })
                .catch(err => {
                    // Absolute fallback strictly reloads
                    window.location.href = href;
                });
        });

        window.addEventListener('popstate', function() {
            window.location.reload();
        });
    </script>
    @stack('scripts')
    <script>
        // Track sidebar scroll position 
        const sidebarNav = document.querySelector('.sidebar-nav');
        if (sidebarNav) {
            sidebarNav.addEventListener('scroll', function() {
                sessionStorage.setItem('kekaSidebarScroll', sidebarNav.scrollTop);
            });
            
            // Restore immediately on load/recalc
            function restoreSidebarScroll() {
                const savedpos = sessionStorage.getItem('kekaSidebarScroll');
                if (savedpos) {
                    sidebarNav.scrollTop = savedpos;
                }
            }
            restoreSidebarScroll();
            
            // Also restore after SPA swaps just to be strictly resilient against browser repaints
            const observer = new MutationObserver(restoreSidebarScroll);
            const contentArea = document.querySelector('.content-area');
            if (contentArea) {
                observer.observe(contentArea, { childList: true, subtree: false });
            }
        }
    </script>
</body>
</html>
