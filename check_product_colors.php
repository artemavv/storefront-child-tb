<?php
// URL of the XML document
$xmlUrl = 'https://tannybunny.com/wp-content/uploads/woo-feed/google/xml/testing-feed.xml'; // Replace with the actual URL

// Load the XML document
$xml = simplexml_load_file($xmlUrl);

// Register the namespace
$xml->registerXPathNamespace('g', 'http://base.google.com/ns/1.0'); 

if ($xml === false) {
    die('Error: Unable to load XML document.');
}

// Start the HTML table
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Items List</title>
    <style>
        table {
            width: 50%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>';

echo date('U');

/*
echo ('<pre>');
print_r($xml->channel->item[0]);
echo ('</pre>');
*/

    echo '<h1>Items List</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Color</th>
        </tr>';

    
// Loop through each item in the XML document

// $p_cnt = count($xml->channel->item);

foreach ($xml->xpath('//item') as $item) {

    $mpn = (string)$item->xpath('g:mpn')[0];
    $title = (string)$item->xpath('g:title')[0];
    $color = (string)$item->xpath('g:color')[0];
  
  // Output a row in the HTML table for each item
  echo "<tr>
          <td>$mpn</td>
          <td>$title</td>
          <td>$color</td>
        </tr>";

}

// End the HTML table
echo '</table>
</body>
</html>';
?>