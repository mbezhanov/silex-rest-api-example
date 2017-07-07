<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class ClearCacheCommand extends Command
{
    protected function configure()
    {
        $this->setName('cache:clear')->setDescription('Clears the application cache');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fs = new Filesystem();

        foreach (new \DirectoryIterator(APP_CACHE_DIR) as $fileInfo) {
            /** @var \SplFileInfo $fileInfo */
            if ($fileInfo->isDir() && !$fileInfo->isDot()) {
                if ($output->isVerbose()) {
                    $output->writeln(sprintf('Deleting folder: %s', $fileInfo->getPathname()));
                }
                $fs->remove($fileInfo->getPathname());
            }
        }

    }
}
