<?php
// migrate/install/stage1.php

$php_version = phpversion();
$pdo_check = extension_loaded('pdo_mysql');
$curl_check = extension_loaded('curl');
$mbstring_check = extension_loaded('mbstring');

$ready = version_compare($php_version, '7.4.0', '>=') && $pdo_check && $curl_check && $mbstring_check;

?>
<div class="text-center mb-5">
    <h2 class="display-6 font-condensed fw-black italic text-white text-uppercase">Stage 1: System Check</h2>
    <p class="text-muted text-uppercase fw-bold ls-widest" style="font-size: 10px;">Verification of operational parameters</p>
</div>

<div class="card bg-dark border-white border-opacity-10 mb-4">
    <div class="card-body p-4">
        <ul class="list-group list-group-flush bg-transparent">
            <li class="list-group-item bg-transparent text-white border-white border-opacity-5 d-flex justify-content-between align-items-center">
                <span>PHP Version (>= 7.4.0)</span>
                <?php if (version_compare($php_version, '7.4.0', '>=')): ?>
                    <span class="badge bg-success rounded-0">PASS (<?php echo $php_version; ?>)</span>
                <?php else: ?>
                    <span class="badge bg-danger rounded-0">FAIL (<?php echo $php_version; ?>)</span>
                <?php endif; ?>
            </li>
            <li class="list-group-item bg-transparent text-white border-white border-opacity-5 d-flex justify-content-between align-items-center">
                <span>PDO MySQL Extension</span>
                <?php if ($pdo_check): ?>
                    <span class="badge bg-success rounded-0">PASS</span>
                <?php else: ?>
                    <span class="badge bg-danger rounded-0">FAIL</span>
                <?php endif; ?>
            </li>
            <li class="list-group-item bg-transparent text-white border-white border-opacity-5 d-flex justify-content-between align-items-center">
                <span>cURL Extension</span>
                <?php if ($curl_check): ?>
                    <span class="badge bg-success rounded-0">PASS</span>
                <?php else: ?>
                    <span class="badge bg-danger rounded-0">FAIL</span>
                <?php endif; ?>
            </li>
            <li class="list-group-item bg-transparent text-white border-white border-opacity-5 d-flex justify-content-between align-items-center">
                <span>Mbstring Extension</span>
                <?php if ($mbstring_check): ?>
                    <span class="badge bg-success rounded-0">PASS</span>
                <?php else: ?>
                    <span class="badge bg-danger rounded-0">FAIL</span>
                <?php endif; ?>
            </li>
        </ul>
    </div>
</div>

<?php if ($ready): ?>
    <div class="text-end">
        <a href="?stage=2" class="btn btn-danger rounded-0 fw-black italic text-uppercase px-5 py-3">Proceed to Database</a>
    </div>
<?php else: ?>
    <div class="alert alert-danger rounded-0 font-condensed fw-bold italic text-uppercase">
        Please resolve the requirements above to continue.
    </div>
<?php endif; ?>
