<?php
// migrate/includes/footer.php
?>
        </main>
      </div>

      <!-- MOBILE OFFCANVAS SIDEBAR -->
      <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarOffcanvas">
        <div class="offcanvas-header border-bottom border-white border-opacity-5">
          <h5 class="offcanvas-title font-condensed fw-black italic">GLOBAL FOOTBALL</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column">
          <nav class="nav flex-column mb-auto overflow-y-auto no-scrollbar">
            <a href="/" class="nav-link text-white fw-black text-uppercase py-2">HOME</a>
            <p class="text-[9px] font-black text-gray-600 uppercase tracking-[0.2em] mb-2 px-3 mt-4">Categories</p>
            <?php
            $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
            $nav_categories = $stmt->fetchAll();
            foreach ($nav_categories as $cat): ?>
              <a
                href="/category/<?php echo e($cat['slug']); ?>"
                class="nav-link text-white-50 hover-white fw-bold text-uppercase py-2 px-3"
                style="font-size: 11px;"
              >
                <?php echo e($cat['name']); ?>
              </a>
            <?php endforeach; ?>

            <p class="text-[9px] font-black text-gray-600 uppercase tracking-[0.2em] mb-2 px-3 mt-4">Pages</p>
            <?php
            $stmt = $pdo->query("SELECT * FROM pages ORDER BY title ASC");
            $nav_pages = $stmt->fetchAll();
            foreach ($nav_pages as $page): ?>
              <a
                href="/page/<?php echo e($page['slug']); ?>"
                class="nav-link text-white-50 hover-white fw-bold text-uppercase py-2 px-3"
                style="font-size: 11px;"
              >
                <?php echo e($page['title']); ?>
              </a>
            <?php endforeach; ?>
          </nav>
          <div class="pt-3 border-top border-white border-opacity-5">
            <a href="/admin/login" class="nav-link text-secondary fw-black text-uppercase" style="font-size: 12px;">ADMIN LOGIN</a>
          </div>
        </div>
      </div>

      <!-- WHATSAPP FLOAT -->
      <a
        href="https://wa.me/<?php echo e($settings['whatsapp_number']); ?>"
        target="_blank"
        class="position-fixed bottom-0 end-0 m-4 btn btn-success rounded-circle shadow-lg p-3 z-3 d-flex align-items-center justify-content-center"
        style="width: 60px; height: 60px;"
      >
        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/></svg>
      </a>
    </div>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
