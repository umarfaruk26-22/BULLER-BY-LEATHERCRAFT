<?php
/**
 * 1-Click Server Side Unzipper for Hostinger
 */
$zipFile = __DIR__ . '/buller.zip';

if (!file_exists($zipFile)) {
    die("Error: buller.zip file not found in " . __DIR__);
}

$zip = new ZipArchive;
$res = $zip->open($zipFile);

if ($res === TRUE) {
    $zip->extractTo(__DIR__);
    $zip->close();
    @unlink($zipFile);
    echo "<h1>✅ SUCCESS: Buller Leather extracted successfully on Hostinger!</h1>";
    echo "<p><a href='./install.php' style='padding:10px 20px; background:#C5A880; color:#111; text-decoration:none; font-weight:bold; border-radius:6px;'>Proceed to 1-Click Installer (install.php)</a></p>";
} else {
    echo "<h1>❌ Failed to extract zip file. Code: " . $res . "</h1>";
}
