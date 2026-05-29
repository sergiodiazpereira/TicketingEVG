<?php
require 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3ODAwNzMwODMsImV4cCI6MTc4MDE1OTQ4MywiZGF0YSI6eyJpZCI6MjYsIm5vbWJyZSI6Ikpvc2VwaCIsImFwZWxsaWRvcyI6IlF1aXNwZSBBbHZhcmV6IiwiZW1haWwiOiJqb3NlcGhxYTMxMzFAZ21haWwuY29tIiwiZm90byI6Imh0dHBzOi8vbGgzLmdvb2dsZXVzZXJjb250ZW50LmNvbS9hL0FDZzhvY0lVWElDTmV0QXVtcllQRlFzNHR4Umh3bjNPQjF6QmhxcFBMZGUxRW9SSzROaEx0UT1zOTYtYyIsInJvbGVzIjpbInN1cGVyX2FkbWluIl0sInRpcG9fcGVyc29uYWwiOiJEb2NlbnRlIn19.HpeTD4f5leOvU8U9IfkGpxsKRid46fc9xZ_tbvC2fC4';
$secret = 'UNA_CLAVE_TUYA_SUPER_SECRETA_INVENTADA';

try {
    $decoded = JWT::decode($token, new Key($secret, 'HS256'));
    echo "OK - Token válido\n";
    print_r($decoded);
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
echo "SERVER TIME: " . time() . " (" . date('Y-m-d H:i:s') . ")\n";
echo "PAYLOAD IAT: 1780073083 (" . date('Y-m-d H:i:s', 1780073083) . ")\n";
echo "PAYLOAD EXP: 1780159483 (" . date('Y-m-d H:i:s', 1780159483) . ")\n";
?>
