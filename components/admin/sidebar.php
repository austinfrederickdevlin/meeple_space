<aside class="admin-sidebar">
            <div>
                <a href="/pages/admin/admin_dashboard.php" class="admin-brand">
                    <i class="fas fa-dice-d20" style="color: #EAB308;"></i>
                    <span>Meeple Space</span>
                </a>

                <ul class="admin-nav">
                    <li>
                        <a href="/pages/admin/admin_dashboard.php"
                            class="<?= $currentFile === 'admin_dashboard.php' ? 'active' : '' ?>">
                            <i class="fas fa-th-large"></i> Dashboard
                        </a>
                    </li>
                </ul>

                <div class="nav-group-title">Manajemen Utama</div>
                <ul class="admin-nav">
                    <li>
                        <a href="/pages/admin/manage_bookings.php"
                            class="<?= $currentFile === 'manage_bookings.php' ? 'active' : '' ?>">
                            <i class="fas fa-calendar-check fa-fw"></i> Bookings
                        </a>
                    </li>
                    <li>
                        <a href="/pages/admin/manage_tables.php"
                            class="<?= $currentFile === 'manage_tables.php' ? 'active' : '' ?>">
                            <i class="fas fa-chair fa-fw"></i> Cafe Tables
                        </a>
                    </li>
                    <li>
                        <a href="/pages/admin/manage_games.php"
                            class="<?= $currentFile === 'manage_games.php' ? 'active' : '' ?>">
                            <i class="fas fa-chess fa-fw"></i> Board Games
                        </a>
                    </li>
                    <li>
                        <a href="/pages/admin/manage_menus.php"
                            class="<?= $currentFile === 'manage_menus.php' ? 'active' : '' ?>">
                            <i class="fas fa-utensils fa-fw"></i> Cafe Menus
                        </a>
                    </li>
                    <li>
                        <a href="/pages/admin/manage_users.php"
                            class="<?= $currentFile === 'manage_users.php' ? 'active' : '' ?>">
                            <i class="fas fa-users fa-fw"></i> Users & Admin
                        </a>
                    </li>
                </ul>
            </div>

            <div class="admin-user-box">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($adminName) ?>&background=1E325C&color=fff"
                    alt="Admin">
                <div>
                    <div class="admin-user-name"><?= htmlspecialchars($adminName) ?></div>
                    <span class="admin-user-role">ADMIN</span>
                </div>
            </div>
        </aside>