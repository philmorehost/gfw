<?php
// migrate/admin/posts_content.php
?>

<div class="space-y-8">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-condensed font-black italic uppercase text-white">Editorial Control</h1>
        <div class="flex space-x-4">
            <a href="?add=1" class="bg-[#ff3e3e] text-white px-6 py-2 rounded-xl font-black uppercase italic flex items-center shadow-lg hover:scale-105 transition-transform text-decoration-none">
                Compose Post
            </a>
        </div>
    </div>

    <?php if ($show_form): ?>
        <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
        <div class="bg-[#0a0e17] p-8 rounded-2xl border border-white/10 space-y-8">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="hidden" name="id" value="<?php echo $edit_post ? $edit_post['id'] : ''; ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <label class="block">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">Headline</span>
                            <input type="text" name="title" required class="mt-1 block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white" value="<?php echo $edit_post ? e($edit_post['title']) : ''; ?>">
                        </label>

                        <div class="grid grid-cols-2 gap-4">
                            <label class="block">
                                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">Category</span>
                                <select name="category_id" required class="mt-1 block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white">
                                    <option value="">Select</option>
                                    <?php
                                    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
                                    $all_categories = $stmt->fetchAll();
                                    foreach ($all_categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" <?php echo ($edit_post && $edit_post['category_id'] == $cat['id']) ? 'selected' : ''; ?>><?php echo e($cat['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                            <div class="block">
                                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">Featured Image</span>
                                <input type="file" name="image_file" class="mt-1 block w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-white/10 file:text-white hover:file:bg-white/20">
                                <input type="text" name="image_url" placeholder="Or URL" class="mt-1 block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-xs text-white" value="<?php echo $edit_post ? e($edit_post['image']) : ''; ?>">
                            </div>
                        </div>

                        <div class="bg-white/5 p-6 rounded-2xl border border-white/5 space-y-4">
                            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-[#ff3e3e]">SEO Metadata</h3>
                            <input type="text" name="meta_title" placeholder="Meta Title" class="w-full bg-black/20 border border-white/5 rounded-lg px-4 py-2 text-xs text-white" value="<?php echo $edit_post ? e($edit_post['meta_title']) : ''; ?>">
                            <textarea name="meta_description" placeholder="Meta Description" class="w-full bg-black/20 border border-white/5 rounded-lg px-4 py-2 text-xs text-white" rows="2"><?php echo $edit_post ? e($edit_post['meta_description']) : ''; ?></textarea>
                            <input type="text" name="meta_keywords" placeholder="Keywords (comma separated)" class="w-full bg-black/20 border border-white/5 rounded-lg px-4 py-2 text-xs text-white" value="<?php echo $edit_post ? e($edit_post['meta_keywords']) : ''; ?>">
                        </div>
                    </div>

                    <div class="space-y-6">
                        <label class="block">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">Excerpt</span>
                            <textarea name="excerpt" class="mt-1 block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white" rows="3"><?php echo $edit_post ? e($edit_post['excerpt']) : ''; ?></textarea>
                        </label>
                        <label class="block">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">Content</span>
                            <textarea name="content" id="editor" class="mt-1 block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white min-h-[400px]"><?php echo $edit_post ? e($edit_post['content']) : ''; ?></textarea>
                        </label>
                        <script>
                            ClassicEditor
                                .create( document.querySelector( '#editor' ) )
                                .catch( error => {
                                    console.error( error );
                                } );
                        </script>
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" name="is_top_story" id="is_top_story" class="w-4 h-4 rounded border-gray-600 bg-transparent text-[#ff3e3e]" <?php echo ($edit_post && $edit_post['is_top_story']) ? 'checked' : ''; ?>>
                            <label for="is_top_story" class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">Top Story</label>
                        </div>
                        <label class="block">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">Publication Date</span>
                            <input type="date" name="date" class="mt-1 block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white" value="<?php echo $edit_post ? $edit_post['date'] : date('Y-m-d'); ?>">
                        </label>
                    </div>
                </div>

                <div class="flex justify-end space-x-6 mt-8">
                    <a href="/admin/posts" class="text-gray-500 font-black uppercase text-xs text-decoration-none flex items-center">Cancel</a>
                    <button type="submit" name="save_post" class="bg-[#ff3e3e] text-white px-10 py-3 rounded-xl font-black uppercase italic tracking-widest shadow-xl">Save & Notify</button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="bg-[#0a0e17] rounded-2xl border border-white/10 overflow-hidden shadow-2xl overflow-x-auto">
            <table class="w-full text-left min-w-[700px]">
                <thead class="bg-white/5 text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">
                    <tr>
                        <th class="px-8 py-4">Article</th>
                        <th class="px-8 py-4">Category</th>
                        <th class="px-8 py-4">Date</th>
                        <th class="px-8 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php foreach ($posts as $post): ?>
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-8 py-6">
                                <div class="flex items-center">
                                    <img src="<?php echo e($post['image']); ?>" class="w-10 h-10 rounded mr-4 object-cover" alt="" />
                                    <div>
                                        <span class="font-bold text-white uppercase italic text-sm"><?php echo e($post['title']); ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <?php
                                $cstmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
                                $cstmt->execute([$post['category_id']]);
                                $cname = $cstmt->fetchColumn();
                                ?>
                                <span class="bg-white/5 text-[9px] px-2 py-1 rounded font-black uppercase text-gray-400"><?php echo e($cname ?: 'Uncategorized'); ?></span>
                            </td>
                            <td class="px-8 py-6 text-[10px] font-bold text-gray-500 uppercase">
                                <?php echo $post['date']; ?>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center space-x-4">
                                    <a href="?edit=<?php echo $post['id']; ?>" class="text-gray-500 hover:text-white">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </a>
                                    <form method="POST" onsubmit="return confirm('Delete this post?');" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                        <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                                        <button type="submit" name="delete_post" class="text-gray-500 hover:text-red-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
