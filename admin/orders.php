<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../login.php');
}

$page_title = 'Manage Orders';

// Handle order status update
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = clean_input($_POST['status']);
    update_order_status($order_id, $status);
}

$orders = get_all_orders();
$all_products = get_all_products();
$products_map = [];
foreach ($all_products as $p) {
    $products_map[$p['id']] = $p;
}

// Sort orders by date (newest first)
usort($orders, function ($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

include '../includes/header.php';
?>

<!-- Admin Orders -->
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
                    <i class="bi bi-receipt me-1 text-success"></i> ORDERS
                </span>
            </div>
            <h1 class="fw-bold mb-0 text-body display-6">Order Management</h1>
            <p class="text-secondary mb-0">Track shipments and update statuses</p>
        </div>
        <div>
            <a href="./" class="btn btn-outline-secondary rounded-pill px-4 bg-white text-dark border-light-subtle fw-bold">
                <i class="bi bi-arrow-left me-2"></i> Dashboard
            </a>
        </div>
    </div>
    
    <div class="card admin-card rounded-4 overflow-hidden shadow-lg">
        <div class="card-body p-0">
            <?php if (!empty($orders)): ?>
                <div class="table-responsive">
                    <table class="table table-dark-custom align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="py-4 ps-4 text-uppercase small fw-bold">Order ID & Date</th>
                                <th class="py-4 text-uppercase small fw-bold">Customer Info</th>
                                <th class="py-4 text-uppercase small fw-bold">Amount & Items</th>
                                <th class="py-4 text-uppercase small fw-bold">Current Status</th>
                                <th class="py-4 pe-4 text-uppercase small fw-bold text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr class="hover-row transition-hover">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(59, 130, 246, 0.1); color: #3B82F6;">
                                                <i class="bi bi-bag-check-fill"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-body">#<?php echo $order['id']; ?></div>
                                                <div class="small" style="color: #6B7280;"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-bold text-body"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                                        <div class="small" style="color: #6B7280;"><?php echo htmlspecialchars($order['customer_email']); ?></div>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-bold text-success">₹<?php echo number_format($order['total_amount'], 2); ?></div>
                                        <?php
        $items = json_decode($order['items'], true);
        if (is_array($items)) {
            echo '<div class="d-flex flex-column gap-1 mt-1">';
            foreach ($items as $pid => $qty) {
                $p_name = isset($products_map[$pid]) ? htmlspecialchars($products_map[$pid]['name']) : 'Unknown Product';
                echo '<div class="small text-secondary"><i class="bi bi-dot"></i> ' . $qty . 'x ' . $p_name . '</div>';
            }
            echo '</div>';
        }
        else {
            echo '<div class="small" style="color: #6B7280;">0 Items</div>';
        }
?>
                                    </td>
                                    <td class="py-3">
                                        <?php
        $statusColor = '';
        $statusIcon = '';
        switch ($order['status']) {
            case 'pending':
                $statusColor = '#F59E0B';
                $statusIcon = 'bi-hourglass-split';
                break;
            case 'processing':
                $statusColor = '#3B82F6';
                $statusIcon = 'bi-gear-wide-connected';
                break;
            case 'shipped':
                $statusColor = '#8B5CF6';
                $statusIcon = 'bi-truck';
                break;
            case 'delivered':
                $statusColor = '#10B981';
                $statusIcon = 'bi-check-circle-fill';
                break;
            case 'cancelled':
                $statusColor = '#EF4444';
                $statusIcon = 'bi-x-circle-fill';
                break;
        }
?>
                                        <span class="badge rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-2" 
                                              style="background-color: <?php echo $statusColor . '20'; ?>; color: <?php echo $statusColor; ?>; border: 1px solid <?php echo $statusColor . '40'; ?>;">
                                            <i class="bi <?php echo $statusIcon; ?>"></i>
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td class="pe-4 py-3 text-end">
                                        <form method="POST" class="d-flex align-items-center justify-content-end gap-2">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <select name="status" class="form-select form-select-sm rounded-pill border-light-subtle bg-white text-dark shadow-none" 
                                                    style="width: 130px; cursor: pointer; border-color: #D1D5DB;">
                                                <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn btn-primary btn-sm rounded-circle d-flex align-items-center justify-content-center shadow-sm hover-scale" 
                                                    style="width: 34px; height: 34px;" title="Update Status">
                                                <i class="bi bi-save"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php
    endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php
else: ?>
                <div class="text-center py-5">
                    <div class="display-1 mb-3" style="color: #D1D5DB;"><i class="bi bi-inbox"></i></div>
                    <h4 class="fw-bold text-body">No orders found</h4>
                    <p class="text-secondary">Orders will appear here once customers start purchasing.</p>
                </div>
            <?php
endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
