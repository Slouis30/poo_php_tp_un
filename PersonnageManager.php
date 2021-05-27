<?php
    class PersonnageManager{
        private $_db;

        public function __construct(PDO $db){
            $this->setDb($db);
        }

        public function setDb($db){
            $this->_db = $db;
        }

        public function add(Personnage $perso){
            $q = $this->_db->prepare("INSERT INTO `personnages`(nom) VALUES(:nom)");
            $q->bindValue(":nom", $perso->nom());
            $q->execute();

            $perso->hydrate(["id" => $this->_db->lastInsertId(), "vie" => 100]);
        }

        public function count(){
            return $this->_db->query("SELECT COUNT(*) FROM `personnages`")->fetchColumn();
        }

        public function delete(Personnage $perso){
            $q = $this->_db->exec("DELETE FROM `personnages` WHERE `id` =" .$perso->id());
        }

        public function exists($info){
            if(is_int($info)){
                return (bool) $this->_db->query("SELECT COUNT(*) FROM `personnages` WHERE id =" .$info)->fetchColumn();
            }
            $q = $this->_db->prepare("SELECT COUNT(*) FROM `personnages` WHERE nom = :nom");
            $q->execute([":nom" => $info]);
            return (bool) $q->fetchColumn();
        }

        public function get($info){
            if(is_int($info)){
                $q = $this->_db->query("SELECT id, nom, vie FROM `personnages` WHERE id =" .$info);
                $data = $q->fetch(PDO::FETCH_ASSOC);
                return new Personnage($data);
            }
            else{
                $q = $this->_db->prepare("SELECT id, nom, vie FROM `personnages` WHERE nom = :nom");
                $q->execute([":nom" => $info]);
                return new Personnage($q->fetch(PDO::FETCH_ASSOC));
            }
        }
        public function getList($nom){
            $persos = [];
            $q = $this->_db->prepare("SELECT id, nom, vie FROM `personnages` WHERE nom <> :nom ORDER BY nom");
            $q->execute([":nom" => $nom]);
            while($data = $q->fetch(PDO::FETCH_ASSOC)){
                $persos[] = new Personnage($data);
            }
            return $persos;
        }
        public function update(Personnage $perso){
            $q = $this->_db->prepare("UPDATE `personnages` SET vie = :vie WHERE id = :id");
            $q->bindValue(":vie", $perso->vie(), PDO::PARAM_INT);
            $q->bindValue(":id", $perso->id(), PDO::PARAM_INT);
            $q->execute();
        }
    }