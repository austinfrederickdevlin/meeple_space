<?php
$pageTitle = "Manajemen Users & Admin";
require_once __DIR__ . "/../../components/admin/layout_header.php";

$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card-navy">
    <div class="card-header-flex">
        <h2>Daftar Pengguna & Admin</h2>
        <button type="button" class="btn btn-yellow" onclick="openModal('modalAddUser')">
            <i class="fas fa-plus"></i> Tambah Akun Baru
        </button>
    </div>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>No. HP</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
                    <td><?= htmlspecialchars($u['full_name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['phone_number'] ?? '-') ?></td>
                    <td>
                        <span class="badge <?= $u['role'] === 'admin' ? 'badge-pending' : 'badge-confirmed' ?>">
                            <?= strtoupper($u['role']) ?>
                        </span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-icon" title="Edit" onclick="openModal('editUser<?= $u['id'] ?>')"><i class="fas fa-pen"></i></button>
                        <a href="/actions/admin/users/delete.php?id=<?= $u['id'] ?>" class="btn btn-icon" title="Hapus" onclick="return confirm('Hapus akun <?= htmlspecialchars($u['username']) ?>?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>

                <!-- MODAL EDIT USER -->
                <div class="modal-backdrop" id="editUser<?= $u['id'] ?>">
                    <div class="modal-box">
                        <form action="/actions/admin/users/update.php" method="POST">
                            <div class="modal-header">
                                <h3>Edit Akun <?= htmlspecialchars($u['username']) ?></h3>
                                <button type="button" class="modal-close" onclick="closeModal('editUser<?= $u['id'] ?>')">×</button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?= $u['id'] ?>">

                                <div class="form-row">
                                    <div class="form-group"><label class="form-label">Username</label><input type="text" name="username" class="form-control" value="<?= htmlspecialchars($u['username']) ?>" required></div>
                                    <div class="form-group"><label class="form-label">Nama Lengkap</label><input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($u['full_name']) ?>" required></div>
                                </div>
                                <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($u['email']) ?>" required></div>
                                <div class="form-row">
                                    <div class="form-group"><label class="form-label">No. HP</label><input type="text" name="phone_number" class="form-control" value="<?= htmlspecialchars($u['phone_number'] ?? '') ?>"></div>
                                    <div class="form-group">
                                        <label class="form-label">Role</label>
                                        <select name="role" class="form-control">
                                            <option value="user" <?= $u['role']=='user'?'selected':'' ?>>User</option>
                                            <option value="admin" <?= $u['role']=='admin'?'selected':'' ?>>Admin</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group"><label class="form-label">Password Baru (Opsional)</label><input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" onclick="closeModal('editUser<?= $u['id'] ?>')">Batal</button>
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

<!-- MODAL TAMBAH USER -->
<div class="modal-backdrop" id="modalAddUser">
    <div class="modal-box">
        <form action="/actions/admin/users/create.php" method="POST">
            <div class="modal-header">
                <h3>Tambah Pengguna / Admin</h3>
                <button type="button" class="modal-close" onclick="closeModal('modalAddUser')">×</button>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Username</label><input type="text" name="username" class="form-control" required></div>
                    <div class="form-group"><label class="form-label">Nama Lengkap</label><input type="text" name="full_name" class="form-control" required></div>
                </div>
                <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">No. HP</label><input type="text" name="phone_number" class="form-control"></div>
                    <div class="form-group">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-control"><option value="user">User</option><option value="admin">Admin</option></select>
                    </div>
                </div>
                <div class="form-group"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddUser')">Batal</button>
                <button type="submit" class="btn btn-yellow">Buat Akun</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . "/../../components/admin/layout_footer.php"; ?>