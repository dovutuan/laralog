<?php

namespace Dovutuan\Laralog\Traits;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

trait LogQuery
{
    private Filesystem $disk;

    /**
     * function log query
     * @return void
     */
    private function logQuery(): void
    {
        if (config('laralog.log_query')) {
            $disk = Storage::disk(config('laralog.disk'));
            $maxSize = config('laralog.size_file');
            $formatDate = config('laralog.format_date');
            $formatDateTime = config('laralog.format_date_time');

            $nameFix = 'query/sql-' . date($formatDate);
            $index = 1;
            $name = "{$nameFix}-{$index}.sql";
            while ($disk->exists($name) && $disk->size($name) >= $maxSize) {
                $index++;
                $name = "{$nameFix}-{$index}.sql";
            }
            $disk->append($name, "----------START---------");

            DB::listen(function ($query) use ($name, $formatDateTime, $disk) {
                $binding = $query->bindings;
                $binding = array_map(
                    fn($bd) => is_object($bd) ? "'" . $bd->format($formatDateTime) . "'" : "'$bd'",
                    $binding
                );

                $boundSql = str_replace(['%', '?'], ['%%', '%s'], $query->sql);
                $boundSql = vsprintf($boundSql, $binding);

                $sql = "Date: " . date($formatDateTime) . "\n";
                $sql .= "Time query: $query->time(ms)\n";
                $sql .= "$boundSql;\n";
                $sql .= "----------END----------\n";

                $disk->append($name, $sql);
            });
        }
    }

}