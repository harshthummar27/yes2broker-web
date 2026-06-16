<?php

namespace App\Services\Legacy;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class LegacySqlReader
{
    private const TABLE = 'legacy_properties_import';

    /**
     * @return Collection<int, object>
     */
    public function records(): Collection
    {
        $this->importToStagingTable();

        return collect(DB::table(self::TABLE)->orderBy('id')->get());
    }

    private function importToStagingTable(): void
    {
        $path = base_path('properties_new.sql');

        if (! is_file($path)) {
            throw new RuntimeException('properties_new.sql not found in project root.');
        }

        Schema::dropIfExists(self::TABLE);

        $sql = file_get_contents($path);

        if ($sql === false) {
            throw new RuntimeException('Unable to read properties_new.sql');
        }

        $sql = str_replace(
            [
                'DROP TABLE IF EXISTS `properties`;',
                'CREATE TABLE `properties`',
                'INSERT INTO `properties`',
            ],
            [
                'DROP TABLE IF EXISTS `'.self::TABLE.'`;',
                'CREATE TABLE `'.self::TABLE.'`',
                'INSERT INTO `'.self::TABLE.'`',
            ],
            $sql
        );

        DB::unprepared($sql);
    }

    public function cleanup(): void
    {
        Schema::dropIfExists(self::TABLE);
    }
}
