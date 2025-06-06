<?php

defined('BASEPATH') || exit('No direct script access allowed');

trait EloquentLoader
{
    /**
     * Array of loaded Eloquent models
     *
     * @var array
     */
    protected $_eloquent_models = [];

    /**
     * Array of Eloquent model paths
     *
     * @var array
     */
    protected $_eloquent_model_paths = [APPPATH . 'models/'];

    /**
     * Load Eloquent Model
     *
     * @param string|array $model Model name(s)
     * @param string $name Optional object name to assign to
     * @return object
     */
    public function eloquent_model($model, $name = '')
    {
        if (empty($model)) {
            return $this;
        }

        if (is_array($model)) {
            foreach ($model as $key => $value) {
                is_int($key) ? $this->eloquent_model($value) : $this->eloquent_model($key, $value);
            }
            return $this;
        }

        $path = '';

        // Is the model in a sub-folder?
        if (($last_slash = strrpos($model, '/')) !== FALSE) {
            $path = substr($model, 0, ++$last_slash);
            $model = substr($model, $last_slash);
        }

        if (empty($name)) {
            $name = $model;
        }

        if (in_array($name, $this->_eloquent_models, TRUE)) {
            return $this;
        }

        $model_class = ucfirst($model);

        // Check if the model class already exists
        if (class_exists($model_class, false)) {
            $this->_eloquent_models[] = $name;
            $this->$name = $model_class;
            return $this;
        }

        // Try to load the model file
        $loaded = false;
        foreach ($this->_eloquent_model_paths as $model_path) {
            $file_path = $model_path . $path . $model_class . '.php';
            
            if (file_exists($file_path)) {
                require_once $file_path;
                $loaded = true;
                break;
            }
        }

        if (!$loaded) {
            show_error('Unable to locate the Eloquent model: ' . $model_class);
        }

        if (!class_exists($model_class, false)) {
            show_error('Model file loaded but class ' . $model_class . ' not found');
        }

        $this->_eloquent_models[] = $name;
        $this->$name = $model_class;

        log_message('info', 'Eloquent Model "' . $model_class . '" loaded');
        
        return $this;
    }

    /**
     * Add Eloquent Model Path
     *
     * @param string $path Path to add
     * @return object
     */
    public function add_eloquent_model_path($path)
    {
        $path = rtrim($path, '/') . '/';
        
        if (!in_array($path, $this->_eloquent_model_paths)) {
            array_unshift($this->_eloquent_model_paths, $path);
        }
        
        return $this;
    }

    /**
     * Get loaded Eloquent models
     *
     * @return array
     */
    public function get_eloquent_models()
    {
        return $this->_eloquent_models;
    }
} 
