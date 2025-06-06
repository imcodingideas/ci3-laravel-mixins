<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once APPPATH . 'core/EloquentLoader.php';

#[\AllowDynamicProperties]
class MY_Controller extends CI_Controller
{
    use EloquentLoader;

    public function __construct()
    {
        parent::__construct();
        
        // Initialize Eloquent
        $this->_init_eloquent();
        
        log_message('info', 'MY_Controller Class Initialized with Eloquent support');
    }
    
    /**
     * Initialize Eloquent ORM
     */
    private function _init_eloquent()
    {
        static $eloquent_initialized = false;
        
        if ($eloquent_initialized) {
            return;
        }
        
        require_once APPPATH . '../vendor/autoload.php';
        
        $capsule = new \Illuminate\Database\Capsule\Manager;
        
        // Use CodeIgniter database configuration
        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => $this->db->hostname,
            'database'  => $this->db->database,
            'username'  => $this->db->username,
            'password'  => $this->db->password,
            'port'      => $this->db->port ?? 3306,
            'charset'   => $this->db->char_set,
            'collation' => $this->db->dbcollat,
            'prefix'    => $this->db->dbprefix,
        ]);
        
        // Make this Capsule instance available globally via static methods
        $capsule->setAsGlobal();
        
        // Setup the Eloquent ORM
        $capsule->bootEloquent();
        
        $eloquent_initialized = true;
        
        log_message('info', 'Eloquent ORM initialized');
    }
} 
