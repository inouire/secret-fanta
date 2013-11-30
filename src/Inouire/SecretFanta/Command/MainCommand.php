<?php

namespace Inouire\SecretFanta\Command;

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
            ->addOption('dry',null,InputOption::VALUE_NONE,'If set, no email will be sent');
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        
        // load list of participants and transform it into an indexed array
        $output->writeln('Loading participants:');
        $participants = Yaml::parse(file_get_contents('conf/participants.yml'));
        $index=array();
        foreach($participants as $name => $email){
            $index[] =  array('name'=>$name, 'email' => $email);
            $output->writeln(' - <info>'.$name.'</info> <comment>('.$email.')</comment>');
        }
        
        $output->writeln('--------------------------------------------');
        
        // shuffle array and recap
        $output->writeln('Shuffling participants list:');
        $random_configuration = $this->getRandomConfiguration($index);
        foreach($random_configuration as $participant){
            $output->writeln(' - <info>'.$participant['name'].' -> '.$participant['target']['name'].'</info>');
        }
        
        $output->writeln('--------------------------------------------');
        
        // send email to each participant to inform him who is is gift target
        $output->writeln('Unleashing reindeers...');
        foreach($random_configuration as $participant){
            $output->writeln(' - Inform <info>'.$participant['name'].'</info> that he has to offer a gift to <info>'.$participant['target']['name'].'</info>');
            
            // build email information
            $subject = 'Instructions mission père Noël';
            $from = array('pere_noel@pole-nord.gouv'=>'Le père noël');
            $to = array($participant['email']=>$participant['name']);
            $content='Ho ho ho, salut '.$participant['name'].' c\'est le père noël.\r\n 
                      Je suis chargé de te dire que tu as été tiré au sort pour faire un cadeau à '.$participant['target']['name'].
                      '. Attention cependant, personne n\'est au courant de la mission que je te confie et aucune autre trace de ce tirage au sort n\'a été conservée 
                      à part cet email. Donc ne le perd pas ! Allez j\'y vais, faut je j\'aille fouetter mes lutins qui font trop de pauses café.';
            
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
            if($result>0){
                $output->writeln('<error>Error while sending email to '.$participant['email'].'</error>');
            }   
        }
        
    }
    
    /**
     * Build a random secret santa configuration from an index of participants
     */
    private function getRandomConfiguration($index){
        
        // shuffle index
        shuffle($index);
        
        // affect a target to each participant
        $nb_participants=count($index);
        $first_participant=$index[0];
        for( $k=0 ; $k<($nb_participants-1); $k++){
            $index[$k]['target']=$index[$k+1];
        }
        $index[$nb_participants-1]['target']=$first_participant;
        
        return $index;
    }
    
}
