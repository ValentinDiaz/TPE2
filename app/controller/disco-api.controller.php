<?php

require_once './app/model/disco.model.php';
require_once './app/view/api.view.php';
require_once './app/helpers/auth-api.helper.php';

class discoApiController {
    private $model;
    private $view;
    private $authHelper;

    private $data;

    public function __construct() {
        $this->model = new discoModel();
        $this->view = new ApiView();
        $this->authHelper = new AuthApiHelper();
        $this->data = file_get_contents("php://input");
    }

    private function getData() {
        return json_decode($this->data);
    }


    public function getAll($params = null){
        $prior=$this->getPrior();
        $condition=$this->getCondition();
        if ($prior && $condition){ 
            $discos=$this->model->getAllDiscos($prior, $condition);
            if ($discos){ 
               return $this->view->response($discos, 200);
            } 
            else { 
                return $this->view->response("no se encontro ningun disco",404 );
            }
        }
        else{
            return $this->view->response("no se encontro ningun disco",404 );
        }  





        
    } 

    public function getDisco($params=null){
        $id = $params[':ID'];
        $disco = $this->model->getDisco($id);
        if ($disco)
            $this->view->response($disco);   
        else 
            $this->view->response("La tarea con el id=$id no existe", 404);
    }  

    public function deleteDisco($params=null){
        $id = $params[':ID'];
        $disco = $this->model->getDisco($id);
        if ($disco) {
        $this->model->removeDisco($id);
        $this->view->response($disco);
        }
        else{
            $this->view->response("La tarea con el id=$id no existe", 404);
        }

    }
    public function insertDisco($params = null) {
        if($this->authHelper->isLoggedIn()){
            $disco = $this->getData();

            if (empty($disco->titulo) || empty($disco->anio) || empty($disco->artista) || empty($disco->id_genero)) {
                $this->view->response("Complete los datos", 400);
            } else {
                $id = $this->model->addDisco($disco->titulo, $disco->anio, $disco->artista, $disco->id_genero);
                $disco= $this->model->getDisco($id);
                $this->view->response($disco, 201);
            }
        }
        $this->view->response("No estas logeado", 401);
        return;
       
    }

    public function updateDisco($params = null){
        $id = $params[':ID'];

        if($this->authHelper->isLoggedIn()){
                $discos = $this->getData();
            
                $disco = $this->model->getDisco($id);

                if ($disco) {
                    $this->model->editDisco( $discos->titulo, $discos->anio, $discos->artista, $discos->id_genero,$id);
                    $this->view->response("se realizo el cambio", 201);       
                }
                else{
                    $this->view->response("La tarea con el id=$id no existe", 404);
                }
            
            }
            $this->view->response("No estas logeado", 401);
            return;

       
       
    }


    private function getPrior(){
        if(isset($_GET["order"])){
             $tempOrder= strtoupper($_GET["order"]);
                if($tempOrder == "ASC"|| $tempOrder == "DESC" );
                    return $tempOrder;
                
                return null;    
        }
        return 'ASC';
    }

    private function getCondition(){
       if(isset($_GET["campo"])){
            $condition= $_GET["campo"];
            if($condition == "titulo" || $condition == "genero" || $condition == "anio" || $condition == "artista" || 
               $condition == "id" ){
               return $condition;   
            }         
        }
            return "id";
    }
}      