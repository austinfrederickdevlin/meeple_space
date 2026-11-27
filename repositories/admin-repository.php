<?php

/**
 * Mengambil daftar seluruh tabel yang ada di database aktif secara otomatis
 */
function getAllDatabaseTables(PDO $pdo): array
{
    $stmt = $pdo->query("SHOW TABLES");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Mengambil daftar kolom beserta tipe datanya dari suatu tabel
 */
function getTableColumns(PDO $pdo, string $table): array
{
    $stmt = $pdo->query("SHOW COLUMNS FROM `$table`");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Mengambil seluruh baris data (READ) dari tabel tertentu
 */
function getAllRecords(PDO $pdo, string $table): array
{
    $cols = array_column(getTableColumns($pdo, $table), 'Field');
    $orderBy = in_array('id', $cols) ? "ORDER BY id DESC" : "";

    $stmt = $pdo->query("SELECT * FROM `$table` $orderBy");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Mengambil data statistik untuk kartu dashboard utama
 */
function getDashboardStats(PDO $pdo, array $existingTables): array
{
    $stats = [
        'bookings' => 0,
        'games' => 0,
        'customers' => 0,
        'revenue' => 0
    ];

    if (in_array('bookings', $existingTables)) {
        $stats['bookings'] = (int) $pdo->query("SELECT COUNT(*) FROM bookings WHERE booking_status != 'Cancelled'")->fetchColumn();
        $stats['games'] = (int) $pdo->query("SELECT COUNT(*) FROM bookings WHERE board_game_id IS NOT NULL")->fetchColumn();
        $stats['revenue'] = (float) $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM bookings WHERE payment_status = 'Paid'")->fetchColumn();
    }

    if (in_array('users', $existingTables)) {
        $stats['customers'] = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
    }

    return $stats;
}

/**
 * Merender field input form secara dinamis berdasarkan tipe kolom database
 */
function renderDynamicFormFields(PDO $pdo, string $table, array $existingTables, ?array $rowData = null): void
{
    $columns = getTableColumns($pdo, $table);

    $fkMap = [
        'user_id' => ['table' => 'users', 'label' => 'email'],
        'table_id' => ['table' => 'cafe_tables', 'label' => 'table_name'],
        'board_game_id' => ['table' => 'board_games', 'label' => 'judul'],
        'booking_id' => ['table' => 'bookings', 'label' => 'booking_code'],
        'menu_item_id' => ['table' => 'menu_items', 'label' => 'nama_menu'],
        'category_id' => ['table' => 'menu_categories', 'label' => 'nama_kategori'],
        'badge_id' => ['table' => 'badges', 'label' => 'nama_badge'],
    ];

    foreach ($columns as $col) {
        $field = $col['Field'];
        $type = $col['Type'];
        $null = $col['Null'];
        $val = $rowData[$field] ?? ($col['Default'] ?? '');

        // Lewati kolom auto-increment dan timestamp otomatis
        if (
            $field === 'id' ||
            strpos($col['Extra'], 'auto_increment') !== false ||
            $field === 'created_at' ||
            $field === 'updated_at'
        ) {
            continue;
        }

        $required = ($null === 'NO' && $col['Default'] === null) ? 'required' : '';
        $label = ucwords(str_replace('_', ' ', $field));

        echo "<div class='mb-3 text-start'>";
        echo "<label class='form-label small fw-semibold text-light'>$label</label>";

        // 1. Foreign Key Dropdown
        if (isset($fkMap[$field]) && in_array($fkMap[$field]['table'], $existingTables)) {
            $refTable = $fkMap[$field]['table'];
            $refCols = array_column(getTableColumns($pdo, $refTable), 'Field');
            $dispCol = in_array($fkMap[$field]['label'], $refCols) ? $fkMap[$field]['label'] : 'id';

            $opts = $pdo->query("SELECT id, `$dispCol` AS disp FROM `$refTable`")->fetchAll(PDO::FETCH_ASSOC);
            echo "<select name='data[$field]' class='form-select form-select-sm' $required>";
            if ($null === 'YES') {
                echo "<option value=''>-- Kosong / NULL --</option>";
            }
            foreach ($opts as $opt) {
                $sel = ($val == $opt['id']) ? 'selected' : '';
                echo "<option value='{$opt['id']}' $sel>#{$opt['id']} - " . htmlspecialchars((string) $opt['disp']) . "</option>";
            }
            echo "</select>";
        }
        // 2. Kolom ENUM
        elseif (strpos($type, 'enum(') === 0) {
            preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
            $enums = explode("','", $matches[1]);
            echo "<select name='data[$field]' class='form-select form-select-sm' $required>";
            foreach ($enums as $e) {
                $sel = ($val === $e) ? 'selected' : '';
                echo "<option value='$e' $sel>$e</option>";
            }
            echo "</select>";
        }
        // 3. Boolean tinyint(1)
        elseif ($type === 'tinyint(1)') {
            echo "<select name='data[$field]' class='form-select form-select-sm'>";
            echo "<option value='0' " . ($val == 0 ? 'selected' : '') . ">0 (Tidak)</option>";
            echo "<option value='1' " . ($val == 1 ? 'selected' : '') . ">1 (Ya)</option>";
            echo "</select>";
        }
        // 4. Textarea
        elseif ($type === 'text') {
            echo "<textarea name='data[$field]' class='form-control form-control-sm' rows='3' $required>" . htmlspecialchars((string) $val) . "</textarea>";
        }
        // 5. Date
        elseif ($type === 'date') {
            echo "<input type='date' name='data[$field]' class='form-control form-control-sm' value='" . htmlspecialchars((string) $val) . "' $required>";
        }
        // 6. Number / Decimal
        elseif (strpos($type, 'int') !== false || strpos($type, 'decimal') !== false) {
            $step = (strpos($type, 'decimal') !== false) ? "step='0.1'" : "";
            echo "<input type='number' $step name='data[$field]' class='form-control form-control-sm' value='" . htmlspecialchars((string) $val) . "' $required>";
        }
        // 7. Password khusus tabel users
        elseif ($field === 'password') {
            $ph = $rowData ? "Kosongkan jika tidak ingin mengubah password" : "Masukkan password";
            $reqPass = $rowData ? '' : $required;
            echo "<input type='password' name='data[$field]' class='form-control form-control-sm' placeholder='$ph' $reqPass>";
        }
        // 8. Input Teks Standar
        else {
            echo "<input type='text' name='data[$field]' class='form-control form-control-sm' value='" . htmlspecialchars((string) $val) . "' $required>";
        }

        echo "</div>";
    }
}