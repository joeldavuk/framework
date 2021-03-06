<?php

namespace System\Configuration;

class JsonConfiguration extends Configuration {
    
    /**
     * Initializes an instance of JsonConfiguration.
     * Reads configuration data if $fileName is supplied.
     * 
     * @method  __construct
     * @param   string $fileName = null
     */
    public function __construct($fileName = null){
        if($fileName){
            $this->open($fileName);
        }
    }
    
    /**
     * Opens a JSON configuration file.
     * Throws ConfigurationException if the JSON file does not decode to an array.
     * Throws ConfigurationFileNotFoundException if the file does not exist.
     * 
     * @method  open
     * @param   string $fileName
     * @return  void
     */
    public function open($fileName){

        if(is_file($fileName)){
            $data = file_get_contents($fileName);
            
            if(substr($data, 0, 5) == '<?php'){
                $sections = explode('?>', $data);
                $data = trim($sections[1]);
            }
            $this->config = json_decode($data, true);

            if(!is_array($this->config)){
                throw new ConfigurationException('Unable to load configuration file. The file maybe corrupt.');
            }
        }
    }
}