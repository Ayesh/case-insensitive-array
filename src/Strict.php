<?php

namespace Ayesh\CaseInsensitiveArray;

class Strict implements \Iterator, \ArrayAccess, \Countable {
    /**
     * @var array
     */
  protected $container = [];

  protected function getHash($key): string {
    return \strtolower($key);
  }

  /**
   * Hash the initial array, and store in the container.
   *
   * @param array $array Optional, an array to start with.
   */
  public function __construct(array $array = []) {
    $this->container = [];

    foreach ($array as $key => $value) {
      $this->offsetSet($key, $value);
    }
  }

  public function offsetExists($offset): bool {
    return isset($this->container[ $this->getHash($offset) ]);
  }

  public function offsetUnset($offset): void {
    $hash = $this->getHash($offset);
    unset($this->container[$hash]);
  }

  public function offsetGet($offset) {
    $hash = $this->getHash($offset);
    if (isset($this->container[$hash])) {
      return $this->container[$hash][0];
    }

    return NULL;
  }


  /**
   * Store the given key and value into the container, and its hash to the
   * hashes list.
   * @param string|null $offset
   * @param mixed $value
   */
  public function offsetSet($offset, $value): void {
    if (NULL === $offset) {
      $this->container[] = [$value, NULL];
      return;
    }
    $this->container[$this->getHash($offset)] = [$value, $offset];
  }

  public function count(): int {
    return \count($this->container);
  }

  public function valid(): bool {
    $key = $this->key();
    return isset($this->container[$this->getHash($key)][0]);
  }

  public function current() {
    $subset = \current($this->container);
    return $subset[0];
  }

  public function key(): ?string {
    $subset = \current($this->container);
    if (!isset($subset[1])) {
      return \key($this->container);
    }
    return $subset[1];
  }

  public function rewind() {
    return \reset($this->container);
  }

  public function next() {
    return \next($this->container);
  }

  public function __debugInfo(): array {
    $return = [];
    foreach ($this->container as $container) {
      if (isset($container[1])) {
        $return[$container[1]] = $container[0];
      }
      else {
        $return[] = $container[0];
      }
    }

    return $return;
  }

}
