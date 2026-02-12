<?php
// admin/api_manager_content.php
?>
<div class="max-w-4xl space-y-10">
    <div>
        <h1 class="text-3xl font-oswald font-black uppercase text-white">API Engine Management</h1>
        <p class="text-gray-500 uppercase tracking-widest font-bold mt-1" style="font-size: 10px;">Configure Tactical Data Feed & Match Intelligence</p>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success rounded-0 font-condensed fw-bold italic text-uppercase shadow-lg border-0 bg-green-600/20 text-green-400">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <!-- API CONFIG -->
        <div class="bg-white/5 border border-white/10 rounded-3xl p-8 shadow-2xl">
            <h3 class="text-xs font-black text-[#ff3e3e] uppercase tracking-widest border-b border-white/5 pb-4 mb-6">Feed Configuration</h3>
            <form method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                <label class="block">
                    <span class="text-[10px] font-black uppercase text-gray-500 tracking-widest">API-Sports Key</span>
                    <input type="text" name="api_key" class="mt-2 block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#ff3e3e] focus:ring-0 transition-colors" value="<?php echo e($settings['api_key']); ?>">
                </label>

                <label class="block">
                    <span class="text-[10px] font-black uppercase text-gray-500 tracking-widest">Base API URL</span>
                    <input type="text" name="api_url" class="mt-2 block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#ff3e3e] focus:ring-0 transition-colors" value="<?php echo e($settings['api_url']); ?>">
                </label>

                <label class="block">
                    <span class="text-[10px] font-black uppercase text-gray-500 tracking-widest">Header Name</span>
                    <input type="text" name="api_header" class="mt-2 block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#ff3e3e] focus:ring-0 transition-colors" value="<?php echo e($settings['api_header']); ?>">
                </label>

                <div class="pt-4">
                    <button type="submit" name="save_api_settings" class="w-full bg-[#ff3e3e] text-white py-4 rounded-xl font-black uppercase italic tracking-widest shadow-xl hover:scale-[1.02] transition-transform">
                        Update Engine
                    </button>
                </div>
            </form>
        </div>

        <!-- CACHE MANAGEMENT -->
        <div class="bg-white/5 border border-white/10 rounded-3xl p-8 shadow-2xl flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-black text-[#ff3e3e] uppercase tracking-widest border-b border-white/5 pb-4 mb-6">Intelligence Cache</h3>
                <p class="text-gray-400 text-xs leading-relaxed mb-6">
                    Match fixtures, league tables, and top scorer data are cached locally to optimize performance and reduce API overhead. Clearing the cache will force the system to download fresh intelligence from the feed providers.
                </p>
                <div class="p-4 bg-white/5 rounded-2xl border border-white/5 mb-8">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Cache Status</span>
                        <span class="px-3 py-1 bg-green-600/20 text-green-400 rounded-full text-[9px] font-black uppercase">Active</span>
                    </div>
                </div>
            </div>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <button type="submit" name="clear_cache" class="w-full border border-white/10 text-white py-4 rounded-xl font-black uppercase italic tracking-widest hover:bg-white/5 transition-colors">
                    Flush API Cache
                </button>
            </form>
        </div>
    </div>
</div>
