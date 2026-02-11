<?php
// admin/subscribers_content.php
?>
<div class="space-y-8">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-condensed font-black italic uppercase text-white">Fanbase Network</h1>
        <div class="bg-[#ff3e3e] text-white px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest"><?php echo count($subscribers); ?> ACTIVE</div>
    </div>

    <div class="bg-[#0a0e17] rounded-2xl border border-white/10 overflow-hidden shadow-2xl">
        <table class="w-full text-left">
            <thead class="bg-white/5 text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">
                <tr>
                    <th class="px-8 py-4">Email Address</th>
                    <th class="px-8 py-4">Joined Date</th>
                    <th class="px-8 py-4 text-right">Control</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php foreach ($subscribers as $sub): ?>
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-8 py-6 font-bold text-white text-sm"><?php echo e($sub['email']); ?></td>
                        <td class="px-8 py-6 text-gray-500 text-xs font-bold uppercase"><?php echo format_date($sub['date_joined']); ?></td>
                        <td class="px-8 py-6 text-right">
                            <form method="POST" onsubmit="return confirm('Unsubscribe this user?');" class="inline">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <input type="hidden" name="email" value="<?php echo e($sub['email']); ?>">
                                <button type="submit" name="delete_subscriber" class="text-gray-500 hover:text-[#ff3e3e]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($subscribers)): ?>
                    <tr><td colspan="3" class="px-8 py-20 text-center italic text-gray-600 uppercase font-black">No subscribers found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
