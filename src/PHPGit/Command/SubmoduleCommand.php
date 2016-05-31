<?php

namespace PHPGit\Command;

use PHPGit\Command;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SubmoduleCommand
 * @package PHPGit\Command
 */
class SubmoduleCommand extends Command
{
    /**
     * @return array
     */
    public function __invoke()
    {
        return $this->status();
    }

    /**
     * @return array
     *
     * @throws \PHPGit\Exception\GitException
     */
    public function status()
    {
        $builder = $this->git
            ->getProcessBuilder()
            ->add('submodule')
            ->add('status');

        $submodules = array();
        $output = $this->git->run($builder->getProcess());
        $lines = $this->split($output);

        foreach ($lines as $line) {
            $line = explode(' ', trim($line));
            $submodules[$line[1]] = array(
                'hash' => $line[0],
                'path' => $line[1],
                'abbrev' => trim($line[2], '()'),
            );
        }

        return $submodules;
    }

    /**
     * @param string $module
     * @param array $options
     *
     * @return bool
     * @throws \PHPGit\Exception\GitException
     */
    public function update($module = null, array $options = array())
    {
        $options = $this->resolve($options);
        $builder = $this->git
            ->getProcessBuilder()
            ->add('submodule')
            ->add('update');

        if ($options['strategy']) {
            $builder->add('--' . $options['strategy']);
            unset($options['strategy']);
        }

        $this->addFlags($builder, $options);

        if ($module) {
            $builder->add($module);
        }

        $this->git->run($builder->getProcess());

        return true;
    }

    /**
     * @param null $module
     *
     * @return bool
     *
     * @throws \PHPGit\Exception\GitException
     */
    public function sync($module = null)
    {
        $builder = $this->git
            ->getProcessBuilder()
            ->add('submodule')
            ->add('sync');

        $this->addFlags($builder, array(
            'recursive' => true,
        ));

        if ($module) {
            $builder->add($module);
        }

        $this->git->run($builder->getProcess());

        return true;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('quiet', true)
            ->setDefault('init', true)
            ->setDefault('recursive', true)
            ->setDefault('force', false)
            ->setDefault('remote', false)
            ->setDefault('strategy', 'checkout')
            ->setAllowedTypes('strategy', array('null', 'string'))
            ->setAllowedValues('strategy', array('checkout', 'merge', 'rebase'));
    }
}
