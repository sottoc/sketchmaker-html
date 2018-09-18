<?php
namespace Sart;

class Alert {
    
    
    public static function flashError($message)
    {
        $_SESSION['flash_error'] = $message;
    }
    
    public static function flashSuccess($message)
    {
        $_SESSION['flash_success'] = $message;
    }
    
    public static function render()
    {
        if(!empty($_SESSION['flash_success']))
        {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                '.$_SESSION['flash_success'].'
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>';
            
            unset($_SESSION['flash_success']);
        }

        if(!empty($_SESSION['flash_error']))
        {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                '.$_SESSION['flash_error'].'
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>';            
            unset($_SESSION['flash_error']);
        }
        
    }
}