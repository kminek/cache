<?php

class Kminek_Cache
{
    const ENGINE_SERIALIZE = 1;
    const ENGINE_JSON = 2;
    const ENGINE_VAR_EXPORT = 3;

    /**
     * @var self
     */
    protected static $instance;

    /**
     * @var int
     */
    protected $engine;

    /**
     * @var string
     */
    protected $dir;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var string
     */
    protected $separator;

    /**
     * @var string
     */
    protected $extension;

    /**
     * @var int
     */
    protected $ttl;

    /**
     * @param self $instance
     */
    public static function setInstance($instance)
    {
        self::$instance = $instance;
    }

    /**
     * @return null|self
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * @param array $options
     */
    public static function configure($options = array())
    {
        self::setInstance(new self($options));
    }

    /**
     * @param array $options
     */
    public function __construct($options = array())
    {
        $defaults = array(
            'engine' => self::ENGINE_SERIALIZE,
            'dir' => sys_get_temp_dir(),
            'prefix' => strtolower(__CLASS__),
            'separator' => '_',
            'extension' => '.cache',
            'ttl' => 60 * 60,
        );
        $options = array_merge($defaults, $options);
        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $filename = $this->getFilename($key);
        if (!file_exists($filename)) {
            return $default;
        }
        switch ($this->engine) {
            case self::ENGINE_SERIALIZE:
            case self::ENGINE_JSON:
                $data = file_get_contents($filename);
                $dataArr = explode("\n", $data);
                $timestamp = (int) array_shift($dataArr);
                break;
            case self::ENGINE_VAR_EXPORT:
                list($timestamp, $value) = require $filename;
                break;
            default:
                throw new Exception('Undefined engine');
        }
        if (time() > $timestamp) {
            return $default;
        }
        if (isset($dataArr) && (self::ENGINE_SERIALIZE === $this->engine)) {
            $value = unserialize(implode("\n", $dataArr));
        }
        if (isset($dataArr) && (self::ENGINE_JSON === $this->engine)) {
            $value = json_decode(implode("\n", $dataArr), true);
        }

        return $value;
    }

    /**
     * @param string   $key
     * @param mixed    $value
     * @param null|int $ttl
     *
     * @return bool
     */
    public function set($key, $value, $ttl = null)
    {
        $filename = $this->getFilename($key);
        $ttl = $ttl ? $ttl : $this->ttl;
        $timestamp = time() + $ttl;
        switch ($this->engine) {
            case self::ENGINE_SERIALIZE:
                $data = $timestamp."\n".serialize($value);
                break;
            case self::ENGINE_JSON:
                $data = $timestamp."\n".json_encode($value);
                break;
            case self::ENGINE_VAR_EXPORT:
                $data = sprintf('<?php return array(%s, %s);', $timestamp, var_export($value, true));
                break;
            default:
                throw new Exception('Undefined engine');
        }

        return (bool) file_put_contents($filename, $data, LOCK_EX);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function delete($key)
    {
        $filename = $this->getFilename($key);
        if (!file_exists($filename)) {
            return false;
        }

        return unlink($filename);
    }

    /**
     * @return bool
     */
    public function clear()
    {
        $pattern = $this->dir.DIRECTORY_SEPARATOR.$this->prefix.'*'.$this->extension;
        $files = glob($pattern);
        foreach ($files as $file) {
            $unlink = unlink($file);
            if (!$unlink) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function getFilename($key)
    {
        return $this->dir.DIRECTORY_SEPARATOR.$this->prefix.$this->separator.$key.$this->extension;
    }
}
