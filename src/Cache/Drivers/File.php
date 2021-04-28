<?php
declare(strict_types=1);

namespace Max\Cache\Drivers;

use Max\Cache\Driver;

class File extends Driver
{

    /**
     * 缓存路径
     * @var string
     */
    protected $path;

    /**
     * 初始化缓存路径
     * File constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->path = env('cache_path') . 'app' . DIRECTORY_SEPARATOR;
        \Max\Tools\File::mkdir($this->path);
    }

    /**
     * 缓存存在判断
     * @param string $key
     * @return bool|void
     */
    public function has($key)
    {
        return file_exists($this->path . $this->_uniqueName($key));
    }

    /**
     * 缓存hash
     * @param string $key
     * @return string
     */
    protected function _uniqueName(string $key)
    {
        return md5(strtolower($key));
    }

    /**
     * 文件缓存设置
     * @param string $key
     * @param null $default
     * @return false|mixed|string|void
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            if (false === ($value = file_get_contents($this->path . $this->_uniqueName($key)))) {
                throw new \InvalidArgumentException('Cache not found: ' . $key, 999);
            }
            return $value;
        }
        return $default;
    }

    /**
     * 缓存设置
     * @param string $key
     * @param string $value
     * @return false|int
     * false 写入失败，int 写入的字节
     */
    public function set($key, $value, $ttl = NULL)
    {
        return file_put_contents($this->path . $this->_uniqueName($key), $value);
    }
}
