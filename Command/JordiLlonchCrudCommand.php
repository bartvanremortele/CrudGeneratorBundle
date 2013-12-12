<?php

/*
 * This file is part of the CrudGeneratorBundle
 *
 * It is based/extended from SensioGeneratorBundle
 *
 * (c) Jordi Llonch <llonch.jordi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JordiLlonch\Bundle\CrudGeneratorBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCrudCommand;
use JordiLlonch\Bundle\CrudGeneratorBundle\Generator\JordiLlonchCrudGenerator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Console\Input\InputOption;


class JordiLlonchCrudCommand extends GenerateDoctrineCrudCommand
{
    protected $generator;
    protected $formGenerator;

    protected $withSort;

    protected function configure()
    {
        parent::configure();

        $this->setName('jordillonch:generate:crud');
        $this->setDescription('A CRUD generator with paginating and filters.');
        $this->getDefinition()->addOption(new InputOption('with-sort', '', InputOption::VALUE_NONE, 'Whether or not to add sorting to list columns'), 1);
        $this->setHelp($this->getHelp().<<<EOT


Using the --with-sort determines whether or not to add sorting to list columns.
EOT
        );
    }

    protected function createGenerator($bundle = null)
    {
        $crudGenerator = new JordiLlonchCrudGenerator($this->getContainer()->get('filesystem'));
        $crudGenerator->setWithSort($this->withSort);
        return $crudGenerator;
    }

    protected function getSkeletonDirs(BundleInterface $bundle = null)
    {
        $skeletonDirs = array();

        if (isset($bundle) && is_dir($dir = $bundle->getPath().'/Resources/SensioGeneratorBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        if (is_dir($dir = $this->getContainer()->get('kernel')->getRootdir().'/Resources/SensioGeneratorBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        $skeletonDirs[] = $this->getContainer()->get('kernel')->locateResource('@JordiLlonchCrudGeneratorBundle/Resources/skeleton');
        $skeletonDirs[] = $this->getContainer()->get('kernel')->locateResource('@JordiLlonchCrudGeneratorBundle/Resources');

        return $skeletonDirs;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'JordiLlonchCrudGeneratorBundle');

        parent::interact($input, $output);

        // sort?
        $withSort = $input->getOption('with-sort') ?: true;
        $output->writeln(array(
            '',
            'You can add sort links to columns of generated index.',
            '',
        ));
        $withSort = $dialog->askConfirmation($output, $dialog->getQuestion('Do you want to add sorting', $withSort ? 'yes' : 'no', '?'), $withSort);
        $input->setOption('with-sort', $withSort);
        $this->withSort = $withSort;

        $output->writeln("");
    }
}
