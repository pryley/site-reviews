<?php

/**
 * Which stubs must be loaded before which.
 *
 * A stub whose class-likes extend, implement or `use` another stub's needs that other stub
 * required first: PHP resolves a parent at declaration time, and nothing autoloads the stubs.
 * The generator allows such an edge on purpose — pruneDanglingClassLikes() counts a parent
 * found in another stub as resolvable, because the files load together — so the order has to
 * be stated somewhere, and this is it. Keys and values are stub filenames; edges are direct
 * only, the loader follows them transitively.
 *
 * Read by tests/pest/mu-plugins/site-reviews-tests.php, which loads the stubs in this order,
 * and by tests/bin/generate-stubs.php, which fails `make stubs` on an edge that is not listed
 * here. Do not hand-maintain it from memory: regenerate and let the scan name the edge.
 */

return [
    'elementorpro.php' => ['elementor.php'], // Elementor\Widget_Base and 71 other parents
    'surecart.php' => ['bricks.php'], // Bricks\Element, under 27 SureCart elements
];
