<?php
// includes/admin_layout.php

function render_admin_layout($content_file, $pdo, $settings, $extra_data = []) {
    extract($extra_data);
    $active_page = basename($_SERVER['PHP_SELF'], '.php');
    if (isset($GLOBALS['admin_page'])) $active_page = $GLOBALS['admin_page'];

    $menuItems = [
        ['name' => 'Dashboard', 'path' => '/admin', 'id' => 'dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['name' => 'Editorial', 'path' => '/admin/posts', 'id' => 'posts', 'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v12a2 2 0 01-2 2'],
        ['name' => 'Categories', 'path' => '/admin/categories', 'id' => 'categories', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16'],
        ['name' => 'Pages', 'path' => '/admin/pages', 'id' => 'pages', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['name' => 'Subscribers', 'path' => '/admin/subscribers', 'id' => 'subscribers', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
        ['name' => 'Moderation', 'path' => '/admin/comments', 'id' => 'comments', 'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
        ['name' => 'API Engine', 'path' => '/admin/api-manager', 'id' => 'api-manager', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
        ['name' => 'Systems', 'path' => '/admin/settings', 'id' => 'settings', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066'],
        ['name' => 'Identity', 'path' => '/admin/profile', 'id' => 'profile', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
    ];

    ?>
    <!DOCTYPE html>
    <html lang="en" data-bs-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Panel | <?php echo e($settings['name']); ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Barlow+Condensed:wght@700;800&display=swap" rel="stylesheet">
        <style>
            :root { --pitch-dark: #05070a; --electric-red: #ff3e3e; }
            body {
                font-family: 'Inter', sans-serif;
                background-color: var(--pitch-dark);
                color: #d1d5db;
                text-rendering: optimizeLegibility;
                -webkit-font-smoothing: antialiased;
            }
            h1, h2, h3, h4, h5, h6, .font-condensed {
                font-family: 'Barlow Condensed', sans-serif;
                text-transform: uppercase;
                font-weight: 800;
                letter-spacing: -0.01em;
            }
            .text-electric-red { color: var(--electric-red); }
            .admin-nav-link {
                transition: all 0.2s ease-in-out;
            }
            .admin-nav-link:hover {
                transform: translateX(4px);
            }
        </style>
    </head>
    <body class="bg-[#05070a] text-gray-300">
        <div class="min-h-screen flex flex-col md:flex-row">
            <!-- MOBILE TOP BAR -->
            <div class="md:hidden bg-[#0a0e17] border-b border-white/5 p-4 flex items-center justify-between sticky top-0 z-[110]">
                <a href="/" class="text-xl font-condensed font-black text-white italic tracking-tighter flex items-center text-decoration-none">
                    <span class="bg-[#ff3e3e] text-white px-1.5 py-0.5 rounded mr-2 not-italic text-sm">GFW</span>
                    ADMIN
                </a>
                <button id="mobileMenuBtn" class="p-2 text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>
                </button>
            </div>

            <!-- ADMIN SIDEBAR -->
            <aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-[#0a0e17] border-r border-white/5 flex-shrink-0 flex flex-col z-[120] transition-transform duration-300 -translate-x-full md:translate-x-0 md:static">
                <div class="p-8 hidden md:block">
                    <a href="/" class="text-2xl font-condensed font-black text-white italic tracking-tighter flex items-center text-decoration-none">
                        <span class="bg-[#ff3e3e] text-white px-2 py-0.5 rounded mr-2 not-italic">GFW</span>
                        ADMIN
                    </a>
                </div>

                <nav class="mt-4 flex-grow px-4 overflow-y-auto">
                    <p class="text-[10px] font-black text-gray-600 uppercase tracking-[0.3em] mb-6 pl-4">Management</p>
                    <div class="space-y-2">
                        <?php foreach ($menuItems as $item): ?>
                            <a
                                href="<?php echo $item['path']; ?>"
                                class="admin-nav-link flex items-center px-4 py-3 rounded-xl group text-decoration-none <?php echo $active_page === $item['id'] ? 'bg-[#ff3e3e] text-white shadow-[0_0_20px_rgba(255,62,62,0.3)]' : 'hover:bg-white/5 hover:text-white text-gray-300'; ?>"
                            >
                                <svg class="w-5 h-5 mr-3 opacity-70 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo $item['icon']; ?>" />
                                </svg>
                                <span class="text-xs font-bold uppercase tracking-widest"><?php echo $item['name']; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </nav>

                <div class="p-6 md:p-8 border-t border-white/5 space-y-4">
                    <a href="/" class="flex items-center text-[10px] font-black text-gray-500 hover:text-white transition-colors uppercase tracking-widest text-decoration-none">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Public Site
                    </a>
                    <a href="/admin/logout" class="flex items-center text-[10px] font-black text-[#ff3e3e] hover:text-white transition-colors uppercase tracking-widest w-full text-left text-decoration-none">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        Logout
                    </a>
                </div>
            </aside>

            <main class="flex-grow p-4 md:p-12 overflow-y-auto bg-gradient-to-br from-[#0a0e17] to-[#05070a]">
                <div class="max-w-6xl mx-auto">
                    <?php include $content_file; ?>
                </div>
            </main>
        </div>

        <script>
            document.getElementById('mobileMenuBtn')?.addEventListener('click', function() {
                document.getElementById('sidebar').classList.toggle('-translate-x-full');
            });
        </script>
    </body>
    </html>
    <?php
}
