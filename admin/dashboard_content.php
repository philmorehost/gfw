<?php
// admin/dashboard_content.php
?>
<div class="space-y-10">
    <div>
        <h1 class="text-4xl font-oswald font-black uppercase text-white italic tracking-tighter">Command Center</h1>
        <p class="text-gray-500 uppercase tracking-widest font-bold mt-1" style="font-size: 10px;">Operational Intelligence & System Overview</p>
    </div>

    <!-- STATS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white/5 border border-white/10 p-8 rounded-3xl backdrop-blur-sm group hover:border-[#ff3e3e]/50 transition-all duration-500 shadow-2xl relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-[#ff3e3e]/5 rounded-full blur-3xl group-hover:bg-[#ff3e3e]/10 transition-colors"></div>
            <p class="text-xs font-black text-gray-500 uppercase tracking-[0.3em] mb-4">Total Intelligence</p>
            <div class="flex items-baseline space-x-2">
                <h2 class="text-5xl font-condensed font-black text-white italic"><?php echo number_format($total_posts); ?></h2>
                <span class="text-xs font-bold text-[#ff3e3e] uppercase italic">Stories</span>
            </div>
        </div>

        <div class="bg-white/5 border border-white/10 p-8 rounded-3xl backdrop-blur-sm group hover:border-[#ff3e3e]/50 transition-all duration-500 shadow-2xl relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-[#ff3e3e]/5 rounded-full blur-3xl group-hover:bg-[#ff3e3e]/10 transition-colors"></div>
            <p class="text-xs font-black text-gray-500 uppercase tracking-[0.3em] mb-4">Terrace Talk</p>
            <div class="flex items-baseline space-x-2">
                <h2 class="text-5xl font-condensed font-black text-white italic"><?php echo number_format($total_comments); ?></h2>
                <span class="text-xs font-bold text-[#ff3e3e] uppercase italic">Comments</span>
            </div>
        </div>

        <div class="bg-white/5 border border-white/10 p-8 rounded-3xl backdrop-blur-sm group hover:border-[#ff3e3e]/50 transition-all duration-500 shadow-2xl relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-[#ff3e3e]/5 rounded-full blur-3xl group-hover:bg-[#ff3e3e]/10 transition-colors"></div>
            <p class="text-xs font-black text-gray-500 uppercase tracking-[0.3em] mb-4">Pending Review</p>
            <div class="flex items-baseline space-x-2">
                <h2 class="text-5xl font-condensed font-black <?php echo $pending_comments > 0 ? 'text-[#ff3e3e]' : 'text-white'; ?> italic"><?php echo number_format($pending_comments); ?></h2>
                <span class="text-xs font-bold text-gray-500 uppercase italic">Awaiting</span>
            </div>
        </div>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="bg-white/5 border border-white/5 rounded-3xl p-10 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent pointer-events-none"></div>
        <h3 class="text-xs font-black text-[#ff3e3e] uppercase tracking-[0.4em] mb-8 border-b border-white/5 pb-4">Tactical Rapid Response</h3>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <a href="/admin/posts?add=1" class="flex flex-col items-center justify-center p-6 bg-white/5 rounded-2xl border border-white/5 hover:bg-[#ff3e3e] hover:text-white transition-all duration-300 group shadow-lg">
                <svg class="w-8 h-8 mb-3 opacity-60 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span class="text-[10px] font-black uppercase tracking-widest">New Post</span>
            </a>

            <a href="/admin/comments" class="flex flex-col items-center justify-center p-6 bg-white/5 rounded-2xl border border-white/5 hover:bg-[#ff3e3e] hover:text-white transition-all duration-300 group shadow-lg">
                <svg class="w-8 h-8 mb-3 opacity-60 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                </svg>
                <span class="text-[10px] font-black uppercase tracking-widest">Moderation</span>
            </a>

            <a href="/admin/settings" class="flex flex-col items-center justify-center p-6 bg-white/5 rounded-2xl border border-white/5 hover:bg-[#ff3e3e] hover:text-white transition-all duration-300 group shadow-lg">
                <svg class="w-8 h-8 mb-3 opacity-60 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
                <span class="text-[10px] font-black uppercase tracking-widest">Settings</span>
            </a>

            <a href="/admin/api-manager" class="flex flex-col items-center justify-center p-6 bg-white/5 rounded-2xl border border-white/5 hover:bg-[#ff3e3e] hover:text-white transition-all duration-300 group shadow-lg">
                <svg class="w-8 h-8 mb-3 opacity-60 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span class="text-[10px] font-black uppercase tracking-widest">API Engine</span>
            </a>
        </div>
    </div>
</div>
