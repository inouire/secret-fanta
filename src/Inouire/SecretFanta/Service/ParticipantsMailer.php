<?php

namespace Inouire\SecretFanta\Service;

use Symfony\Component\Yaml\Yaml;
        
class ParticipantsMailer {
    
    private $mailer;
    
    private $bypass_email = null;
    
    // TODO make it configurable
    private $subject='Instructions mission père Noël';
    private $from_email=array('pere_noel@gmail.com'=>'Le père noël');
            
    public function __construct(){
        $transport = \Swift_MailTransport::newInstance();
        $this->mailer = \Swift_Mailer::newInstance($transport); 
    }
    
    /**
     * Set a bypass email: all email will be sent to this address
     */
    public function setBypassEmail($email){
        $this->bypass_email = $email;
    }
    
    /**
     * Email a participant with all information about the secret santa
     */
    public function emailParticipant($participant){
        
        // set to_email
        if($this->bypass_email != null){
            $to_email = array($this->bypass_email => $participant['name']);
        }else{
            $to_email = array($participant['email'] => $participant['name']);
        }
        
        // set content
        $content='Ho ho ho, salut '.$participant['name'].' c\'est le père noël. 
            
Je suis chargé de te dire que tu as été tiré au sort pour faire un cadeau à '.$participant['target']['name'].'.
Attention cependant, personne n\'est au courant de la mission que je te confie et aucune autre trace de ce tirage au sort n\'a été conservée  à part cet email. Donc ne le perd pas ! 

Allez j\'y vais, faut je j\'aille fouetter mes lutins qui font trop de pauses café.';

        // prepare message
        $message = \Swift_Message::newInstance()
                        ->setSubject($this->subject)
                        ->setFrom($this->from_email)
                        ->setTo($to_email)
                        ->setBody($content);
            
        // send email
         return $this->mailer->send($message);
    }

}
