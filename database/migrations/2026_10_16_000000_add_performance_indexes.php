<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Indeks untuk query yang dijalankan di hampir setiap halaman publik:
     * Article::published() -> where status = 1 and publish_date <= now() order by publish_date.
     */
    private array $indexes = [
        'articles' => [
            'articles_status_publish_date_index' => ['status', 'publish_date'],
        ],
        'comments' => [
            'comments_article_id_created_at_index' => ['article_id', 'created_at'],
        ],
    ];

    public function up(): void
    {
        foreach ($this->indexes as $tableName => $indexes) {
            $existing = $this->existingIndexes($tableName);

            foreach ($indexes as $name => $columns) {
                if (!in_array($name, $existing, true)) {
                    Schema::table($tableName, fn (Blueprint $table) => $table->index($columns, $name));
                }
            }
        }
    }

    public function down(): void
    {
        foreach ($this->indexes as $tableName => $indexes) {
            $existing = $this->existingIndexes($tableName);

            foreach (array_keys($indexes) as $name) {
                if (in_array($name, $existing, true)) {
                    Schema::table($tableName, fn (Blueprint $table) => $table->dropIndex($name));
                }
            }
        }
    }

    private function existingIndexes(string $table): array
    {
        $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes($table);

        return array_map('strtolower', array_keys($indexes));
    }
};
