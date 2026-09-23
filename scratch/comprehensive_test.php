<?php
/**
 * Comprehensive Test Suite for PHP Native Rebuild
 */

$baseUrl = 'http://localhost/event-management';
$cookieFile = __DIR__ . '/cookie.txt';
if (file_exists($cookieFile)) unlink($cookieFile);

function makeRequest($url, $method = 'GET', $postData = null, $useCookie = true, $followLocation = false) {
    global $cookieFile;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $followLocation);

    if ($useCookie) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    }

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($postData) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        }
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    curl_close($ch);

    return [
        'code'    => $httpCode,
        'headers' => $headers,
        'body'    => $body
    ];
}

$results = [];

// 1. Test Auth: Dashboard without login redirects to login
echo "--- Test 1: Dashboard without login ---\n";
$res = makeRequest("{$baseUrl}/dashboard.php", 'GET', null, false);
$hasRedirect = ($res['code'] === 302 && strpos($res['headers'], 'Location: login.php') !== false);
$results['Auth Guard (unauthenticated -> login)'] = $hasRedirect ? 'PASS' : "FAIL (code {$res['code']})";
echo "Result: " . $results['Auth Guard (unauthenticated -> login)'] . "\n";

// 2. Test Login with Invalid Credentials
echo "\n--- Test 2: Login with wrong password ---\n";
$res = makeRequest("{$baseUrl}/login.php", 'POST', [
    'email' => 'manager@eo.com',
    'password' => 'wrongpass'
]);
$hasError = (strpos($res['body'], 'Email atau password salah') !== false);
$results['Login with invalid password rejected'] = $hasError ? 'PASS' : 'FAIL';
echo "Result: " . $results['Login with invalid password rejected'] . "\n";

// 3. Test Login with Valid Credentials
echo "\n--- Test 3: Login with valid credentials ---\n";
$res = makeRequest("{$baseUrl}/login.php", 'POST', [
    'email' => 'manager@eo.com',
    'password' => 'password123'
]);
$loginSuccess = ($res['code'] === 302 && strpos($res['headers'], 'Location: dashboard.php') !== false);
$results['Login manager@eo.com / password123'] = $loginSuccess ? 'PASS' : "FAIL (code {$res['code']})";
echo "Result: " . $results['Login manager@eo.com / password123'] . "\n";

// 4. Test Dashboard with Authenticated Session
echo "\n--- Test 4: Dashboard stats ---\n";
$res = makeRequest("{$baseUrl}/dashboard.php");
$hasStats = (
    strpos($res['body'], 'Total Event') !== false &&
    strpos($res['body'], 'Menunggu Review') !== false &&
    strpos($res['body'], 'Event Disetujui') !== false &&
    strpos($res['body'], 'Event Ditolak') !== false
);
$results['Dashboard Stats Display'] = $hasStats ? 'PASS' : 'FAIL';
echo "Result: " . $results['Dashboard Stats Display'] . "\n";

// 5. Test Filters
echo "\n--- Test 5: Filters (all, submitted, approved, rejected) ---\n";
$resAll = makeRequest("{$baseUrl}/dashboard.php?filter=all");
$resSub = makeRequest("{$baseUrl}/dashboard.php?filter=submitted");
$resApp = makeRequest("{$baseUrl}/dashboard.php?filter=approved");
$resRej = makeRequest("{$baseUrl}/dashboard.php?filter=rejected");

$filterPass = (
    strpos($resSub['body'], 'Annual Corporate Gala') !== false &&
    strpos($resApp['body'], 'Wedding Celebration') !== false &&
    strpos($resRej['body'], 'Product Launch') !== false
);
$results['Filter tabs (all, submitted, approved, rejected)'] = $filterPass ? 'PASS' : 'FAIL';
echo "Result: " . $results['Filter tabs (all, submitted, approved, rejected)'] . "\n";

// 6. Test Detail Pages for all 3 events
echo "\n--- Test 6: Detail Pages ---\n";
$resD1 = makeRequest("{$baseUrl}/event-detail.php?id=1");
$resD2 = makeRequest("{$baseUrl}/event-detail.php?id=2");
$resD3 = makeRequest("{$baseUrl}/event-detail.php?id=3");

$detailPass = (
    strpos($resD1['body'], 'Annual Corporate Gala') !== false &&
    strpos($resD2['body'], 'Wedding Celebration') !== false &&
    strpos($resD3['body'], 'Product Launch') !== false &&
    strpos($resD3['body'], 'Alasan Penolakan Event') !== false
);
$results['Detail page viewing for all 3 events'] = $detailPass ? 'PASS' : 'FAIL';
echo "Result: " . $results['Detail page viewing for all 3 events'] . "\n";

// 7. Test Not Found for invalid ID
echo "\n--- Test 7: Not Found ---\n";
$resNotFound = makeRequest("{$baseUrl}/event-detail.php?id=9999");
$resNotInt   = makeRequest("{$baseUrl}/event-detail.php?id=abc");
$notFoundPass = (
    strpos($resNotFound['body'], 'Event tidak ditemukan') !== false &&
    strpos($resNotInt['body'], 'Event tidak ditemukan') !== false
);
$results['Event not found handling (invalid id / non-existent id)'] = $notFoundPass ? 'PASS' : 'FAIL';
echo "Result: " . $results['Event not found handling (invalid id / non-existent id)'] . "\n";

// 8. Test Approve Event (id=1: submitted -> approved)
echo "\n--- Test 8: Approve Event ---\n";
$resApprove = makeRequest("{$baseUrl}/approve-event.php", 'POST', ['event_id' => 1]);
$resDetailAfterApprove = makeRequest("{$baseUrl}/event-detail.php?id=1");
$approvePass = (
    $resApprove['code'] === 302 &&
    strpos($resDetailAfterApprove['body'], 'Event berhasil disetujui') !== false &&
    strpos($resDetailAfterApprove['body'], 'Sudah Disetujui') !== false
);
$results['Approve submitted event -> approved'] = $approvePass ? 'PASS' : 'FAIL';
echo "Result: " . $results['Approve submitted event -> approved'] . "\n";

// Reset event 1 back to submitted for reject testing
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=event_management', 'root', '');
$pdo->exec("UPDATE events SET status = 'submitted', rejection_reason = NULL WHERE id = 1");

// 9. Test Reject Validation (empty rejection reason)
echo "\n--- Test 9: Reject with empty reason ---\n";
$resRejectEmpty = makeRequest("{$baseUrl}/reject-event.php", 'POST', [
    'event_id' => 1,
    'rejection_reason' => '   '
]);
$resDetailAfterEmptyReject = makeRequest("{$baseUrl}/event-detail.php?id=1");
$rejectEmptyPass = (
    strpos($resDetailAfterEmptyReject['body'], 'Alasan penolakan wajib diisi') !== false
);
$results['Reject with empty reason rejected by validation'] = $rejectEmptyPass ? 'PASS' : 'FAIL';
echo "Result: " . $results['Reject with empty reason rejected by validation'] . "\n";

// 10. Test Reject with valid reason
echo "\n--- Test 10: Reject with valid reason ---\n";
$reasonText = 'Jadwal berbenturan dengan agenda internal korporat.';
$resRejectValid = makeRequest("{$baseUrl}/reject-event.php", 'POST', [
    'event_id' => 1,
    'rejection_reason' => $reasonText
]);
$resDetailAfterValidReject = makeRequest("{$baseUrl}/event-detail.php?id=1");
$rejectValidPass = (
    $resRejectValid['code'] === 302 &&
    strpos($resDetailAfterValidReject['body'], 'Event telah ditolak beserta alasan penolakan yang disimpan') !== false &&
    strpos($resDetailAfterValidReject['body'], $reasonText) !== false &&
    strpos($resDetailAfterValidReject['body'], 'Sudah Ditolak') !== false
);
$results['Reject event with reason stored'] = $rejectValidPass ? 'PASS' : 'FAIL';
echo "Result: " . $results['Reject event with reason stored'] . "\n";

// Reset database back to original clean seed data
$pdo->exec(file_get_contents(__DIR__ . '/../database/event_management.sql'));

// 11. Test Security: Reject GET on POST-only endpoints
echo "\n--- Test 11: GET on approve-event.php and reject-event.php ---\n";
$resGetApprove = makeRequest("{$baseUrl}/approve-event.php", 'GET');
$resGetReject  = makeRequest("{$baseUrl}/reject-event.php", 'GET');
$securityGetPass = ($resGetApprove['code'] === 405 && $resGetReject['code'] === 405);
$results['Action endpoints reject GET method (405)'] = $securityGetPass ? 'PASS' : "FAIL (approve: {$resGetApprove['code']}, reject: {$resGetReject['code']})";
echo "Result: " . $results['Action endpoints reject GET method (405)'] . "\n";

// 12. Test Logout
echo "\n--- Test 12: Logout ---\n";
$resLogout = makeRequest("{$baseUrl}/logout.php", 'POST');
$resAfterLogout = makeRequest("{$baseUrl}/dashboard.php");
$logoutPass = (
    $resLogout['code'] === 302 &&
    strpos($resLogout['headers'], 'Location: login.php') !== false &&
    $resAfterLogout['code'] === 302 &&
    strpos($resAfterLogout['headers'], 'Location: login.php') !== false
);
$results['Logout destroys session and blocks dashboard'] = $logoutPass ? 'PASS' : 'FAIL';
echo "Result: " . $results['Logout destroys session and blocks dashboard'] . "\n";

echo "\n============================================\n";
echo "SUMMARY RESULTS:\n";
echo "============================================\n";
$allPass = true;
foreach ($results as $testName => $status) {
    echo "{$testName}: {$status}\n";
    if ($status !== 'PASS') $allPass = false;
}
echo "============================================\n";
echo "OVERALL: " . ($allPass ? 'ALL TESTS PASSED!' : 'SOME TESTS FAILED!') . "\n";
