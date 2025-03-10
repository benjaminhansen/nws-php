<?php

namespace BenjaminHansen\NWS\Enums;

use BenjaminHansen\NWS\Traits\CanGetDescription;

enum CacheDriver: string
{
    use CanGetDescription;

    case Files = 'Files';
    case Leveldb = 'Leveldb';
    case Memcached = 'Memcached';
    case Redis = 'Redis';
    case Devnull = 'Devnull';
    case Devrandom = 'Devrandom';
    case Memory = 'Memory';
    case Sqlite = 'Sqlite';
    case Predis = 'Predis';
    case Ssdb = 'Ssdb';
    case Apcu = 'Apcu';
}
