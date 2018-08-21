<?php

namespace App\Command;

use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CacheClearCommand extends Command
{
    /**
     * @var CacheInterface
     */
    private $cache;

    protected static $defaultName = 'app:cache:clear';

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument(
                'repository',
                InputArgument::REQUIRED,
                'Repository to clear cache in the format owner/repo'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $repository = $input->getArgument('repository');
        $cacheKey = 'repo;' . str_replace('/', ';', $repository);

        if ($this->cache->has($cacheKey)) {
            $this->cache->delete($cacheKey);
            $io->success("Cache deleted for $repository");

        } else {
            $io->note("Cache not found for $repository");
        }
    }
}
