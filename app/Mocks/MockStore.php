<?php

namespace App\Mocks;

use App\Support\ApiMock;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * Armazenamento em memória (com persistência opcional) dos dados mock.
 */
class MockStore
{
    private static ?array $data = null;

    private static function statePath(): string
    {
        return storage_path('app/mocks/state.json');
    }

    public static function all(): array
    {
        if (self::$data === null) {
            self::load();
        }

        return self::$data;
    }

    public static function reset(): void
    {
        self::$data = MockData::seed();

        if (File::exists(self::statePath())) {
            File::delete(self::statePath());
        }
    }

    public static function collection(string $key): array
    {
        $data = self::all();

        return $data[$key] ?? [];
    }

    public static function find(string $key, $id, string $idField): ?array
    {
        foreach (self::collection($key) as $item) {
            if (isset($item[$idField]) && (string) $item[$idField] === (string) $id) {
                return $item;
            }
        }

        return null;
    }

    public static function create(string $key, array $record, string $idField): array
    {
        $data = self::all();
        $items = $data[$key] ?? [];

        if (!isset($record[$idField])) {
            $counters = $data['_counters'] ?? [];
            $nextId = ($counters[$key] ?? count($items)) + 1;
            $record[$idField] = $nextId;
            $data['_counters'][$key] = $nextId;
        }

        $items[] = $record;
        $data[$key] = array_values($items);

        self::save($data);

        return $record;
    }

    public static function update(string $key, $id, array $changes, string $idField): ?array
    {
        $data = self::all();
        $updated = null;

        foreach ($data[$key] ?? [] as $index => $item) {
            if (isset($item[$idField]) && (string) $item[$idField] === (string) $id) {
                $data[$key][$index] = array_merge($item, $changes);
                $updated = $data[$key][$index];
                break;
            }
        }

        if ($updated !== null) {
            self::save($data);
        }

        return $updated;
    }

    public static function delete(string $key, $id, string $idField): bool
    {
        $data = self::all();
        $before = count($data[$key] ?? []);

        $data[$key] = array_values(array_filter(
            $data[$key] ?? [],
            fn ($item) => !isset($item[$idField]) || (string) $item[$idField] !== (string) $id
        ));

        if (count($data[$key]) < $before) {
            self::save($data);
            return true;
        }

        return false;
    }

    public static function softDelete(string $key, $id, string $idField): bool
    {
        return self::update($key, $id, ['deleted_at' => now()->format('Y-m-d H:i:s')], $idField) !== null;
    }

    private static function load(): void
    {
        $path = self::statePath();

        if (ApiMock::persist() && File::exists($path)) {
            try {
                self::$data = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
                return;
            } catch (\Throwable $e) {
                Log::warning('MockStore: state.json inválido, usando seed.', ['error' => $e->getMessage()]);
            }
        }

        self::$data = MockData::seed();
    }

    private static function save(array $data): void
    {
        self::$data = $data;

        if (!ApiMock::persist()) {
            return;
        }

        $dir = dirname(self::statePath());
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        File::put(self::statePath(), json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
