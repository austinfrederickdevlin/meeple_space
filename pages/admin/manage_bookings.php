<?php
$pageTitle = "Manajemen Bookings";
require_once __DIR__ . "/../../components/admin/layout_header.php";

$bookings = $pdo->query("
    SELECT b.*, ct.table_name, bg.judul AS game_title 
    FROM bookings b
    LEFT JOIN cafe_tables ct ON b.table_id = ct.id
    LEFT JOIN board_games bg ON b.board_game_id = bg.id
    ORDER BY b.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$tables = $pdo->query("SELECT * FROM cafe_tables")->fetchAll(PDO::FETCH_ASSOC);
$games  = $pdo->query("SELECT * FROM board_games")->fetchAll(PDO::FETCH_ASSOC);
$autoTicket = 'BKG-' . strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 5));
?>

<div class="card-navy">
    <div class="card-header-flex">
        <h2>Daftar Reservasi (Bookings)</h2>
        <button type="button" class="btn btn-yellow" onclick="openModal('modalAddBooking')">
            <i class="fas fa-plus"></i> Buat Booking Baru
        </button>
    </div>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kode Tiket</th>
                    <th>Customer</th>
                    <th>Meja</th>
                    <th>Board Game</th>
                    <th>Jadwal</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $b): ?>
                <tr>
                    <td><strong style="color: #EAB308;"><?= htmlspecialchars($b['booking_code']) ?></strong></td>
                    <td><?= htmlspecialchars($b['customer_name']) ?><br><small style="color: #94A3B8;"><?= htmlspecialchars($b['phone_number']) ?></small></td>
                    <td><?= htmlspecialchars($b['table_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($b['game_title'] ?? '-') ?></td>
                    <td><?= $b['booking_date'] ?> (<?= $b['arrival_time'] ?>)</td>
                    <td>Rp. <?= number_format($b['total_amount'], 0, ',', '.') ?></td>
                    <td>
                        <span class="badge badge-<?= strtolower($b['booking_status']) ?>">
                            <?= $b['booking_status'] ?>
                        </span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-icon" title="Lihat Detail" onclick="openModal('detailBooking<?= $b['id'] ?>')"><i class="fas fa-eye"></i></button>
                        <button type="button" class="btn btn-icon" title="Edit" onclick="openModal('editBooking<?= $b['id'] ?>')"><i class="fas fa-pen"></i></button>
                        <a href="/actions/admin/bookings/delete.php?id=<?= $b['id'] ?>" class="btn btn-icon" title="Hapus" onclick="return confirm('Hapus tiket <?= $b['booking_code'] ?>?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>

                <!-- MODAL DETAIL BOOKING -->
                <div class="modal-backdrop" id="detailBooking<?= $b['id'] ?>">
                    <div class="modal-box">
                        <div class="modal-header">
                            <h3>Detail Tiket #<?= htmlspecialchars($b['booking_code']) ?></h3>
                            <button type="button" class="modal-close" onclick="closeModal('detailBooking<?= $b['id'] ?>')">×</button>
                        </div>
                        <div class="modal-body" style="text-align: left; font-size: 0.9rem;">
                            <p style="margin-bottom: 8px;"><strong>Customer:</strong> <?= htmlspecialchars($b['customer_name']) ?> (<?= htmlspecialchars($b['phone_number']) ?>)</p>
                            <p style="margin-bottom: 8px;"><strong>Meja & Jumlah Orang:</strong> <?= htmlspecialchars($b['table_name'] ?? '-') ?> (<?= $b['number_of_people'] ?> Orang)</p>
                            <p style="margin-bottom: 8px;"><strong>Board Game:</strong> <?= htmlspecialchars($b['game_title'] ?? 'Tidak memilih') ?></p>
                            <p style="margin-bottom: 8px;"><strong>Jadwal Kedatangan:</strong> <?= $b['booking_date'] ?> pukul <?= htmlspecialchars($b['arrival_time']) ?></p>
                            <p style="margin-bottom: 8px;"><strong>Catatan:</strong> <?= htmlspecialchars($b['additional_notes'] ?: '-') ?></p>
                            <hr style="border-color: rgba(255,255,255,0.15); margin: 12px 0;">
                            <p style="margin-bottom: 8px;"><strong>Harga Meja:</strong> Rp. <?= number_format($b['table_price'], 0, ',', '.') ?> | <strong>Pajak:</strong> Rp. <?= number_format($b['tax_amount'], 0, ',', '.') ?></p>
                            <p style="margin-bottom: 8px;"><strong>Total Tagihan:</strong> <span style="color: #EAB308; font-weight: 700;">Rp. <?= number_format($b['total_amount'], 0, ',', '.') ?></span> (<?= $b['payment_status'] ?>)</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="closeModal('detailBooking<?= $b['id'] ?>')">Tutup</button>
                        </div>
                    </div>
                </div>

                <!-- MODAL EDIT BOOKING -->
                <div class="modal-backdrop" id="editBooking<?= $b['id'] ?>">
                    <div class="modal-box">
                        <form action="/actions/admin/bookings/update.php" method="POST">
                            <div class="modal-header">
                                <h3>Edit Tiket #<?= htmlspecialchars($b['booking_code']) ?></h3>
                                <button type="button" class="modal-close" onclick="closeModal('editBooking<?= $b['id'] ?>')">×</button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?= $b['id'] ?>">

                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Nama Customer</label>
                                        <input type="text" name="customer_name" class="form-control" value="<?= htmlspecialchars($b['customer_name']) ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">No. HP</label>
                                        <input type="text" name="phone_number" class="form-control" value="<?= htmlspecialchars($b['phone_number']) ?>" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Meja</label>
                                        <select name="table_id" class="form-control">
                                            <?php foreach ($tables as $t): ?>
                                                <option value="<?= $t['id'] ?>" <?= $t['id'] == $b['table_id'] ? 'selected' : '' ?>><?= $t['table_name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Board Game</label>
                                        <select name="board_game_id" class="form-control">
                                            <option value="">-- Tidak Ada --</option>
                                            <?php foreach ($games as $g): ?>
                                                <option value="<?= $g['id'] ?>" <?= $g['id'] == $b['board_game_id'] ? 'selected' : '' ?>><?= $g['judul'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Tanggal</label>
                                        <input type="date" name="booking_date" class="form-control" value="<?= $b['booking_date'] ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Jam Kedatangan</label>
                                        <input type="text" name="arrival_time" class="form-control" value="<?= htmlspecialchars($b['arrival_time']) ?>" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Status Booking</label>
                                        <select name="booking_status" class="form-control">
                                            <option value="Pending" <?= $b['booking_status']=='Pending'?'selected':'' ?>>Pending</option>
                                            <option value="Confirmed" <?= $b['booking_status']=='Confirmed'?'selected':'' ?>>Confirmed</option>
                                            <option value="Cancelled" <?= $b['booking_status']=='Cancelled'?'selected':'' ?>>Cancelled</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Status Pembayaran</label>
                                        <select name="payment_status" class="form-control">
                                            <option value="Unpaid" <?= $b['payment_status']=='Unpaid'?'selected':'' ?>>Unpaid</option>
                                            <option value="Paid" <?= $b['payment_status']=='Paid'?'selected':'' ?>>Paid</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" onclick="closeModal('editBooking<?= $b['id'] ?>')">Batal</button>
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

<!-- MODAL TAMBAH BOOKING -->
<div class="modal-backdrop" id="modalAddBooking">
    <div class="modal-box">
        <form action="/actions/admin/bookings/create.php" method="POST">
            <div class="modal-header">
                <h3>Buat Tiket Booking Baru</h3>
                <button type="button" class="modal-close" onclick="closeModal('modalAddBooking')">×</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" style="color: #EAB308;">Kode Tiket (Otomatis)</label>
                    <input type="text" name="booking_code" class="form-control" value="<?= $autoTicket ?>" readonly>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Nama Customer</label><input type="text" name="customer_name" class="form-control" required></div>
                    <div class="form-group"><label class="form-label">Nomor HP</label><input type="text" name="phone_number" class="form-control" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Pilih Meja</label>
                        <select name="table_id" class="form-control" required>
                            <?php foreach ($tables as $t): ?>
                                <option value="<?= $t['id'] ?>"><?= $t['table_name'] ?> (Rp <?= number_format($t['price_per_session']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Board Game</label>
                        <select name="board_game_id" class="form-control">
                            <option value="">-- Opsional --</option>
                            <?php foreach ($games as $g): ?>
                                <option value="<?= $g['id'] ?>"><?= $g['judul'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row-3">
                    <div class="form-group"><label class="form-label">Tanggal</label><input type="date" name="booking_date" class="form-control" required></div>
                    <div class="form-group"><label class="form-label">Jam</label><input type="text" name="arrival_time" class="form-control" placeholder="14.00" required></div>
                    <div class="form-group"><label class="form-label">Jml Orang</label><input type="number" name="number_of_people" class="form-control" value="2" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Harga Meja (Rp)</label><input type="number" name="table_price" class="form-control" value="50000" required></div>
                    <div class="form-group"><label class="form-label">Pajak (Rp)</label><input type="number" name="tax_amount" class="form-control" value="10000" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Status Booking</label>
                        <select name="booking_status" class="form-control"><option value="Pending">Pending</option><option value="Confirmed">Confirmed</option></select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status Pembayaran</label>
                        <select name="payment_status" class="form-control"><option value="Unpaid">Unpaid</option><option value="Paid">Paid</option></select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddBooking')">Batal</button>
                <button type="submit" class="btn btn-yellow">Simpan Booking</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . "/../../components/admin/layout_footer.php"; ?>