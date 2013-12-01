<?php

namespace Inouire\SecretFanta\Service;

use Symfony\Component\Yaml\Yaml;
        
class ParticipantsManager {
    
    private $participants;
    
    private $index;
    
    private $couples_index;
    
    public function __construct($file_name){
        // load yml content
        $this->participants = Yaml::parse(file_get_contents($file_name));
        
        // build index
        $this->index=array();
        foreach($this->participants as $name => $email){
            $this->index[] =  array('name'=>$name, 'email' => $email);
        }
        
        // build couples index
        // TODO
    }
    
    /**
     * Get the list of participants, as an array of name => email
     */
    public function getParticipantsList(){
        return $this->participants;
    }
    
    /**
     * Generate a santa configuration from the list of participants,
     * which does not contain any couple relation
     */
    public function generateConfigurationWithoutCouple(){
        $has_couple = true;
        while($has_couple){
            $config = $this->generateConfiguration();
            $has_couple = $this->configurationHasCouple($config);
        }
        return $config;
    }
    
    /**
     * Generate a santa configuration from the list of participants
     */
    public function generateConfiguration(){
        
        // create a copy of the index
        $temp_index = $this->index;
        
        // shuffle index
        shuffle($temp_index);
        
        // affect a target to each participant
        $nb_participants=count($temp_index);
        $first_participant=$temp_index[0];
        for( $k=0 ; $k<($nb_participants-1); $k++){
            $temp_index[$k]['target']=$temp_index[$k+1];
        }
        $temp_index[$nb_participants-1]['target']=$first_participant;
        
        return $temp_index;
    }
    
    /**
     * Search for couples in a santa configuration
     */
    private function configurationHasCouple($config){
        // Look in couple index
        // TODO
        return false;
    }

}
