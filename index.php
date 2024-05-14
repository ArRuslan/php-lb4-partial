<?php

// task 1
class TableParser {
    private DOMDocument $dom;
    private DOMElement $bodyRoot;
    private DOMElement | null $currentTable = null;

    function __construct() {
        $this->dom = new DOMDocument();
        $html = $this->dom->createElement("html");
        $head = $this->dom->createElement("head");

        $title = $this->dom->createElement("title");
        $title->nodeValue = "Lb4";
        $head->append($title);

        $style = $this->dom->createElement("style");
        $style->nodeValue = "table, td { border: 1px solid black; }";
        $head->append($style);

        $this->bodyRoot = $this->dom->createElement("body");

        $html->append($head, $this->bodyRoot);
        $this->dom->append($html);
    }

    private function writeTable(string $type, string $value): void {
        if($this->currentTable != null) {
            $row = $this->dom->createElement("tr");
            $typeTd = $this->dom->createElement("td");
            $nameTd = $this->dom->createElement("td");

            $typeTd->nodeValue = $type;
            $nameTd->nodeValue = $value;

            $row->append($typeTd, $nameTd);
            $this->currentTable->append($row);
        }
    }

    function elementStart($parser, string $name, array $attrs): void {
        if($name === "TABLE") {
            $this->currentTable = $this->dom->createElement("table");
            $this->bodyRoot->append($this->currentTable);
        }

        $this->writeTable("Element start", $name);
    }

    function elementEnd($parser, string $name): void {
        $this->writeTable("Element end", $name);

        if($name === "TABLE") {
            $this->currentTable = null;
        }
    }

    function elementContent($parser, string $data): void {
        $this->writeTable("Element content", $data);
    }

    function save(string $filename): void {
        $this->dom->save($filename);
    }
}

$parser = xml_parser_create();
$table = new TableParser();

// task 2
xml_set_element_handler($parser, array($table, "elementStart"), array($table, "elementEnd"));
xml_set_character_data_handler($parser, array($table, "elementContent"));

// task 3 ??
$xml_data = "<table><tr><td>Row 1, Cell 1</td><td>Row 1, Cell 2</td></tr><tr><td>Row 2, Cell 1</td><td>Row 2, Cell 2</td></tr></table>";

if (!xml_parse($parser, $xml_data, true)) {
    die(sprintf("XML error: %s at line %d",
        xml_error_string(xml_get_error_code($parser)),
        xml_get_current_line_number($parser)));
}

$table->save("idk.html");
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
