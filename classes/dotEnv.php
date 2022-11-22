<?php
class DotEnv
{
    protected $path;

    public function __construct(string $path)
    {
        try {
            if (!file_exists($path))
                throw new \InvalidArgumentException(sprintf('Cannot find file %s', $path));
            $this->path = $path;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function load(): void
    {
        try {
            if (!is_readable($this->path))
                throw new \InvalidArgumentException(sprintf('Cannot read file %s', $this->path));

            $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                list($name, $val) = explode('=', $line, 2);
                $name = trim($name);
                $val = trim($val);

                if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                    putenv(sprintf('%s=%s', $name, $val));
                    $_ENV[$name] = $val;
                    $_SERVER[$name] = $val;
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
