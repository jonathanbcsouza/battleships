<?php

use App\Classes\Grid;
use PHPUnit\Framework\TestCase;

class GridTest extends TestCase
{
    private Grid $grid;
    private int $size = 10;
    private int $num_ships = 5;

    protected function setUp(): void
    {
        $this->grid = new Grid($this->size, $this->num_ships);
    }

    public function testGridSize(): void
    {
        $grid_array = $this->grid->getGrid();
        $this->assertCount($this->size, $grid_array);

        foreach ($grid_array as $row) {
            $this->assertCount($this->size, $row);
        }
    }

    public function testShipsPlacement(): void
    {
        $grid_array = $this->grid->getGrid();
        $ship_count = 0;

        foreach ($grid_array as $row) {
            foreach ($row as $cell) {
                if ($cell === SHIP_ICON) {
                    $ship_count++;
                }
            }
        }

        $this->assertEquals($this->num_ships, $ship_count);
    }
}
