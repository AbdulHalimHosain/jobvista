<?php

/**
 * Get the base path
 * 
 * @param string $path
 * @return string
 */
function basePath($path = '') {
    return __DIR__ . '/' . $path;
}

/**
 * Load the views
 * 
 * @param string $name
 * @return void
 */

function loadView($name, $data = []) 
{   
    $viewPath = basePath("App/views/{$name}.view.php");
    if (file_exists($viewPath)) {
        extract($data);
        require $viewPath;
    } else {
        throw new Exception("{$name} not found");
    }
}

/**
 * Load the partial
 * 
 * @param string $name
 * @return void
 */

 function loadPartial($name, $data = [])
 {
     $partialPath = basePath("App/views/partials/{$name}.php");
     if (file_exists($partialPath)) {
        extract($data);
        require $partialPath;
     } else {
        throw new Exception("{$name} not found");
     }
 }   

/**
 * Inspect the value(s)
 * 
 * @param mixed $value
 * @return void
 */
function inspect($value){
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
}

/**
 * Inspect the value(s) and die
 * 
 * @param mixed $value
 * @return void
 */
function inspectAndDie($value){
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
    die();
}

/**
 * fORMAT SALARY
 * 
 * @param string $salary
 * @return string formatted salary
 */

function formatSalary($salary){
    return '$' . number_format(floatval($salary));
}

/**
 * sanitize data
 * 
 * @param string $dirty
 * @return string
 */

function sanitize($dirty){
    return filter_var(trim($dirty), FILTER_SANITIZE_SPECIAL_CHARS);
}

/**
 * Redirect to a page
 * 
 * @param string $url
 * @return void
 */

function redirect($url){
    header("Location: {$url}");
    exit;
}