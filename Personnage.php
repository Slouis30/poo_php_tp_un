<?php
    class Personnage{
        private $_id;
        private $_nom;
        private $_vie;

        const DEGATS = 5;
        const PERSONNAGE_PAREIL = "Le personnage est le même";
        const PERSONNAGE_MORT = "Le personnage est mort";
        const PERSONNAGE_FRAPPE = "Le personnage frappe";

        public function __construct(array $data){
            $this->hydrate($data);
        }

        public function hydrate(array $data){
            foreach($data as $key => $value){
                $setter = "set" . ucfirst($key);
                if(method_exists($this, $setter)){
                    $this->$setter($value);
                }
            }
        }

        public function frapper(Personnage $persoAFrapper){
            if($persoAFrapper->id() == $this->_id){
                return self::PERSONNAGE_PAREIL;
            }
            return $persoAFrapper->perdreVie();
        }

        public function perdreVie(){
            $this->_vie -= self::DEGATS;
            if($this->_vie <= 0){
                return self::PERSONNAGE_MORT;
            }
            else{
                return self::PERSONNAGE_FRAPPE;
            }
        }

        public function setId($id){
            if($id > 0){
                $this->_id = $id;
            }
        }
        public function setVie($vie){
            $vie = (int) $vie;
            if($vie >= 0 && $vie <= 100){
                $this->_vie = $vie;
            }
        }
        public function setNom($nom){
            if(is_string($nom) && strlen($nom) <= 40){
                $this->_nom = $nom;
            }
            else{
                trigger_error("Le nom doit être une chaine de caractères de 40 caractères maximum.", E_USER_ERROR);
            }
        }
        public function id(){
            return $this->_id;
        }
        public function nom(){
            return $this->_nom;
        }
        public function vie(){
            return $this->_vie;
        }
    }