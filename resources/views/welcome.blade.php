<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'AttendMS') }} - Modern HR System</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0ea5e9;
            --secondary: #6366f1;
            --accent: #f43f5e;
            --bg-color: #0f172a;
            --surface: rgba(30, 41, 59, 0.7);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(14, 165, 233, 0.15), transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(99, 102, 241, 0.15), transparent 25%);
            background-attachment: fixed;
        }

        /* Glassmorphism Navbar */
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            padding: 1.5rem 4rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(12px);
            z-index: 100;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            animation: slideDown 0.8s ease backwards;
        }

        .logo {
            font-size: 1.75rem;
            font-weight: 800;
            background: linear-gradient(135deg, #38bdf8, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-links a {
            color: var(--text-main);
            text-decoration: none;
            margin-left: 2rem;
            font-weight: 500;
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .nav-links a.btn-login {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav-links a.btn-login:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .nav-links a.btn-register {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4);
        }

        .nav-links a.btn-register:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 6px 20px rgba(14, 165, 233, 0.6);
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0 2rem;
            position: relative;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 20%;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(99,102,241,0.2) 0%, transparent 70%);
            z-index: -1;
            filter: blur(50px);
            animation: pulseBg 8s infinite alternate;
        }

        .badge {
            background: rgba(244, 63, 94, 0.15);
            color: #fb7185;
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 2rem;
            border: 1px solid rgba(244, 63, 94, 0.3);
            animation: fadeIn 1s ease 0.2s backwards;
        }

        .hero h1 {
            font-size: 5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            animation: popIn 1s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.4s backwards;
        }

        .highlight {
            background: linear-gradient(135deg, #38bdf8, #a78bfa, #f43f5e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-size: 200% auto;
            animation: textShine 4s linear infinite;
        }

        .hero p {
            font-size: 1.25rem;
            color: var(--text-muted);
            max-width: 600px;
            margin-bottom: 3rem;
            animation: fadeIn 1s ease 0.6s backwards;
        }

        .cta-group {
            display: flex;
            gap: 1.5rem;
            animation: slideUp 1s ease 0.8s backwards;
        }

        .cta-main {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .cta-main:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(99, 102, 241, 0.6);
        }

        /* Floating 3D Cards */
        .floating-cards {
            display: flex;
            gap: 2rem;
            margin-top: 5rem;
            flex-wrap: wrap;
            justify-content: center;
            animation: fadeIn 1.5s ease 1s backwards;
        }

        .feature-card {
            background: var(--surface);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 24px;
            width: 250px;
            text-align: left;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
        }

        .feature-card:nth-child(1) { animation: float 6s ease-in-out infinite; }
        .feature-card:nth-child(2) { animation: float 6s ease-in-out infinite 2s; }
        .feature-card:nth-child(3) { animation: float 6s ease-in-out infinite 4s; }

        .feature-card:hover {
            transform: translateY(-15px) scale(1.05) rotate(2deg);
            border-color: rgba(56, 189, 248, 0.5);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 0 20px rgba(56, 189, 248, 0.2);
            z-index: 10;
        }

        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .icon-blue { background: rgba(56, 189, 248, 0.2); color: #38bdf8; }
        .icon-purple { background: rgba(167, 139, 250, 0.2); color: #a78bfa; }
        .icon-rose { background: rgba(244, 63, 94, 0.2); color: #f43f5e; }

        .feature-card h3 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .feature-card p {
            font-size: 0.95rem;
            color: var(--text-muted);
            margin: 0;
            line-height: 1.4;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes popIn {
            from { opacity: 0; transform: scale(0.9) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        @keyframes slideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }

        @keyframes pulseBg {
            0% { transform: translateX(-50%) scale(1); opacity: 0.5; }
            100% { transform: translateX(-50%) scale(1.2); opacity: 1; }
        }

        @keyframes textShine {
            to { background-position: 200% center; }
        }

        /* Particles container */
        .particles {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            overflow: hidden; z-index: -2; pointer-events: none;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: rise linear infinite;
        }

        @keyframes rise {
            from { transform: translateY(100vh) scale(0); opacity: 0; }
            50% { opacity: 1; }
            to { transform: translateY(-10vh) scale(1); opacity: 0; }
        }

    </style>
</head>
<body>

    <div class="particles" id="particles"></div>

    <nav>
        <div class="logo">
            AttendMS
        </div>
        <div class="nav-links">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-register">Go to Dashboard </a>
            @else
                <a href="{{ route('login') }}" class="btn-login">Log in</a>

            @endauth
        </div>
    </nav>

    <div class="hero">
        <div class="badge">AttendMS 2026 Edition</div>
        <h1>Experience the Future <br>of <span class="highlight">HR Management</span></h1>
        <p>Empower your workforce with beautiful, dynamic attendance tracking, seamless leave approvals, and breathtaking analytics all in one platform.</p>
        
        <div class="cta-group">
            @auth
                <a href="{{ url('/dashboard') }}" class="cta-main">
                    Enter Workspace </a>
            @else
                <a href="{{ route('login') }}" class="cta-main">
                    Sign In Now </a>
            @endauth
        </div>

        <div class="floating-cards">
            <div class="feature-card">
                <div class="card-icon icon-blue"></div>
                <h3>Smart Attendance</h3>
                <p>Web check-ins with real-time tracking, IPs, and regularizations styled to perfection.</p>
            </div>
            <div class="feature-card">
                <div class="card-icon icon-purple"></div>
                <h3>Visual Leaves</h3>
                <p>Apply for leaves dynamically, see upcoming holidays, and interact with managers seamlessly.</p>
            </div>
            <div class="feature-card">
                <div class="card-icon icon-rose"></div>
                <h3>Rich Analytics</h3>
                <p>Enterprise-grade dashboards with Keka-inspired metric visualization and demographics.</p>
            </div>
        </div>
    </div>

    <script>
        // Generate rising particles for background effect
        const particlesContainer = document.getElementById('particles');
        for (let i = 0; i < 30; i++) {
            const particle = document.createElement('div');
            particle.classList.add('particle');
            
            // Random properties
            const size = Math.random() * 15 + 5;
            const left = Math.random() * 100;
            const duration = Math.random() * 10 + 10;
            const delay = Math.random() * 10;
            
            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;
            particle.style.left = `${left}%`;
            particle.style.animationDuration = `${duration}s`;
            particle.style.animationDelay = `${delay}s`;
            
            particlesContainer.appendChild(particle);
        }
    </script>
</body>
</html>
