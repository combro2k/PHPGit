<?php

namespace PHPGit\Command\Ls;

use PHPGit\Command;
use PHPGit\Exception\GitException;

class RemoteCommand extends Command
{
    /**
     * @param $remote
     * @param $startWith
     *
     * @return array
     *
     * @throws GitException
     */
    public function tags($remote = null, $startWith = null)
    {
        if ($remote === null) {
            $config = $this->git->config(array('global' => false, 'system' => false));
            $remote = $config['remote.origin.url'];
        }

        $builder = $this->git
            ->getProcessBuilder()
            ->add('ls-remote')
            ->add('--tags')
            ->add($remote);

        if ($startWith) {
            $builder->add($startWith);
        }

        $process = $builder->getProcess();
        $output = $this->git->run($process);

        $objects = array();
        foreach ($this->split($output) as $line) {
            list($hash, $ref) = explode("\t", $line);
            $name = substr($ref, strlen('refs/tags/'));

            if (array_key_exists($name, $objects)) {
                var_dump($name);
                throw new GitException('Duplicate name!');
            }
            $objects[$name] = $hash;
        }

        return uksort($objects, function ($a, $b) {
            return version_compare($a, $b, '<');
        }) ? $objects : array();
    }

    /**
     * @param $remote
     *
     * @param $startWith
     * @return array
     * @throws GitException
     */
    public function heads($remote = null, $startWith = null)
    {
        if ($remote === null) {
            $config = $this->git->config(array('global' => false, 'system' => false));
            $remote = $config['remote.origin.url'];
        }

        $builder = $this->git
            ->getProcessBuilder()
            ->add('ls-remote')
            ->add('--heads')
            ->add($remote);

        if ($startWith) {
            $builder->add($startWith);
        }

        $process = $builder->getProcess();
        $output = $this->git->run($process);

        $objects = array();
        foreach ($this->split($output) as $line) {
            list($hash, $ref) = explode("\t", $line);
            $name = substr($ref, strlen('refs/heads/'));

            if (array_key_exists($name, $objects)) {
                var_dump($name);
                throw new GitException('Duplicate name!');
            }
            $objects[$name] = $hash;
        }

        return uksort($objects, function ($a, $b) {
            return version_compare($a, $b, '<');
        }) ? $objects : array();
    }

    /**
     * @param $remote
     * @param string $startWith
     *
     * @return array
     *
     * @throws GitException
     */
    public function __invoke($remote = null, $startWith = null)
    {
        if ($remote === null) {
            $config = $this->git->config(array('global' => false, 'system' => false));
            $remote = $config['remote.origin.url'];
        }

        $builder = $this->git
            ->getProcessBuilder()
            ->add('ls-remote')
            ->add($remote);

        if ($startWith) {
            $builder->add($startWith);
        }

        $process = $builder->getProcess();
        $output = $this->git->run($process);

        $objects = array();
        foreach ($this->split($output) as $line) {
            list($hash, $ref) = explode("\t", $line);
            if ($ref === 'HEAD') {
                $objects['default'] = $hash;

                continue;
            }

            list($type, $name) = explode('/', substr($ref, stripos($ref, '/', 0) + 1), 2);
            $objects[$type][$name] = $hash;
        }

        if (array_key_exists('tags', $objects)) {
            uksort($objects['tags'], function ($a, $b) {
                return version_compare($a, $b, '<');
            });
        }

        if (array_key_exists('heads', $objects)) {
            uksort($objects['heads'], function ($a, $b) {
                return version_compare($a, $b, '<');
            });
        }

        return ksort($objects) ? $objects : array();
    }
}