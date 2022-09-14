<?php

declare(strict_types=1);

/**
 * Created on Thu Mar 17 2022
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2022 Tangkoko
 **/


namespace Nicoren\CronBundle\DependencyInjection\Configuration;

use function explode;
use function is_numeric;
use function md5;
use function parse_str;
use function preg_match;
use function preg_replace;
use function preg_replace_callback;
use function strpos;
use function strrpos;
use function strstr;
use function substr;
use function urldecode;

class RedisConfiguration
{
    protected string $dsn;

    protected ?string $password = null;

    protected ?string $username = null;

    protected ?string $host = null;

    /** @var int|string */
    protected $port = 6379;

    protected ?string $socket = null;

    protected bool $tls = false;

    /** @var string|int|null */
    protected $database = null;

    protected ?int $weight = null;

    protected ?string $alias = null;

    protected bool $isParsed = false;

    public function __construct(string $dsn, ?bool $isEnv = false)
    {
        $this->dsn = $dsn;
        if (!$isEnv) {
            $this->parseDsn($dsn);
        }
    }

    /** @return int|string|null */
    public function getDatabase()
    {
        $this->parseDsn($this->dsn);
        return $this->database;
    }

    public function setDatabase($database): self
    {
        $this->parseDsn($this->dsn);
        $this->database = $database;
        return $this;
    }

    public function getWeight(): ?int
    {
        $this->parseDsn($this->dsn);
        return $this->weight;
    }


    public function getHost(): ?string
    {
        $this->parseDsn($this->dsn);
        return $this->host;
    }

    public function getPassword(): ?string
    {
        $this->parseDsn($this->dsn);
        return $this->password;
    }

    public function setPassword($password): self
    {
        $this->parseDsn($this->dsn);
        $this->password = $password;
        return $this->password;
    }



    public function getUsername(): ?string
    {
        $this->parseDsn($this->dsn);
        return $this->username;
    }

    public function setUsername($username): self
    {
        $this->parseDsn($this->dsn);
        $this->username = $username;
        return $this;
    }


    /** @return string|int */
    public function getPort()
    {
        $this->parseDsn($this->dsn);
        if ($this->socket !== null) {
            return 0;
        }

        return $this->port ?: 6379;
    }

    public function getSocket(): ?string
    {
        $this->parseDsn($this->dsn);
        return $this->socket;
    }

    public function getTls(): bool
    {
        $this->parseDsn($this->dsn);
        return $this->tls;
    }

    public function getAlias(): ?string
    {
        $this->parseDsn($this->dsn);
        return $this->alias;
    }

    public function getPersistentId(): string
    {
        $this->parseDsn($this->dsn);
        return md5($this->dsn);
    }

    public function isValid(): bool
    {
        $this->parseDsn($this->dsn);
        if (strpos($this->dsn, 'redis://') !== 0 && strpos($this->dsn, 'rediss://') !== 0) {
            return false;
        }

        if ($this->getHost() !== null && $this->getPort() !== null) {
            return true;
        }

        return $this->getSocket() !== null;
    }

    protected function parseDsn(string $dsn): void
    {
        if ($this->isParsed) {
            return;
        }

        $dsn = preg_replace('#rediss?://#', '', $dsn); // remove "redis://" and "rediss://"
        $pos = strrpos($dsn, '@');
        if ($pos !== false) {
            // parse password
            $password = substr($dsn, 0, $pos);

            if (strstr($password, ':')) {
                [, $password] = explode(':', $password, 2);
            }

            $this->password = urldecode($password);

            $dsn = substr($dsn, $pos + 1);
        }

        $dsn = preg_replace_callback('/\?(.*)$/', [$this, 'parseParameters'], $dsn); // parse parameters
        if (preg_match('#^(.*)/(\d+|%[^%]+%)$#', $dsn, $matches)) {
            // parse database
            $this->database = is_numeric($matches[2]) ? (int) $matches[2] : $matches[2];
            $dsn            = $matches[1];
        }

        if (preg_match('#^([^:]+)(:(\d+|%[^%]+%))?$#', $dsn, $matches)) {
            if (!empty($matches[1])) {
                // parse host/ip or socket
                if ($matches[1][0] === '/') {
                    $this->socket = $matches[1];
                } else {
                    $this->host = $matches[1];
                }
            }

            if ($this->socket === null && !empty($matches[3])) {
                // parse port
                $this->port = is_numeric($matches[3]) ? (int) $matches[3] : $matches[3];
            }
        } elseif (preg_match('#^\[([^\]]+)](:(\d+))?$#', $dsn, $matches)) { // parse enclosed IPv6 address and optional port
            if (!empty($matches[1])) {
                $this->host = $matches[1];
            }

            if (!empty($matches[3])) {
                $this->port = (int) $matches[3];
            }
        }

        $this->tls = strpos($this->dsn, 'rediss://') === 0;
        $this->isParsed = true;
    }

    /** @param mixed[] $matches */
    protected function parseParameters(array $matches): string
    {
        parse_str($matches[1], $params);

        if (!empty($params['weight'])) {
            $this->weight = (int) $params['weight'];
        }

        if (!empty($params['alias'])) {
            $this->alias = $params['alias'];
        }

        return '';
    }

    public function __toString(): string
    {
        return $this->dsn;
    }
}
