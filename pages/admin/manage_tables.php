<?php
$pageTitle = "Manajemen Cafe Tables";
require_once __DIR__ . "/../../components/admin/layout_header.php";

$tables = $pdo->query("SELECT * FROM cafe_tables ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card-navy">
    <div class="card-header-flex">
        <h2>Daftar Meja Kafe</h2>
        <button type="button" class="btn btn-yellow" onclick="openModal('modalAddTable')">
            <i class="fas fa-plus"></i> Tambah Meja Baru
        </button>
    </div>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Meja</th>
                    <th>Kapasitas</th>
                    <th>Harga per Sesi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tables as $t): ?>
                <tr>
                    <td><?= $t['id'] ?></td>
                    <td><strong><?= htmlspecialchars($t['table_name']) ?></strong></td>
                    <td><?= htmlspecialchars($t['capacity']) ?></td>
                    <td>Rp. <?= number_format($t['price_per_session'], 0, ',', '.') ?></td>
                    <td>
                        <button type="button" class="btn btn-icon" title="Edit" onclick="openModal('editTable<?= $t['id'] ?>')"><i class="fas fa-pen"></i></button>
                        <a href="/actions/admin/tables/delete.php?id=<?= $t['id'] ?>" class="btn btn-icon" title="Hapus" onclick="return confirm('Hapus meja ini?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>

                <!-- MODAL EDIT MEJA -->
                <div class="modal-backdrop" id="editTable<?= $t['id'] ?>">
                    <div class="modal-box">
                        <form action="/actions/admin/tables/update.php" method="POST">
                            <div class="modal-header">
                                <h3>Edit <?= htmlspecialchars($t['table_name']) ?></h3>
                                <button type="button" class="modal-close" onclick="closeModal('editTable<?= $t['id'] ?>')">×</button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                <div class="form-group"><label class="form-label">Nama Meja</label><input type="text" name="table_name" class="form-control" value="<?= htmlspecialchars($t['table_name']) ?>" required></div>
                                <div class="form-group"><label class="form-label">Kapasitas</label><input type="text" name="capacity" class="form-control" value="<?= htmlspecialchars($t['capacity']) ?>" required></div>
                                <div class="form-group"><label class="form-label">Harga per Sesi (Rp)</label><input type="number" name="price_per_session" class="form-control" value="<?= $t['price_per_session'] ?>" required></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" onclick="closeModal('editTable<?= $t['id'] ?>')">Batal</button>
                                <button type="submit" class="btn btn-yellow">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL TAMBAH MEJA -->
<div class="modal-backdrop" id="modalAddTable">
    <div class="modal-box">
        <form action="/actions/admin/tables/create.php" method="POST">
            <div class="modal-header">
                <h3>Tambah Meja Kafe</h3>
                <button type="button" class="modal-close" onclick="closeModal('modalAddTable')">×</button>
            </div>
            <div class="modal-body">
                <div class="form-group"><label class="form-label">Nama Meja</label><input type="text" name="table_name" class="form-control" placeholder="Contoh: Table 9" required></div>
                <div class="form-group"><label class="form-label">Kapasitas</label><input type="text" name="capacity" class="form-control" placeholder="Contoh: 2-4 Person" required></div>
                <div class="form-group"><label class="form-label">Harga per Sesi (Rp)</label><input type="number" name="price_per_session" class="form-control" placeholder="50000" required></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddTable')">Batal</button>
                <button type="submit" class="btn btn-yellow">Simpan Meja</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . "/../../components/admin/layout_footer.php"; ?>