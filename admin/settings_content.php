<?php
// admin/settings_content.php
?>
<div class="max-w-4xl space-y-8">
    <h1 class="text-3xl font-oswald font-bold uppercase text-white">System Settings</h1>

    <?php if ($success): ?>
        <div class="alert alert-success rounded-0 font-condensed fw-bold italic text-uppercase"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="bg-[#0a0e17] rounded-lg shadow-sm border border-white/10 overflow-hidden">
        <form method="POST" class="p-8 space-y-6" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-full">
                    <h3 class="text-xs font-black text-[#ff3e3e] uppercase tracking-widest border-bottom border-white border-opacity-5 pb-2 mb-4">General Settings</h3>
                </div>
                <label class="block">
                    <span class="text-sm font-bold uppercase text-gray-500">Website Name</span>
                    <input type="text" name="name" class="mt-1 block w-full bg-white/5 border border-white/10 rounded px-4 py-2 text-white" value="<?php echo e($settings['name']); ?>">
                </label>
                <label class="block">
                    <span class="text-sm font-bold uppercase text-gray-500">Tagline</span>
                    <input type="text" name="tagline" class="mt-1 block w-full bg-white/5 border border-white/10 rounded px-4 py-2 text-white" value="<?php echo e($settings['tagline']); ?>">
                </label>
                <div class="col-span-full">
                    <span class="text-sm font-bold uppercase text-gray-500">Site Logo</span>
                    <div class="mt-2 flex items-center space-x-4">
                        <?php if ($settings['logo']): ?>
                            <img src="<?php echo e($settings['logo']); ?>" alt="Logo" class="h-12 bg-gray-800 p-2 rounded">
                        <?php endif; ?>
                        <input type="file" name="logo_file" class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-white/10 file:text-white hover:file:bg-white/20">
                    </div>
                    <input type="text" name="logo_url" placeholder="Or enter Logo URL" class="mt-2 block w-full bg-white/5 border border-white/10 rounded px-4 py-2 text-white text-xs" value="<?php echo e($settings['logo']); ?>">
                </div>
                <label class="block">
                    <span class="text-sm font-bold uppercase text-gray-500">WhatsApp Contact</span>
                    <input type="text" name="whatsapp_number" class="mt-1 block w-full bg-white/5 border border-white/10 rounded px-4 py-2 text-white" value="<?php echo e($settings['whatsapp_number']); ?>">
                </label>

                <div class="col-span-full mt-4">
                    <h3 class="text-xs font-black text-[#ff3e3e] uppercase tracking-widest border-bottom border-white border-opacity-5 pb-2 mb-4">Email & SMTP Settings</h3>
                </div>
                <label class="block">
                    <span class="text-sm font-bold uppercase text-gray-500">Admin Email</span>
                    <input type="email" name="admin_email" class="mt-1 block w-full bg-white/5 border border-white/10 rounded px-4 py-2 text-white" value="<?php echo e($settings['admin_email']); ?>">
                </label>
                <label class="block">
                    <span class="text-sm font-bold uppercase text-gray-500">SMTP Sender Address</span>
                    <input type="email" name="smtp_sender" class="mt-1 block w-full bg-white/5 border border-white/10 rounded px-4 py-2 text-white" value="<?php echo e($settings['smtp_sender']); ?>">
                </label>
                <label class="block">
                    <span class="text-sm font-bold uppercase text-gray-500">SMTP Host</span>
                    <input type="text" name="smtp_host" class="mt-1 block w-full bg-white/5 border border-white/10 rounded px-4 py-2 text-white" value="<?php echo e($settings['smtp_host'] ?? ''); ?>">
                </label>
                <label class="block">
                    <span class="text-sm font-bold uppercase text-gray-500">SMTP Port</span>
                    <input type="number" name="smtp_port" class="mt-1 block w-full bg-white/5 border border-white/10 rounded px-4 py-2 text-white" value="<?php echo e($settings['smtp_port'] ?? 587); ?>">
                </label>
                <label class="block">
                    <span class="text-sm font-bold uppercase text-gray-500">SMTP User</span>
                    <input type="text" name="smtp_user" class="mt-1 block w-full bg-white/5 border border-white/10 rounded px-4 py-2 text-white" value="<?php echo e($settings['smtp_user'] ?? ''); ?>">
                </label>
                <label class="block">
                    <span class="text-sm font-bold uppercase text-gray-500">SMTP Password</span>
                    <input type="password" name="smtp_pass" class="mt-1 block w-full bg-white/5 border border-white/10 rounded px-4 py-2 text-white" value="<?php echo e($settings['smtp_pass'] ?? ''); ?>">
                </label>
                <label class="block">
                    <span class="text-sm font-bold uppercase text-gray-500">SMTP Encryption (tls/ssl)</span>
                    <input type="text" name="smtp_encryption" class="mt-1 block w-full bg-white/5 border border-white/10 rounded px-4 py-2 text-white" value="<?php echo e($settings['smtp_encryption'] ?? 'tls'); ?>">
                </label>

                <div class="col-span-full mt-4">
                    <h3 class="text-xs font-black text-[#ff3e3e] uppercase tracking-widest border-bottom border-white border-opacity-5 pb-2 mb-4">Social Media Handles</h3>
                </div>
                <label class="block">
                    <span class="text-sm font-bold uppercase text-gray-500">Facebook URL</span>
                    <input type="text" name="fb_url" class="mt-1 block w-full bg-white/5 border border-white/10 rounded px-4 py-2 text-white" value="<?php echo e($settings['fb_url']); ?>">
                </label>
                <label class="block">
                    <span class="text-sm font-bold uppercase text-gray-500">Twitter URL</span>
                    <input type="text" name="tw_url" class="mt-1 block w-full bg-white/5 border border-white/10 rounded px-4 py-2 text-white" value="<?php echo e($settings['tw_url']); ?>">
                </label>
                <label class="block">
                    <span class="text-sm font-bold uppercase text-gray-500">Instagram URL</span>
                    <input type="text" name="ig_url" class="mt-1 block w-full bg-white/5 border border-white/10 rounded px-4 py-2 text-white" value="<?php echo e($settings['ig_url']); ?>">
                </label>
                <label class="block">
                    <span class="text-sm font-bold uppercase text-gray-500">YouTube URL</span>
                    <input type="text" name="yt_url" class="mt-1 block w-full bg-white/5 border border-white/10 rounded px-4 py-2 text-white" value="<?php echo e($settings['yt_url']); ?>">
                </label>

                <div class="col-span-full mt-4">
                    <h3 class="text-xs font-black text-[#ff3e3e] uppercase tracking-widest border-bottom border-white border-opacity-5 pb-2 mb-4">Autopost API Keys</h3>
                </div>
                <label class="block">
                    <span class="text-sm font-bold uppercase text-gray-500">Facebook Access Token</span>
                    <input type="text" name="fb_access_token" class="mt-1 block w-full bg-white/5 border border-white/10 rounded px-4 py-2 text-white" value="<?php echo e($settings['fb_access_token']); ?>">
                </label>
                <label class="block">
                    <span class="text-sm font-bold uppercase text-gray-500">Twitter API Key</span>
                    <input type="text" name="tw_api_key" class="mt-1 block w-full bg-white/5 border border-white/10 rounded px-4 py-2 text-white" value="<?php echo e($settings['tw_api_key']); ?>">
                </label>
                <label class="block">
                    <span class="text-sm font-bold uppercase text-gray-500">Twitter API Secret</span>
                    <input type="password" name="tw_api_secret" class="mt-1 block w-full bg-white/5 border border-white/10 rounded px-4 py-2 text-white" value="<?php echo e($settings['tw_api_secret']); ?>">
                </label>
                <label class="block">
                    <span class="text-sm font-bold uppercase text-gray-500">LinkedIn Access Token</span>
                    <input type="text" name="li_access_token" class="mt-1 block w-full bg-white/5 border border-white/10 rounded px-4 py-2 text-white" value="<?php echo e($settings['li_access_token']); ?>">
                </label>
                <label class="block">
                    <span class="text-sm font-bold uppercase text-gray-500">Meta/Insta Access Token</span>
                    <input type="text" name="meta_access_token" class="mt-1 block w-full bg-white/5 border border-white/10 rounded px-4 py-2 text-white" value="<?php echo e($settings['meta_access_token']); ?>">
                </label>
            </div>

            <div class="pt-6 border-t border-white/5 flex justify-end">
                <button type="submit" class="bg-[#ff3e3e] text-white px-10 py-3 rounded-xl font-black uppercase italic tracking-widest shadow-xl hover:scale-105 transition-transform">
                    Save All Changes
                </button>
            </div>
        </form>
    </div>
</div>
