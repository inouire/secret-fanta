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
            ->addOption('debug',null,InputOption::VALUE_NONE,'If set, all information about will be displayed. Use it for debug only');
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        
        // load participants manager (participants + couples)
        $output->writeln('Loading participants:');
        $pm = new ParticipantsManager('conf/participants.yml');
        
        // recap list of participants and couples
        foreach($pm->getParticipantsList() as $name => $email){
            $output->writeln(' - <info>'.$name.'</info> <comment>('.$email.')</comment>');
        }
        foreach($pm->getCouplesList() as $couple){
            $output->writeln(' - <info>'.$couple[0].'</info> and <info>'.$couple[1].'</info> are in couple');
        }
        
        // generate configuration, without any couple
        $output->write('Generating configuration that avoid couples...');
        $config = $pm->generateConfigurationWithoutCouple();
        $output->writeln('<info> done</info>');
          
        // load mailer     
        $output->writeln('Unleashing reindeers:');
        $mailer = new ParticipantsMailer();
        $mailer->loadContentTemplate('conf/mail_content.html');
        $mailer->setBypassEmail($input->getOption('bypass'));
                
        // send email to each participant to inform him who is is gift target
        foreach($config as $participant){
            if($input->getOption('debug')){
                $name = $participant['name'];
                $target_name = $participant['target']['name'];
            }else{
                $name = '****';
                $target_name = '****';
            }
            $output->writeln(' - Inform <info>'.$name.'</info> that he has to offer a gift to <info>'.$target_name.'</info>');
            if(!$mailer->emailParticipant($participant)){
                $output->writeln('<error>Error while sending email to '.$participant['email'].'</error>');
            }
        }
        
    }
    
}
