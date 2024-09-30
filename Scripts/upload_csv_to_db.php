<?php

include "dbconnection.php";

class Upload {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
        if($this->conn->connect_error) {
            die("Connection Failed: " . $this->conn->connect_error);
        }
    }
    public function loadJson($jsonfile) {
        $carddata = file_get_contents($jsonfile);
        $cards = json_decode($carddata, true);
        if (!$cards) {
            die("ERROR: failed to decode json.");
        }
        return $cards;
    }
    public function insertData($cards, $jsonfile) {
        $jsondata = file_get_contents($jsonfile);
        $cards = json_decode($jsondata, true);
        foreach($cards as $card) {
            $cardid = $card["id"] ?? null;
            $mana_cost = $this->getManaCost($card['mana_cost'] ?? null);
            $mana_type = $this->getManaType($card['produced_mana'] ?? []);
            $mana_value = $card['cmc'] ?? null;
            $power = isset($card['power']) ? intval($card['power']) : null;
            $toughness = isset($card['toughness']) ? intval($card['toughness']) : null;
            $expansion = $card['set_name'] ?? null;
            $rarity = $card['rarity'] ?? null;
            $card_number = isset($card['collector_number']) ? intval($card['collector_number']) : null;
            $artist = $card['artist'] ?? null;
            $this->insertCard($cardid, $mana_cost, $mana_type, $mana_value, $power, $toughness, $expansion, $rarity, $card_number, $artist);
        }
    }
    private function insertCard($cardid, $mana_cost, $mana_type, $mana_value, $power, $toughness, $expansion, $rarity, $card_number, $artist) {
        $sql = "INSERT INTO magic_criteria (card_id, mana_cost, mana_type, mana_value, power, toughness, expansion, rarity, card_number, artist) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $statement = $this->conn->prepare($sql);
        if($statement === false) {
            die("ERROR: " . $this->conn->error);
        }
        $statement->bind_param(
            'sisisissis',
            $cardid,
            $mana_cost,
            $mana_type,
            $mana_value,
            $power,
            $toughness,
            $expansion,
            $rarity,
            $card_number,
            $artist
        );
        if(!$statement->execute()) {
            die("ERROR: " . $statement->error);
        }
        $statement->close();
    }
    private function getManaCost($mana_cost) {
        if(empty($mana_cost)) {
            return null;
        }
        return strlen(preg_replace('/[^0-9]/', '', $mana_cost)) + substr_count($mana_cost, '{');
    }
    private function getManaType($mana_type) {
        return !empty($mana_types) ? implode(',', $mana_types) : null;
    }
}
$upload = new Upload($conn);
$upload->loadJson('null info right now');

$conn->close();
?>
