<?php
/**
 * OMNIX OS - Buy Airtime Service
 */

require_once('../../config/db.php');
require_once('../../config/functions.php');

if (!isUserLoggedIn()) {
    redirect('../../login.php');
}

$user = getUserData($conn);
$balance = getUserBalance($conn, $user['id']);
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = sanitizeInput($_POST['phone']);
    $provider = sanitizeInput($_POST['provider']);
    $amount = floatval($_POST['amount']);
    
    if (empty($phone) || empty($provider) || $amount <= 0) {
        $error = 'Please fill all fields correctly';
    } elseif ($balance < $amount) {
        $error = 'Insufficient balance. Current balance: ₦' . number_format($balance, 2);
    } else {
        updateBalance($conn, $user['id'], $amount, 'subtract');
        $description = "Airtime topup - $provider - $phone";
        addTransaction($conn, $user['id'], 'airtime', $amount, $description);
        sendNotification($conn, $user['id'], 'Airtime Purchased', "Successfully purchased ₦$amount airtime for $phone", 'success');
        
        $message = 'Airtime purchased successfully!';
        $balance = getUserBalance($conn, $user['id']);
    }
}

$airtime_plans = [
    ['provider' => 'MTN', 'amounts' => [100, 200, 500, 1000, 2000, 5000]],
    ['provider' => 'Airtel', 'amounts' => [100, 200, 500, 1000, 2000, 5000]],
    ['provider' => 'Glo', 'amounts' => [100, 200, 500, 1000, 2000, 5000]],
    ['provider' => '9Mobile', 'amounts' => [100, 200, 500, 1000, 2000, 5000]],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buy Airtime</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: linear-gradient(135deg, #0f1419 0%, #1a1f2e 100%); }
        .glass { background: rgba(20, 25, 35, 0.7); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="text-slate-100 min-h-screen">

    <!-- Header -->
    <nav class="glass fixed top-0 w-full z-50 h-16 border-b border-[#282f3b] flex items-center px-6">
        <a href="index.php" class="flex items-center gap-2 text-slate-400 hover:text-white transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back
        </a>
    </nav>

    <!-- Main Content -->
    <main class="pt-24 pb-12 px-6">
        <div class="max-w-2xl mx-auto">

            <!-- Page Title -->
            <section class="mb-8">
                <h1 class="text-4xl font-bold mb-2">📱 Buy Airtime</h1>
                <p class="text-slate-400">Purchase airtime for any network</p>
            </section>

            <!-- Balance Card -->
            <div class="glass rounded-xl p-6 border border-[#282f3b] mb-6">
                <p class="text-slate-400 text-sm mb-1">Available Balance</p>
                <h2 class="text-3xl font-bold">₦<?= number_format($balance, 2) ?></h2>
            </div>

            <!-- Messages -->
            <?php if ($message): ?>
            <div class="bg-green-900/20 border border-green-500/30 text-green-400 p-4 rounded-lg mb-6">
                <?= $message ?>
            </div>
            <?php endif; ?>

            <?php if ($error): ?>
            <div class="bg-red-900/20 border border-red-500/30 text-red-400 p-4 rounded-lg mb-6">
                <?= $error ?>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <div class="glass rounded-xl p-6 border border-[#282f3b]">
                <form method="POST" class="space-y-4">
                    
                    <!-- Phone Number -->
                    <div>
                        <label class="block text-sm font-500 mb-2">Phone Number</label>
                        <input type="text" name="phone" placeholder="Enter phone number" required class="w-full bg-[#1a1f2e] border border-[#282f3b] rounded-lg px-4 py-2 text-slate-100 focus:border-[#f7931a] focus:outline-none transition">
                    </div>

                    <!-- Provider -->
                    <div>
                        <label class="block text-sm font-500 mb-2">Network Provider</label>
                        <select name="provider" required class="w-full bg-[#1a1f2e] border border-[#282f3b] rounded-lg px-4 py-2 text-slate-100 focus:border-[#f7931a] focus:outline-none transition">
                            <option value="">Select Provider</option>
                            <option value="MTN">MTN</option>
                            <option value="Airtel">Airtel</option>
                            <option value="Glo">Glo</option>
                            <option value="9Mobile">9Mobile</option>
                        </select>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-500 mb-2">Amount (₦)</label>
                        <input type="number" name="amount" placeholder="Enter amount" min="100" step="100" required class="w-full bg-[#1a1f2e] border border-[#282f3b] rounded-lg px-4 py-2 text-slate-100 focus:border-[#f7931a] focus:outline-none transition">
                    </div>

                    <!-- Quick Select Amounts -->
                    <div>
                        <label class="block text-sm font-500 mb-2">Quick Select</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" onclick="document.querySelector('input[name=amount]').value=100" class="bg-[#1a1f2e] hover:bg-[#282f3b] p-2 rounded text-sm transition">₦100</button>
                            <button type="button" onclick="document.querySelector('input[name=amount]').value=500" class="bg-[#1a1f2e] hover:bg-[#282f3b] p-2 rounded text-sm transition">₦500</button>
                            <button type="button" onclick="document.querySelector('input[name=amount]').value=1000" class="bg-[#1a1f2e] hover:bg-[#282f3b] p-2 rounded text-sm transition">₦1000</button>
                            <button type="button" onclick="document.querySelector('input[name=amount]').value=2000" class="bg-[#1a1f2e] hover:bg-[#282f3b] p-2 rounded text-sm transition">₦2000</button>
                            <button type="button" onclick="document.querySelector('input[name=amount]').value=5000" class="bg-[#1a1f2e] hover:bg-[#282f3b] p-2 rounded text-sm transition">₦5000</button>
                            <button type="button" onclick="document.querySelector('input[name=amount]').value=10000" class="bg-[#1a1f2e] hover:bg-[#282f3b] p-2 rounded text-sm transition">₦10000</button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-gradient-to-r from-[#f7931a] to-[#ac3973] hover:from-[#f7a031] hover:to-[#b04680] text-white font-600 py-3 rounded-lg transition">
                        Buy Airtime
                    </button>
                </form>
            </div>

        </div>
    </main>

</body>
</html>
