<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'news_aggregator');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function shorten_text($text, $limit) {
    if (strlen($text) > $limit) {
        $text = substr($text, 0, $limit);
        $text = substr($text, 0, strrpos($text, ' '));
        $text .= '...';
    }
    return $text;
}

// Fetching RSS feeds
$feed = [];
$result = $conn->query("SELECT * FROM feeds");
while ($row = $result->fetch_assoc()) {
    $xml = @simplexml_load_file($row['url']);
    if (!$xml) continue;

    foreach ($xml->channel->xpath('//item') as $xml_item) {
        $feed_item = [
            'title' => strip_tags(trim($xml_item->title)),
            'description' => strip_tags(trim($xml_item->description)),
            'link' => strip_tags(trim($xml_item->link)),
            'date' => strtotime($xml_item->pubDate),
            'source' => $row['name']
        ];
        $feed[] = $feed_item;
    }
}

// Sorting feeds by date
usort($feed, function($a, $b) {
    return $b['date'] - $a['date'];
});
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض الأخبار</title>
    <link href="public/styles.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-bold mb-4 text-center">عرض الأخبار</h2>

        <div class="space-y-4">
            <?php foreach ($feed as $item): ?>
                <div class="bg-white shadow-md rounded p-4">
                    <a href="<?= $item['link'] ?>" target="_blank" class="block text-xl font-bold mb-2"><?= shorten_text($item['title'], 60) ?></a>
                    <p class="text-sm text-gray-700 mb-2"><?= shorten_text($item['description'], 150) ?></p>
                    <div class="flex justify-between items-center">
                        <small class="text-gray-500"><?= $item['source'] ?></small>
                        <small class="text-gray-500"><?= date('Y-m-d H:i', $item['date']) ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
