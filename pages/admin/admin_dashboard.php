<?php
require_once __DIR__ . "/../../config/helpers.php";
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../middleware/auth.php";
require_once __DIR__ . "/../../repositories/admin-repository.php";

sessionStart();

// Proteksi halaman: hanya admin yang boleh masuk
if (($_SESSION['role'] ?? '') !== 'admin') {
    redirect('/pages/auth/login.php');
}

$existingTables = getAllDatabaseTables($pdo);
$page           = $_GET['page'] ?? 'dashboard';
$adminName      = $_SESSION['username'] ?? 'Admin';

$successMsg = $_SESSION['admin_success'] ?? null;
$errorMsg   = $_SESSION['admin_error'] ?? null;
unset($_SESSION['admin_success'], $_SESSION['admin_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Meeple Space</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy-dark: #1E325C;
            --navy-card: #223866;
            --sidebar-bg: #E2F1F8;
            --yellow-accent: #EAB308;
            --yellow-hover: #CA8A04;
        }
        body { font-family: 'Poppins', sans-serif; background-color: #F4F7FA; margin: 0; }
        .wrapper { display: flex; min-height: 100vh; }
        
        /* Sidebar */
        .sidebar {
            width: 260px; background-color: var(--sidebar-bg);
            display: flex; flex-direction: column; justify-content: space-between;
            padding: 22px 16px; flex-shrink: 0;
        }
        .brand-logo {
            display: flex; align-items: center; gap: 10px; font-weight: 700;
            font-size: 1.2rem; color: var(--navy-dark); text-decoration: none; margin-bottom: 20px;
        }
        .nav-section-title {
            font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
            color: #64748B; margin: 14px 0 6px 12px; letter-spacing: 0.5px;
        }
        .nav-menu { list-style: none; padding: 0; margin: 0; }
        .nav-menu li { margin-bottom: 4px; }
        .nav-menu a {
            display: flex; align-items: center; gap: 10px; padding: 9px 14px;
            border-radius: 10px; color: var(--navy-dark); font-weight: 500;
            font-size: 0.85rem; text-decoration: none; transition: 0.2s;
        }
        .nav-menu a.active, .nav-menu a:hover {
            background-color: var(--yellow-accent); color: #111827; font-weight: 600;
        }

        /* Main Area */
        .main-area { flex-grow: 1; display: flex; flex-direction: column; min-width: 0; }
        .top-header {
            background-color: var(--navy-dark); color: white;
            padding: 18px 35px; display: flex; justify-content: space-between; align-items: center;
        }
        .btn-logout {
            background-color: var(--yellow-accent); color: #111827; font-weight: 600;
            border-radius: 20px; padding: 6px 22px; font-size: 0.85rem; text-decoration: none;
        }
        .content-body { padding: 28px 32px; flex-grow: 1; }

        /* Cards & Tables */
        .stat-card, .list-card, .table-card {
            background-color: var(--navy-card); color: white;
            border-radius: 14px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .stat-card { position: relative; margin-bottom: 16px; }
        .stat-card .title { font-size: 0.82rem; color: #E2E8F0; margin-bottom: 6px; }
        .stat-card .icon-top { position: absolute; top: 18px; right: 18px; color: var(--yellow-accent); }
        .stat-card .value-main { font-size: 1.55rem; font-weight: 700; color: var(--yellow-accent); }
        .stat-card .value-white { font-size: 1.35rem; font-weight: 700; color: #FFF; }

        .custom-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
        .custom-table thead th {
            background-color: #162647; color: #CBD5E1; font-weight: 500;
            padding: 12px 14px; white-space: nowrap;
        }
        .custom-table tbody td {
            padding: 12px 14px; border-bottom: 1px solid rgba(255,255,255,0.1); vertical-align: middle;
        }
        .btn-action {
            background-color: var(--yellow-accent); color: #111827; border: none;
            width: 28px; height: 28px; border-radius: 6px; display: inline-flex;
            align-items: center; justify-content: center; font-size: 0.78rem;
            margin: 1px; cursor: pointer; text-decoration: none;
        }
        .modal-content { background-color: var(--navy-card); color: white; border-radius: 14px; }
        .modal-header, .modal-footer { border-color: rgba(255,255,255,0.12); }
    </style>
</head>
<body>

<div class="wrapper">
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div>
            <a href="/index.php" class="brand-logo">
                <i class="fas fa-dice-d20 text-warning fa-lg"></i>
                <span>Meeple Space</span>
            </a>

            <ul class="nav-menu">
                <li>
                    <a href="?page=dashboard" class="<?= $page === 'dashboard' ? 'active' : '' ?>">
                        <i class="fas fa-th-large"></i> Dashboard Overview
                    </a>
                </li>
            </ul>

            <div class="nav-section-title">Manajemen Database</div>
            <ul class="nav-menu">
                <?php foreach ($existingTables as $tbl): ?>
                <li>
                    <a href="?page=<?= htmlspecialchars($tbl) ?>" class="<?= $page === $tbl ? 'active' : '' ?>">
                        <i class="fas fa-table fa-fw"></i> <?= ucwords(str_replace('_', ' ', $tbl)) ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="d-flex align-items-center gap-2 pt-3 border-top">
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($adminName) ?>&background=1E325C&color=fff" class="rounded-circle" width="40" height="40" alt="Admin">
            <div>
                <h6 class="m-0 fw-bold" style="color: var(--navy-dark); font-size: 0.9rem;"><?= htmlspecialchars($adminName) ?></h6>
                <span style="font-size: 0.7rem; font-weight: 700; color: var(--yellow-hover);">ADMIN</span>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="main-area">
        <header class="top-header">
            <h4 class="m-0 fs-5 fw-semibold">
                <?= $page === 'dashboard' ? 'Admin Dashboard' : 'Kelola Tabel: ' . ucwords(str_replace('_', ' ', $page)) ?>
            </h4>
            <div class="d-flex gap-2">
                <a href="/index.php" class="btn btn-outline-light btn-sm rounded-pill px-3">Lihat Website</a>
                <a href="/actions/auth/logout.php" class="btn-logout">Logout</a>
            </div>
        </header>

        <div class="content-body">
            <?php if ($successMsg): ?>
                <div class="alert alert-success alert-dismissible fade show py-2 small">
                    <?= htmlspecialchars($successMsg) ?>
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($errorMsg): ?>
                <div class="alert alert-danger alert-dismissible fade show py-2 small">
                    <?= htmlspecialchars($errorMsg) ?>
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($page === 'dashboard'): ?>
                <?php $stats = getDashboardStats($pdo, $existingTables); ?>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="stat-card mb-0">
                            <div class="title">Active Bookings</div>
                            <i class="far fa-calendar icon-top"></i>
                            <div class="value-main"><?= $stats['bookings'] ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card mb-0">
                            <div class="title">Games Checked Out</div>
                            <i class="fas fa-star icon-top"></i>
                            <div class="value-white"><?= $stats['games'] ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card mb-0">
                            <div class="title">Total Customers</div>
                            <i class="fas fa-user icon-top"></i>
                            <div class="value-white"><?= $stats['customers'] ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card mb-0">
                            <div class="title">Total Revenue</div>
                            <i class="fas fa-coins icon-top"></i>
                            <div class="value-white" style="font-size: 1.15rem;">Rp. <?= number_format($stats['revenue'], 0, ',', '.') ?></div>
                        </div>
                    </div>
                </div>

                <div class="table-card">
                    <h6 class="fw-bold mb-3">Akses Cepat Manajemen Tabel</h6>
                    <p class="small text-white-50">Pilih salah satu tabel di bawah ini atau melalui menu di sebelah kiri untuk menambah (Create), melihat detail (Read), mengubah (Update), atau menghapus (Delete) data:</p>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <?php foreach ($existingTables as $tbl): ?>
                            <a href="?page=<?= htmlspecialchars($tbl) ?>" class="btn btn-warning btn-sm fw-semibold">
                                <i class="fas fa-database me-1"></i> <?= ucwords(str_replace('_', ' ', $tbl)) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

            <?php elseif (in_array($page, $existingTables)): ?>
                <?php
                $columns = array_column(getTableColumns($pdo, $page), 'Field');
                $records = getAllRecords($pdo, $page);
                ?>
                <div class="table-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="m-0 fw-bold">Tabel: <code><?= htmlspecialchars($page) ?></code></h5>
                            <small class="text-white-50">Total: <?= count($records) ?> data</small>
                        </div>
                        <button class="btn btn-warning btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalCreate">
                            <i class="fas fa-plus me-1"></i> Tambah Data
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <?php foreach ($columns as $col): ?>
                                        <th><?= ucwords(str_replace('_', ' ', $col)) ?></th>
                                    <?php endforeach; ?>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($records)): ?>
                                    <tr><td colspan="<?= count($columns) + 1 ?>" class="text-center py-4 text-white-50">Belum ada data di tabel ini.</td></tr>
                                <?php endif; ?>

                                <?php foreach ($records as $idx => $row): ?>
                                    <?php $rowId = $row['id'] ?? $idx; ?>
                                    <tr>
                                        <?php foreach ($columns as $col): ?>
                                            <td>
                                                <?php
                                                $val = (string)($row[$col] ?? '');
                                                echo htmlspecialchars(strlen($val) > 40 ? substr($val, 0, 40) . '...' : $val);
                                                ?>
                                            </td>
                                        <?php endforeach; ?>
                                        <td class="text-center text-nowrap">
                                            <!-- READ DETAIL -->
                                            <button class="btn-action" title="Detail" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $rowId ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if (isset($row['id'])): ?>
                                                <!-- UPDATE -->
                                                <button class="btn-action" title="Edit" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id'] ?>">
                                                    <i class="fas fa-pen"></i>
                                                </button>
                                                <!-- DELETE -->
                                                <a href="/actions/admin/crud.php?table=<?= urlencode($page) ?>&action=delete&id=<?= $row['id'] ?>" class="btn-action" title="Hapus" onclick="return confirm('Yakin ingin menghapus data #<?= $row['id'] ?>?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                    <!-- MODAL DETAIL (READ) -->
                                    <div class="modal fade" id="modalDetail<?= $rowId ?>" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold">Detail Data (<?= htmlspecialchars($page) ?>)</h6>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start small">
                                                    <?php foreach ($row as $k => $v): ?>
                                                        <div class="mb-2 border-bottom border-secondary pb-1">
                                                            <strong class="text-warning"><?= ucwords(str_replace('_', ' ', $k)) ?>:</strong><br>
                                                            <span><?= nl2br(htmlspecialchars((string)($v ?? '-'))) ?></span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- MODAL EDIT (UPDATE) -->
                                    <?php if (isset($row['id'])): ?>
                                    <div class="modal fade" id="modalEdit<?= $row['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <form action="/actions/admin/crud.php" method="POST" class="modal-content">
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold">Edit Data #<?= $row['id'] ?></h6>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="table" value="<?= htmlspecialchars($page) ?>">
                                                    <input type="hidden" name="action" value="update">
                                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                    <?php renderDynamicFormFields($pdo, $page, $existingTables, $row); ?>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-warning btn-sm fw-bold">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- MODAL CREATE (TAMBAH DATA) -->
                <div class="modal fade" id="modalCreate" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <form action="/actions/admin/crud.php" method="POST" class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title fw-bold">Tambah Data Baru ke <code><?= htmlspecialchars($page) ?></code></h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="table" value="<?= htmlspecialchars($page) ?>">
                                <input type="hidden" name="action" value="create">
                                <?php renderDynamicFormFields($pdo, $page, $existingTables, null); ?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-warning btn-sm fw-bold">Simpan Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>