<?php

// task 1
function elementStart($parser, string $name, array $attrs): void {
    echo "<$name>";
}

function elementEnd($parser, string $name): void {
    echo "</$name>";
}

function elementContent($parser, string $data): void {
    echo $data;
}

$parser = xml_parser_create();

// task 2
xml_set_element_handler($parser, "elementStart", "elementEnd");
xml_set_character_data_handler($parser, "elementContent");

// task 3 ??
$xml_data = "<table><tr><td>Row 1, Cell 1</td><td>Row 1, Cell 2</td></tr><tr><td>Row 2, Cell 1</td><td>Row 2, Cell 2</td></tr></table>";

if (!xml_parse($parser, $xml_data, true)) {
    die(sprintf("XML error: %s at line %d",
        xml_error_string(xml_get_error_code($parser)),
        xml_get_current_line_number($parser)));
}
echo "\n\n";

// task 4
$dom = new DOMDocument();

$xml_file = "users.xml";
if (file_exists($xml_file)) {
    $dom->load($xml_file);
    $root = $dom->documentElement;
} else {
    $root = $dom->createElement("users");

    for($i = 0; $i < 10; $i++) {
        $user = $dom->createElement("user");

        $username = $dom->createElement("username");
        $username->nodeValue = "user$i";
        $email = $dom->createElement("email");
        $email->nodeValue = "user$i@gmail.com";

        $user->append($username, $email);
        $root->appendChild($user);
    }

    $dom->appendChild($root);
    $dom->save($xml_file);
}

foreach($root->childNodes as $user) {
    if($user->localName != "user") {
        continue;
    }
    foreach($user->childNodes as $node) {
        if($node->localName != "username" && $node->localName != "email") {
            continue;
        }
        echo "$node->localName: $node->nodeValue\n";
    }
    echo "\n";
}
