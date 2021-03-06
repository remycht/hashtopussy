<?php

namespace DBA;

class SupertaskTask extends AbstractModel {
  private $supertaskTaskId;
  private $taskId;
  private $supertaskId;
  
  function __construct($supertaskTaskId, $taskId, $supertaskId) {
    $this->supertaskTaskId = $supertaskTaskId;
    $this->taskId = $taskId;
    $this->supertaskId = $supertaskId;
  }
  
  function getKeyValueDict() {
    $dict = array();
    $dict['supertaskTaskId'] = $this->supertaskTaskId;
    $dict['taskId'] = $this->taskId;
    $dict['supertaskId'] = $this->supertaskId;
    
    return $dict;
  }
  
  function getPrimaryKey() {
    return "supertaskTaskId";
  }
  
  function getPrimaryKeyValue() {
    return $this->supertaskTaskId;
  }
  
  function getId() {
    return $this->supertaskTaskId;
  }
  
  function setId($id) {
    $this->supertaskTaskId = $id;
  }
  
  function getTaskId(){
    return $this->taskId;
  }
  
  function setTaskId($taskId){
    $this->taskId = $taskId;
  }
  
  function getSupertaskId(){
    return $this->supertaskId;
  }
  
  function setSupertaskId($supertaskId){
    $this->supertaskId = $supertaskId;
  }

  const SUPERTASK_TASK_ID = "supertaskTaskId";
  const TASK_ID = "taskId";
  const SUPERTASK_ID = "supertaskId";
}
