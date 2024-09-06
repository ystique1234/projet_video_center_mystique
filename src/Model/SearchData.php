<?php

namespace App\Model;

class SearchData
{
    public ?string $query = '';  // Initialisation à une chaîne vide
    public ?int $page = 1;       // Initialisation à 1 pour éviter les valeurs nulles
    public ?bool $isPremium = null;  // Nullable pour permettre la recherche avec ou sans filtre premium

    // Getter pour la propriété isPremium
    public function getIsPremium(): ?bool
    {
        return $this->isPremium;
    }

    // Setter pour la propriété isPremium
    public function setIsPremium(?bool $isPremium): self
    {
        $this->isPremium = $isPremium;
        return $this;
    }

    // Getter pour la propriété query
    public function getQuery(): ?string
    {
        return $this->query;
    }

    // Setter pour la propriété query
    public function setQuery(?string $query): self
    {
        $this->query = $query;
        return $this;
    }

    // Getter pour la propriété page
    public function getPage(): ?int
    {
        return $this->page;
    }

    // Setter pour la propriété page
    public function setPage(?int $page): self
    {
        $this->page = $page;
        return $this;
    }
}

