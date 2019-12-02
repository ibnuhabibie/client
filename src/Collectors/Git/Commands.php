<?php

namespace Laracatch\Client\Collectors\Git;

class Commands
{
    public const INITIALIZED = "git rev-parse --git-dir";
    public const HASH = "git log --pretty=format:'%H' -n 1";
    public const MESSAGE = "git log --pretty=format:'%s' -n 1";
    public const TAG = 'git describe --tags --abbrev=0';
    public const REMOTE = 'git config --get remote.origin.url';
    public const STATUS = 'git status -s';

    public static $enumerated = [
        self::INITIALIZED,
        self::HASH,
        self::MESSAGE,
        self::TAG,
        self::REMOTE,
        self::STATUS
    ];
}
