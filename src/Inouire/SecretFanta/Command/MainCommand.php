<?php

namespace Inouire\SecretFanta\Command;

use Inouire\SecretFanta\Service\ParticipantsManager;
use Inouire\SecretFanta\Service\ParticipantsMailer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
        
class MainCommand extends Command{
    
    protected function configure(){
        $this
            ->setName('reindeer:unleash')
            ->setDescription('Organise a secret santa and send emails')
            ->addOption('bypass',null,InputOption::VALUE_OPTIONAL,'Send all email to this address instead')
            ->addOption('dry',null,InputOption::VALUE_NONE,'If set, no email will be sent (not implemented yet)');
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        
        // load participants manager (participants + couples)
        $output->writeln('Loading participants:');
        $pm = new ParticipantsManager('conf/participants.yml');
        
        // recap list of participants
        foreach($pm->getParticipantsList() as $name => $email){
            $output->writeln(' - <info>'.$name.'</info> <comment>('.$email.')</comment>');
        }
        
        // generate configuration, without any couple
        $output->write('Generating configuration that avoid couples...');
        $config = $pm->generateConfigurationWithoutCouple();
        $output->writeln('<info> done</info>');
          
        // load mailer     
        $output->writeln('Unleashing reindeers:');
        $mailer = new ParticipantsMailer();
        $mailer->setBypassEmail($input->getOption('bypass'));
                
        // send email to each participant to inform him who is is gift target
        foreach($config as $participant){
            $output->writeln(' - Inform <info>'.$participant['name'].'</info> that he has to offer a gift to <info>'.$participant['target']['name'].'</info>');
            if(!$mailer->emailParticipant($participant)){
                $output->writeln('<error>Error while sending email to '.$participant['email'].'</error>');
            }
        }
        
    }
    
}
