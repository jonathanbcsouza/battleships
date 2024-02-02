<?php

namespace App\Classes;

require_once __DIR__ . '/../Configs/constants.php';

class Grid
{
    private int $size;
    private array $grid;
    private int $num_ships;
    private string $empty_icon;
    private string $ship_icon;

    public function __construct(int $size, int $num_ships)
    {
        $this->size = $size;
        $this->num_ships = $num_ships;
        $this->empty_icon = EMPTY_ICON;
        $this->ship_icon = SHIP_ICON;
        $this->grid = $this->buildGrid();
        $this->placeShips();
    }

    private function buildGrid(): array
    {
        $grid = array_fill(0, $this->size, array_fill(0, $this->size, $this->empty_icon));
        return $grid;
    }

    private function placeShips(): void
    {
        $ships_placed = 0;
        while ($ships_placed < $this->num_ships) {
            $randomRow = rand(0, $this->size - 1);
            $randomCol = rand(0, $this->size - 1);

            if ($this->grid[$randomRow][$randomCol] == $this->empty_icon) {
                $this->grid[$randomRow][$randomCol] = $this->ship_icon;
                $ships_placed++;
            }
        }
    }

    public function getGrid(): array
    {
        return $this->grid;
    }
}
