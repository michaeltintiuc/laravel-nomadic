<?php

namespace ChrisHalbert\LaravelNomadic;

use Illuminate\Database\Migrations\MigrationCreator;

/**
 * Class NomadicMigrationCreator
 * @package ChrisHalbert\LaravelNomadic
 */
class NomadicMigrationCreator extends MigrationCreator
{
    /**
     * Get the path to the stubs.
     * @return string
     */
    public function getStubPath()
    {
        return __DIR__ . '/stubs';
    }

    /**
     * Get the path to the stubs.
     * @return string
     */
    public function stubPath()
    {
        return $this->getStubPath();
    }
}
