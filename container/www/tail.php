<?php
declare(strict_types=1);
set_time_limit(0);
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

$baseDir = __DIR__ . '/logs';
$fname = basename($_GET['file'] ?? '');
$path = realpath("$baseDir/$fname");
if (!$path || strncmp($path, realpath($baseDir), strlen(realpath($baseDir))) !== 0) {
  http_response_code(404); echo "event: error\ndata: invalid file\n\n"; exit;
}

$fp = fopen($path, 'r');
if (!$fp) { echo "event: error\ndata: cannot open file\n\n"; exit; }

$size = filesize($path);
$start = max(0, $size - 200 * 1024);
fseek($fp, $start);

echo "event: init\n";
echo "data: " . json_encode(stream_get_contents($fp)) . "\n\n";
@ob_flush(); @flush();

$pos = ftell($fp);
while (true) {
  clearstatcache(true, $path);
  $newSize = filesize($path);

  if ($newSize < $pos) {
    fclose($fp);
    $fp = fopen($path, 'r');
    $pos = 0;
    echo "event: rotate\ndata: reset\n\n";
    @ob_flush(); @flush();
  }

  if ($newSize > $pos) {
    fseek($fp, $pos);
    $chunk = fread($fp, $newSize - $pos);
    $pos = ftell($fp);
    echo "data: " . json_encode($chunk) . "\n\n";
    @ob_flush(); @flush();
  }

  usleep(300000); // 300ms
}
