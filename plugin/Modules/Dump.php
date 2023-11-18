<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Helpers\Cast;

/**
 * @see: https://github.com/jacobstr/dumpling
 */
class Dump
{
    public $depth;
    public $ignore;

    protected int $level = 0;
    protected array $result = [];
    protected array $stack = [];

    /**
     * @param mixed $value
     */
    public function dump($value, int $depth = 3, array $ignore = []): string
    {
        $this->depth = glsr()->filterInt('console/depth', $depth);
        $this->ignore = $ignore;
        $this->reset();
        $this->inspect($value);
        $result = rtrim(implode('', $this->result), "\n");
        $this->reset();
        return $result;
    }

    protected function formatKey(string $key): string
    {
        $result = [];
        $result[] = str_repeat(' ', $this->level * 4).'[';
        if ("\0" === $key[0]) {
            $keyParts = explode("\0", $key);
            $result[] = $keyParts[2].(('*' === $keyParts[1]) ? ':protected' : ':private');
        } else {
            $result[] = $key;
        }
        $result[] = '] => ';
        return implode('', $result);
    }

    /**
     * @param mixed $subject
     */
    protected function inspect($subject): void
    {
        ++$this->level;
        if ($subject instanceof \Closure) {
            $this->inspectClosure($subject);
        } elseif (is_object($subject)) {
            $this->inspectObject($subject);
        } elseif (is_array($subject)) {
            $this->inspectArray($subject);
        } else {
            $this->inspectPrimitive($subject);
        }
        --$this->level;
    }

    protected function inspectArray(array $subject): void
    {
        if ($this->level > $this->depth) {
            $this->result[] = "Nested Array\n";
            return;
        }
        if (empty($subject)) {
            $this->result[] = "Array ()\n";
            return;
        }
        $this->result[] = "Array (\n";
        foreach ($subject as $key => $val) {
            if (false === $this->isIgnoredKey($key)) {
                $this->result[] = str_repeat(' ', $this->level * 4).'['.$key.'] => ';
                $this->inspect($val);
            }
        }
        $spacing = str_repeat(' ', ($this->level - 1) * 4);
        $this->result[] = "{$spacing})\n";
    }

    protected function inspectClosure(\Closure $subject): void
    {
        $reflection = new \ReflectionFunction($subject);
        $params = array_map(function ($param) {
            return ($param->isPassedByReference() ? '&$' : '$').$param->name;
        }, $reflection->getParameters());
        $params = implode(', ', $params);
        $this->result[] = "Closure ({$params}) { ... }\n";
    }

    /**
     * @param mixed $subject
     */
    protected function inspectObject($subject): void
    {
        $classname = get_class($subject);
        if ($this->level > $this->depth) {
            $this->result[] = "Nested {$classname} Object\n";
            return;
        }
        if ($subject instanceof \ArrayObject) {
            $this->result[] = "{$classname} ArrayObject (\n";
        } else {
            $this->result[] = "{$classname} Object (\n";
            $subject = (array) $subject;
        }
        foreach ($subject as $key => $val) {
            if (false === $this->isIgnoredKey($key)) {
                $this->result[] = $this->formatKey(Cast::toString($key));
                $this->inspect($val);
            }
        }
        $this->result[] = str_repeat(' ', ($this->level - 1) * 4).")\n";
    }

    /**
     * @param mixed $subject
     */
    protected function inspectPrimitive($subject): void
    {
        if (true === $subject) {
            $subject = '(bool) true';
        } elseif (false === $subject) {
            $subject = '(bool) false';
        } elseif (null === $subject) {
            $subject = '(null)';
        }
        $this->result[] = "{$subject}\n";
    }

    protected function isIgnoredKey(string $key): bool
    {
        return in_array($key, $this->ignore);
    }

    protected function reset(): void
    {
        $this->level = 0;
        $this->result = [];
        $this->stack = [];
    }
}
