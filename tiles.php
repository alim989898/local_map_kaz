<?php

$db = new PDO('sqlite:' . __DIR__ . '/kazakhstan.mbtiles');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$z = intval($_GET['z'] ?? 0);
$x = intval($_GET['x'] ?? 0);
$y = intval($_GET['y'] ?? 0);

/*
 * MBTiles хранит Y в TMS формате,
 * браузеры используют XYZ.
 */
$tmsY = (1 << $z) - 1 - $y;

$sql = "
SELECT tile_data
FROM tiles
WHERE zoom_level = ?
  AND tile_column = ?
  AND tile_row = ?
LIMIT 1
";

$stmt = $db->prepare($sql);
$stmt->execute([$z, $x, $tmsY]);

$tile = $stmt->fetchColumn();

if (!$tile) {
    http_response_code(404);
    exit;
}

header('Content-Type: application/x-protobuf');
header('Content-Encoding: gzip');
header('Cache-Control: public, max-age=86400');

echo $tile;