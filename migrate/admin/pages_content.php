<?php
// migrate/admin/pages_content.php
?>
<div class="space-y-8">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-condensed font-black italic uppercase text-white">Site Pages</h1>
        <a href="?add=1" class="bg-[#ff3e3e] text-white px-6 py-2 rounded-xl font-black uppercase italic flex items-center shadow-lg hover:scale-105 transition-transform text-decoration-none">
            New Page
        </a>
    </div>

    <?php if ($show_form): ?>
        <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
        <div class="bg-[#0a0e17] p-8 rounded-2xl border border-white/10 space-y-8">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="hidden" name="id" value="<?php echo $edit_page ? $edit_page['id'] : ''; ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <label class="block">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">Page Title</span>
                        <input type="text" name="title" required class="mt-1 block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white" value="<?php echo $edit_page ? e($edit_page['title']) : ''; ?>">
                    </label>
                    <label class="block">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">Slug</span>
                        <input type="text" name="slug" class="mt-1 block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white" value="<?php echo $edit_page ? e($edit_page['slug']) : ''; ?>">
                    </label>
                    <div class="col-span-full">
                        <label class="block">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">Page Content</span>
                            <textarea name="content" id="editor" class="mt-1 block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white"><?php echo $edit_page ? e($edit_page['content']) : ''; ?></textarea>
                        </label>
                        <script>
                            ClassicEditor
                                .create( document.querySelector( '#editor' ) )
                                .catch( error => {
                                    console.error( error );
                                } );
                        </script>
                    </div>
                </div>

                <div class="flex justify-end space-x-6 mt-8">
                    <a href="/admin/pages" class="text-gray-500 font-black uppercase text-xs text-decoration-none flex items-center">Cancel</a>
                    <button type="submit" name="save_page" class="bg-[#ff3e3e] text-white px-10 py-3 rounded-xl font-black uppercase italic tracking-widest shadow-xl">Save Page</button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="bg-[#0a0e17] rounded-2xl border border-white/10 overflow-hidden shadow-2xl">
            <table class="w-full text-left">
                <thead class="bg-white/5 text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">
                    <tr>
                        <th class="px-8 py-4">Title</th>
                        <th class="px-8 py-4">Slug</th>
                        <th class="px-8 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php foreach ($pages as $page): ?>
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-8 py-6 text-white font-bold italic uppercase"><?php echo e($page['title']); ?></td>
                            <td class="px-8 py-6 text-gray-500 text-xs font-mono"><?php echo e($page['slug']); ?></td>
                            <td class="px-8 py-6">
                                <div class="flex items-center space-x-4">
                                    <a href="?edit=<?php echo $page['id']; ?>" class="text-gray-500 hover:text-white">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </a>
                                    <form method="POST" onsubmit="return confirm('Delete this page?');" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                        <input type="hidden" name="id" value="<?php echo $page['id']; ?>">
                                        <button type="submit" name="delete_page" class="text-gray-500 hover:text-red-500">
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
