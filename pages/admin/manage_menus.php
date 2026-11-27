<?php
$pageTitle = "Manajemen Cafe Menus";
require_once __DIR__ . "/../../components/admin/layout_header.php";

$menus = $pdo->query("
    SELECT m.*, c.nama_kategori 
    FROM menu_items m 
    LEFT JOIN menu_categories c ON m.category_id = c.id 
    ORDER BY m.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$categories = $pdo->query("SELECT * FROM menu_categories")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card-navy">
    <div class="card-header-flex">
        <h2>Daftar Makanan & Minuman</h2>
        <button type="button" class="btn btn-yellow" onclick="openModal('modalAddMenu')">
            <i class="fas fa-plus"></i> Tambah Menu Baru
        </button>
    </div>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama Menu</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Favorit Home</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menus as $m): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($m['nama_menu']) ?></strong></td>
                    <td><?= htmlspecialchars($m['nama_kategori'] ?? '-') ?></td>
                    <td>Rp. <?= number_format($m['harga'], 0, ',', '.') ?></td>
                    <td><?= $m['is_favorite_home'] ? 'Ya' : 'Tidak' ?></td>
                    <td>
                        <button type="button" class="btn btn-icon" title="Detail" onclick="openModal('detailMenu<?= $m['id'] ?>')"><i class="fas fa-eye"></i></button>
                        <button type="button" class="btn btn-icon" title="Edit" onclick="openModal('editMenu<?= $m['id'] ?>')"><i class="fas fa-pen"></i></button>
                        <a href="/actions/admin/menus/delete.php?id=<?= $m['id'] ?>" class="btn btn-icon" title="Hapus" onclick="return confirm('Hapus menu <?= htmlspecialchars($m['nama_menu']) ?>?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>

                <!-- MODAL DETAIL MENU -->
                <div class="modal-backdrop" id="detailMenu<?= $m['id'] ?>">
                    <div class="modal-box">
                        <div class="modal-header">
                            <h3>Detail <?= htmlspecialchars($m['nama_menu']) ?></h3>
                            <button type="button" class="modal-close" onclick="closeModal('detailMenu<?= $m['id'] ?>')">×</button>
                        </div>
                        <div class="modal-body" style="text-align: left; font-size: 0.9rem;">
                            <p style="margin-bottom: 8px;"><strong>Kategori:</strong> <?= htmlspecialchars($m['nama_kategori'] ?? '-') ?></p>
                            <p style="margin-bottom: 8px;"><strong>Harga:</strong> Rp. <?= number_format($m['harga'], 0, ',', '.') ?></p>
                            <p style="margin-bottom: 8px;"><strong>Path Gambar:</strong> <?= htmlspecialchars($m['gambar']) ?></p>
                            <p style="margin-bottom: 8px;"><strong>Deskripsi:</strong><br><?= nl2br(htmlspecialchars($m['deskripsi'] ?: '-')) ?></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="closeModal('detailMenu<?= $m['id'] ?>')">Tutup</button>
                        </div>
                    </div>
                </div>

                <!-- MODAL EDIT MENU -->
                <div class="modal-backdrop" id="editMenu<?= $m['id'] ?>">
                    <div class="modal-box">
                        <form action="/actions/admin/menus/update.php" method="POST">
                            <div class="modal-header">
                                <h3>Edit <?= htmlspecialchars($m['nama_menu']) ?></h3>
                                <button type="button" class="modal-close" onclick="closeModal('editMenu<?= $m['id'] ?>')">×</button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?= $m['id'] ?>">

                                <div class="form-group"><label class="form-label">Nama Menu</label><input type="text" name="nama_menu" class="form-control" value="<?= htmlspecialchars($m['nama_menu']) ?>" required></div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Kategori</label>
                                        <select name="category_id" class="form-control">
                                            <?php foreach ($categories as $c): ?>
                                                <option value="<?= $c['id'] ?>" <?= $c['id'] == $m['category_id'] ? 'selected' : '' ?>><?= $c['nama_kategori'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group"><label class="form-label">Harga (Rp)</label><input type="number" name="harga" class="form-control" value="<?= $m['harga'] ?>" required></div>
                                </div>
                                <div class="form-group"><label class="form-label">Path Gambar</label><input type="text" name="gambar" class="form-control" value="<?= htmlspecialchars($m['gambar']) ?>" required></div>
                                <div class="form-group">
                                    <label class="form-label">Tampilkan di Top Items?</label>
                                    <select name="is_favorite_home" class="form-control">
                                        <option value="0" <?= $m['is_favorite_home']==0?'selected':'' ?>>Tidak</option>
                                        <option value="1" <?= $m['is_favorite_home']==1?'selected':'' ?>>Ya</option>
                                    </select>
                                </div>
                                <div class="form-group"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="2"><?= htmlspecialchars($m['deskripsi'] ?? '') ?></textarea></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" onclick="closeModal('editMenu<?= $m['id'] ?>')">Batal</button>
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

<!-- MODAL TAMBAH MENU -->
<div class="modal-backdrop" id="modalAddMenu">
    <div class="modal-box">
        <form action="/actions/admin/menus/create.php" method="POST">
            <div class="modal-header">
                <h3>Tambah Menu Cafe Baru</h3>
                <button type="button" class="modal-close" onclick="closeModal('modalAddMenu')">×</button>
            </div>
            <div class="modal-body">
                <div class="form-group"><label class="form-label">Nama Menu</label><input type="text" name="nama_menu" class="form-control" required></div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select name="category_id" class="form-control">
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= $c['nama_kategori'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label">Harga (Rp)</label><input type="number" name="harga" class="form-control" value="30000" required></div>
                </div>
                <div class="form-group"><label class="form-label">Path Gambar</label><input type="text" name="gambar" class="form-control" placeholder="assets/menu.jpg" required></div>
                <div class="form-group">
                    <label class="form-label">Tampilkan di Top Items?</label>
                    <select name="is_favorite_home" class="form-control"><option value="0">Tidak</option><option value="1">Ya</option></select>
                </div>
                <div class="form-group"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="2"></textarea></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddMenu')">Batal</button>
                <button type="submit" class="btn btn-yellow">Simpan Menu</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . "/../../components/admin/layout_footer.php"; ?>