<?php
/**
 * Model to send emails
 * @author N. Z. <n.z@software-art.com>
 * @package CanvasSketchMaker
 */
namespace Sart;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer extends ModelAbstract{
    private $_mail = null;
    public $error;
        
    public function __construct()
    {
        $this->_mail = new PHPMailer(true);  
        //$this->_mail->SMTPDebug = 2;                                 // Enable verbose debug output
        
        //Read config file
        $cfg = parse_ini_file("config.ini",true);
        if(empty($cfg['smtp']))
        {
            throw new Exception('No Smtp settings!');
        }
        
        //Server info
        $this->_mail->isSMTP();                                      // Set mailer to use SMTP
        $this->_mail->Host = !empty($cfg['smtp']['host']) ? $cfg['smtp']['host'] : '';  // Specify main and backup SMTP servers
        $this->_mail->SMTPAuth = !empty($cfg['smtp']['auth']) ? $cfg['smtp']['auth'] : true;                               // Enable SMTP authentication
        $this->_mail->Username = !empty($cfg['smtp']['username']) ? $cfg['smtp']['username'] : '';                 // SMTP username
        $this->_mail->Password = !empty($cfg['smtp']['password']) ? $cfg['smtp']['password'] : '';                          // SMTP password
        $this->_mail->SMTPSecure = !empty($cfg['smtp']['secure']) ? $cfg['smtp']['secure'] : 'ssl';                          // Enable TLS encryption, `ssl` also accepted
        $this->_mail->Port = !empty($cfg['smtp']['port']) ? $cfg['smtp']['port'] : '465';
        
        //Set default values
        $this->_mail->setFrom('robot@sketchmakerpro.com', 'SketchMaker');
        $this->_mail->isHTML(true);  
        
        //$mail->addReplyTo('info@example.com', 'Information');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');
    }
    
    /**
     * Send license to the user
     * @param \Sart\User $user object with user info
     * @return bool
     */
    public function emailLicense($user)
    {
        
        $this->_mail->clearAddresses();
        $this->_mail->addAddress($user->email);
        $this->_mail->Subject = 'Your License Details';
        $this->_mail->Body    =
"Hi {$user->getFullName()}! <br/><br/>
Below you can find your license details: <br />
<p><b>{$user->license}</b></p>";
        //$mail->AltBody = "Hi {$user->getFullName()} \n Below you can find your license details:\n\n\n {$user->license}";
        
        return $this->_send();   
    }

    
    public function emailPasswordReset($user)
    {
        $baseUrl =  (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        
        $this->_mail->clearAddresses();
        $this->_mail->addAddress($user->email);
        $this->_mail->Subject = 'Instructions for password resets';
        $this->_mail->Body    =
"Hi {$user->getFullName()}! <br/><br/>
Somebody requested to reset your password in our system. To do this please visit link below and set new password:
<p><b><a href=\"{$baseUrl}/reset.php?key={$user->reset_key}\">Reset Password</a></b></p>
";
        //$mail->AltBody = "Hi {$user->getFullName()} \n Below you can find your license details:\n\n\n {$user->license}";
        
        return $this->_send();           
    }
    
    /**
     * Send email when video has been done
     * @param \Sart\User $user object with user info
     * @param string $projectName  project info
     * @param \Sart\User $video object with video info
     * @return bool
     */
    public function emailRenderFinished($user,$projectName,$video)
    {
        
        $baseUrl =  defined('RENDER_BASE_URL') ? RENDER_BASE_URL : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        
        $this->_mail->clearAddresses();
        $this->_mail->addAddress($user->email);
        $this->_mail->Subject = 'Your Video Has been rendered';
        $this->_mail->Body    =
"Hi {$user->getFullName()}! <br/><br/>
<p>
Your video for project \"{$projectName}\" has been rendered <br />
</p><p>
<a href=\"{$baseUrl}/videos.php\">Go to Videos Page</a>
</p>
";
        //$mail->AltBody = "Hi {$user->getFullName()} \n Below you can find your license details:\n\n\n {$user->license}";
        
        return $this->_send();   
    }    
    
    /**
     * 
     */
    private function _send()
    {
        try{
            if($this->_mail->send())
            {
                return true;
            }else{
                $this->error = $this->_mail->ErrorInfo;
            }
        }catch(Exception $e)
        {
            $this->error = $e->getMessage(); 
        }
        
        return false;
    }
}