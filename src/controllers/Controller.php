<?php

namespace Controllers;

use App\core\{

View , Session , Database , Response , Request , Application

};


class Controller {

    protected Request $request;
    protected Response $response;
    protected Database $db;
    protected Session $session;
    protected View $view;
        
public function __construct(Application $app)
{
    $this->request = $app->request;
    $this->response = $app->response;
    $this->db = $app->db;
    $this->session = $app->session;
    $this->view = $app->view;
    
}

}