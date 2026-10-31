<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Config;

/**
 * A cached manifest of the classes Acorn discovers at boot (commands,
 * providers, listeners).
 *
 * Discovery normally scans the filesystem and reflects every candidate file on
 * each invocation. `acorn:optimize` writes this manifest once; when it is
 * present the Application loads the listed classes directly and skips the scan,
 * which is a meaningful win for package-rich apps. `acorn:clear` removes it.
 */
final class DiscoveryManifest
{
    public function __construct(private readonly string $path)
    {
        //
    }

    public function delete(): bool
    {
        if ($this->exists()) {
            return unlink($this->path);
        }

        return false;
    }

    public function exists(): bool
    {
        return is_file($this->path);
    }

    public function path(): string
    {
        return $this->path;
    }

    /**
     * Read the manifest, returning null when it is missing or malformed so the
     * caller falls back to a live scan rather than trusting a corrupt file.
     *
     * @return array{commands: list<string>, providers: list<string>, listeners: list<string>}|null
     */
    public function read(): ?array
    {
        if (!$this->exists()) {
            return null;
        }

        $content = file_get_contents($this->path);
        if ($content === false) {
            return null;
        }

        $data = json_decode($content, true);
        if (!is_array($data)) {
            return null;
        }

        return [
            'commands' => $this->stringList($data['commands'] ?? []),
            'providers' => $this->stringList($data['providers'] ?? []),
            'listeners' => $this->stringList($data['listeners'] ?? []),
        ];
    }

    /**
     * Write the manifest to disk.
     *
     * @param array{commands: list<string>, providers: list<string>, listeners: list<string>} $manifest
     */
    public function write(array $manifest): bool
    {
        $dir = dirname($this->path);

        if (!is_dir($dir) && !mkdir($dir, 0700, true) && !is_dir($dir)) {
            return false;
        }

        return file_put_contents($this->path, json_encode($manifest, JSON_PRETTY_PRINT)) !== false;
    }

    /**
     * @param mixed $value
     * @return list<string>
     */
    private function stringList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return array_values(array_filter($value, 'is_string'));
    }
}
