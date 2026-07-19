<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../login.php');
}

$page_title = 'Manage Users';

// Handle user role update if requested
if (isset($_POST['update_role'])) {
    $user_id = intval($_POST['user_id']);
    $role = clean_input($_POST['role']);
    
    $users = read_csv(USERS_CSV);
    foreach ($users as &$user) {
        if ($user['id'] == $user_id && $user['id'] != $_SESSION['user_id']) { // prevent self-demotion
            $user['role'] = $role;
            break;
        }
    }
    write_csv(USERS_CSV, $users);
}

// Handle user deletion if requested
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    if ($user_id != $_SESSION['user_id']) { // prevent self-deletion
        $users = read_csv(USERS_CSV);
        $filtered = array_filter($users, function($user) use ($user_id) {
            return $user['id'] != $user_id;
        });
        write_csv(USERS_CSV, array_values($filtered));
    }
}

$users = read_csv(USERS_CSV);

// Sort users by ID descending
usort($users, function ($a, $b) {
    return $b['id'] - $a['id'];
});

include '../includes/header.php';
?>

<!-- Admin Users -->
<style>
    body { background-color: #F8F9FA !important; }
    .admin-card { background-color: #FFFFFF; border: 1px solid #E5E7EB; }
    .table-dark-custom { --bs-table-bg: transparent; --bs-table-color: #6B7280; --bs-table-border-color: #E5E7EB; }
    .table-dark-custom th { color: #374151; background-color: #E5E7EB; border-bottom: 1px solid #D1D5DB; }
    .table-dark-custom td { vertical-align: middle; border-bottom: 1px solid #E5E7EB; color: #6B7280; }
    .hover-row:hover td { background-color: rgba(0,0,0,0.02); color: #1F2937; }
</style>

<div class="container-fluid px-4 py-5" style="min-height: 100vh;">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <div class="d-flex align-items-center gap-3 mb-1">
                <span class="badge rounded-pill px-3 py-1 fw-bold" style="background-color: #D1D5DB; color: #374151; border: 1px solid #4B5563;">
                    <i class="bi bi-people-fill me-1 text-primary"></i> USERS
                </span>
            </div>
            <h1 class="fw-bold mb-0 text-body display-6">User Management</h1>
            <p class="text-secondary mb-0">Manage customer accounts and administrative roles</p>
        </div>
        <div>
            <a href="./" class="btn btn-outline-secondary rounded-pill px-4 bg-white text-dark border-light-subtle fw-bold shadow-sm hover-scale">
                <i class="bi bi-arrow-left me-2"></i> Dashboard
            </a>
        </div>
    </div>
    
    <div class="card admin-card rounded-4 overflow-hidden shadow-lg">
        <div class="card-body p-0">
            <?php if (!empty($users)): ?>
                <div class="table-responsive">
                    <table class="table table-dark-custom align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="py-4 ps-4 text-uppercase small fw-bold">ID & Avatar</th>
                                <th class="py-4 text-uppercase small fw-bold">User Details</th>
                                <th class="py-4 text-uppercase small fw-bold">Account Role</th>
                                <th class="py-4 pe-4 text-uppercase small fw-bold text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr class="hover-row transition-hover">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
                                                 style="width: 44px; height: 44px; background: <?php echo $user['role'] == 'admin' ? 'linear-gradient(135deg, #7C3AED, #3B82F6)' : 'linear-gradient(135deg, #10B981, #059669)'; ?>;">
                                                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-body">#<?php echo $user['id']; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-bold text-body" style="font-size: 1.05rem;"><?php echo htmlspecialchars($user['name']); ?></div>
                                        <div class="small" style="color: #6B7280;"><i class="bi bi-envelope-at me-1"></i><?php echo htmlspecialchars($user['email']); ?></div>
                                    </td>
                                    <td class="py-3">
                                        <?php if ($user['role'] == 'admin'): ?>
                                            <span class="badge rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-2" 
                                                  style="background-color: rgba(124, 58, 237, 0.1); color: #7C3AED; border: 1px solid rgba(124, 58, 237, 0.2);">
                                                <i class="bi bi-shield-lock-fill"></i> Administrator
                                            </span>
                                        <?php else: ?>
                                            <span class="badge rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-2" 
                                                  style="background-color: rgba(16, 185, 129, 0.1); color: #10B981; border: 1px solid rgba(16, 185, 129, 0.2);">
                                                <i class="bi bi-person-fill"></i> Customer
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="pe-4 py-3 text-end">
                                        <div class="d-flex align-items-center justify-content-end gap-2">
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <form method="POST" class="d-flex gap-2 m-0">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <select name="role" class="form-select form-select-sm rounded-pill border-light-subtle bg-white text-dark shadow-none" 
                                                        style="width: 120px; cursor: pointer; border-color: #D1D5DB;" onchange="this.form.submit()">
                                                    <option value="customer" <?php echo $user['role'] == 'customer' ? 'selected' : ''; ?>>Customer</option>
                                                    <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                </select>
                                                <input type="hidden" name="update_role" value="1">
                                            </form>
                                            
                                            <a href="?delete=<?php echo $user['id']; ?>" 
                                               class="btn btn-outline-danger btn-sm rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm hover-scale ms-2"
                                               style="width: 32px; height: 32px; padding: 0;"
                                               onclick="return confirm('Are you sure you want to delete this user?')"
                                               title="Delete User">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                            <?php else: ?>
                                            <span class="badge bg-light text-muted border px-3 py-2 rounded-pill">Current User</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <div class="display-1 mb-3" style="color: #D1D5DB;"><i class="bi bi-people"></i></div>
                    <h4 class="fw-bold text-body">No users found</h4>
                    <p class="text-secondary">Users will appear here once they register.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
