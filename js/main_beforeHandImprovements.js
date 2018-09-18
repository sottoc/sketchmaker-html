function CanvasRecorder(container, d, Konva, Templates, bootbox, jQuery) {
  this.container = container;
  this.bootbox = bootbox;
  // this.CCapture = CCapture;
  this.capturer = null;
  this.d = d;
  this.animation = null;
  this.prevId = 0;
  this.t = Templates;
  this.Konva = Konva;
  this.$ = jQuery;
  this.history = [];
  this.projects = null;
  this.projectId = null;
  this.userFolder = null;
  this.projectName = null;
  this.screenRatio = null;
  this.fileFormat = null;
  this.videoResolution = null;
  this.backgroundLayer = null;
  this.background = null;
  this.backgroundImage = null;
  this.autoSave = null;
  this.slides = [
    {
      id: 0
    }
  ];
  this.currentSlide = 1;
  this.currentSlideDuration = 0;
  this.currentSlideDurationDisplayed = 0;
  this.stage = {};
  this._tmp_stage = {};
  
  this.layer = false;
  this.resizeHandle = {
    stroke: '#666',
    fill: '#ddd',
    strokeWidth: 2,
    radius: 8
  };
  this.loadableImageClass = 'loadableImage';
  this.templates = {};
  this.animationDuration = 0;
  this._delayBetweenElements = 0.5;
  
  
  this.lastSave = null;
  this.notifyOptions = {
    title: "CanvasRecorder",
    location: 'br', // ('tl' | 'tr' | 'bl' | 'br' | 'tc' | 'bc' - default: 'tr')
    style: 'notice', // ('default' | 'error' | 'notice' | 'warning' - default: 'default')
    message: "No message was set"
  };
  this.overlay = null;
  
  this.overlay_full = null;
  
  this.imagesBrowserComponent = null; // Container with images managing ui
  this.audioPlayerComponent = null;
  
  
  this._animationInProgress = [];
  this._hand = null;
  
  this._currentTextElement = null;
  this._currentText = null;
  this._currentTextTimeout = null;

    this.preloadedPaths = [];
    
    this.defferedQueue = [];
    
    
    this._currentPath = null;
 
    //Function, that should be used at next animation rendering step
    this._lastRenderFunction =  null;   
  
    //If we between slide transitions
    this._slideTransitionInProcess  = false;
    
    //Is all listeners were initialized
    this._isInitialized = false;
    
    //Flag to see if we should scroll down list of design items after open of tab or no
    this._tmp_scroll_to_bottom = false;

    //Attribute to store selected "attach audio to item" button
    this._tmpCurrentAttatchToItemBtn = null;
    
    //Array with audio objects
    this.audioQueue = [];
    
}

CanvasRecorder.prototype.getUniqueId = function getUniqueId(prefix) {
  prefix = (prefix !== undefined) ? prefix.toString() : 'element';
  this.prevId++;
  this.stage.attrs.prevId = this.prevId;
  return prefix+this.prevId;
};

CanvasRecorder.prototype.updateSlidePreviews = function updateSlidePreviews() {
  var slides = this.stage.find('.slide');
  console.log(
    'updateSlidePreviews',
    slides
  );
  this.t.insert('#slidesPreviewsContainerSlides', 'slidePreview', slides);
};

CanvasRecorder.prototype.initAutoSave = function initAutoSave() {
  var context = this;
  
  if(this.autoSave)
  {
    this.cancelAutoSave();
  }
  
  this.autoSave = setTimeout(function (event) {
    this.saveProject(function(){
        context.initAutoSave();
    });
  }.bind(this), 120 * 1000);
};

CanvasRecorder.prototype.cancelAutoSave = function cancelAutoSave() {
  if (this.autoSave) {
    clearTimeout(this.autoSave);
    this.autoSave = null;
  }
};

CanvasRecorder.prototype.init = function init() {
  var context = this;
  this.bootbox.setDefaults({
    size: "small"
  });

  this.cancelAutoSave();

    this.overlay = this.$('#overlay');
    this.overlay_full = this.$('#overlay_full');
  
    var hash = window.location.hash.substr(1);
    if(hash.indexOf('loadProject')>=0)
    {
        this.listProjects();
    }else{
        this.askForProjectName();        
    }
  
    this.$('[href="#tab-images"]').tab('show');

  
    // List with handle
    Sortable.create(this.d.getElementById('designList'), {
        handle: '.sortable-handle',
        animation: 150,
        onSort: function(event){
            //console.log('sort',event);
            
            context.layer.children.reorder(event.oldIndex,event.newIndex);
            
            context.$(event.to).find('.index').each(function(idx){
                jQuery(this).text(idx+1);
            });
            
            
            
        }
    });
  

  //this.$("ol.dropdown-menu").sortable({
  //  group: 'nav'
  //});

    //Attach google fonts slector
    this.$( "#typeFace" ).higooglefonts({
        selectedCallback:function(e){
            context.overlay_full.show();
        },
        loadedCallback:function(font){
            context.overlay_full.hide();
            if(Array.isArray(context.stage.attrs.loadedFonts) )
            {
                if(context.stage.attrs.loadedFonts.indexOf(font)==-1)
                {
                        context.stage.attrs.loadedFonts.push(font);
                }
            }else{
                context.stage.attrs.loadedFonts = [font];
            }
        }
    });  
  
  
    this.createStage();

    //Init image browser
    this.imagesBrowserComponent = new ImageBrowser('#imagesBrowserComponent',this.$,this.t);
    this.imagesBrowserComponent.init();
    
    //additional actions for design tab selections
    this.$('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        switch(e.target.hash)
        {
            case '#tab-background':
                this.imagesBrowserComponent.appendTo(e.target.hash);
                this.imagesBrowserComponent.activate('a[href="#backgrounds"]');
                break;
            case '#tab-images':
                this.imagesBrowserComponent.appendTo(e.target.hash);
                this.imagesBrowserComponent.activate($('a[href="#user_images"]').closest('li').next().find('a'));
                break;
            default:
                break;
        }
      
    }.bind(context)) ;
    
    this.$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        switch(e.target.hash)
        {
            case '#tab-design':
                    if(this._tmp_scroll_to_bottom)
                    {
                        setTimeout(function(){
                            this.$('#tab-design .simplebar-scroll-content').scrollTop(4000);
                            this._tmp_scroll_to_bottom = false;                            
                        }.bind(this),100);

                    }
                break;
            default:
                break;            
        }
    }.bind(context)) ;

    //If we at background tab, then we can use only background folder
    this.$('body').on('click','#imagesDirectoryList a[data-toggle="tab"]',function(event){
        if(context.$(this).parents('#tab-background').length && context.$(this).is(':not(a[href="#backgrounds"])'))
        {
            context.$('a[href="#tab-images"]').click();
        }
        if(context.$(this).parents('#tab-images').length && context.$(this).is('a[href="#backgrounds"]'))
        {
            context.$('a[href="#tab-background"]').click();
        }        
    });

    //Init audio player
    this.audioPlayerComponent = new AudioPlayer('#audioListContainer ',this.$,this.t);
  
  
    if(this._isInitialized)
    {
      return;
    }    
  
    this.addEventListeners(this);
    this._isInitialized = true;
};

CanvasRecorder.prototype.notify = function notify(options) {
  if (typeof options === 'string') {
    var message = options;
    options = this.notifyOptions;
    options.message = message;
  } else {
    Object.keys(this.notifyOptions).map( function(key) {
      options[key] = options.hasOwnProperty(key) ? options[key] : this.notifyOptions[key];
    }.bind(this) );
  }

  return this.$.growl(options);
};

CanvasRecorder.prototype.createStage = function createStage(json) {
    var context = this;
  json = json || false;

  if (json) {
    this.overlay_full.show();
    
    this.adjustScreenRatio(this.screenRatio);
    this.stage = this.Konva.Node.create(json, this.container);
    
    //Load all fonts
    this.loadAllFontsFromStage();
    
    //restore audio settings
    if(this.stage.attrs.audio)
    {
        this.audioPlayerComponent.setSelected(this.stage.attrs.audio);
    }
    
    //restore background settings
    this.backgroundLayer = this.stage.findOne('.backgroundLayer');
    this.background = this.stage.findOne('.background');
    this.backgroundImage = this.stage.findOne('.backgroundImage');

    //restore hands settings
    if(this.stage.attrs.hand_drop)
    {
        this.selectHand('drop',this.stage.attrs.hand_drop);
    }
    if(this.stage.attrs.hand_write)
    {
        this.selectHand('write',this.stage.attrs.hand_write);
    }    
    
    //restore background Mode
    if(this.stage.attrs.backgroundMode)
    {
        this.setBackgroundMode(this.stage.attrs.backgroundMode, true);
    }else{
        this.setBackgroundMode('single',true);
    }
    
    this.fixProblems();
    this.resetAnchors();

    this.layer = this.stage.findOne('.slide');


    if(this.layer)
    {
        this.displaySlide(this.layer.index);
    }
    
    this.t.insert('#slidesPreviewsContainerSlides', 'slidePreview', this.stage.find('.slide'));

    this.prevId = this.stage.attrs.prevId !== undefined ? this.stage.attrs.prevId : Object.keys(this.Konva.ids).length;
    
    this.updateData();
    
    //Preload svgs
    var svgs = this.stage.find('.svg_img');
    
    for(var i = 0; i <svgs.length; i++)
    {
        this.defferedQueue.push(this.preloadSvg(svgs[i].attrs.src,svgs[i]));
    }    
    
    //Preload audio
    var groups = this.stage.find('Group');
    for(var i = 0; i <groups.length; i++)
    {
        if(groups[i].attrs.attached_mp3 !== undefined)
        this.defferedQueue.push(this.preloadAudio(groups[i].id(), groups[i].attrs.attached_mp3));
    }  

    context.$.when.apply(this.$, this.defferedQueue).then( function(){
        context.overlay_full.hide();
    } ).fail(function(){
        if(jQuery().growl)
        {
            jQuery.growl(
                {
                    location: 'tc', 
                    style: 'error', 
                    message: 'Error during preload all elements from project. Please check your internet connection or contact administrator.'
                }
            );
        }
    });

  
  } else {
    this.resetStage();

    //Clear background
    if(this.backgroundLayer)
    {
        this.backgroundLayer.destroyChildren();
        this.backgroundLayer.destroy();
        this.backgroundLayer = null;
        this.backgroundImage = null;
        this.background = null;
    }

    this.addStageElements();

    
    //Default settings
    //set default hands
    this.selectHand('write','/img/hand/write/hand1.png');
    this.selectHand('drop','/img/hand/drop/image_hand.png');
    
    //set default slide transition
    this.setSlideTransition();
    this.setBackgroundMode('single',true);
  }

  this.initAutoSave();

  setTimeout(function () {
    if (this.projectName !== null)
      this.saveProject();
  }.bind(this), 3000);
  
};

CanvasRecorder.prototype.reloadStageSizes = function()
{
    this.stage.setWidth(this.d.getElementById(this.container).offsetWidth);
    this.stage.setHeight(this.d.getElementById(this.container).offsetHeight);
};

CanvasRecorder.prototype.resetStage = function resetStage() {
  this.$('#slidesPreviewsContainerSlides').html('');

  this.stage = new this.Konva.Stage({
    container: this.container,
    width: this.d.getElementById(this.container).offsetWidth,
    height: this.d.getElementById(this.container).offsetHeight
  });
  this.prevId = 0;
};

CanvasRecorder.prototype.addStageElements = function addStageElements() {
  this.addBackgroundLayer();

  this.addSlide();
};

CanvasRecorder.prototype.addBackgroundLayer = function addBackgroundLayer() {
  this.backgroundLayer = new this.Konva.Layer({
    name: 'backgroundLayer'
  });
  this.stage.add(this.backgroundLayer);

  this.background = new Konva.Rect({
    x: 0,
    y: 0,
    width: this.d.getElementById(this.container).offsetWidth,
    height: this.d.getElementById(this.container).offsetHeight,
    // fill: '#e5f1e8',
    fill: '#FFFFFF',
    name: 'background'
  });
  
  this.backgroundLayer.add(this.background);

  this.backgroundLayer.draw();
};

CanvasRecorder.prototype.setBackground = function setBackground(picker) {
  if (picker.constructor.name === 'jscolor') {
    if(this.stage.attrs.backgroundMode == 'perslide')
    {
        this.processSlideBackgroundColor(this.layer,'#' + picker.toString() );
        this.redrawSlidePreview(this.currentSlide);
    }else{
        this.background.setFill('#' + picker.toString());
        this.backgroundLayer.draw();
        this.redrawSlidePreview();
    }
    this.stage.batchDraw();

  } else {
    //console.log('setBackground', picker);
  }
};


/**
 * Set background image
 * @method
 * @author n.z@software-art.com
 */
CanvasRecorder.prototype.setBackgroundImage = function setBackgroundImage(imageData)
{
    var context = this;
    if(this.stage.attrs.backgroundMode == 'perslide')
    {
        this.processSlideBackgroundImage(this.layer,imageData);
    }else{

        if(context.backgroundImage)
        {
            context.backgroundImage.destroy();    
        }
        
        
        imageData = this._stretchImage(imageData);
        var backgroundImage = new Konva.Image({
            x: imageData.x,
            y: imageData.y,
            width: imageData.width,
            height: imageData.height,
            src: imageData.src,
            name: 'backgroundImage'
        });
    
        if(this.stage.attrs.backgroundMode == 'perslide')
        {
            this.layer.attrs.backgroundImage = imageData;
        } 
        
        context.backgroundLayer.add(backgroundImage);
        context.overlay_full.show();
        var object = new Image();
        object.onload = function () {
           backgroundImage.image(object);
           context.overlay_full.hide();
           context.backgroundLayer.draw();
           context.updateData(context.backgroundLayer);
           context.redrawSlidePreview();
         }.bind(context);
    
        object.src = imageData.src;
       
        context.backgroundImage = backgroundImage;
    }
};



CanvasRecorder.prototype.fixProblems = function fixProblems() {
  this.stage.find('Image').forEach(function (imageNode) {
    var src = imageNode.getAttr('src')
      , image = new Image()
      , group = imageNode.getParent();

    this.fixPosition(imageNode, group);

    image.onload = function () {
      imageNode.show();
      imageNode.image(image);
      // imageNode.getLayer().batchDraw();
      imageNode.getLayer().draw();

      this.redrawSlidePreview(imageNode.getLayer().index);
    }.bind(this);

    image.src = src;
  }.bind(this));

  this.stage.find('Text').forEach(function (textNode) {
    var group = textNode.getParent();

    this.fixPosition(textNode, group);

    this.addTextEventListeners(textNode, this);
  }.bind(this));
  
  if(this.backgroundLayer)
  {
    this.backgroundLayer.setX(0);
    this.backgroundLayer.setY(0);
    if(this.backgroundImage)
    {
        var correctPos = this._stretchImage({width: this.backgroundImage.getWidth(), height: this.backgroundImage.getHeight()});
        this.backgroundImage.setX(correctPos.x);
        this.backgroundImage.setY(correctPos.y);
    }
  }
  
  
    //Remove all transitions group, that could left from previous  cycles and was not properly cleaned
    this._clearTransitions();
  
};

CanvasRecorder.prototype.fixPosition = function fixPosition(node, group) {
  group.setX(group.getX() + node.getX());
  group.setY(group.getY() + node.getY());
  node.setX(0);
  node.setY(0);
};

CanvasRecorder.prototype.resetAnchors = function resetAnchors() {
  var context = this;

  this.stage.find('Group').forEach(function removeOldAnchorsAndAddNewOnes(group) {
    
    if(!context._checkGroup(group))
    {
        return;
    }
    
    var firstChild = group.children[0];

    group.findOne('.topLeft').destroy();
    group.findOne('.topRight').destroy();
    group.findOne('.bottomLeft').destroy();
    group.findOne('.bottomRight').destroy();

    context.addAnchors(group, firstChild.getWidth(), firstChild.getHeight(), context);
  });
};

CanvasRecorder.prototype.askForProjectName = function askForProjectName(saveAs) {
  saveAs = saveAs || false;
  var context = this;
  var modal = this.bootbox.dialog({
    message: this.$("#newProjectDialogContent").html(),
    title: !saveAs ? "Enter new project name" : "Enter project name",
    closeButton: false,
    buttons: [
      {
        label: "Create Project",
        className: "btn btn-primary pull-left",
        callback: function() {
          var projectName = modal.find('#projectName').val()
            , screenRatio = modal.find('#screenRatio').val()
            ;

          if (projectName !== '') {
            this.projectName = projectName;

            this.adjustScreenRatio(screenRatio);
            

            this.reloadStageSizes();
            this.$('#projectName').text(projectName);
            if (saveAs) {
              this.saveProject();
            }
            modal.modal("hide");
          } else {
            console.log('projectName',projectName);
            console.log('screenRatio',screenRatio);

            modal.find('#projectName').addClass('is-invalid');
          }
            //Open images tab as default          
            context.$('a[href="#tab-images"]').click();
          return false;
        }.bind(this)
      },
      {
        label: "Load Existing",
        className: "btn btn-secondary pull-left",
        callback: function() {
          modal.modal("hide");
          this.listProjects();
        }.bind(this)
      }
    ],
    show: false
  });

  modal.modal("show");
};

CanvasRecorder.prototype.selectProjectToLoad = function selectProjectToLoad() {
    var variants = [],
        context = this;
    for(var i=0; i < this.projects.length; i++)
    {
        variants.push({
            text: context.projects[i].text,
            value: i
        });
    }

    variants.unshift({
      text: 'Choose one...',
      value: ''
    });
  this.bootbox.prompt({
    title: "Load project",
    inputType: 'select',
    inputOptions: variants,
    callback: function loadProjectCallback(result) {
      if (result === '' || result === null) {
        context.askForProjectName();
      } else {
        
        context.projectName = context.projects[result].text;
        context.$('#projectName').text(context.projectName);
        context.projectId = context.projects[result].projectId;
        
        context.slides = JSON.parse(context.projects[result].value);
        context.screenRatio = context.detectRatioFromSlides();
        context.createStage(context.slides);
        
        //Load transtions
        if(context.stage.attrs.slideTransitionIn && context.stage.attrs.slideTransitionOut)
        {
            context.$('.seffect-thumb.active').removeClass('active');
            context.$('.seffect-thumb[data-slide-in="'+context.stage.attrs.slideTransitionIn+'"][data-slide-out="'+context.stage.attrs.slideTransitionOut+'"]').addClass('active');
        }
        
        //if(context.stage.attrs.slideTransitionOut)
        //{
        //    context.$('#slideTransitionOut').val(context.stage.attrs.slideTransitionOut);
        //}
        
        //Open images tab as default          
        context.$('a[href="#tab-images"]').click();
      }
    },
    buttons: {
      confirm: {
        label: 'OK',
        className: 'btn-primary'
      },
      cancel: {
        label: 'New Project',
        className: 'btn-secondary'
      }
    }
  });
};

CanvasRecorder.prototype.listProjects = function listProjects(event) {
  var context = this;
  this.overlay_full.show();
  this.$.get('api/listProjects.php', function processListProjectsResult (data) {
    context.overlay_full.hide();
    context.projects = data.result;
    context.selectProjectToLoad();
  }, 'json').fail(function(){
    context.overlay_full.hide();    
  });
};

CanvasRecorder.prototype.saveProject = function saveProject(callback) {
    var ctx = this;
    this.stage.attrs['prevId'] = this.prevId;
    this.stage.attrs['audio'] = this.audioPlayerComponent.selected;
    return this.$.post('api/saveProject.php', {
      // data: JSON.stringify(this.slides),
      data: this.stage.toJSON(),
      name: this.projectName,
      duration: this.getAnimationDuration()
    }, function processSaveFileResult (data) {
        if(ctx.$.isFunction(callback))
        {
            callback();
        }
        console.log(data);
        this.lastSave = new Date();
        if (data.result !== false) {
            this.notify("Project saved!");
            this.projectId = data.result;
        }
    }.bind(this), 'json');
};

CanvasRecorder.prototype.addImage = function addImage(item, context) {
  
  var aspectRatio = item.width/(item.height > 0 ? item.height : 1);
  
  if (item.width >= context.d.getElementById(context.container).offsetWidth) {
    item.width = context.d.getElementById(context.container).offsetWidth - 20;
    //item.height = context.d.getElementById(context.container).offsetHeight / context.d.getElementById(context.container).offsetWidth * item.width;
    item.height = item.width /  aspectRatio;
  } else if (item.height >= context.d.getElementById(context.container).offsetHeight) {
    item.height = context.d.getElementById(context.container).offsetHeight - 20;
    //item.width = context.d.getElementById(context.container).offsetWidth / context.d.getElementById(context.container).offsetHeight * item.height;
    item.width = item.height * aspectRatio;
  }

  
  var image = new context.Konva.Image({
    width: item.width,
    height: item.height,
    src: item.src,
    aspectRatio: aspectRatio,
    default_w: item.width,
    id: this.getUniqueId('image_group_image')
  });

  var group = new context.Konva.Group({
    x: context.d.getElementById(context.container).offsetWidth / 2 - item.width / 2,
    y: context.d.getElementById(context.container).offsetHeight / 2 - item.height / 2,
    beforeNext: 2,
    // delay: this.currentSlideDuration,
    // duration: 2,
    id: this.getUniqueId('image_group_group'),
    draggable: true
  });
  // this.currentSlideDuration += 2;
  context.layer.add(group);
  group.add(image);
  context.addAnchors(group, item.width, item.height, context);

    context._tmp_scroll_to_bottom = true;
  
  var object = new Image();
    context.overlay_full.show();
  object.onload = function () {
    image.image(object);
    context.overlay_full.hide();
    context.layer.draw();

    context.updateData(context.layer);
  }.bind(context);

  object.src = item.src;
  
  
  if(item.src.split('.').pop()=='svg')
  {
    image.setName('svg_img');
    this.preloadSvg(item.src,image).then(function(){
        context.updateData(context.layer);
    });
  }

  
};

CanvasRecorder.prototype.addText = function addText (item, context) {
  var ctx = context.layer.getCanvas().getContext("2d");
  ctx.font = item.fontSize + 'px ' + item.fontFamily;
  var stageWidth = context.stage.getWidth();
  //Autoresize to fit stage
  while(ctx.measureText(item.text).width  > stageWidth)
  {
    item.fontSize--;
    ctx.font = item.fontSize + 'px ' + item.fontFamily;
  }
  
  item.width = ctx.measureText(item.text).width;
  item.height = item.fontSize;
  
    item.id =  this.getUniqueId('txt_group_text');
  var text = new context.Konva.Text(item);

  var group = new context.Konva.Group({
    x: context.d.getElementById(context.container).offsetWidth / 2 - item.width / 2,
    y: context.d.getElementById(context.container).offsetHeight / 2 - item.height / 2,
    beforeNext: 2,
    // delay: this.currentSlideDuration,
    // duration: 2,
    id: this.getUniqueId('txt_group_group'),
    draggable: true
  });
  // this.currentSlideDuration += 2;
  context.layer.add(group);
  group.add(text);
  context.addAnchors(group, item.width, item.height, context);

  context.layer.draw();
  
  context._tmp_scroll_to_bottom = true;
  
  context.updateData(context.layer);

  this.addTextEventListeners(text, context);
  
};


/**
 * @method
 * @memberOf CanvasRecorder.prototype
 */
CanvasRecorder.prototype.updateText  = function(item,context)
{
    var context = this;
    var group = context.layer.findOne('#' + item.groupId);
    
    if(!group || !context._checkGroup(group))
    {
        return;
    }

    var firstChild = group.children[0];

    if (firstChild.className != 'Text' && firstChild.nodeType != 'Text') {
        console.warn('Not Text node provided for edit!');
        return false;        
    }

    var ctx = context.layer.getCanvas().getContext("2d");
    ctx.font = item.height + 'px ' + item.fontFamily;
    var stageWidth = context.stage.getWidth();
    //Autoresize to fit stage
    var change_x = false;
    while(ctx.measureText(item.text).width  > stageWidth)
    {
      item.fontSize--;
      ctx.font = item.fontSize + 'px ' + item.fontFamily;
      change_x = true;
    }
  
    item.width = ctx.measureText(item.text).width;
    item.height = item.fontSize;    
    
    
    firstChild.fontSize(item.height);
    firstChild.fontFamily(item.fontFamily);
    //firstChild.text(item.text);
    //Workaraound to fix bug
    firstChild.setAttr('text',item.text);
    //firstChild.attrs.text = item.text;
    firstChild.fill(item.fill);
    
    if(change_x)
    {
        group.x(0);
    }
    
    group.findOne('.topLeft').destroy();
    group.findOne('.topRight').destroy();
    group.findOne('.bottomLeft').destroy();
    group.findOne('.bottomRight').destroy();

    context.addAnchors(group, item.width, item.height, context);
    

    context.layer.draw();
    context.updateData(context.layer);
};

CanvasRecorder.prototype.addTextEventListeners = function addTextEventListeners (text, context) {
  text.on('dblclick', function () {
    // create textarea over canvas with absolute position

    // first we need to find its positon
    var textPosition = text.getAbsolutePosition();
    var stageBox = context.stage.getContainer().getBoundingClientRect();

    var areaPosition = {
      x: textPosition.x + stageBox.left,
      y: textPosition.y + stageBox.top
    };


    // create textarea and style it
    var textarea = document.createElement('textarea');
    document.body.appendChild(textarea);

    textarea.value = text.text();
    textarea.style.position = 'absolute';
    textarea.style.top = areaPosition.y + 'px';
    textarea.style.left = areaPosition.x + 'px';
    textarea.style.width = text.width();

    textarea.focus();


    textarea.addEventListener('keydown', function (e) {
      // hide on enter
      if (e.keyCode === 13) {
        text.text(textarea.value);
        context.layer.draw();
        document.body.removeChild(textarea);
      }
    });
  });
};


/**
 *  Move anchors and preserver aspect ratio
 *  @method
 *  @memberOf CanvasRecorder.prototype
 */
CanvasRecorder.prototype.anchorMoved = function anchorMoved (activeAnchor) {
  var group = activeAnchor.getParent()
    // , stage = group.getStage()
    ;

    var topLeft = group.get('.topLeft')[0];
    var topRight = group.get('.topRight')[0];
    var bottomRight = group.get('.bottomRight')[0];
    var bottomLeft = group.get('.bottomLeft')[0];
   
    var anchorX = activeAnchor.getX();
    var anchorY = activeAnchor.getY();


    var topLeftY = topLeft.getY();
    
    var width = topRight.getX() - topLeft.getX();

    
    if(group.children[0].className=='Image')
    {
        var aspectRatio = group.getChildren()[0].attrs.aspectRatio;
        //var org_w = group.getChildren()[0].attrs.default_w;
        //var diff = width / org_w;
        var newY = width / aspectRatio;
  
        switch (activeAnchor.getName()) {
            case 'topLeft':
                topRight.setY(anchorY);
                bottomLeft.setX(anchorX);
                bottomLeft.setY(topLeftY + newY);
                bottomRight.setY(topLeftY + newY);
            break;
            case 'topRight':
                topLeft.setY(anchorY);
                bottomRight.setX(anchorX);
                bottomLeft.setY(topLeftY + newY);
                bottomRight.setY(topLeftY + newY);
            break;
            case 'bottomRight':
                bottomLeft.setY(topLeftY + newY);
                topRight.setX(anchorX);
                bottomRight.setY(topLeftY + newY);
                bottomRight.setX(anchorX);
            break;
            case 'bottomLeft':
                bottomRight.setY(topLeftY + newY);
                topLeft.setX(anchorX);
                bottomLeft.setY(topLeftY + newY);
                bottomLeft.setX(anchorX);
            break;
        }  
    }else{
        
        // update anchor positions
        switch (activeAnchor.getName()) {
          case 'topLeft':
            topRight.setY(anchorY);
            bottomLeft.setX(anchorX);
            break;
          case 'topRight':
            topLeft.setY(anchorY);
            bottomRight.setX(anchorX);
            break;
          case 'bottomRight':
            bottomLeft.setY(anchorY);
            topRight.setX(anchorX);
            break;
          case 'bottomLeft':
            bottomRight.setY(anchorY);
            topLeft.setX(anchorX);
            break;
        }        
    }
  

    width = topRight.getX() - topLeft.getX();
    var height = bottomLeft.getY() - topLeft.getY();

  group.getChildren(function (node) {
    if (node.getName() !== 'topLeft' && node.getName() !== 'topRight'
      && node.getName() !== 'bottomRight' && node.getName() !== 'bottomLeft')
      if (width && height) {
        var pos = topLeft.position();
        node.x(pos.x);
        node.y(pos.y);

        node.width(width);
        node.height(height);            
        
        if (group.children[0].className === 'Text' || group.children[0].nodeType === 'Text') {
            group.children[0].fontSize(height);
        }
      }
  });

  //Update data without triggering of list items redrawing
  this.updateData(false);
};

CanvasRecorder.prototype.scrollTo = function scrollTo(targetElement, position, speed) {
  speed = speed || 300;
  var scrollWidth = $(targetElement).get(0).scrollWidth;
  var clientWidth = $(targetElement).get(0).clientWidth;
  if (position === 'end' || position === undefined) {
    this.$(targetElement).animate({ scrollLeft: scrollWidth - clientWidth },
      {
        duration: speed
      });
  } else if (position === 'start') {
    this.$(targetElement).animate({ scrollLeft: 0 },
      {
        duration: speed
      });
  }
};

CanvasRecorder.prototype.updateData = function updateData(updateListItems) {
  updateListItems = updateListItems || false;
  this.slides = this.slides || {};

  this.slides = JSON.parse(this.stage.toJSON());
    if(updateListItems)
    {
        this.displayListItems();
    }
    if(this.layer!== undefined)
        this.redrawSlidePreview(this.layer.index);
};

CanvasRecorder.prototype.redrawSlidePreview = function redrawSlidePreview(index) {
    var currentIndex = this.currentSlide;
  index = index || false;
  var el, context = this;

  var slides = this.stage.find('.slide');
  if (!index) {
    Object.keys(slides).forEach(function (key) {
      if (slides.hasOwnProperty(key)) {
        var slide = slides[key];

        if (slide.nodeType === 'Layer') {
            
            if(context.stage.attrs.backgroundMode == 'single')
            {
                //Add background to layer to generate preview
                context.background.moveTo(slide);
                
                if(context.backgroundImage)
                {
                  context.backgroundImage.moveTo(slide);
                  context.backgroundImage.moveToBottom();
            }
            context.background.moveToBottom();
            }
          context.displaySlide(slide.index,null, false);

          console.log('redraw slide', slide.index);

          el = context.d.querySelector('.slidePreview[data-slide-preview-id="' + slide.index + '"]');

          if(el){
            el.setAttribute('src', context.layer.getCanvas().toDataURL('image/png'));
          }
          if(context.stage.attrs.backgroundMode == 'single')
          {
            //Move background back to background layer
            context.background.moveTo(context.backgroundLayer);
            
            if(context.backgroundImage)
            {
              context.backgroundImage.moveTo(context.backgroundLayer);
              context.backgroundImage.moveToBottom();
            }
            context.background.moveToBottom();
            context.backgroundLayer.draw();
          }
        }
      }
    });
    context.displaySlide(currentIndex,null, false);
  } else {
    Object.keys(slides).forEach(function (key) {
      if (slides.hasOwnProperty(key)) {
        var slide = slides[key];
        if (slide.nodeType === 'Layer' && slide.index === index ) {
          

            //Add background to layer to generate preview
            context.background.moveTo(slide);
            
            if(context.backgroundImage)
            {
              context.backgroundImage.moveTo(slide);
              context.backgroundImage.moveToBottom();
            }
            context.background.moveToBottom();
          
          var visible = slide.isVisible();

          
          if (!visible) {
            slide.show();
            slide.draw();
          }
          
          slide.draw();
          
          el = context.d.querySelector('.slidePreview[data-slide-preview-id="'+slide.index+'"]');
          el.setAttribute('src', slide.getCanvas().toDataURL('image/png'));
          if (!visible) {
            slide.hide();
            slide.draw();
          }
          
            //Move background back to background layer
            context.background.moveTo(context.backgroundLayer);
            
            if(context.backgroundImage)
            {
              context.backgroundImage.moveTo(context.backgroundLayer);
              context.backgroundImage.moveToBottom();
            }
            context.background.moveToBottom();
            context.backgroundLayer.draw();
        }
      }
    });
  }
};

CanvasRecorder.prototype.displayListItems = function displayListItems() {
    var items = this.getCurrentSlideContents() || [],
        display = [];
        var context = this;
        console.log('displayListItems', items);
        var $container = this.$('#designList');
        $container.html('');
        items.forEach(function (item, index) {
          if (this._checkGroup(item)) {
            var favouriteChild = item.children[0]
              , type = favouriteChild.className
              , attrs = favouriteChild.attrs
              , d = (type === 'Image' ) ? '<img src="'+attrs.src+'" class="designListItemImage">' : attrs.text
            ;
        
            var data = {
              index: index + 1,
              type: type,
              data: d,
              duration: item.attrs.duration,
              id: item.attrs.id,
              delay: item.attrs.delay,
              beforeNext: item.attrs.beforeNext,
              noHand: item.attrs.noHand ? ' checked ': '',
              drawingEffect: item.attrs.drawingEffect ? ' checked ': '',
              attached_1: item.attrs.attached_mp3 && item.attrs.attached_mp3.substr(item.attrs.attached_mp3.length - 2)=='_1' ? 'attached' : '',
              attached_2: item.attrs.attached_mp3 && item.attrs.attached_mp3.substr(item.attrs.attached_mp3.length - 2)=='_2' ? 'attached' : '',
              attached_3: item.attrs.attached_mp3 && item.attrs.attached_mp3.substr(item.attrs.attached_mp3.length - 2)=='_3' ? 'attached' : '',
            };
            var templateName = 'designListItem';
            switch(type)
            {
                case 'Image':
                    if(favouriteChild.attrs.src.split('.').pop() !=='svg' )
                    {
                        templateName = 'designListItemImage';    
                    }else{
                        if(favouriteChild.attrs.canDraw)
                        {
                            templateName = 'designListItemSvg';
                            data.boldness = '<option value="1" '+(item.attrs.boldness==1 ? 'selected' : '')+'>1</option><option value="2" '+(item.attrs.boldness==2 ? 'selected' : '')+'>2</option><option value="3" '+(item.attrs.boldness==3 ? 'selected' : '')+'>3</option>';
                            data.strokeColor = item.attrs.strokeColor ? item.attrs.strokeColor : '000000';
                        }else{
                            templateName = 'designListItem';
                        }
                    }
                    break;
                case 'Text':
                    templateName = 'designListItemText';
                    break;
                default:
                    break;
            }
            context.t.append('#designList', templateName, data);
            context.$('#designList .jscolor').each(function(){
                var picker = new jscolor(this);
            });
          }
          
        }.bind(this));
   
    //this.t.insert('#designList', 'designListItem', display);
};

CanvasRecorder.prototype.addAnchors = function addAnchors(group, x, y, context) {
  context._addAnchor(group, 0, 0, 'topLeft', context);
  context._addAnchor(group, x, 0, 'topRight', context);
  context._addAnchor(group, x, y, 'bottomRight', context);
  context._addAnchor(group, 0, y, 'bottomLeft', context);
};

CanvasRecorder.prototype._addAnchor = function _addAnchor(group, x, y, name, context) {
  var anchor = new context.Konva.Circle({
    x: x,
    y: y,
    stroke: context.resizeHandle.stroke,
    fill: context.resizeHandle.fill,
    strokeWidth: context.resizeHandle.strokeWidth,
    radius: context.resizeHandle.radius,
    name: name,
    draggable: true,
    dragOnTop: false
  });

  this.addAnchorEventListeners(group, anchor, context);

  group.add(anchor);
};

CanvasRecorder.prototype.addAnchorEventListeners = function addAnchorEventListeners(group, anchor, context) {
  var layer = group.getLayer();

  anchor.on('dragmove', function () {
    context.anchorMoved(this);
    layer.draw();
  });
  anchor.on('mousedown touchstart', function () {
    group.setDraggable(false);
    this.moveToTop();
  });
  anchor.on('dragend', function () {
    group.setDraggable(true);
    layer.draw();
  });
  // add hover styling
  anchor.on('mouseover', function () {
    var layer = this.getLayer();
    document.body.style.cursor = 'pointer';
    this.setStrokeWidth(4);
    layer.draw();
  });
  anchor.on('mouseout', function () {
    var layer = this.getLayer();
    document.body.style.cursor = 'default';
    this.setStrokeWidth(2);
    layer.draw();
  });
};


CanvasRecorder.prototype.addSlide = function addSlide() {
  this.currentSlide = this.stage.find('.slide').length + 1;

  // this.createStage();

  console.log(this.layer);
  if (this.layer) this.layer.hide();

  this.layer = new this.Konva.Layer({
    name: 'slide'
  });
  this.stage.add(this.layer);
  // this.addStageElements();
  this.t.append('#slidesPreviewsContainerSlides', 'slidePreview', this.layer);
  this.scrollTo(this.$('#slidesPreviewsContainer'));
  this.$('a[href="#tab-images"]').tab('show');

  
  // this.updateSlidePreviews();
  this.displayListItems();
  this.updateSlideControls(this.currentSlide);
  
  
};

CanvasRecorder.prototype.deleteSlideClick = function deleteSlideClick(event) {
  var el = event.target
    , itemId = parseInt(this.$(el).closest('.slideControl').attr('data-id'))
    , container = el.closest('.slidePreviewContainer')
    , context = this
    , slides = this.stage.find('.slide');

  for (var key in slides) {
    if (slides.hasOwnProperty(key)) {
      var slide = slides[key];
      if (slide.index === itemId) {
        console.log('destroy', slide);
        slide.destroy();
        // container.remove();

        this.slides = this.stage.find('.slide');
        this.t.insert('#slidesPreviewsContainerSlides', 'slidePreview', this.slides);
        context.displayListItems();
        break;
      }
    }
  }

  setTimeout(function(){
    context.redrawSlidePreview();
  }.bind(this), 100);
};

CanvasRecorder.prototype.clickSlidePreview = function clickSlidePreview(event) {
  var el = event.target
    , itemId = parseInt(this.$(el).closest('.slidePreviewContainer').attr('data-id'));

  this.displaySlide(itemId);
};

CanvasRecorder.prototype.displaySlide = function displaySlide(itemId,effect,switchToDesignTab) {
    effect = effect || false;
    switchToDesignTab = switchToDesignTab || false;
  var slides = this.stage.find('.slide');

  console.log('displaySlide', itemId);

  var prev = null;
  Object.keys(slides).forEach(function (key) {
    if (slides.hasOwnProperty(key)) {
        var slide = slides[key];
        if (slide.nodeType === 'Layer') {
    
            if (slide.index === itemId) {
                slide.show();
                if(effect)
                {
                    this.slideTransitions(slide,prev,effect,effect.callback);
                }       
                this.currentSlide = itemId;
                this.layer = slide;
                this.layer.draw();

                // this.redrawSlidePreview(slide.index);
                if(switchToDesignTab)
                {
                    this.$('[href="#tab-design"]').tab('show');
                }
            } else {
                slide.hide();
            }
          prev = slide;
        }
    }
  }.bind(this));

  this.updateSlideControls(itemId);
  this.displayListItems();

  this.stage.draw();
};

CanvasRecorder.prototype.goToNextSlide = function goToNextSlide() {
  var slides = this.stage.find('.slide');

  if (this.currentSlide < slides.length) {
    var args = {
        duration:0.5,
    };
    
    if(this.stage.attrs.slideTransitionIn)
    {
        args.effectIn = this.stage.attrs.slideTransitionIn;
    }
    
    if(this.stage.attrs.slideTransitionOut)
    {
        args.effectOut = this.stage.attrs.slideTransitionOut;
    }
    
    this.displaySlide(this.currentSlide + 1,args);
  }
};

CanvasRecorder.prototype.previewSlideAnimation = function previewSlideAnimation(event) {
  
  //cancel autosave during animation
  this.cancelAutoSave();
    
  this._animationInProgress = [];
  
  this.startAnimation();

  this.animation = new this.Konva.Animation(function animateSlide (frame) {
    this.playAnimation(frame, this, true);
  }.bind(this), this.layer);

  this.animation.start();
};

CanvasRecorder.prototype.previewFullAnimation = function previewFullAnimation(event) {
  
  //cancel autosave during animation
  this.cancelAutoSave();
  
  this._animationInProgress = [];
  
  this.displaySlide(1);

  this.startAnimation();

  this.animation = new this.Konva.Animation(function animateSlide (frame) {
    this.playAnimation(frame, this);
  }.bind(this), this.stage.find('Layer'));

  this.animation.start();
  
  this.audioPlayerComponent.playSelected();
};


/**
 * Attach event listeners
 * @method
 * @memberOf CanvasRecorder.prototype
 */
CanvasRecorder.prototype.addEventListeners = function(context) {
  //var imageLoadTrigger = 'pictureLoadInput'
  //  , previewContainer = 'pictureLoadPreview'
  //  ;

  window.onbeforeunload = function(){
    if (new Date() - this.lastSave > 5000) {
      this.saveProject();
      return 'Please make sure you have saved your project';
    }
  }.bind(this);

  context.$('#'+this.container).on('dragover',
    function allowDrop(ev) {
      ev.preventDefault();
    });

  context.$('body').on('mouseover mousemove dragmove', function(evt) {
    if (evt.target.parentElement.className === 'konvajs-content') {
      if (!context.capturer)
        // context.updateData(context.layer)
        context.redrawSlidePreview(context.layer.index);
    }
  });

  window.addEventListener("dragover",function(e){
    e = e || event;
    e.preventDefault();
  },false);

  context.$('body').on('dragstart', '.' + context.loadableImageClass, function (event) {
    // event.preventDefault();

    event.originalEvent.dataTransfer.setData('text', 'anything');
    event.originalEvent.dataTransfer.setData('loadable', true);
    event.originalEvent.dataTransfer.setData('width', event.target.width);
    event.originalEvent.dataTransfer.setData('height', event.target.height);
    var src = context.$(event.target).attr('data-src') ||  event.target.src;
    event.originalEvent.dataTransfer.setData('src', src);
    event.originalEvent.dataTransfer.setData('parentTab', context.$(event.target).closest('.tab-pane.root').attr('id'));
    if($(event.currentTarget).is('.trigger-collapse'))
    {
        event.originalEvent.dataTransfer.setData('triggerCollapse', true);    
    }
  }.bind(context));

  window.addEventListener("drop", function(e) {
    e = e || event;
    e.preventDefault();
    if (e.dataTransfer.getData('loadable')) {

        switch(e.dataTransfer.getData('parentTab'))
        {
            case 'tab-images':
                var x = e.clientX, y = e.clientY,
                  elementMouseIsOver = context.d.elementFromPoint(x, y);
          
                if (elementMouseIsOver.parentElement.className === 'konvajs-content') {
                  // context.addImage(e.target, context);
                  context.addImage({
                    width: parseInt(e.dataTransfer.getData('width')),
                    height: parseInt(e.dataTransfer.getData('height')),
                    src: e.dataTransfer.getData('src')
                  }, context);
                  context.$('[href="#tab-design"]').tab('show');
                }
                break;
            case 'tab-background':
                context.setBackgroundImage({
                    width: parseInt(e.dataTransfer.getData('width')),
                    height: parseInt(e.dataTransfer.getData('height')),
                    src: e.dataTransfer.getData('src')
                  });
                break;
            default:
      }
      
      
    }
    
    if (e.dataTransfer.getData('triggerCollapse')) {
        jQuery('.qq-collapse').click();
    }
  },false);

  context.$('body').on('click', '.projectListItem', function (event) {
    event.preventDefault();
    var projectId = context.$(this).attr('data-id')
      , projectData = context.$(this).attr('data-project')
      ;
    console.log(JSON.parse(projectData));
  });

  context.$('body').on('click', '.removeItemButton', function (event) {
    var itemId = context.$(this).attr('data-id'), element = context.layer.findOne('#' + itemId) ;

    element.destroy();
    context.layer.draw();
    context.updateData(context.layer);
  });

    // Process add image button
    context.$('body').on('click','.load-image', function(event){
        var img = context.$(this).prev('img');
        //console.log(img);
        var src = img.attr('data-src') ||  img.attr('src');
        img = img[0];
        switch(context.$(this).closest('.tab-pane.root').attr('id'))
        {
            case 'tab-images':
                context.addImage({
                    width: parseInt(img.width),
                    height: parseInt(img.height),
                    src:src,
                  },context);
                break;
            case 'tab-background':
                context.setBackgroundImage({
                    width: parseInt(img.width),
                    height: parseInt(img.height),
                    src:src,
                    x:0,
                    y:0
                  });
                break;
            default:
        }

    });
    
  
  document.body.onclick = function(e) {   //when the document body is clicked
    if (window.event) {
      e = event.srcElement;           //assign the element clicked to e (IE 6-8)
    }
    else {
      e = e.target;                   //assign the element clicked to e
    }

    
    // if (e.className && e.className.indexOf(context.loadableImageClass) !== -1) {
    //   context.addImage(e, context);
    //   context.$('[href="#tab-design"]').tab('show')
    // }
  };

  context.$('body').on('change keyup', '.designListInput', function (event) {
    var param = context.$(this).attr('data-name')
      , value = context.$(this).val()
      , id = context.$(this).attr('data-id')
      , element = context.layer.findOne('#' + id)
      ;

    if(param=='noHand' || param=='drawingEffect')
    {
        value = context.$(this).is(':checked') ? 1 : 0;
    }else if(param == 'strokeColor'){
        context._changeSvgPreviewColor(element,value);
    }else if(param == 'boldness'){
        context._changeSvgThickness(element,value);
    }
    element.setAttr(param, value);
  });

  context.$('#recordVideo').on('click', context.enqueueVideo.bind(context));

  context.$('#downloadVideo').on('click', function downloadVideo(event) {
    window.open('/videos.php','_blank');
    //context.capturer.save();
  }.bind(context));

    context.$('#newProject').on('click', function(){
        this.projectName = null;
        this.cancelAutoSave();
        this.init();
        return false;
    }.bind(context));
    
  context.$('#saveProject').on('click', context.saveProject.bind(context));
  context.$('#saveProjectAs').on('click', function saveProjectAs(event) {
    context.askForProjectName(true);
  });

  context.$('#openProject').on('click', context.listProjects.bind(context));

  context.$('#downloadFile').on('click', function downloadFileClick(event) {
    event.preventDefault();

    context.downloadFile('savedData.json', JSON.stringify(context.slides, null, 2));
  }.bind(context));

  context.$('#newSlide,.action-add-new-slide').on('click', context.addSlide.bind(context));
  context.$('#clearSlide').on('click',context.clearCurrentSlide.bind(context));

    context.$('body').on('click', '.slideDelete', context.deleteSlideClick.bind(context));
    context.$('body').on('click', '.slidePreviewContainer', context.clickSlidePreview.bind(context));
    context.$('body').on('click', '.slideMove', context.moveSlide.bind(context));
  
  context.$('#previewSlide').on('click', context.previewSlideAnimation.bind(context));

  context.$('#animationPreview').on('click', context.previewFullAnimation.bind(context));

  context.$('#animationPreviewStop').on('click', function () {
    context.stopAnimation();
    context.resetAnimation(context);
    context.audioPlayerComponent.stopSelected();
    
    if(context._hand)
    {
        context._hand.destroy();
        context._hand = null;
    }
    if(context._currentTextElement)
    {
        context._currentTextElement.text(context._currentText);
        context._currentTextElement  = null;
    }       

  });

  context.overlay.on('click', function () {
    context.stopAnimation();
    context.resetAnimation(context);
    context.audioPlayerComponent.stopSelected();
    
    if(context._hand)
    {
        context._hand.destroy();
        context._hand = null;
    }
    if(context._currentTextElement)
    {
        context._currentTextElement.text(context._currentText);
        context._currentTextElement = null;
    }
    
  });

  
  context.$('#addText').on('click', function addTextClick (event) {
    event.preventDefault();
    var size = context.$('#fontSize').val()
      , text = context.$('#thisIsText').val()
      , color = context.$('#fontColor').val()
      , family = context.$('#typeFace').val()
      groupId = context.$('#textKonvaId').val()
      ;
      
      if(groupId)
      {
        context.loadFont(family,false,function(){
            var context = this;
            context.updateText({
              fontFamily: family,
              fill: '#' + color,
              text: text,
              width: 200,
              align: 'left',
              height: parseInt(size),
              fontSize: parseInt(size),
              groupId:groupId,
            }, context);
    
            context.$('[href="#tab-design"]').tab('show');
            context.$('#thisIsText').val('');
            context.$('#textKonvaId').val('');
            
            $('#addText').text('Add');
            
        }.bind(context));    
    
      }else{
        context.loadFont(family,false,function(){
            var context = this;
            context.addText({
              fontFamily: family,
              fill: '#' + color,
              text: text,
              width: 200,
              align: 'left',
              height: parseInt(size),
              fontSize: parseInt(size)
            }, context);
    
            context.$('[href="#tab-design"]').tab('show');
            context.$('#thisIsText').val('');        
            
        }.bind(context));
    }
    
    

  }.bind(context));
  
  
    //Fix growl hanging when window is not active
    this.$(document).on('visibilitychange',function(){
        console.log(document.visibilityState);
        if(document.visibilityState == 'visible')
        {
            var growls = this.$('.growl');
            if(growls.length > 1)
            {
                growls.not(':last').remove();
            }            
        }
    }.bind(context));
  
    //Clear background buttons
    this.$('#removeBackground').on('click',function(){
        if(context.stage.attrs.backgroundMode=='perslide')
        {
            var bgColor = context.layer.findOne('.slideBackgroundColor');
            if(bgColor)
            {
                bgColor.setFill('#ffffff');
                bgColor.setX(0);
                bgColor.setY(0);
                bgColor.setHeight(context.stage.attrs.height);
                bgColor.setWidth(context.stage.attrs.width);                
                bgColor.moveToBottom();
                context.layer.draw();
            }
        }else{
            context.background.setFill('#ffffff');
            context.background.setX(0);
            context.background.setY(0);
            context.background.setHeight(context.stage.attrs.height);
            context.background.setWidth(context.stage.attrs.width);
            context.backgroundLayer.draw();
            context.redrawSlidePreview();
        }
        return false; 
    });
    
    this.$('#removeBackgroundImage').on('click',function(){
        if(context.stage.attrs.backgroundMode=='perslide')
        {
            var bgImage = context.layer.findOne('.slideBackgroundImage');
            if(bgImage)
            {
                bgImage.destroy();
                context.layer.draw();
            }            

        }else{
            if(context.backgroundImage)
            {
                context.backgroundImage.destroy();
                context.backgroundImage = null;
                context.stage.batchDraw();
                context.redrawSlidePreview();
            }            
        }

        return false; 
    });
  
  
    // Hands Listeners
    this.$('.hand-thumb').on('click',function(){
        context.selectHand($(this).data('type'),$(this).data('img'),this);  
    });
    
    // Effect Listeners
    this.$('.seffect-thumb').on('click',function(){
        context.$('.seffect-thumb').removeClass('active');
        context.$(this).addClass('active');
        context.setSlideTransition();
    });    
    
    //Load text for edit button
    this.$('body').on('click', '.loadTextForEdit', function () {
        var itemId = context.$(this).closest('.list-group-item').attr('data-id');
        element = context.layer.findOne('#' + itemId);
        context.loadTextForEdit(element);
        context.$('a[href="#tab-text"]').tab('show');
    });
    
    //Background mode process select
    this.$('input[name="backgroundMode"]').on('change',function(event){
        console.log(event);
        if(confirm('All current background settings will be overwritten!'))
        {
            context.setBackgroundMode(context.$(this).val());
        }else{
            context.$(this)[0].checked = null;
            context.$(this).removeAttr("checked");
            context.$('input[name="backgroundMode"]').not(this)[0].checked = true;
            event.preventDefault();
            event.stopPropagation();
            return false;
        }
    });
    
    //Handle attach audio to item buttons
    this.$('body').on('click', '.attachAudioToItem',function(event){
        audioItemUploader.reset();
        context._tmpCurrentAttatchToItemBtn = $(this);
        context.$('#btnAttachToKonvaItem').hide();
        //We need to be sure, that project has been saved and has projectId before attach any audio
        if(!context.proejctId)
        {
            var promise = context.saveProject();
            promise.done(function(){
                 context.$('#modalAddAudioItem').modal('show');
            });
        }else{
            context.$('#modalAddAudioItem').modal('show');
        }
       event.preventDefault();
    });
    
    this.$('#btnAttachToKonvaItem').on('click',function(event){
        var element = context.layer.findOne('#' + context._tmpCurrentAttatchToItemBtn.closest('.list-group-item').data('id'));
        
        context.attachAudioToItem(element,context._tmpCurrentAttatchToItemBtn);
        
        context._tmpCurrentAttatchToItemBtn.closest('div').find('.attachAudioToItem').removeClass('attached');
        context._tmpCurrentAttatchToItemBtn.addClass('attached');
        context.$('#modalAddAudioItem').modal('hide');
        event.preventDefault();
    });
    
};

CanvasRecorder.prototype.getCurrentSlideContents = function getCurrentSlideContents(context) {
  context = context || this;

  var slides = context.stage.find('.slide')
    , currentSlide;

  Object.keys(slides).forEach(function (key) {
    if (slides.hasOwnProperty(key)) {
      var slide = slides[key];
      if (slide.nodeType === 'Layer' && slide.isVisible()) {
        currentSlide = slide.children;
      }
    }
  });

  return currentSlide;
};

CanvasRecorder.prototype.getAnimationDuration = function getAnimationDuration() {
  var slides = this.stage.find('.slide')
    , duration = 0;
    
    if(slides.length && (this.stage.attrs.slideTransitionIn || this.stage.attrs.slideTransitionOut) )
    {
        duration = (slides.length-1) * 0.5;
    }
    
    var context = this;
  Object.keys(slides).forEach(function (key) {
    if (slides.hasOwnProperty(key)) {
      var slide = slides[key];
      if (slide.nodeType === 'Layer') {
        slide.children.forEach(function (element) {
          if (context._checkGroup(element)) {
            duration += parseInt(element.attrs.beforeNext) + context._delayBetweenElements;
          }
        });
      }
    }
  });

  return duration;
};

CanvasRecorder.prototype.getCurrentSlideDuration = function getCurrentSlideDuration() {
  var elements = this.getCurrentSlideContents()
    , duration = 0, context = this;

  elements.forEach(function (element) {
    if (this._checkGroup(element)) {
      duration += parseInt(element.attrs.beforeNext) + context._delayBetweenElements;
    }
  }.bind(this));

  return duration;
};

CanvasRecorder.prototype.startAnimation = function startAnimation() {
  var elements = this.getCurrentSlideContents()
    , delay = 0, context = this;

    if(!elements.length)
    {
        return false;
    }
    
  this.overlay.show();

  this._lastRenderFunction = null;
  
  elements.forEach(function (element) {
    if (this._checkGroup(element)) {
    //    console.log(element);
    //    console.log('#' + element.attrs.id);
    //    console.log(this.layer.findOne('#' + element.attrs.id));
      var realElement = element.children[0];//this.layer.findOne('#' + element.attrs.id).children[0];
     if(!realElement || !element.attrs.id)
        {
           return;
        }
      
      realElement.hide();
      element.attrs.delay = delay;
      element.attrs.duration = (this.getCurrentSlideDuration() + delay );
      delay += parseInt(element.attrs.beforeNext) + context._delayBetweenElements;
      this.hideHandles('#' + element.attrs.id);
    }
  }.bind(this));

  this.layer.draw();
  
};

/**
 * @method
 */
CanvasRecorder.prototype.stopAnimation = function stopAnimation(event) {
  var elements = this.getCurrentSlideContents();

    elements.forEach(function (element) {
        if (this._checkGroup(element)) {
            element.show();
            var realElement =  element.children[0]; ///this.layer.findOne('#' + element.attrs.id).children[0];
      
            realElement.show();
            this.showHandles('#' + element.attrs.id);
        }
    }.bind(this));

  
  
  if(this._currentTextTimeout !== null)
  {
    clearTimeout(this._currentTextTimeout);
  }
  
    //Remove all transitions group, that could left from previous  cycles and was not properly cleaned
    this._clearTransitions();
  
  
  this.overlay.hide();

  this.layer.draw();
  
  //Restore autosave
  this.initAutoSave();
};

CanvasRecorder.prototype.hideHandles = function hideHandles (id) {
  this.layer.findOne(id).children[1].hide();
  this.layer.findOne(id).children[2].hide();
  this.layer.findOne(id).children[3].hide();
  this.layer.findOne(id).children[4].hide();
};

CanvasRecorder.prototype.showHandles = function showHandles (id) {
  this.layer.findOne(id).children[1].show();
  this.layer.findOne(id).children[2].show();
  this.layer.findOne(id).children[3].show();
  this.layer.findOne(id).children[4].show();
};

CanvasRecorder.prototype.animateSlide = function animateSlide (frame, context) {
  var time = frame.time / 1000 - this.animationDuration,
    timeDiff = frame.timeDiff,
    frameRate = frame.frameRate;

  var slideBg = context.layer.findOne('.slideBackgroundGroup');
  if(slideBg)
  {
    slideBg.moveToBottom();
  }    
    
  // https://konvajs.github.io/api/Konva.Layer.html#moveTo__anchor
  context.background.moveTo(context.layer);
  
  if(context.backgroundImage)
  {
    context.backgroundImage.moveTo(context.layer);
    context.backgroundImage.moveToBottom();
  }
  context.background.moveToBottom();
  

  
  var elements = this.getCurrentSlideContents(context);

  elements.forEach(function (element) {
    if (this._checkGroup(element)) {
      var realElement = context.layer.findOne('#' + element.attrs.id).children[0];

      //realElement.hide();

      if (realElement.isVisible()) {
        if (time > element.attrs.delay + element.attrs.duration) {
            realElement.hide();
        }
      } else {
        if (time > element.attrs.delay && time < element.attrs.delay + element.attrs.duration) {
          
            //realElement.show();
            context.showRealElement(realElement)
        }
      }
    }
  }.bind(this));
  
  if(this._lastRenderFunction)
    this._lastRenderFunction();  
};

CanvasRecorder.prototype.playAnimation = function playAnimation (frame, context, slideOnly) {
  var time = frame.time / 1000,
    timeDiff = frame.timeDiff,
    frameRate = frame.frameRate;

  if (time > context.getCurrentSlideDuration() + context.animationDuration && !slideOnly) {
    context.animationDuration += context.getCurrentSlideDuration();

        if (context.getAnimationDuration() > context.animationDuration) {
          context.stopAnimation();
          context.goToNextSlide();
          context.startAnimation();
            animated = true;
        }
    }
    context.animateSlide(frame, context);    


  if( context.capturer ) context.capturer.capture( context.layer.getCanvas() );


  if (time > this.getAnimationDuration() || ( slideOnly && time > context.getCurrentSlideDuration() )) {
    if( context.capturer ) {
      context.showDownloadButton();
      context.capturer.stop();
    }
    
    if(context._hand)
    {
        context._hand.destroy();
        context._hand = null;
    }
    if(context._currentTextElement)
    {
        context._currentTextElement.text(context._currentText);
        context._currentTextElement = null
    }    

    
    context.stopAnimation();
    context.resetAnimation(context);
    if(slideOnly)
    {
        context.redrawSlidePreview(this.currentSlide);    
    }else{
        context.redrawSlidePreview();
    }
    
    this.audioPlayerComponent.stopSelected();
  }
};

CanvasRecorder.prototype.resetAnimation = function resetAnimation(context) {
  context.animation.stop();

  context.background.moveTo(context.backgroundLayer);
  if(context.backgroundImage)
  {
    context.backgroundImage.moveTo(context.backgroundLayer);
    context.backgroundImage.moveToBottom();
  }
  context.background.moveToBottom();
  
  

    //Show all not visible elements that could hangs from prev animations
    //var slides = context.stage.find('.slide');
    //Object.keys(slides).forEach(function (key) {
    //  if (slides.hasOwnProperty(key)) {
    //    var slide = slides[key];
    //    if (slide.nodeType === 'Layer') {
    //        slide.children.forEach(function (element) {
    //            if (context._checkGroup(element)) {
    //              var realElement = element.children[0];
    //        
    //              if (!realElement.isVisible()) {
    //                realElement.show();
    //
    //              }
    //            }
    //        });
    //    }
    //  }
    //});

    //Draw changes
    context.backgroundLayer.draw();
    
    context.animationDuration = 0;
    this._slideTransitionInProcess = false; 
};

CanvasRecorder.prototype.downloadFile = function downloadFile (filename, text) {
  var element = document.createElement('a');
  element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
  element.setAttribute('download', filename);

  element.style.display = 'none';
  document.body.appendChild(element);

  element.click();

  document.body.removeChild(element);
};

CanvasRecorder.prototype.enqueueVideo = function enqueueVideo () {
  // promise.fail(this.notify("Failed saving the project"));

  var options16by9 = this.$("#videoExportOptionsDialog").find('.16by9')
    , options9by16 = this.$("#videoExportOptionsDialog").find('.9by16')
    ;

  options9by16.hide().removeAttr('selected');
  options16by9.hide().removeAttr('selected');

    this.$("#videoExportOptionsDialog").find('.' + this.screenRatio)
    .show()
    .filter(".default").attr('selected','selected');

  var modal = this.bootbox.dialog({
    message: this.$("#videoExportOptionsDialog").html(),
    title: "Video Export Settings" ,
    closeButton: false,
    buttons: [
      {
        label: "Render Video",
        className: "btn btn-primary pull-left",
        callback: function() {
          var fileFormat = modal.find('#fileFormat').val()
            , videoResolution = modal.find('#videoResolution').val()
          ;

          console.log('fileFormat', fileFormat);
          console.log('videoResolution', videoResolution);

          if (videoResolution !== '') {
            this.videoResolution = videoResolution;
            this.fileFormat = fileFormat;

            modal.modal("hide");

            var promise = this.saveProject();

            promise.done(this.enqueueVideoPromiseDone.bind(this));
          } else {
            modal.find('#videoResolution').addClass('is-invalid');
          }

          return false;
        }.bind(this)
      },
      {
        label: "Cancel",
        className: "btn btn-secondary pull-left",
        callback: function() {
          modal.modal("hide");
        }.bind(this)
      }
    ],
    show: false
  });

  modal.modal("show");
};

CanvasRecorder.prototype.enqueueVideoPromiseDone = function enqueueVideoPromiseDone() {
  this.$.post('api/enqueueVideo.php', {
    project: this.projectId,
    fileFormat: this.fileFormat,
    videoResolution: this.videoResolution
  }, this.enqueueVideoPostCallback.bind(this), 'json');
};

CanvasRecorder.prototype.enqueueVideoPostCallback = function enqueueVideoPostCallback(data) {
    if('error' in data)
    {
        this.notify({
            location: 'tc', // ('tl' | 'tr' | 'bl' | 'br' | 'tc' | 'bc' - default: 'tr')
            style: 'error', // ('default' | 'error' | 'notice' | 'warning' - default: 'default')
            message: data.error
        });
    }else{
        this.notify("Project #"+data.result+" queued for video rendering!");
        console.log(data);
        window.location.href = '/videos.php';        
    }
};

CanvasRecorder.prototype.showVideoProgress = function showVideoProgress () {
  var b = this.$('#videoGenerationProgress').find('button');
  b.prop('disabled', 'disabled');
  b.html('<i class="fa fa-spinner fa-spin fa-fw"></i> Generating your video');
  this.$('#videoGenerationProgress').show();
};

CanvasRecorder.prototype.showDownloadButton = function showDownloadButton () {
  var context = this
    , button = this.$('#videoGenerationProgress').find('button');
  button.prop('disabled', false);
  button.text('Your Video is Ready: Click here to download it');
  button.on('click', function (event) {
    event.preventDefault();
    context.capturer.save();
  });
};

CanvasRecorder.prototype.detectRatioFromSlides = function detectRatioFromSlides()
{
    var context = this;
    if(context.slides.attrs.height )
    {
        return context.slides.attrs.width/context.slides.attrs.height > 1 ? '16by9' : '9by16';
    }
    return null;
};

CanvasRecorder.prototype.updateSlideControls = function updateSlideControls(itemId)
{
    console.log('Update Slide control', itemId);
    //Update delete button
    this.$('#deleteSlide').attr('data-id',itemId);
};


CanvasRecorder.prototype.clearCurrentSlide = function clearCurrentSlide()
{
    console.log('children',this.layer.children);
    this.layer.destroyChildren();
    this.layer.clear();    
    this.updateData(this.layer);
};

/**
 * @method
 * @memberOf CanvasRecorder.prototype 
 */
CanvasRecorder.prototype.moveSlide = function moveSlide(event)
{
    var context = this;
    var $el = context.$(event.target).closest('.slideMove');
    var s_id = parseInt($el.closest('.card').attr('data-id'));
    var new_s_id = null;
    
    if($el.is('.slideMoveForward'))
    {
        new_s_id = s_id !== (context.slides.children.length-1) ? s_id+1 : null;
    }else{
        new_s_id = s_id!==1 ? s_id - 1 : null;
    }
    
    if(new_s_id)
    {
        var cur_slide = context.currentSlide !== s_id && context.currentSlide !==new_s_id ? context.currentSlide :
            (context.currentSlide == s_id ? (context.currentSlide=new_s_id, new_s_id) :( context.currentSlide=s_id , s_id));
        
        context._swapNodes(s_id, new_s_id, context.stage.children);
        context.updateData();
        context.redrawSlidePreview(s_id);
        context.redrawSlidePreview(new_s_id);
        context.updateSlideControls(cur_slide);
    }
    event.preventDefault();
    event.stopPropagation();
};

CanvasRecorder.prototype._swapNodes = function swapNodes(i_from, i_to, parent)
{
    if(parent[i_from].getName() !== parent[i_to].getName() )
    {
        console.warn('Node must have the same names and belong to the same parent');
        return false;
    }
    
    var name = parent[i_from].getName();
    var i_f = null, i_t = null, temp = null;
    
    for (var index = 0; index < this.Konva.names[name].length; ++index) {
        if(this.Konva.names[name][index]._id === parent[i_from]._id)
        {
            i_f = index;
        }
        if(this.Konva.names[name][index]._id === parent[i_to]._id)
        {
            i_t = index;
        }        
    }
    if(i_f!== null && i_t!==null)
    {
        temp = this.Konva.names[name][i_t];
        var i_f_index = this.Konva.names[name][i_f].index;
        this.Konva.names[name][i_t] = this.Konva.names[name][i_f];
        this.Konva.names[name][i_t].index = temp.index;
        this.Konva.names[name][i_f] = temp;
        this.Konva.names[name][i_f].index = i_f_index;
    }
    
    temp = parent[i_from];
    parent[i_from] = parent[i_to];
    parent[i_to] = temp;
    return this;
};

CanvasRecorder.prototype.setSlideTransition = function setSlideTransition()
{
    var transition = this.$('.seffect-thumb.active')
    if(transition.length){
        this.stage.attrs.slideTransitionIn = transition.data('slide-in');
        this.stage.attrs.slideTransitionOut = transition.data('slide-out');        
    }


};

/**
 * @method
 * @methodOf CanvasRecorder.prototype
 * @param type - type of hand - write or drop
 * @param hand - url of image
 * @param sender - optional link to event target DOM object
 */
CanvasRecorder.prototype.selectHand = function(type,hand,sender)
{
    sender = sender || false;
    var attr = 'hand_'+type;
    if(sender)
    {
        sender = this.$(sender);
        if(sender.is('.active'))
        {
            sender.removeClass('active');
            this.stage.attrs[attr] = null;        
        }else{
            this.$('.hand-thumb[data-type="'+type+'"]').removeClass('active');
            sender.addClass('active');
            this.stage.attrs[attr] = hand;        
        }
    }else{
        this.$('.hand-thumb[data-type="'+type+'"]').removeClass('active');
        this.$('[data-img="'+hand+'"]').addClass('active');
        this.stage.attrs[attr] = hand;        
    }


    
};

/**
 * @method
 * @methodOf CanvasRecorder.prototype
 * @param node - Konva group node
 */
CanvasRecorder.prototype.loadTextForEdit = function(node)
{
    var text = undefined;
    if(node.nodeType === 'Group')
    {
        text = node.children[0];
    }
    this.$('#thisIsText').val(text.attrs.text);
    this.$('#typeFace').val(text.attrs.fontFamily).trigger('change');
    //check if we have such fontSize in select
    var $fSize = this.$('#fontSize');
    var fSize = parseInt(text.attrs.fontSize);
    if($fSize.find('[value="'+fSize+'"]').length == 0)
    {
        var opt = null;
        var done = false;
        $fSize.find('option').each(function(){
            if(done){
                return false;
            }
            var $this = $(this);
            if(parseInt($(this).text()) < fSize)
            {
                opt = $this;
            }else{
                done = true;
                if(opt){
                    opt.after('<option value="'+fSize+'">'+fSize+'</option>');
                }else{
                    $fSize.prepend('<option value="'+fSize+'">'+fSize+'</option>');
                }
            }
        })
    }
    this.$('#fontSize').val(text.attrs.fontSize);
    this.$('#fontColor')[0].jscolor.fromString(text.attrs.fill.replace('#',''));
    this.$('#textKonvaId').val(node.getId());
    this.$('#addText').text('Update');
}



/**
 * @method
 * @methodOf CanvasRecorder.prototype
 * @param  String mode - background mode - single or perslide
 * @param Bool update  - update or no form element 
 */
CanvasRecorder.prototype.setBackgroundMode = function(mode,update)
{
    update = update || false;
    this.stage.attrs.backgroundMode = mode;
    if(mode === 'single')
    {
        this.stage.find('.slideBackgroundGroup').forEach(function (groupNode) {
            groupNode.destroyChildren();
            groupNode.destroy();
        }.bind(this));
    }
    else if(update == false)
    {
        this.background.setFill('#ffffff');
        this.background.setX(0);
        this.background.setY(0);
        this.background.setHeight(this.stage.attrs.height);
        this.background.setWidth(this.stage.attrs.width);
        this.backgroundLayer.draw();        
        if(this.backgroundImage)
        {
            this.backgroundImage.destroy();
            this.backgroundImage = null;
            this.backgroundLayer.draw();
        }
    }
    
    if(update)
    {
        this.$('input[name="backgroundMode"]').val([mode]);
    }
    this.redrawSlidePreview();
}

/**
 * @method
 * @methodOf CanvasRecorder.prototype
 * @param  Konva.Node slide - slide where to change background settings
 * @param String color  - hex color code
 */
CanvasRecorder.prototype.processSlideBackgroundColor = function(slide,color){
    var group = this._getSlideBackgroundGroup(slide);
    
    group.findOne('.slideBackgroundColor').setFill(color).show();
    slide.draw();
}

/**
 * @method
 * @methodOf CanvasRecorder.prototype
 * @param  Konva.Node slide - slide where to change background settings
 * @param String color  - hex color code
 */
CanvasRecorder.prototype.processSlideBackgroundImage = function(slide, imageData)
{
    var group = this._getSlideBackgroundGroup(slide);

    var context = this;
    var sBgImage = slide.findOne('.slideBackgroundImage');
   

    if(sBgImage)
    {
        sBgImage.destroy();    
    }
        
        
    imageData = this._stretchImage(imageData);
    var backgroundImage = new Konva.Image({
        x: imageData.x,
        y: imageData.y,
        width: imageData.width,
        height: imageData.height,
        src: imageData.src,
        name: 'slideBackgroundImage'
    });
      
    group.add(backgroundImage);

        
    var object = new Image();
    object.onload = function () {
        backgroundImage.image(object);
        slide.draw()
        context.redrawSlidePreview(this.currentSlide);
    }.bind(context);
    
    object.src = imageData.src;
       
}

/**
 * @method
 * @methodOf CanvasRecorder.prototype
 * @param  Konva.Node slide - slide where to change background settings
  */
CanvasRecorder.prototype._getSlideBackgroundGroup = function(slide)
{
    var group = slide.findOne('.slideBackgroundGroup');
    if(!group)
    {
        group = new this.Konva.Group({
            x:0,
            y:0,
            name: 'slideBackgroundGroup',
        });
        slide.add(group);
        group.moveToBottom();
        var colorBg = new Konva.Rect({
            x: 0,
            y: 0,
            width: this.d.getElementById(this.container).offsetWidth,
            height: this.d.getElementById(this.container).offsetHeight,
            fill: '#FFFFFF',
            name: 'slideBackgroundColor'
          });

        group.add(colorBg);
    }
    return group;
}

/**
 * Perform set of actions to adjust environment to project screen ratio
 */
CanvasRecorder.prototype.adjustScreenRatio = function(screenRatio){
    if(screenRatio)
    {
        this.screenRatio = screenRatio;    
    }
    
    this.$('#'+this.container+',#slidesPreviewsContainer, body').removeClass('r9by16 r16by9').addClass('r'+this.screenRatio);
    switch(screenRatio)
    {
        case '16by9':
            this.$('#fontSize').val('64');
            break;
        case '9by16':
            this.$('#fontSize').val('32');
            break;
        default:
            break;
    }
}


/**
 * Attach audio to item and perform set of actions that required during preview
 */
CanvasRecorder.prototype.attachAudioToItem = function(duration)
{
    
    var context = this,
    attach_id = context._tmpCurrentAttatchToItemBtn.data('attach-id'),
    element = context.layer.findOne('#' + context._tmpCurrentAttatchToItemBtn.closest('.list-group-item').data('id'));

    context.overlay_full.show();
 
    element.setAttr('attached_mp3',attach_id);
    var audio = new Audio();
    context.audioQueue[element.id()] = audio; 
    $(audio).on("loadedmetadata", function(){
        duration = duration || audio.duration;
        context._tmpCurrentAttatchToItemBtn.closest('.list-group-item').find('[data-name="beforeNext"]').val(Math.ceil(duration)).change();
        context.overlay_full.hide();
    });
    
    audio.src = URL.createObjectURL(audioItemUploader.getFile(0));
    
    context._tmpCurrentAttatchToItemBtn.closest('div').find('.attachAudioToItem').removeClass('attached');
    context._tmpCurrentAttatchToItemBtn.addClass('attached');
    context.$('#modalAddAudioItem').modal('hide');
    

}


/***
 * Image Browser Class
 */
function ImageBrowser(container,jQuery,Templates)
{
    this.parent = null;
    this.container  = jQuery(container);
    this.$ = jQuery;
    this.t = Templates;
    this.imagesDirectories = null;
    
}
 

/**
 * Perform set of actions to init image browser
 * @method
 * @memberOf ImageBrowser.prototype
 */
ImageBrowser.prototype.init  = function(callback)
{
    var context = this;
    
    context.$('body').on('click', '.allowDelete .delete', function(){
        return context.deleteImageFromList(this);
    });

    this.loadImagesList();
    this.parent = this.container.closest('.tab-pane');
    if(context.$.isFunction(callback))
    {
        callback.bind(this);
    }
 };

/**
 * Load available images from server and render UI
 * @method 
 * @memberOf ImageBrowser.prototype
 */
ImageBrowser.prototype.loadImagesList = function loadImagesList() {
  var context = this;

  return this.$.get('api/loadImagesList.php', function loadImagesListResult (data){
    console.log(data);
    context.t.insert('#imagesDirectoryList', 'imagesDirectoryLink', typeof data.result.directories=== 'object' ? Object.values(data.result.directories) : data.result.directories);
    var i,item,id;
    
    for(i in data.result.directories)
    {
        id = data.result.directories[i].id;
        if(data.result.files[id] ==undefined  || data.result.files[id].length == 0)
        {
            item = { items: '', id: data.result.directories[i].id };
        }else{
            var html = '';
            for( let j=0; j< data.result.files[id].length; j++ )
            {
                var img = data.result.files[id][j];
                if(img.ext == 'svg')
                {
                    html += context.t.html('imagesDirectoryImageSvg',[img]);
                }else{
                    html += context.t.html('imagesDirectoryImage',[img]);
                }
    
            }
            item = { items: html, id: data.result.directories[i].id };
        }
        context.t.append('#imagesDirectoryTabs','imagesDirectoryTab',item);    
        
        //context.$('.loadableImage.img-svg').each(function(){
        //    var $e = context.$(this);
        //    context.$.get($e.attr($e.prop("nodeName") === "OBJECT" ? "data" : "src"), function(data) {
        //      $e.wrap('<div class="img-svg loadableImage loaded"></div>').replaceWith(data.documentElement);
        //    });
        //});
        
        
    }
    
    context.imagesDirectories = context.$('#imagesDirectoryList');
    
    //context.imagesDirectories.find('.nav-link').first().tab('show');
    context.imagesDirectories.find('a[href="#user_images"]').closest('li').next().find('a').tab('show');
    context.$('#imagesDirectoryWrap').addClass('loaded').delay(1000).queue(function(next){
        context.$(this).removeClass('loading');
        next();
    });
    context.$('[data-toggle="tooltip"]',context.imagesDirectories).tooltip();
  }, 'json');
};

/**
 * Delete selected image from user folder
 * @method 
 * @memberOf ImageBrowser.prototype
 * @param {String} selector
 */
ImageBrowser.prototype.deleteImageFromList = function deleteImageFromList(sender)
{
    var context = this;
    if(confirm('Are you sure you want to delete this record?'+"\n"+'You will need to delete it from all slides where it used!'))
    {
        var $sender = context.$(sender);
        var url = $sender.closest('div').find('img').attr('src').substr(25);
        context.$.ajax({
            url: '/api/upload.php/uf/'+btoa(url),
            type: 'DELETE',
            success: function(data) {
                if(!data.success)
                {
                    alert(data.error);
                }else{
                    $sender.closest('.fix-square').remove();
                }
            },
            error:function()
            {
                alert('Error during image delete!'); 
            },
            dataType :'json',
        });
    }
    return false;
};

ImageBrowser.prototype.appendTo = function attachTo(container)
{
    var parent = this.$(container);
    if(!this.parent.is(parent))
    {
        console.log('reattach image browser');
        this.container.detach().appendTo(container);
        this.parent = parent;
    }
};

ImageBrowser.prototype.activate = function activate(selector)
{
    this.$(selector).click();
};


/***
 * Audio Player
 */
function AudioPlayer(container,jQuery,Templates)
{
    this.parent = null;
    this.container  = jQuery(container);
    this.$ = jQuery;
    this.t = Templates;
    this.player = null;
    this.playlist = null;
    this.uploadBtn = null;
    this.selected = null;
    this.currentTime = 0;
    
    this._init();
}

AudioPlayer.count = 0;

 /***
 * @method
 * @memberOf AudioPlayer.prototype
 */
AudioPlayer.prototype._init  = function _init(){
    //Create player
    this.player  = this.$('<audio id="audioPlayer'+AudioPlayer.count+'" class="audio-player d-none" src="" type="audio/mp3" controls="controls"></audio>').appendTo(this.container);
    
    //Create tracklist container
    this.playlist =  this.$('<ul id="audioPlaylist'+AudioPlayer.count+'"  class="audio-playlist list-group loading"></ul>').appendTo(this.container);
    
    this.uploadBtn = this.$('#uploadAudioFile');
    
    this.loadListFromServer();
    
    this.addEventListeners();
};

/**
 * @method
 * @memberOf AudioPlayer.prototype
 */
AudioPlayer.prototype.loadListFromServer = function loadListFromServer()
{
    var context = this;
    var listName = '#audioPlaylist'+AudioPlayer.count;
    context.$(listName).html('');
    return this.$.get('api/loadAudioList.php', function loadAudioListResult (data){
        console.log('Track List Loaded', data);
        for(var i=0;i < data.result.length; i++)
        {
            context.t.append(listName, (data.result[i].path.indexOf('storage')>=0 ? 'audioListItemUser' : 'audioListItem'), data.result[i]);            
        }

    
        context.playlist = context.$(listName);
   
        context.$('[data-toggle="tooltip"]',context.playlist).tooltip();
        
        context.playlist.addClass('loaded').delay(1000).queue(function(next){
            context.playlist.removeClass('loading');
            next();
        });
        new SimpleBar(context.container[0],{
            autoHide: false,
        });        
    }, 'json');    
};

/**
 * @method
 * @memberOf AudioPlayer.prototype
 */
AudioPlayer.prototype.addEventListeners = function()
{
    var context = this;
    this.$(this.container).on('click','.audio-play', context.play.bind(this));
    this.$(this.container).on('click','.audio-pause', context.pause.bind(this));
    this.$(this.container).on('click','.audio-stop', context.stop.bind(this));
    this.$(this.container).on('click','.audio-add', context.add.bind(this));
    this.$(this.container).on('click','.audio-remove', context.remove.bind(this));
    this.$(this.uploadBtn).on('click',context.openUploadDlg);
    
    this.$(this.container).on('click', '.audio-delete', function(){
        return context.deleteAudioFromList(this);
    });    
    
    this.player.bind('ended', context.ended.bind(this));
};

/**
 * Add "added" label to track item
 * @method
 * @memberOf AudioPlayer.prototype
 * @param String selected  - path of track
 */
AudioPlayer.prototype.setSelected = function setSelected(selected)
{
    var item =  this.playlist.find('li[data-audio-url="'+selected+'"]');

    if(item.length)
    {
        this.addSelected(item);
    }else{
        console.warn('Can not find selected audio track!');
    }
};


AudioPlayer.prototype.addSelected = function addSelected(item)
{
    this.selected = item.data('audio-url');
    this.playlist
        .find('.audio-remove').hide().end()
        .find('.audio-add').show().end()
        .find('.audio-label-added').remove();
   
    this.$('.audio-add',item).hide().closest('.btn-group').before('<span class="audio-label-added badge badge-pill badge-success mr-3">ADDED</span>');
    this.$('.audio-remove',item).show();

    this.$('#audioAdditionalInfo').html('<b>Music Track Added:</b> '+item.find('h5').text());
    
};


/**
 * play selected track. Called from previewAnimation
 * @method
 * @memberOf AudioPlayer.prototype
 */
AudioPlayer.prototype.playSelected = function playSelected ()
{
    if(this.selected)
    {
        this.player[0].pause();
        this.player[0].currentTime = 0;
        this.player.attr('src',this.selected);
        
        this.player[0].play();
    }
};

/**
 * stop selected track. Called from previewAnimation
 * @method
 * @memberOf AudioPlayer.prototype
 */
AudioPlayer.prototype.stopSelected = function stopSelected()
{
    if(this.selected)
    {
        this.player[0].pause();
        this.player[0].currentTime = 0;
    }
}


/**
 * @method
 * @memberOf AudioPlayer.prototype
 */
AudioPlayer.prototype.play = function play(event)
{
    event.preventDefault();
    var item = this.$(event.target).closest('.list-group-item');

    this.player[0].pause();
    
    var src = item.data('audio-url');
    
    this.player[0].currentTime =  src == this.player.attr('src') ? this.currentTime : (this.player.attr('src', src) , 0);    
    
    this.addDurationLabel(item);
    
    this.player[0].play();
   
    this.playlist
        .find('.active').removeClass('active').end()
        .find('.audio-pause:visible').hide().end()
        .find('.audio-play:hidden').show().end()
        .find('.audio-stop').attr('disabled','disabled');
    
    item.addClass('active');
    
    this.$('.audio-play',item).hide();
    this.$('.audio-pause',item).show();
    this.$('.audio-stop',item).removeAttr('disabled');
};

/**
 * @method
 * @memberOf AudioPlayer.prototype
 */
AudioPlayer.prototype.pause = function pause(event)
{
    event.preventDefault();
    var item = this.$(event.target).closest('.list-group-item');
    
    this.player[0].pause();
    this.currentTime = this.player[0].currentTime;
 
    this.$('.audio-play',item).show();
    this.$('.audio-pause',item).hide();
};

/**
 * @method
 * @memberOf AudioPlayer.prototype
 */
AudioPlayer.prototype.stop = function stop(event)
{
    event.preventDefault();
    var item = this.$(event.target).closest('.list-group-item');
 
    this.player[0].pause();
    
    this.currentTime = 0;
    
    this.playlist.find('.active').
    removeClass('active');
   
    this.$('.audio-play',item).show();
    this.$('.audio-pause',item).hide();
    this.$('.audio-stop',item).attr('disabled','disabled');
}; 

/**
 * @method
 * @memberOf AudioPlayer.prototype
 */
AudioPlayer.prototype.add = function add(event)
{
    event.preventDefault();
    var item = this.$(event.target).closest('.list-group-item');
    this.addSelected(item);
};




/**
 * @method
 * @memberOf AudioPlayer.prototype
 */
AudioPlayer.prototype.remove = function remove(event)
{
    event.preventDefault();
    
    this.selected = null;
    
    var item = this.$(event.target).closest('.list-group-item');
    
    this.$('.audio-remove',item).hide();
    this.playlist.find('.audio-add').show();
    this.$('.audio-label-added',item).remove();
    this.$('#audioAdditionalInfo').html('');
};

/**
 * @method
 * @memberOf AudioPlayer.prototype
 */
AudioPlayer.prototype.ended = function ended()
{
    
    this.currentTime = 0;
    
    var item = this.playlist
        .find('.active');
    
    this.$('.audio-play',item).show();
    this.$('.audio-pause',item).hide();
    this.$('.audio-stop',item).attr('disabled','disabled');

};


/**
 * @method
 * @memberOf AudioPlayer.prototype
 */
AudioPlayer.prototype.addDurationLabel = function add(item)
{
    var context = this;
    if(this.$('.audio-label-duration',item).length)
    {
        return;
    }
    
    setTimeout(function(){
        if(context.player[0].readyState > 0)
        {
            context.$('h5',this).append('<span class="audio-label-duration">'+context._formatDuration(context.player[0].duration)+'</span>');    
        }
    }.bind(item), 1000);
    
};
/**
 * @method
 * @memberOf AudioPlayer.prototype
 */
AudioPlayer.prototype.openUploadDlg = function(event)
{
    audioLibraryUploader.reset();
    $('#modalAddAudioToLibrary').modal('show');    
};

/**
 * Delete selected audio from user folder
 * @method 
 * @memberOf AudioPlayer.prototype
 * @param {String} selector
 */
AudioPlayer.prototype.deleteAudioFromList = function deleteAudioFromList(sender)
{
    var context = this;
    if(confirm('Are you sure you want to delete this record?'+"\n"+'You will need to delete it from all slides where it used!'))
    {
        var $sender = context.$(sender);
        var url = $sender.closest('li').data('audio-url').substr(25);
        context.$.ajax({
            url: '/api/upload.php/uf/'+  btoa(unescape(encodeURIComponent(url))),
            type: 'DELETE',
            success: function(data) {
                if(!data.success)
                {
                    alert(data.error);
                }else{
                    $sender.closest('li').remove();
                }
            },
            error:function()
            {
                alert('Error during audio delete!'); 
            },
            dataType :'json',
        });
    }
    return false;
};


/**
 * @method
 * @memberOf AudioPlayer.prototype
 * @param integer time in seconds
 */
AudioPlayer.prototype._formatDuration = function fancyTimeFormat(time)
{   
    // Hours, minutes and seconds
    var secs = ~~(time % 60);
    var mins = ~~((time / 60) );
    var hrs = ~~(time / 3600);
    


    // Output like "1:01" or "4:03:59" or "123:03:59"
    var ret = "";

    if (hrs > 0) {
        ret += "" + hrs + ":" + (mins < 10 ? "0" : "");
    }

    ret += "" + mins + ":" + (secs < 10 ? "0" : "");
    ret += "" + secs;
    return ret;
};