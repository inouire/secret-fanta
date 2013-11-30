<?php

namespace Inouire\SecretFanta\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
        
class MainCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('reindeer:unleash')
            ->setDescription('Generate a secret santa config and send emails')
            ->addOption('dry',null,InputOption::VALUE_NONE,'If set, no email will be sent');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        // loading list of participants and display it
        $output->writeln('Loading participants:');
        $participants = Yaml::parse(file_get_contents('conf/chimneys.yml'));
        foreach($participants as $name => $email){
            $output->writeln(' - <info>'.$name.'</info> <comment>('.$email.')</comment>');
        }
        

        $output->writeln('<error>Reindeers not ready yet</error>');
    }
}
