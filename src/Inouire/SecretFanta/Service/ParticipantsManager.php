<?php

namespace Inouire\SecretFanta\Service;

use Symfony\Component\Yaml\Yaml;
        
class ParticipantsManager {
        
    private $people;
     
    private $couples;
    
    private $people_index;
    
    private $couples_index;
    
    public function __construct($file_name){
        
        // load content of yml config file
        $participants = Yaml::parse(file_get_contents($file_name));
        
        // get people
        if(array_key_exists('people', $participants) &&  $participants['people'] != null && count($participants['people']) >= 3 ){
            $this->people = $participants['people'];
        }else{
            throw new \Exception('There must be at least three participants to the secret santa');
        }
        
        // get couples
        if(array_key_exists('couples', $participants) &&  $participants['couples'] != null){
            $this->couples = $participants['couples'];
        }else{
            $this->couples = array();
        }
        
        // build people index
        $this->people_index=array();
        foreach($this->people as $name => $email){
            $this->people_index[] =  array('name'=>$name, 'email' => $email);
        }
        
        // build couples index
        $this->couples_index = array();
        foreach($this->couples as $key => $people){
            $this->couples_index[$people[0]] = $key; 
            $this->couples_index[$people[1]] = $key; 
        }
    }
    
    /**
     * Get the list of participants, as an array of name => email
     */
    public function getParticipantsList(){
        return $this->people;
    }
    
    /**
     * Get the list of couples, as an array of [name1,name2]
     */
    public function getCouplesList(){
        return $this->couples;
    }
    
    /**
     * Generate a santa configuration from the list of participants,
     * which does not contain any couple relation
     */
    public function generateConfigurationWithoutCouple(){
        $has_couple = true;
        while($has_couple){
            print('.');
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
        $temp_index = $this->people_index;
        
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
        
        // iterate on each participant to detect couple
        foreach($config as $participant){
            
            // get participant information
            $name = $participant['name'];
            $target_name = $participant['target']['name'];
            
            //check if the two people are in the couple base
            if(    array_key_exists($name, $this->couples_index) 
                && array_key_exists($target_name, $this->couples_index) ){
                //check if they belong to the same couple
                if( $this->couples_index[$name] == $this->couples_index[$target_name]){
                    return true;
                }
            }
        }
        
        // nothing detected
        return false;
    }

}
