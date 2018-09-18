<?php
require 'classes/bootstrap.php';
if(!$user->isAdmin){
    header('Location: /');
    die();
}

$cfg = parse_ini_file('classes/config.ini',true);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Admin</title>

    <link href="vendor/twbs/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    <link href="vendor/fortawesome/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/jquery.growl.css" rel="stylesheet">
    <link href="js/fine-uploader/fine-uploader-gallery.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
</head>

<body class="">
<?php
$mTargetBlank = true;
include_once('includes/navbar.php');
?>

<div class="container appContainer">
  <div class="row">
    <main role="main" class="col-12">
        <h4 class="mb-4">Admin</h4>
        <form id="form_settings" action="api/saveSettings.php" method="post">
            <div class="row">
              <div class="col-sm-4">
                <div class="card mb-4">
                  <div class="card-body">
                    <h5 class="card-title">Total User Count</h5>
                    <p class="card-text" style="font-size: 2em; font-weight:bold; "><?php  echo \Sart\User::model()->getTotalCount(); ?></p>
                  </div>
                </div>
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Render Settings</h5>
                    <div class="card-text">
                        <div class="form-group">
                          <label for="cfg_max_render">Max Render Processes</label>
                          <input type="text" name="cfg[render][max_render_process]" class="form-control" id="cfg_max_render" value="<?php echo $cfg['render']['max_render_process']; ?>">
                        </div>                        
                    </div>
                    <button type="submit" href="#" class="btn btn-primary" name="section" value="render">Save Settings</a>
                  </div>
                </div>                
              </div>
              <div class="col-sm-4">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">SMTP Settings</h5>
                    <div class="card-text">
                        <div class="form-group">
                          <label for="cfg_smpt_host">Host</label>
                          <input type="text" name="cfg[smtp][host]" class="form-control" id="cfg_smpt_host" value="<?php echo $cfg['smtp']['host']; ?>">
                        </div>                        
                        <div class="form-group">
                          <label for="cfg_smpt_port">Port</label>
                          <input type="text" name="cfg[smtp][port]" class="form-control" id="cfg_smpt_port" value="<?php echo $cfg['smtp']['port']; ?>">
                        </div>
                        <div class="form-group">
                          <label for="cfg_smpt_secure">Secure</label>
                          <input type="text" name="cfg[smtp][secure]" class="form-control" id="cfg_smpt_secure" value="<?php echo $cfg['smtp']['secure']; ?>">
                        </div>
                        <div class="form-group">
                          <label for="cfg_smpt_username">Username</label>
                          <input type="text" name="cfg[smtp][username]" class="form-control" id="cfg_smpt_username" value="<?php echo $cfg['smtp']['username']; ?>">
                        </div>
                        <div class="form-group">
                          <label for="cfg_smpt_password">Password</label>
                          <input type="text" name="cfg[smtp][password]" class="form-control" id="cfg_smpt_password" value="<?php echo $cfg['smtp']['password']; ?>">
                        </div>                          
                    </div>
                    <button type="submit" href="#" class="btn btn-primary" name="section" value="s">Save Settings</a>
                  </div>
                </div>
              </div>
              <div class="col-sm-4">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Load Samples</h5>
                    <p class="card-text">
                        Upload archive with samples to fill folders(images/audio)
                    </p>
                    <button type="button" id="btnOpenSamplesDlg" href="#" class="btn btn-primary">Upload Files</a>
                  </div>
                </div> 
              </div>
            </div>
        </form>
    </main>
  </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modalLoadSamples">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Load archive with samples</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="samplesUploader"></div>
      </div>
    </div>
  </div>
</div>
<script type="text/template" id="qq-samples-template">
    <div class="qq-uploader-selector qq-uploader qq-gallery text-center" qq-drop-area-text="Drop files here">
        <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
            <span class="qq-upload-drop-area-text-selector"></span>
        </div>
        <div class="qq-upload-button-selector btn btn-primary ">
            <div>Upload a file</div>
        </div>


        <span class="qq-drop-processing-selector qq-drop-processing">
            <span>Processing dropped files...</span>
            <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
        </span>

        <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container mt-2">
            <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
        </div>
        <div class="qq-progress-bar-container-selector">
            <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
        </div>  

        <ul class="qq-upload-list-selector qq-upload-list" role="region" aria-live="polite" aria-relevant="additions removals">
            <li>
                <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
                <div class="qq-progress-bar-container-selector qq-progress-bar-container">
                    <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
                </div>
                <span class="qq-upload-spinner-selector qq-upload-spinner"></span>

                <button type="button" class="qq-upload-cancel-selector qq-upload-cancel">X</button>
                <button type="button" class="qq-upload-retry-selector qq-upload-retry">
                    <span class="qq-btn qq-retry-icon" aria-label="Retry"></span>
                    Retry
                </button>

                <div class="qq-file-info">
                    <div class="qq-file-name">
                        <span class="qq-upload-file-selector qq-upload-file"></span>
                        <span class="qq-edit-filename-icon-selector qq-btn qq-edit-filename-icon" aria-label="Edit filename"></span>
                    </div>
                    <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
                    <span class="qq-upload-size-selector qq-upload-size"></span>
                    <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">
                        <span class="qq-btn qq-delete-icon" aria-label="Delete"></span>
                    </button>
                    <button type="button" class="qq-btn qq-upload-pause-selector qq-upload-pause">
                        <span class="qq-btn qq-pause-icon" aria-label="Pause"></span>
                    </button>
                    <button type="button" class="qq-btn qq-upload-continue-selector qq-upload-continue">
                        <span class="qq-btn qq-continue-icon" aria-label="Continue"></span>
                    </button>
                </div>
            </li>
        </ul>

        <dialog class="qq-alert-dialog-selector">
            <div class="qq-dialog-message-selector"></div>
            <div class="qq-dialog-buttons">
                <button type="button" class="qq-cancel-button-selector">Close</button>
            </div>
        </dialog>

        <dialog class="qq-confirm-dialog-selector">
            <div class="qq-dialog-message-selector text-center font-weight-bold"></div>
            <div class="alert alert-warning">Be sure to remove it from each slides where you have used it!</div>
            <div class="qq-dialog-buttons">
                <button type="button" class="qq-cancel-button-selector btn">No</button>
                <button type="button" class="qq-ok-button-selector btn btn-danger">Yes</button>
            </div>
        </dialog>

        <dialog class="qq-prompt-dialog-selector">
            <div class="qq-dialog-message-selector"></div>
            <input type="text">
            <div class="qq-dialog-buttons">
                <button type="button" class="qq-cancel-button-selector btn">Cancel</button>
                <button type="button" class="qq-ok-button-selector btn">Ok</button>
            </div>
        </dialog>
    </div>
</script>

<?php
include_once('includes/footer.php');
?>
<div class="hide" id="sink"></div>
<div id="overlay_full"></div>
<script src="vendor/components/jquery/jquery.js"></script>
<script src="vendor/twbs/bootstrap/assets/js/vendor/popper.min.js"></script>
<script src="vendor/twbs/bootstrap/dist/js/bootstrap.js"></script>
<script src="js/jquery.growl.js"></script>
<script src="js/fine-uploader/fine-uploader.min.js"></script>
<script src="js/admin.js"></script>
<script type="text/javascript">
    var samplesUploader = new qq.FineUploader({
        template: 'qq-samples-template',
        element: document.getElementById("samplesUploader"),
        request: {
            endpoint: "/api/uploadSamples.php"
        },
        chunking: {
            enabled: false,
            concurrent: {
                enabled: false
            },
            success: {
                endpoint: "/api/uploadSamples.php?done"
            }
        },
        resume: {
            enabled: true
        },
        multiple: false,
        retry: {
            enableAuto: false,
            showButton: true
        },
        validation:{
            allowedExtensions:['zip'],
            itemLimit: 1,
        },
        callbacks: {
            onComplete: function(id,name,data,xhr)
            {
                if(data.success)
                {
                    $('#modalLoadSamples').modal('hide');
                    jQuery.growl(
                        {
                            title: 'Success!',
                            location: 'br', // ('tl' | 'tr' | 'bl' | 'br' | 'tc' | 'bc' - default: 'tr')
                            style: 'notice', // ('default' | 'error' | 'notice' | 'warning' - default: 'default')
                            message: 'Samples has been uploaded!'
                        }
                    );             
                }else{
                    if(jQuery().growl)
                    {
                        jQuery.growl(
                            {
                                location: 'tc', // ('tl' | 'tr' | 'bl' | 'br' | 'tc' | 'bc' - default: 'tr')
                                style: 'error', // ('default' | 'error' | 'notice' | 'warning' - default: 'default')
                                message: data.error
                            }
                        );
                    }
                    console.error(data.error);                    
                }
                
           },
            onSubmit:function(id,name)
            {
            }
        },
    });      
</script>
</body>
</html>
