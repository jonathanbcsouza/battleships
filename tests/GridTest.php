<?php

use App\Classes\Grid;
use PHPUnit\Framework\TestCase;

class GridTest extends TestCase
{
    private Grid $grid;
    private int $size = 10;
    private int $numShips = 5;

    protected function setUp(): void
    {
        $this->grid = new Grid($this->size, $this->numShips);
    }

    public function testGridSize(): void
    {
        $gridArray = $this->grid->getGrid();
        $this->assertCount($this->size, $gridArray);

        foreach ($gridArray as $row) {
            $this->assertCount($this->size, $row);
        }
    }

    public function testShipsPlacement(): void
    {
        $gridArray = $this->grid->getGrid();
        $shipCount = 0;

        foreach ($gridArray as $row) {
            foreach ($row as $cell) {
                if ($cell === SHIP_ICON) {
                    $shipCount++;
                }
            }
        }

        $this->assertEquals($this->numShips, $shipCount);
    }
}
