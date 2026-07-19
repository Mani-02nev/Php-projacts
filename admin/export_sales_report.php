<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../login.php');
}

$stats = get_stats();
$orders = get_all_orders();

// Sort orders by newest first
usort($orders, function ($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report - Univault</title>
    <!-- Use basic styles to ensure it prints perfectly clean -->
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; padding: 30px; color: #222; }
        .report-header { text-align: center; border-bottom: 2px solid #222; margin-bottom: 40px; padding-bottom: 20px; }
        .report-header h1 { margin: 0; font-size: 28px; text-transform: uppercase; letter-spacing: 2px; font-weight: 700; }
        .report-header p { margin: 10px 0 0 0; color: #555; font-size: 14px; }
        .summary-box { display: flex; justify-content: space-between; margin-bottom: 40px; gap: 20px; }
        .stat-item { border: 1px solid #ddd; padding: 20px; flex: 1; text-align: center; background: #fafafa; border-radius: 8px; }
        .stat-item h3 { margin: 0; font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px; }
        .stat-item p { margin: 15px 0 0 0; font-size: 24px; font-weight: 800; color: #111; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        th, td { border-bottom: 1px solid #eee; padding: 12px; text-align: left; font-size: 13px; }
        th { background-color: #f8f9fa; text-transform: uppercase; font-weight: 700; color: #444; border-bottom: 2px solid #ddd; }
        tr:nth-child(even) { background-color: #fafafa; }
        .footer { text-align: center; font-size: 12px; color: #888; margin-top: 60px; border-top: 1px solid #ddd; padding-top: 20px; }
        
        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .status-delivered { background: #d1fae5; color: #065f46; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-processing { background: #dbeafe; color: #1e40af; }
        .status-shipped { background: #ede9fe; color: #5b21b6; }
        .status-cancelled { background: #fee2e2; color: #b91c1c; }

        @media print {
            body { padding: 0; background: #fff; }
            .no-print { display: none; }
            .stat-item { border: 1px solid #ccc; }
        }
    </style>
</head>
<body onload="setTimeout(() => window.print(), 500)">
    <div class="no-print" style="margin-bottom: 20px; display: flex; justify-content: flex-end;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #7C3AED; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; box-shadow: 0 4px 6px rgba(124, 58, 237, 0.2);">
            🖨️ Print / Save as PDF
        </button>
    </div>

    <div class="report-header">
        <h1>Sales & Analytics Report</h1>
        <p>Univault System - Generated on <?php echo date('F j, Y, g:i a'); ?></p>
    </div>

    <div class="summary-box">
        <div class="stat-item">
            <h3>Total Revenue</h3>
            <p>₹<?php echo number_format($stats['total_revenue'], 2); ?></p>
        </div>
        <div class="stat-item">
            <h3>Total Orders</h3>
            <p><?php echo $stats['total_orders']; ?></p>
        </div>
        <div class="stat-item">
            <h3>Products Sold</h3>
            <p><?php echo $stats['total_products']; ?></p>
        </div>
        <div class="stat-item">
            <h3>Active Users</h3>
            <p><?php echo $stats['total_users']; ?></p>
        </div>
    </div>

    <h3 style="margin-bottom: 20px; text-transform: uppercase; font-size: 16px; border-left: 4px solid #7C3AED; padding-left: 10px;">Recent Order Transactions</h3>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (array_slice($orders, 0, 50) as $order): ?>
            <tr>
                <td style="font-weight: bold;">#<?php echo $order['id']; ?></td>
                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                <td style="font-weight: bold;">₹<?php echo number_format($order['total_amount'], 2); ?></td>
                <td>
                    <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($orders)): ?>
            <tr>
                <td colspan="5" style="text-align: center; padding: 30px; color: #888;">No transactions found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>This is a highly confidential system-generated report. Univault &copy; <?php echo date('Y'); ?></p>
    </div>
</body>
</html>
