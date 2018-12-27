<?php
    /*-----
    COMPANY: the3ds team
    AUTHOR: Tom DINH
    PROJECT: core DA_Engine
    DESCRIPTION: --
    DATE: 12/May/2015
    Ver 1.2
    -----*/

    interface VO_interface {
        public function getValue($varName);
        public function setValue($varName, $inputValue);
        public function toArray();
        public function toJSON();
        public function setTypeOf($arrName, $arrType = NULL); //what is this???

        public function setPK();
        public function setPKArr($pkArray);
        //version 1
        public function idRetrieve($id);
        public function retrieve();
        public function insertDB_returning();
        public function insertDB();
        #public function updateDB();
        #public function updateDBFields();
        #public function deleteDB($condition);
        #public function deleteDB_custom($condition);
        // version 1.6
        public function read($pk);
        public function create();
        public function update($where=NULL, $data=NULL);
        public function delete($condition);
    }
?>
