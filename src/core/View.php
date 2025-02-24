<?php

namespace App\core;

use Exception;

class View {

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    private string $layout = 'main'; 
    private array $sections = []; 
    private ?string $currentSection = null;
    protected Session $session; 

    public function setLayout(string $layout) {
        $this->layout = $layout;
    }

    public function startSection(string $name) {
        if ($this->currentSection !== null) {
            throw new Exception("Nested sections are not allowed.");
        }
        $this->currentSection = $name;
        ob_start(); 
    }

    public function endSection() {
        if ($this->currentSection === null) {
            throw new Exception("No active section to end.");
        }
        $this->sections[$this->currentSection] = ob_get_clean();
        $this->currentSection = null;
    }

    public function section(string $name) {
        return $this->sections[$name] ?? ''; 
    }

    public function render(string $template, array $data = []) {
        $templatePath = ROOT_DIR . "/views/{$template}.view.php";
        $layoutPath = ROOT_DIR . "/views/layouts/{$this->layout}.php";

        if (!file_exists($templatePath)) {
            throw new Exception("View file '{$templatePath}' not found.");
        }

        if (!file_exists($layoutPath)) {
            throw new Exception("Layout file '{$layoutPath}' not found.");
        }

        extract($data);
        
      
        ob_start();
        include $templatePath;
        ob_end_flush(); 

    
        ob_start();
        include $layoutPath;
        return ob_get_clean();
    }

    public function include(string $partial, array $data = []) {
        $partialPath = ROOT_DIR . "/views/partials/{$partial}.php";

        if (!file_exists($partialPath)) {
            throw new Exception("Partial '{$partialPath}' not found.");
        }

        extract($data);
        include $partialPath;
    }
}
