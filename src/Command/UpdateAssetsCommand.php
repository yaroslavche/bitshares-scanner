<?php

namespace App\Command;

use App\Service\BitsharesClient;

use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateAssetsCommand extends ContainerAwareCommand
{
    private $bitshares;
    private $objectManager;

    public function __construct(BitsharesClient $bitshares, ObjectManager $objectManager)
    {
        $this->bitshares = $bitshares;
        $this->objectManager = $objectManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('btss:update:assets')
            ->setDescription('Update assets.')
            ->setHelp('This command allows you to update assets and markets')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->bitshares->updateAssets($this->objectManager);
    }
}
