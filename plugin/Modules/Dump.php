<?php

namespace GeminiLabs\SiteReviews\Modules;

/**
 * @see: https://github.com/jacobstr/dumpling
 */
class Dump
{
    public $depth;
    public $ignore;

    protected $level = 0;
    protected $result = [];
    protected $stack = [];

    /**
     * @param mixed $value
     * @param int $depth
     * @return string
     */
    public function dump($value, $depth = 3, array $ignore = [])
    {
        $this->depth = glsr()->filterInt('console/depth', $depth);
        $this->ignore = $ignore;
        $this->reset();
        $this->inspect($value);
        $result = rtrim(implode('', $this->result), "\n");
        $this->reset();
        return $result;
    }

    /**
     * @param string $key
     * @return string
     */
    protected function formatKey($key)
    {
        $result = [];
        $result[] = str_repeat(' ', $this->level * 4).'[';
        if (is_string($key) && "\0" === $key[0]) {
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
     * @return void
     */
    protected function inspect($subject)
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

    /**
     * @return void
     */
    protected function inspectArray(array $subject)
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
        $this->result[] = str_repeat(' ', ($this->level - 1) * 4).")\n";
    }

    /**
     * @return void
     */
    protected function inspectClosure(\Closure $subject)
    {
        $reflection = new \ReflectionFunction($subject);
        $params = array_map(function ($param) {
            return ($param->isPassedByReference() ? '&$' : '$').$param->name;
        }, $reflection->getParameters());
        $this->result[] = 'Closure ('.implode(', ', $params).') { ... }'."\n";
    }

    /**
     * @param object $subject
     * @return void
     */
    protected function inspectObject($subject)
    {
        $classname = get_class($subject);
        if ($this->level > $this->depth) {
            $this->result[] = 'Nested '.$classname." Object\n";
            return;
        }
        if ($subject instanceof \ArrayObject) {
            $this->result[] = $classname." ArrayObject (\n";
        } else {
            $this->result[] = $classname." Object (\n";
            $subject = (array) $subject;
        }
        foreach ($subject as $key => $val) {
            if (false === $this->isIgnoredKey($key)) {
                $this->result[] = $this->formatKey($key);
                $this->inspect($val);
            }
        }
        $this->result[] = str_repeat(' ', ($this->level - 1) * 4).")\n";
    }

    /**
     * @param mixed $subject
     * @return void
     */
    protected function inspectPrimitive($subject)
    {
        if (true === $subject) {
            $subject = '(bool) true';
        } elseif (false === $subject) {
            $subject = '(bool) false';
        } elseif (null === $subject) {
            $subject = '(null)';
        }
        $this->result[] = $subject."\n";
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function isIgnoredKey($key)
    {
        return in_array($key, $this->ignore);
    }

    /**
     * @return void
     */
    protected function reset()
    {
        $this->level = 0;
        $this->result = [];
        $this->stack = [];
    }
}
