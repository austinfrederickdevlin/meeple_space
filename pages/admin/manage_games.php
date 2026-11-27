<?php
$pageTitle = "Manajemen Board Games";
require_once __DIR__ . "/../../components/admin/layout_header.php";

$games = $pdo->query("SELECT * FROM board_games ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$categories = ['Card','Board','Strategic','Memory','Trivia','Dexterity','Word','Puzzle','Cooperative','Mind'];
?>

<div class="card-navy">
    <div class="card-header-flex">
        <h2>Koleksi Board Games</h2>
        <button type="button" class="btn btn-yellow" onclick="openModal('modalAddGame')">
            <i class="fas fa-plus"></i> Tambah Board Game
        </button>
    </div>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Pemain</th>
                    <th>Durasi</th>
                    <th>Kesulitan</th>
                    <th>Most Wanted</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($games as $g): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($g['judul']) ?></strong></td>
                    <td><?= $g['kategori_utama'] ?></td>
                    <td><?= htmlspecialchars($g['pemain']) ?></td>
                    <td><?= htmlspecialchars($g['durasi']) ?></td>
                    <td><?= $g['tingkat_kesulitan'] ?></td>
                    <td><?= $g['is_most_wanted'] ? 'Ya' : 'Tidak' ?></td>
                    <td>
                        <button type="button" class="btn btn-icon" title="Detail" onclick="openModal('detailGame<?= $g['id'] ?>')"><i class="fas fa-eye"></i></button>
                        <button type="button" class="btn btn-icon" title="Edit" onclick="openModal('editGame<?= $g['id'] ?>')"><i class="fas fa-pen"></i></button>
                        <a href="/actions/admin/games/delete.php?id=<?= $g['id'] ?>" class="btn btn-icon" title="Hapus" onclick="return confirm('Hapus game <?= htmlspecialchars($g['judul']) ?>?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>

                <!-- MODAL DETAIL GAME -->
                <div class="modal-backdrop" id="detailGame<?= $g['id'] ?>">
                    <div class="modal-box">
                        <div class="modal-header">
                            <h3>Detail <?= htmlspecialchars($g['judul']) ?></h3>
                            <button type="button" class="modal-close" onclick="closeModal('detailGame<?= $g['id'] ?>')">×</button>
                        </div>
                        <div class="modal-body" style="text-align: left; font-size: 0.9rem;">
                            <p style="margin-bottom: 8px;"><strong>Kategori:</strong> <?= $g['kategori_utama'] ?> | <strong>Kesulitan:</strong> <?= $g['tingkat_kesulitan'] ?></p>
                            <p style="margin-bottom: 8px;"><strong>Pemain:</strong> <?= htmlspecialchars($g['pemain']) ?> Orang | <strong>Durasi:</strong> <?= htmlspecialchars($g['durasi']) ?></p>
                            <p style="margin-bottom: 8px;"><strong>Path Gambar:</strong> <?= htmlspecialchars($g['gambar']) ?></p>
                            <p style="margin-bottom: 8px;"><strong>Deskripsi:</strong><br><?= nl2br(htmlspecialchars($g['deskripsi'] ?: '-')) ?></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="closeModal('detailGame<?= $g['id'] ?>')">Tutup</button>
                        </div>
                    </div>
                </div>

                <!-- MODAL EDIT GAME -->
                <div class="modal-backdrop" id="editGame<?= $g['id'] ?>">
                    <div class="modal-box">
                        <form action="/actions/admin/games/update.php" method="POST">
                            <div class="modal-header">
                                <h3>Edit <?= htmlspecialchars($g['judul']) ?></h3>
                                <button type="button" class="modal-close" onclick="closeModal('editGame<?= $g['id'] ?>')">×</button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?= $g['id'] ?>">

                                <div class="form-group"><label class="form-label">Judul Game</label><input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($g['judul']) ?>" required></div>
                                <div class="form-row">
                                    <div class="form-group"><label class="form-label">Pemain</label><input type="text" name="pemain" class="form-control" value="<?= htmlspecialchars($g['pemain']) ?>" required></div>
                                    <div class="form-group"><label class="form-label">Durasi</label><input type="text" name="durasi" class="form-control" value="<?= htmlspecialchars($g['durasi']) ?>" required></div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Kategori</label>
                                        <select name="kategori_utama" class="form-control">
                                            <?php foreach($categories as $kat): ?>
                                                <option value="<?= $kat ?>" <?= $g['kategori_utama']==$kat?'selected':'' ?>><?= $kat ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Kesulitan</label>
                                        <select name="tingkat_kesulitan" class="form-control">
                                            <option value="Easy" <?= $g['tingkat_kesulitan']=='Easy'?'selected':'' ?>>Easy</option>
                                            <option value="Medium" <?= $g['tingkat_kesulitan']=='Medium'?'selected':'' ?>>Medium</option>
                                            <option value="Hard" <?= $g['tingkat_kesulitan']=='Hard'?'selected':'' ?>>Hard</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group"><label class="form-label">Path Gambar</label><input type="text" name="gambar" class="form-control" value="<?= htmlspecialchars($g['gambar']) ?>" required></div>
                                <div class="form-group">
                                    <label class="form-label">Tampilkan di Most Wanted?</label>
                                    <select name="is_most_wanted" class="form-control">
                                        <option value="0" <?= $g['is_most_wanted']==0?'selected':'' ?>>Tidak</option>
                                        <option value="1" <?= $g['is_most_wanted']==1?'selected':'' ?>>Ya</option>
                                    </select>
                                </div>
                                <div class="form-group"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="2"><?= htmlspecialchars($g['deskripsi'] ?? '') ?></textarea></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" onclick="closeModal('editGame<?= $g['id'] ?>')">Batal</button>
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

<!-- MODAL TAMBAH GAME -->
<div class="modal-backdrop" id="modalAddGame">
    <div class="modal-box">
        <form action="/actions/admin/games/create.php" method="POST">
            <div class="modal-header">
                <h3>Tambah Board Game Baru</h3>
                <button type="button" class="modal-close" onclick="closeModal('modalAddGame')">×</button>
            </div>
            <div class="modal-body">
                <div class="form-group"><label class="form-label">Judul Game</label><input type="text" name="judul" class="form-control" required></div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Jumlah Pemain</label><input type="text" name="pemain" class="form-control" placeholder="2-6" required></div>
                    <div class="form-group"><label class="form-label">Durasi</label><input type="text" name="durasi" class="form-control" placeholder="30 min" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select name="kategori_utama" class="form-control">
                            <?php foreach($categories as $kat): ?>
                                <option value="<?= $kat ?>"><?= $kat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kesulitan</label>
                        <select name="tingkat_kesulitan" class="form-control">
                            <option value="Easy">Easy</option>
                            <option value="Medium">Medium</option>
                            <option value="Hard">Hard</option>
                        </select>
                    </div>
                </div>
                <div class="form-group"><label class="form-label">Path Gambar</label><input type="text" name="gambar" class="form-control" placeholder="assets/game.jpg" required></div>
                <div class="form-group">
                    <label class="form-label">Tampilkan di Most Wanted?</label>
                    <select name="is_most_wanted" class="form-control"><option value="0">Tidak</option><option value="1">Ya</option></select>
                </div>
                <div class="form-group"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="2"></textarea></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddGame')">Batal</button>
                <button type="submit" class="btn btn-yellow">Simpan Game</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . "/../../components/admin/layout_footer.php"; ?>