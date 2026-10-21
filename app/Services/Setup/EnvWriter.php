<?php

namespace App\Services\Setup;

class EnvWriter
{
    public function path(): string
    {
        return base_path('.env');
    }

    public function exists(): bool
    {
        return is_file($this->path());
    }

    /**
     * @param  array<string, string|null>  $values
     */
    public function write(array $values): void
    {
        $content = $this->exists()
            ? (string) file_get_contents($this->path())
            : (string) file_get_contents(base_path('.env.example'));

        foreach ($values as $key => $value) {
            $content = $this->setValue($content, $key, $this->escape((string) $value));
        }

        file_put_contents($this->path(), $content);
    }

    public function escape(string $value): string
    {
        if ($value === '') {
            return '';
        }

        if (preg_match('/[\s#="\']/', $value)) {
            return '"'.str_replace(['\\', '"'], ['\\\\', '\\"'], $value).'"';
        }

        return $value;
    }

    private function setValue(string $content, string $key, string $value): string
    {
        $pattern = '/^'.preg_quote($key, '/').'=.*/m';

        if (preg_match($pattern, $content)) {
            return (string) preg_replace($pattern, $key.'='.$value, $content);
        }

        return rtrim($content).PHP_EOL.$key.'='.$value.PHP_EOL;
    }
}
