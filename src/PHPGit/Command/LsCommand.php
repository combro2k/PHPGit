<?php

namespace PHPGit\Command;

use PHPGit\Command;
use PHPGit\Git;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Manage set of tracked repositories - `git remote`.
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 *
 * @method remote($remote, $startWith = null) Check all remote tags and heads
 */
class LsCommand extends Command
{
    /** @var Ls\RemoteCommand */
    public $remote;

    /**
     * @param Git $git
     */
    public function __construct(Git $git)
    {
        parent::__construct($git);

        $this->remote = new Ls\RemoteCommand($git);
    }

    /**
     * Calls sub-commands.
     *
     * @param string $name      The name of a property
     * @param array  $arguments An array of arguments
     *
     * @throws \BadMethodCallException
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (isset($this->{$name}) && is_callable($this->{$name})) {
            return call_user_func_array($this->{$name}, $arguments);
        }

        throw new \BadMethodCallException(sprintf('Call to undefined method %s::%s()', __CLASS__, $name));
    }
}
