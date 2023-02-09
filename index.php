<?php
    date_default_timezone_set('Europe/Istanbul');
    header("Access-Control-Allow-Origin: *");
    header('Content-type: application/json');

$content = iconv('windows-1254', 'utf-8', file_get_contents("http://www.koeri.boun.edu.tr/scripts/lst0.asp"));

preg_match_all("/<pre>(.*?)<\/pre>/s", $content, $pre);

$rows = explode("\n", str_replace(["<pre>", "</pre>"], "", $pre[0][0]));
for ($i = 0; $i < 7; $i++) array_shift($rows);

$rows = array_filter(array_map(function ($row) {

    $parts = explode('  ', $row);

    $parts = array_filter($parts, function ($row) {
        return strlen($row) > 1;
    });

    $parts = array_map(function ($part) {

        if ($part === '-.-') return null;

        return strip_tags(
            htmlspecialchars(
                str_replace(
                    ["\t", "\s", "\w", "\r", "\n"],
                    '',
                    trim($part)
                )
            )
        );
    }, $parts);

    return array_values($parts);

}, $rows), function ($row) {

    return count($row) > 2;
});

$arr = [];


foreach ($rows as $row) {

    $arr[] = [
        'hash' => md5($row[0] . sprintf("%s,%s", $row[1], $row[2]) . $row[5] . $row[7]),
        'date' => $row[0],
        'geolocation' => sprintf("%s,%s", $row[1], $row[2]),
        'depth' => $row[3],
        'ml' => $row[5],
        'lokasyon' => $row[7],
        'precision' => $row[8]
    ];
}

echo json_encode(array(
    "status" => "true",
    "serverloadms"=>floor(microtime(true) * 1000),
    "name" => "api.depremtr.com.tr",
    "desc" => "Lutfen gereksiz yere kullanmayiniz!",
    'source' => 'http://www.koeri.boun.edu.tr',
    'data' => $last_entry = reset($arr)
));