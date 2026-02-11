<?php
// migrate/admin/profile_content.php
?>
<div class="max-w-xl mx-auto space-y-8">
    <h1 class="text-3xl font-condensed font-black italic uppercase text-white">Identity Management</h1>

    <?php if ($success): ?>
        <div class="alert alert-success rounded-0 font-condensed fw-bold italic text-uppercase"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="bg-[#0a0e17] border border-white/5 p-10 rounded-3xl shadow-2xl">
        <div class="flex items-center space-x-6 mb-10 border-b border-white/5 pb-10">
            <div class="w-20 h-20 rounded-full bg-[#ff3e3e] flex items-center justify-center text-3xl font-black text-white italic shadow-lg">
                <?php echo strtoupper(substr($user['email'], 0, 2)); ?>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white uppercase italic">Site Administrator</h2>
                <p class="text-[10px] text-gray-500 font-black uppercase tracking-widest"><?php echo e($user['email']); ?></p>
            </div>
        </div>

        <form method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div>
                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 block">Email Address</label>
                <input
                    type="email"
                    name="email"
                    required
                    class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-[#ff3e3e]"
                    value="<?php echo e($user['email']); ?>"
                >
            </div>

            <div>
                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 block">New Password (leave blank to keep current)</label>
                <input
                    type="password"
                    name="new_password"
                    class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-[#ff3e3e]"
                    placeholder="NEW SECURE PASSWORD"
                >
            </div>

            <button type="submit" name="update_profile" class="w-full bg-[#ff3e3e] text-white py-4 rounded-2xl font-black uppercase italic tracking-widest hover:bg-white hover:text-black transition-all shadow-xl">
                Update Identity
            </button>
        </form>
    </div>
</div>
