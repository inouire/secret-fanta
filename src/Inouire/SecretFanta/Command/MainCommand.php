<?php

namespace Inouire\SecretFanta\Command;

use Inouire\SecretFanta\Service\ParticipantsManager;
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
                
        // send email to each participant to inform him who is is gift target
        $output->writeln('Unleashing reindeers:');
        foreach($config as $participant){
            
            $output->writeln(' - Inform <info>'.$participant['name'].'</info> that he has to offer a gift to <info>'.$participant['target']['name'].'</info>');
            
            // build email information
            $subject = 'Instructions mission père Noël';
            $from = array('pere_noel@gmail.com'=>'Le père noël');
            if($input->getOption('bypass')!=null){
                $to = array($input->getOption('bypass')=>$participant['name']);
            }else{
                $to = array($participant['email']=>$participant['name']);
            }
            $content='Ho ho ho, salut '.$participant['name'].' c\'est le père noël. 
            
Je suis chargé de te dire que tu as été tiré au sort pour faire un cadeau à '.$participant['target']['name'].'.
Attention cependant, personne n\'est au courant de la mission que je te confie et aucune autre trace de ce tirage au sort n\'a été conservée  à part cet email. Donc ne le perd pas ! 

Allez j\'y vais, faut je j\'aille fouetter mes lutins qui font trop de pauses café.';
            
            // prepare email
            $transport = \Swift_MailTransport::newInstance();
            $mailer = \Swift_Mailer::newInstance($transport);  
            $message = \Swift_Message::newInstance()
                        ->setSubject($subject)
                        ->setFrom($from)
                        ->setTo($to)
                        ->setBody($content);
            
            // send email
            $result = $mailer->send($message);
            if(!$result){
                $output->writeln('<error>Error while sending email to '.$participant['email'].'</error>');
            }   
        }
        
    }
    
}
