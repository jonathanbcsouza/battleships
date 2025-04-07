<?php

namespace App\Configs;

// Define constants in the global namespace
if (!defined('HIT_DIST')) define("HIT_DIST", 0);
if (!defined('HOT_DIST')) define("HOT_DIST", 1);
if (!defined('WARM_DIST')) define("WARM_DIST", 3);
if (!defined('COLD_DIST')) define("COLD_DIST", 5);

if (!defined('EMPTY_ICON')) define("EMPTY_ICON", '🪼');
if (!defined('SHIP_ICON')) define("SHIP_ICON", '🚢');
if (!defined('ROCKET_ICON')) define("ROCKET_ICON", '🚀');
if (!defined('EXPLOSION_ICON')) define("EXPLOSION_ICON", '💥');
if (!defined('TROPHIE_ICON')) define("TROPHIE_ICON", '🏆');

if (!defined('GRID_SIZE')) define("GRID_SIZE", 8);
if (!defined('NUM_ROCKETS')) define("NUM_ROCKETS", 20);
if (!defined('NUM_SHIPS')) define("NUM_SHIPS", 2);
