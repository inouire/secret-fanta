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
        
        // load list of participants and transform it into an indexed array
        $output->writeln('Loading participants:');
        $participants = Yaml::parse(file_get_contents('conf/participants.yml'));
        $index=array();
        foreach($participants as $name => $email){
            $index[] =  array('name'=>$name, 'email' => $email);
            $output->writeln(' - <info>'.$name.'</info> <comment>('.$email.')</comment>');
        }
        
        $output->writeln('--------------------------------------------');
        
        //shuffle array and display it again
        $output->writeln('Shuffling participants list:');
        shuffle($index);
        foreach($index as $participant){
            $output->writeln(' - <info>'.$participant['name'].'</info> <comment>('.$participant['email'].')</comment>');
        }
        
        // send email
        //TODO
        //$message = \Swift_Message::newInstance();

        $output->writeln('<error>Reindeers not ready yet</error>');
    }
}
