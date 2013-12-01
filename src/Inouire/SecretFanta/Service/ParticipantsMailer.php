<?php

namespace Inouire\SecretFanta\Service;

use Symfony\Component\Yaml\Yaml;
        
class ParticipantsMailer {
    
    private $mailer;
    
    private $bypass_email = null;
    
    // TODO make it configurable
    private $subject='Instructions mission père Noël';
    private $from_email=array('pere_noel@gmail.com'=>'Le père noël');
    private $content_template;
            
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
    
    public function loadContentTemplate($file){
        $this->content_template = file_get_contents($file);
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
        
        // replace name and target name in html template
        $content = str_replace ( '{{name}}',   $participant['name']           , $this->content_template );
        $content = str_replace ( '{{target}}', $participant['target']['name'] , $content );
        
        // prepare message
        $message = \Swift_Message::newInstance()
                        ->setSubject($this->subject)
                        ->setFrom($this->from_email)
                        ->setTo($to_email)
                        ->setBody($content,'text/html');
            
        // send email
         return $this->mailer->send($message);
    }

}
