<?php
/**
 * GitHub Pages Sitemap Generator UI
 * Site: https://lets-play.site
 */

$baseUrl = "https://lets-play.site";
$rootDir = __DIR__;
$outputFile = $rootDir . "/sitemap.xml";

$ignore = [
    '.git',
    '.github',
    'node_modules',
    'README.md',
    'readme.md',
    'CNAME',
    'sitemap.xml',
    'sitemap-generator.php'
];

$urls = [];
$ignoredFiles = [];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootDir)
);

foreach ($iterator as $file) {

    if ($file->isDir()) {
        continue;
    }

    $filePath = $file->getPathname();
    $relativePath = str_replace($rootDir . DIRECTORY_SEPARATOR, '', $filePath);

    $skip = false;

    foreach ($ignore as $ignored) {
        if (stripos($relativePath, $ignored) !== false) {
            $skip = true;
            $ignoredFiles[] = $relativePath;
            break;
        }
    }

    if ($skip) {
        continue;
    }

    if (pathinfo($filePath, PATHINFO_EXTENSION) !== 'html') {
        $ignoredFiles[] = $relativePath;
        continue;
    }

    $urlPath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);

    $urlPath = preg_replace('/index\.html$/i', '', $urlPath);

    $urlPath = preg_replace('/\.html$/i', '', $urlPath);

    $fullUrl = rtrim($baseUrl, '/') . '/' . ltrim($urlPath, '/');

    $urls[] = [
        'loc' => $fullUrl,
        'lastmod' => date('Y-m-d', filemtime($filePath))
    ];
}

/**
 * Generate XML
 */
$xml = new DOMDocument('1.0', 'UTF-8');
$xml->formatOutput = true;

$urlset = $xml->createElement('urlset');
$urlset->setAttribute(
    'xmlns',
    'http://www.sitemaps.org/schemas/sitemap/0.9'
);

foreach ($urls as $urlData) {

    $url = $xml->createElement('url');

    $loc = $xml->createElement('loc', htmlspecialchars($urlData['loc']));
    $lastmod = $xml->createElement('lastmod', $urlData['lastmod']);

    $url->appendChild($loc);
    $url->appendChild($lastmod);

    $urlset->appendChild($url);
}

$xml->appendChild($urlset);
$xml->save($outputFile);

$sitemapUrl = $baseUrl . "/sitemap.xml";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sitemap Generator</title>

    <style>
        body {
            background: #0f172a;
            color: #f8fafc;
            font-family: Arial, sans-serif;
            padding: 40px 20px;
        }

        .container {
            max-width: 850px;
            margin: auto;
            background: #111827;
            border-radius: 14px;
            padding: 35px;
            box-shadow: 0 0 25px rgba(0,0,0,0.3);
        }

        h1 {
            margin-top: 0;
            color: #38bdf8;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit,minmax(180px,1fr));
            gap: 15px;
            margin-top: 25px;
            margin-bottom: 30px;
        }

        .card {
            background: #1e293b;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .number {
            font-size: 34px;
            font-weight: bold;
            color: #4ade80;
        }

        .label {
            margin-top: 8px;
            color: #cbd5e1;
        }

        .success {
            background: #052e16;
            border: 1px solid #14532d;
            padding: 18px;
            border-radius: 10px;
            margin-bottom: 25px;
            color: #bbf7d0;
        }

        a {
            color: #38bdf8;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .ignored-list {
            margin-top: 25px;
            background: #1e293b;
            padding: 20px;
            border-radius: 10px;
            max-height: 300px;
            overflow-y: auto;
        }

        .ignored-list ul {
            padding-left: 20px;
        }

        .ignored-list li {
            margin-bottom: 6px;
            color: #cbd5e1;
            font-size: 14px;
        }

        .footer {
            margin-top: 30px;
            color: #94a3b8;
            font-size: 13px;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>🗺️ Sitemap Generator</h1>

    <div class="success">
        ✅ sitemap.xml generated successfully.
    </div>

    <div class="stats">

        <div class="card">
            <div class="number"><?php echo count($urls); ?></div>
            <div class="label">Pages Added</div>
        </div>

        <div class="card">
            <div class="number"><?php echo count($ignoredFiles); ?></div>
            <div class="label">Files Ignored</div>
        </div>

    </div>

    <p>
        🔗 Sitemap URL:
        <br><br>
        <a href="<?php echo $sitemapUrl; ?>" target="_blank">
            <?php echo $sitemapUrl; ?>
        </a>
    </p>

    <div class="ignored-list">
        <h3>Ignored Files</h3>

        <ul>
            <?php foreach ($ignoredFiles as $file): ?>
                <li><?php echo htmlspecialchars($file); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="footer">
        GitHub Pages Compatible • Auto-generated XML Sitemap
    </div>

</div>

</body>
</html>
