<?php
// admin/comments_content.php
?>
<div class="space-y-8">
    <h1 class="text-3xl font-condensed font-black italic uppercase text-white">TERRACE MODERATION</h1>

    <div class="bg-[#0a0e17] rounded-2xl border border-white/10 overflow-hidden shadow-2xl overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead class="bg-white/5 text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">
                <tr>
                    <th class="px-8 py-4">Author & Content</th>
                    <th class="px-8 py-4">Target Post</th>
                    <th class="px-8 py-4">Status</th>
                    <th class="px-8 py-4 text-right">Moderation Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php foreach ($comments as $comment): ?>
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-8 py-6">
                            <p class="font-black text-[#ff3e3e] uppercase italic text-sm"><?php echo e($comment['author']); ?></p>
                            <p class="text-[10px] text-gray-500 mt-1 mb-2"><?php echo format_date($comment['date']); ?></p>
                            <p class="text-sm text-white font-medium line-clamp-2 max-w-md">"<?php echo e($comment['text']); ?>"</p>
                        </td>
                        <td class="px-8 py-6 text-xs font-bold text-gray-400 uppercase tracking-tighter">
                            <?php echo e($comment['post_title'] ?: "Post ID: " . $comment['post_id']); ?>
                        </td>
                        <td class="px-8 py-6">
                            <?php
                            $status = $comment['status'];
                            $badge_class = 'bg-yellow-500/10 text-yellow-400';
                            if ($status === 'approved') $badge_class = 'bg-green-500/10 text-green-400';
                            if ($status === 'rejected') $badge_class = 'bg-red-500/10 text-red-400';
                            if ($status === 'spam') $badge_class = 'bg-orange-500/10 text-orange-400';
                            ?>
                            <span class="<?php echo $badge_class; ?> px-2 py-0.5 rounded text-[10px] font-black uppercase"><?php echo $status; ?></span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end space-x-2">
                                <form method="POST" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                    <input type="hidden" name="id" value="<?php echo $comment['id']; ?>">
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" name="update_status" title="Approve" class="p-2 rounded-lg bg-white/5 text-green-500 hover:bg-green-500 hover:text-white transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    </button>
                                </form>
                                <form method="POST" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                    <input type="hidden" name="id" value="<?php echo $comment['id']; ?>">
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" name="update_status" title="Reject" class="p-2 rounded-lg bg-white/5 text-red-500 hover:bg-red-500 hover:text-white transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </form>
                                <form method="POST" onsubmit="return confirm('Delete this comment?');" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                    <input type="hidden" name="id" value="<?php echo $comment['id']; ?>">
                                    <button type="submit" name="delete_comment" title="Delete" class="p-2 rounded-lg bg-white/5 text-gray-500 hover:bg-black hover:text-white transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($comments)): ?>
                    <tr><td colspan="4" class="px-8 py-20 text-center italic text-gray-600 uppercase font-black">No comments found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
