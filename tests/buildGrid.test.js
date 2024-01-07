import { buildGrid } from '../helpers/helpers.js';

test('buildGrid: creates a grid of the correct size', () => {
  const grid = buildGrid(10);
  expect(grid.length).toBe(10);
  for (let row of grid) {
    expect(row.length).toBe(10);
  }
});