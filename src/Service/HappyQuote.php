<?php
// src/Service/HappyQuote.php
namespace App\Service;

class HappyQuote
{
    private array $quotes = [
        "Aujourd'hui est un nouveau jour !",
        "Crois en toi et tout est possible.",
        "Un sourire peut changer ta journée.",
        "Chaque petit pas compte.",
        "La persévérance mène au succès."
    ];

    public function getHappyMessage(): string
    {
        return $this->quotes[array_rand($this->quotes)];
    }
}
