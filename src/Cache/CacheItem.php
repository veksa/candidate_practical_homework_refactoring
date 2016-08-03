<?php
namespace Language\Cache;

use DateTime;
use DateTimeInterface;
use Psr\Cache\CacheItemInterface;

class CacheItem implements CacheItemInterface
{
    /**
     * @var string;
     */
    private $key;

    /**
     * @var mixed
     */
    private $value = null;

    /**
     * @var DateTimeInterface
     */
    private $expiresAt;

    /**
     * @var bool
     */
    private $isHit = false;

    /**
     * CacheItem constructor.
     * @param string $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Returns the key for the current cache item.
     *
     * @return string The key string for this cache item.
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Retrieves the value of the item from the cache associated with this object's key.
     *
     * @return mixed The value corresponding to this cache item's key, or null if not found.
     */
    public function get()
    {
        if ($this->isHit()) {
            return $this->value;
        }

        // expired
        return null;
    }

    /**
     * Confirms if the cache item lookup resulted in a cache hit.
     *
     * @return bool True if the request resulted in a cache hit. False otherwise.
     */
    public function isHit()
    {
        if (!$this->isHit) {
            return false;
        }
        if (null === $this->expiresAt) {
            return true;
        }
        $now = new DateTime();

        return $now < $this->expiresAt;
    }

    /**
     * Sets the value represented by this cache item.
     *
     * @param mixed $value The serializable value to be stored.
     *
     * @return static The invoked object.
     */
    public function set($value)
    {
        $this->isHit = true;
        $this->value = $value;

        return $this;
    }

    /**
     * Sets the expiration time for this cache item.
     *
     * @param \DateTimeInterface $expiration
     *
     * @return static The called object.
     */
    public function expiresAt($expiration)
    {
        $this->expiresAt = $expiration;

        return $this;
    }

    /**
     * Sets the expiration time for this cache item.
     *
     * @param int|\DateInterval $time
     *
     * @return static The called object.
     */
    public function expiresAfter($time)
    {
        if ($time instanceof \DateInterval) {
            $this->expiresAt = (new DateTime())->add($time);
        } elseif (is_numeric($time)) {
            $this->expiresAt = (new DateTime())->add(new \DateInterval('PT' . $time . 'S'));
        } else {
            $this->expiresAt = null;
        }

        return $this;
    }

    /**
     * Checks if a key is valid for APCu cache storage
     *
     * @param $key
     * 
     * @return bool
     */
    public static function isValidKey($key)
    {
        $invalid = '{}()/\@:';
        if (is_string($key) && !preg_match('/[' . preg_quote($invalid, '/') . ']/', $key)) {
            return true;
        }

        return false;
    }
}