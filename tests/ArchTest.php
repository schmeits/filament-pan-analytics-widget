<?php

declare(strict_types=1);

/*
 * tests/ArchTest.php
 *
 * Architecture rules for the package source.
 */

arch('does not use debugging functions', function () {
    expect(['dd', 'dump', 'ray', 'var_dump'])->each->not->toBeUsed();
});

arch('declares strict types everywhere', function () {
    expect('Schmeits\FilamentPanAnalyticsWidget')->toUseStrictTypes();
});
