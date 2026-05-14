<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Walkwel AttendMS - @yield('title', 'Dashboard')</title>
    
    <!-- PWA Mobile App Support -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#007bff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Walkwel">
    <link rel="apple-touch-icon" href="https://ui-avatars.com/api/?name=Walkwel&background=007bff&color=fff&size=192">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar { transition: all 0.3s ease; }
        
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); z-index: 1000; position: fixed; height: 100vh; }
            .sidebar.show { transform: translateX(0); }
        }
        
        @media (min-width: 769px) {
            .sidebar.show { width: 0; overflow: hidden; border: none; padding: 0; margin: 0; }
            .sidebar.show ~ .main-wrapper { margin-left: 0; }
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
                <div class="sidebar-logo" style="display: flex; align-items: center; gap: 12px; padding: 10px 0;">
                    <svg width="42" height="42" viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg" style="flex-shrink: 0;">
                      <g fill="white" stroke="white" stroke-width="13" stroke-linecap="round">
                        <line x1="28" y1="38" x2="46" y2="82" />
                        <line x1="48" y1="38" x2="66" y2="82" />
                        <circle cx="76" cy="38" r="6.5" stroke="none" />
                        <line x1="92" y1="38" x2="92" y2="82" />
                        <circle cx="108" cy="38" r="6.5" stroke="none" />
                      </g>
                    </svg>
                    <div style="display: flex; flex-direction: column; line-height: 1.2; padding-top: 2px;">
                        <span style="font-size: 1.15rem; font-weight: 700; color: white;">Walkwel</span>
                        <span style="font-size: 1.15rem; font-weight: 700; color: white;">AttendMS</span>
                    </div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <style>
                    .nav-section { cursor: pointer; display: flex; justify-content: space-between; align-items: center; user-select: none; }
                    .nav-group { overflow: hidden; transition: max-height 0.3s ease; }
                    .nav-section i.caret { transition: transform 0.3s ease; }
                    .nav-section.collapsed i.caret { transform: rotate(-90deg); }
                </style>
                <script>
                    function toggleNavGroup(id, element) {
                        const group = document.getElementById(id);
                        if (group) {
                            if (group.style.maxHeight && group.style.maxHeight !== '0px') {
                                group.style.maxHeight = '0px';
                                element.classList.add('collapsed');
                            } else {
                                group.style.maxHeight = group.scrollHeight + 'px';
                                element.classList.remove('collapsed');
                            }
                        }
                    }
                    
                    document.addEventListener('DOMContentLoaded', function() {
                        document.querySelectorAll('.nav-group').forEach(group => {
                            if (!group.querySelector('.nav-item.active')) { 
                                group.style.maxHeight = '0px';
                                const section = group.previousElementSibling;
                                if(section && section.classList.contains('nav-section')) section.classList.add('collapsed');
                            } else {
                                group.style.maxHeight = group.scrollHeight + 'px';
                            }
                        });
                    });
                </script>

                <div class="nav-section" onclick="toggleNavGroup('nav-main', this)">Main <i class="fas fa-chevron-down caret"></i></div>
                <div id="nav-main" class="nav-group">
                    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        Dashboard
                    </a>
                </div>
                
                <div class="nav-section" onclick="toggleNavGroup('nav-my-data', this)">My Data <i class="fas fa-chevron-down caret"></i></div>
                <div id="nav-my-data" class="nav-group">
                    <a href="{{ route('attendance.index') }}" class="nav-item {{ request()->routeIs('attendance.index') ? 'active' : '' }}">
                        My Attendance
                    </a>
                    <a href="{{ route('attendance.calendar') }}" class="nav-item {{ request()->routeIs('attendance.calendar') ? 'active' : '' }}">
                        Calendar
                    </a>
                    <a href="{{ route('regularization.index') }}" class="nav-item {{ request()->routeIs('regularization.*') ? 'active' : '' }}">
                        Regularization
                    </a>
                    <a href="{{ route('leaves.index') }}" class="nav-item {{ request()->routeIs('leaves.index') ? 'active' : '' }}">
                        My Leaves
                        @php $empApproved = \App\Models\LeaveRequest::where('user_id', auth()->id())->where('status', 'approved')->where('updated_at', '>', session('last_seen_leaves_at', now()->subDays(3)))->count(); @endphp
                        @if($empApproved > 0) <span style="margin-left:auto; background:#10b981; box-shadow: 0 0 8px #10b981; width:8px; height:8px; border-radius:50%; display:inline-block;" title="Leave successfully approved recently!"></span> @endif
                    </a>

                </div>

                @if(auth()->user()->hasAnyRole(['manager', 'admin', 'hr', 'super_admin']))
                    <div class="nav-section" onclick="toggleNavGroup('nav-approvals', this)">Team & Approvals <i class="fas fa-chevron-down caret"></i></div>
                    <div id="nav-approvals" class="nav-group">
                        <a href="{{ route('attendance.team') }}" class="nav-item {{ request()->routeIs('attendance.team') ? 'active' : '' }}">
                            Team Attendance
                        </a>
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
                    </div>
                @endif

                @if(auth()->user()->hasAnyRole(['admin', 'hr', 'super_admin']))
                    <div class="nav-section" onclick="toggleNavGroup('nav-admin', this)">HR Administration <i class="fas fa-chevron-down caret"></i></div>
                    <div id="nav-admin" class="nav-group">
                        <a href="{{ route('org.dashboard') }}" class="nav-item {{ request()->routeIs('org.dashboard') ? 'active' : '' }}">
                            Org Dashboard  
                            @if(isset($pc) && $pc > 0) <span style="margin-left:auto; background:#10b981; box-shadow: 0 0 8px #10b981; width:8px; height:8px; border-radius:50%; display:inline-block;" title="Leaves Pending"></span> @endif
                        </a>
                        <a href="{{ route('employees.index') }}" class="nav-item {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                            Employees
                        </a>
                        <a href="{{ route('roles.index') }}" class="nav-item {{ request()->routeIs('roles.index') ? 'active' : '' }}">
                            Roles & Permissions
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
                    </div>
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
                    <form action="{{ route('logout') }}" method="POST" style="margin-left:auto;" onsubmit="return confirm('Are you sure you want to securely log out?');">
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
                    <!-- Mobile Brand Replacement -->
                    <div class="mobile-brand" style="align-items:center; gap:8px;">
                        <svg width="28" height="28" viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">
                            <g fill="var(--primary)" stroke="var(--primary)" stroke-width="13" stroke-linecap="round">
                            <line x1="28" y1="38" x2="46" y2="82" />
                            <line x1="48" y1="38" x2="66" y2="82" />
                            <circle cx="76" cy="38" r="6.5" stroke="none" />
                            <line x1="92" y1="38" x2="92" y2="82" />
                            <circle cx="108" cy="38" r="6.5" stroke="none" />
                            </g>
                        </svg>
                        <div style="display:flex; flex-direction:column; line-height:1.1;">
                            <span style="font-size:0.9rem; font-weight:800; color:var(--text);">Walkwel</span>
                            <span style="font-size:0.9rem; font-weight:800; color:var(--text); opacity:0.8;">AttendMS</span>
                        </div>
                    </div>
                    <button class="topbar-back-btn" onclick="history.back()" style="margin-right: 1rem; border: none; background: transparent; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; width: 35px; height: 35px; border-radius: 50%; transition: all 0.2s;" onmouseover="this.style.background='var(--border)';" onmouseout="this.style.background='transparent';" title="Go Back">
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
                            <input type="hidden" name="latitude" value="">
                            <input type="hidden" name="longitude" value="">
                            <button type="button" onclick="performLocationCheck(this.form)" class="btn btn-checkin"><span class="desktop-text">Web </span>Check-in</button>
                        </form>
                    @elseif($todayAtt->check_in && !$todayAtt->check_out)
                        <form action="{{ route('attendance.check_out') }}" method="POST" style="display:inline;">
                            @csrf
                            <input type="hidden" name="latitude" value="">
                            <input type="hidden" name="longitude" value="">
                            <button type="button" onclick="performLocationCheck(this.form)" class="btn btn-checkout"><span class="desktop-text">Web </span>Check-out</button>
                        </form>
                    @else
                        <button class="btn btn-outline" disabled>Check-in Done</button>
                    @endif
                    
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to securely log out?');">
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

    <!-- Mobile Bottom Navigation Structure -->
    <div class="mobile-submenu-overlay" id="mobile-submenu-overlay" onclick="closeAllMobileMenus()"></div>

    <!-- My Data Submenu -->
    <div class="mobile-submenu" id="mobile-submenu-my-data">
        <div class="mobile-submenu-title">My Data</div>
        <div class="submenu-grid">
            <a href="{{ route('attendance.index') }}" class="submenu-item" onclick="closeAllMobileMenus()"><i class="fas fa-user-check"></i> Attendance</a>
            <a href="{{ route('attendance.calendar') }}" class="submenu-item" onclick="closeAllMobileMenus()"><i class="fas fa-calendar-alt"></i> Calendar</a>
            <a href="{{ route('regularization.index') }}" class="submenu-item" onclick="closeAllMobileMenus()"><i class="fas fa-clock"></i> Checks</a>
            <a href="{{ route('leaves.index') }}" class="submenu-item" onclick="closeAllMobileMenus()"><i class="fas fa-plane-departure"></i> Leaves</a>
        </div>
    </div>

    @if(auth()->user()->hasAnyRole(['manager', 'admin', 'hr', 'super_admin']))
    <!-- Team & Approvals Submenu -->
    <div class="mobile-submenu" id="mobile-submenu-team">
        <div class="mobile-submenu-title">Team & Approvals</div>
        <div class="submenu-grid">
            <a href="{{ route('attendance.team') }}" class="submenu-item" onclick="closeAllMobileMenus()"><i class="fas fa-users"></i> Team Attd.</a>
            <a href="{{ route('leaves.approvals') }}" class="submenu-item" onclick="closeAllMobileMenus()"><i class="fas fa-check-circle"></i> Leaves</a>
            <a href="{{ route('regularization.approvals') }}" class="submenu-item" onclick="closeAllMobileMenus()"><i class="fas fa-check-square"></i> Regularize</a>
            <a href="{{ route('hr.probations') }}" class="submenu-item" onclick="closeAllMobileMenus()"><i class="fas fa-chart-line"></i> Performance</a>
        </div>
    </div>
    @endif

    @if(auth()->user()->hasAnyRole(['admin', 'hr', 'super_admin']))
    <!-- HR Admin Submenu -->
    <div class="mobile-submenu" id="mobile-submenu-admin">
        <div class="mobile-submenu-title">HR Administration</div>
        <div class="submenu-grid">
            <a href="{{ route('org.dashboard') }}" class="submenu-item" onclick="closeAllMobileMenus()"><i class="fas fa-building"></i> Dashboard</a>
            <a href="{{ route('employees.index') }}" class="submenu-item" onclick="closeAllMobileMenus()"><i class="fas fa-user-tie"></i> Employees</a>
            <a href="{{ route('roles.index') }}" class="submenu-item" onclick="closeAllMobileMenus()"><i class="fas fa-user-shield"></i> Roles</a>
            <a href="{{ route('attendance.report') }}" class="submenu-item" onclick="closeAllMobileMenus()"><i class="fas fa-file-alt"></i> Reports</a>
        </div>
    </div>
    @endif

    <nav class="mobile-bottom-nav">
        <a href="{{ route('dashboard') }}" class="mobile-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" onclick="closeAllMobileMenus()">
            <i class="fas fa-home"></i>
            <span>Main</span>
        </a>
        <div class="mobile-nav-item {{ request()->routeIs('attendance.*') || request()->routeIs('regularization.index') || request()->routeIs('leaves.index') ? 'active' : '' }}" onclick="toggleMobileMenu('my-data')">
            <i class="fas fa-user-clock"></i>
            <span>My Data</span>
        </div>
        
        @if(auth()->user()->hasAnyRole(['manager', 'admin', 'hr', 'super_admin']))
        <div class="mobile-nav-item {{ request()->routeIs('attendance.team') || request()->routeIs('leaves.approvals') || request()->routeIs('regularization.approvals') || request()->routeIs('hr.probations') ? 'active' : '' }}" onclick="toggleMobileMenu('team')">
            <i class="fas fa-users-cog"></i>
            <span>Team</span>
        </div>
        @endif

        @if(auth()->user()->hasAnyRole(['admin', 'hr', 'super_admin']))
        <div class="mobile-nav-item {{ request()->routeIs('org.dashboard') || request()->routeIs('employees.*') || request()->routeIs('roles.*') || request()->routeIs('reports.*') ? 'active' : '' }}" onclick="toggleMobileMenu('admin')">
            <i class="fas fa-building"></i>
            <span>Admin</span>
        </div>
        @endif
    </nav>

    <!-- JS -->
    <script>
        function toggleMobileMenu(id) {
            const menu = document.getElementById('mobile-submenu-' + id);
            const overlay = document.getElementById('mobile-submenu-overlay');
            if (menu && menu.classList.contains('show')) {
                closeAllMobileMenus();
            } else {
                closeAllMobileMenus();
                if(menu) menu.classList.add('show');
                if(overlay) overlay.classList.add('show');
            }
        }

        function closeAllMobileMenus() {
            document.querySelectorAll('.mobile-submenu').forEach(m => m.classList.remove('show'));
            const overlay = document.getElementById('mobile-submenu-overlay');
            if(overlay) overlay.classList.remove('show');
        }

        // Live Clock
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' });
            document.getElementById('live-clock').textContent = timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Sidebar Toggle & Dismissal
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        // Close sidebar when clicking on the content overlay (outside sidebar) on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                const sidebar = document.getElementById('sidebar');
                const toggleBtn = e.target.closest('.toggle-sidebar');
                if (sidebar.classList.contains('show') && !sidebar.contains(e.target) && !toggleBtn) {
                    sidebar.classList.remove('show');
                }
            }
        });

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

        // HTML5 Geolocation Interceptor for Check-in / Check-out
        function performLocationCheck(form) {
            if (navigator.geolocation) {
                const btn = form.querySelector('button');
                const origText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Locating...';
                btn.disabled = true;

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        form.querySelector('input[name="latitude"]').value = position.coords.latitude;
                        form.querySelector('input[name="longitude"]').value = position.coords.longitude;
                        form.submit();
                    },
                    function(error) {
                        // Denied or failed GPS. Submit anyway to let the Server-side Wi-Fi IP validation decide.
                        console.warn("GPS Unavailable. Falling back strictly to Wi-Fi IP validation.");
                        form.submit(); 
                    },
                    { timeout: 8000, enableHighAccuracy: true }
                );
            } else {
                form.submit();
            }
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
            
            if (link.classList.contains('nav-item')) {
                // Ensure sidebar gracefully closes on mobile after selection
                if (window.innerWidth <= 768) {
                    document.getElementById('sidebar').classList.remove('show');
                }
                
                document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
                link.classList.add('active');
                
                // Instantly clear notification dots/badges visually for the clicked item
                const notifSpans = link.querySelectorAll('span');
                notifSpans.forEach(span => {
                    if (span.style.borderRadius) span.remove();
                });
            }

            // Sync Mobile Bottom Nav Active States dynamically
            if (link.classList.contains('submenu-item') || link.classList.contains('mobile-nav-item')) {
                document.querySelectorAll('.mobile-nav-item').forEach(el => el.classList.remove('active'));
                
                if (link.classList.contains('mobile-nav-item') && !link.hasAttribute('onclick')) {
                    link.classList.add('active'); // 'Main' tab directly maps
                } else {
                    const parentMenu = link.closest('.mobile-submenu');
                    if (parentMenu) {
                        const menuId = parentMenu.id.replace('mobile-submenu-', '');
                        const matchingTab = Array.from(document.querySelectorAll('.mobile-nav-item')).find(el => el.getAttribute('onclick') && el.getAttribute('onclick').includes(menuId));
                        if (matchingTab) matchingTab.classList.add('active');
                    }
                }
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
                        if (typeof initMobileCardToggles === 'function') setTimeout(initMobileCardToggles, 50);
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

        function initMobileCardToggles() {
            document.querySelectorAll('.card').forEach(card => {
                if (card.querySelector('.mobile-toggle-header')) return; // Already explicitly processed
                if (card.classList.contains('hero-card') || card.classList.contains('leave-card')) return;
                
                let header = card.querySelector('.card-header');
                if (!header) return;
                
                // Do not auto-collapse complex headers with active buttons (like generic_modules with Add New)
                // This prevents the dynamic chevron from corrupting the flex layout and overlapping the action radius.
                if (header.querySelector('button') || header.querySelector('.btn')) return;
                
                let body = card.querySelector('.card-body, .table-responsive, .calendar-grid, ul');
                if (!body) return;
                
                body.classList.add('mobile-collapsible-body');
                
                let title = header.querySelector('h3, .card-title');
                if (title && !header.querySelector('.mobile-chevron')) {
                    title.style.display = 'flex';
                    title.style.justifyContent = 'space-between';
                    title.style.alignItems = 'center';
                    title.style.width = '100%';
                    title.insertAdjacentHTML('beforeend', '<i class="fas fa-chevron-down mobile-chevron" style="transition:0.3s; transform:rotate(-90deg);"></i>');
                }
                
                header.classList.add('mobile-toggle-header');
                header.onclick = function(e) {
                    if (e.target.closest('a') || e.target.closest('button')) return;
                    let isHidden = window.getComputedStyle(body).display === 'none';
                    body.style.display = isHidden ? 'block' : 'none';
                    let icon = header.querySelector('.mobile-chevron');
                    if (icon) icon.style.transform = isHidden ? 'rotate(0deg)' : 'rotate(-90deg)';
                };
            });
        }
        document.addEventListener('DOMContentLoaded', initMobileCardToggles);
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

    <!-- PWA Installation Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                .then(reg => console.log('Walkwel Mobile App registered successfully.'))
                .catch(err => console.warn('PWA registration failed: ', err));
            });
        }
    </script>
</body>
</html>
