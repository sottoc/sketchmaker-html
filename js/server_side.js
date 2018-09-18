function CanvasRecorder(container, d, Konva, Templates, CCapture, jQuery) {
  this.container = container;
  this.CCapture = CCapture;
  this.capturer = null;
  this.d = d;
  this.animation = null;
  this.prevId = 0;
  this.t = Templates;
  this.Konva = Konva;
  this.$ = jQuery;
  this.projects = null;
  this.projectName = null;
  this.projectId = null;
  this.requestId = null;
  this.backgroundLayer = null;
  this.background = null;
  this.backgroundImage = null;
  this.slides = [
    {
      id: 0
    }
  ];
  this.currentSlide = 1;
  this.currentSlideDuration = 0;
  this.currentSlideDurationDisplayed = 0;
  this.stage = {};
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
  this.overlay_full = null;
  
  this._animationInProgress = [];
  this._hand = null;
  
  this._currentTextElement = null;
  this._currentText = null;
  this._currentTextTimeout = null;
  
    this.defferedQueue = [];
  
    this.preloadedPaths = [];
    
    this.preloadedImages = [];
 
    this._lastRenderFunction =  null;
    
    //If we between slide transitions
    this._slideTransitionInProcess  = false;
    
    this._delayBetweenElements = 0.5;
    

    this._frameCount = 0;
    this._fullAnimationDuration = 0;
    this._updateProgress = 0;
    this.audioQueue = [];
}

CanvasRecorder.prototype.getUniqueId = function getUniqueId(prefix) {
  prefix = (prefix !== undefined) ? prefix.toString() : 'element';
  this.prevId++;
  return prefix+this.prevId;
};

CanvasRecorder.prototype.init = function init() {

  this.createStage();

  this.addEventListeners(this);
};

CanvasRecorder.prototype.createStage = function createStage(json) {
  json = json || false;

  if (json) {
    this.stage = this.Konva.Node.create(json, this.container);
    
    this.layer = this.stage.findOne('.slide');
    this.backgroundLayer = this.stage.findOne('.backgroundLayer');
    this.background = this.stage.findOne('.background');
    this.backgroundImage = this.stage.findOne('.backgroundImage');
    
    this.fixProblems();
    this.resetAnchors();

    
    this.displaySlide(this.layer.index);

    this._resize(this.stage);
    this.adjustSizeForServer();

    // this.t.insert('#slidesPreviewsContainer', 'slidePreview', this.stage.find('.slide'));
    this.prevId = this.Konva.ids.length;
    this.updateData();
    
    //Preload images
    var images = this.stage.find('Image');
    for(var i = 0; i <images.length; i++)
    {
        this.defferedQueue.push(this.preloadImage(images[i].attrs.src));
    }    

    //Preload svgs
    var svgs = this.stage.find('.svg_img');
    
    for(var i = 0; i <svgs.length; i++)
    {
        this.defferedQueue.push(this.preloadSvg(svgs[i].attrs.src,svgs[i]));
    }
    
    
    
  } else {
    this.resetStage();

    this.addStageElements();
  }
};

CanvasRecorder.prototype.adjustSizeForServer = function adjustSizeForServer() {
  this.stage.find('Layer').forEach(this._resize.bind(this));
  this.stage.find('Group').forEach(this._resize.bind(this));
  this.stage.find('Image').forEach(this._resize.bind(this));
  this.stage.find('Text').forEach(this._resize.bind(this));
  this.stage.find('Rect').forEach(this._resize.bind(this));
};

CanvasRecorder.prototype._resize = function _resize(node) {
  var width = node.getAttr('width')
    , height = node.getAttr('height')
    , x = node.x()
    , y = node.y()
    , factor = 2
  ;

  node.width(width * factor);
  node.height(height * factor);
  node.x(x * factor);
  node.y(y * factor);


  if (node.className === 'Text') {
    node.fontSize(node.getTextHeight() * factor);
  }


  if (node.nodeType !== 'Stage')
  {
    node.getLayer().batchDraw();
  }



};

CanvasRecorder.prototype.resetStage = function resetStage() {
  this.$('#slidesPreviewsContainer').html('');

  this.stage = new this.Konva.Stage({
    container: this.container,
    width: this.d.getElementById(this.container).offsetWidth,
    height: this.d.getElementById(this.container).offsetHeight
  });
};

CanvasRecorder.prototype.addStageElements = function addStageElements() {
  this.addBackgroundLayer();

  this.addSlide();
};

CanvasRecorder.prototype.addBackgroundLayer = function addBackground() {
  this.backgroundLayer = new this.Konva.Layer({
    name: 'backgroundLayer'
  });
  this.stage.add(this.backgroundLayer);

  this.background = new Konva.Rect({
    x: 0,
    y: 0,
    width: this.d.getElementById(this.container).offsetWidth,
    height: this.d.getElementById(this.container).offsetHeight,
    fill: '#e5f1e8',
    name: 'background'
  });

  this.backgroundLayer.add(this.background);

  this.backgroundLayer.draw();
};

CanvasRecorder.prototype.fixProblems = function fixProblems() {
  this.stage.find('Image').forEach(function (imageNode) {
    if(imageNode.name() == 'svg_img'){
        return false;
    }
    var src = imageNode.getAttr('src')
      , image = new Image()
      , group = imageNode.getParent();

    this.fixPosition(imageNode, group);

    image.onload = function () {
      imageNode.image(image);
      imageNode.getLayer().batchDraw();

      // this.redrawSlidePreview(imageNode.getLayer().index);
    }.bind(this);

    image.crossOrigin = "Anonymous";
    image.src = src;
  }.bind(this));

  this.stage.find('Text').forEach(function (textNode) {
    var group = textNode.getParent();

    this.fixPosition(textNode, group);
  }.bind(this));
  
    //Fix backgrounds position
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

    //context.addAnchors(group, firstChild.getWidth(), firstChild.getHeight(), context);
  });
};



CanvasRecorder.prototype.addImage = function addImage(item, context) {
  if (item.width >= context.d.getElementById(context.container).offsetWidth) {
    item.width = context.d.getElementById(context.container).offsetWidth - 20;
    item.height = context.d.getElementById(context.container).offsetHeight / context.d.getElementById(context.container).offsetWidth * item.width;
  } else if (item.height >= context.d.getElementById(context.container).offsetHeight) {
    item.height = context.d.getElementById(context.container).offsetHeight - 20;
    item.width = context.d.getElementById(context.container).offsetWidth / context.d.getElementById(context.container).offsetHeight * item.height;
  }

  var image = new context.Konva.Image({
    width: item.width,
    height: item.height,
    src: item.src
  });

  var group = new context.Konva.Group({
    x: context.d.getElementById(context.container).offsetWidth / 2 - item.width / 2,
    y: context.d.getElementById(context.container).offsetHeight / 2 - item.height / 2,
    beforeNext: 2,
    // delay: this.currentSlideDuration,
    // duration: 2,
    id: this.getUniqueId('image_group_element'),
    draggable: true
  });
  // this.currentSlideDuration += 2;
  context.layer.add(group);
  group.add(image);
  //context.addAnchors(group, item.width, item.height, context);

  var object = new Image();
  object.onload = function () {
    image.image(object);
    context.layer.draw();

    context.updateData(context.layer);
  }.bind(context);

  object.src = item.src;
};

CanvasRecorder.prototype.addText = function addText (item, context) {
  var ctx = context.layer.getCanvas().getContext("2d");
  ctx.font = item.fontSize + 'px ' + item.fontFamily;
  item.width = ctx.measureText(item.text).width;

  var text = new context.Konva.Text(item);

  var group = new context.Konva.Group({
    x: context.d.getElementById(context.container).offsetWidth / 2 - item.width / 2,
    y: context.d.getElementById(context.container).offsetHeight / 2 - item.height / 2,
    beforeNext: 2,
    // delay: this.currentSlideDuration,
    // duration: 2,
    id: this.getUniqueId('txt_group_element'),
    draggable: true
  });
  // this.currentSlideDuration += 2;
  context.layer.add(group);
  group.add(text);
  //context.addAnchors(group, item.width, item.height, context);

  context.layer.draw();
  context.updateData(context.layer);


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


CanvasRecorder.prototype.updateData = function updateData() {
  this.slides = this.slides || {};

  this.slides = JSON.parse(this.stage.toJSON());

  //this.displayListItems();
  // this.redrawSlidePreview(this.layer.index);
};


CanvasRecorder.prototype.setProjectName = function setProjectName(string) {
  this.projectName = string;
};

CanvasRecorder.prototype.setProjectId = function setProjectId(string) {
  this.projectId = string;
};

CanvasRecorder.prototype.setRequestId = function setRequestId(string) {
  this.requestId = string;
};

CanvasRecorder.prototype.recordVideo = function recordVideo(event) {

  // context.capturer = new context.CCapture( {
  //   format: 'ffmpegserver',
  //   //workersPath: "3rdparty/",
  //   //format: 'gif',
  //   verbose: false,
  //   framerate: 30,
  //   onProgress: function( p ) { console.log( ( p * 100 ) + '%' ) },
  //   extension: ".mp4",
  //   codec: "libx264",
  // } );

  this.capturer = new this.CCapture( {
    verbose: false,
    display: false,
    framerate: 30,
    quality: 100,
    //motionBlurFrames: 100/30,
    format: 'webm',
    // name: 'Generated Video',
    name: this.projectId+'_'+this.requestId,
    workersPath: 'js/',
    // timeLimit: context.currentSlideDuration,
    // timeLimit: context.getCurrentSlideDuration(),
    frameLimit: 0,
    autoSaveTime: 0,
    // onProgress: function( p ) { console.log( ( p * 100 ) + '%' ) }
  } );
  // console.log(context.capturer);

 
  this.startAnimation();
 
 this._fullAnimationDuration = this.getAnimationDuration();
 console.log('_fullAnimationDuration', this._fullAnimationDuration);
 
 
  this.animation = new this.Konva.Animation(function animateSlide (frame) {
    //console.log('Animation frame!--------------------------------------', Date.now());
    this.playAnimation(frame, this);
  }.bind(this), this.stage.find('Layer'));

  this.animation.start();
this.capturer.start();
};

CanvasRecorder.prototype.displaySlide = function displaySlide(itemId,effect) {
    effect = effect || false;
  var slides = this.stage.find('.slide');

  //console.log('displaySlide', itemId);

  var prev = null;
  Object.keys(slides).forEach(function (key) {
    if (slides.hasOwnProperty(key)) {
        var slide = slides[key];
        if (slide.nodeType === 'Layer') {
    
            if (slide.index === itemId) {
                slide.show();
                if(effect)
                {
                    if(prev)
                    {
                        slide.hide();
                        prev.show();
                    }
                    this.slideTransitions(slide,prev,effect,function(){ slide.show(); prev.hide(); }.call(this, slide,prev));
                }       
                this.currentSlide = itemId;
                this.layer = slide;
    
            } else {
                slide.hide();
            }
          prev = slide;
        }
    }
  }.bind(this));

  this.stage.draw();
};

CanvasRecorder.prototype.goToNextSlide = function goToNextSlide() {
  var slides = this.stage.find('.slide');

  if (this.currentSlide < slides.length) {
    var args = {
        duration:0.5
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

CanvasRecorder.prototype.addEventListeners = function(context) {
  context.$('#downloadVideo').on('click', function downloadVideo(event) {
    //context.capturer.save();
  }.bind(context));

  context.$('#downloadFile').on('click', function downloadFileClick(event) {
    event.preventDefault();

    context.downloadFile('savedData.json', JSON.stringify(context.slides, null, 2));
  }.bind(context));


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
    , duration = 0, context = this;
    
    if(slides.length && (this.stage.attrs.slideTransitionIn || this.stage.attrs.slideTransitionOut) )
    {
        duration = (slides.length-1) * 0.5;
    }
        
    
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




//CanvasRecorder.prototype.getCurrentSlideDuration = function getCurrentSlideDuration() {
//  var slides = this.stage.find('.slide');
//  
//  var elements = this.getCurrentSlideContents()
//    , duration = 0, context = this;
//
//    if(this.currentSlide && (this.currentSlide < slides.length))
//    {
//        duration = 0.5;
//    }
//    
//  elements.forEach(function (element) {
//    if (this._checkGroup(element)) {
//      duration += parseInt(element.attrs.beforeNext) + context._delayBetweenElements;
//    }
//  }.bind(this));
//
//  return duration;
//};


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

    this._lastRenderFunction = null;
    
  elements.forEach(function (element) {
    if (this._checkGroup(element)) {
      var realElement = this.layer.findOne('#' + element.attrs.id);
      
      if(realElement){
        realElement = realElement.children[0]; 
      }else{
        return;
      }

      realElement.hide();
      element.attrs.delay = delay;
      element.attrs.duration = (this.getCurrentSlideDuration() + delay);
      delay += parseInt(element.attrs.beforeNext) + context._delayBetweenElements;
      //this.hideHandles('#' + element.attrs.id);
    }
  }.bind(this));

  //this.layer.draw();
};

CanvasRecorder.prototype.stopAnimation = function stopAnimation(event) {
  var elements = this.getCurrentSlideContents();

  elements.forEach(function (element) {
    if (this._checkGroup(element)) {
        var realElement = this.layer.findOne('#' + element.attrs.id);
        
        if(realElement){
          realElement = realElement.children[0]; 
        }else{
          return;
        }

      realElement.show();
      //this.showHandles('#' + element.attrs.id);
    }
  }.bind(this));

    if(this._currentTextTimeout !== null)
    {
        clearTimeout(this._currentTextTimeout);
    }
  
  
    //Remove all transitions group, that could left from previous  cycles and was not properly cleaned
    this._clearTransitions();
  
  this.layer.draw();
};


CanvasRecorder.prototype.animateSlide = function animateSlide (frame, context) {
  var time = frame.time / 1000 - this.animationDuration,
    timeDiff = frame.timeDiff,
    frameRate = frame.frameRate;

    this.updateProgress();
    
    this._frameCount++;

    
//console.log('AnimateSlide', time);
//console.log('timeDiff', frame.timeDiff);
//console.log('frame.frameRate', frame.frameRate);
//console.log('frame.time', frame.time);
//console.log('_frameCount',this._frameCount);
  
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
      var realElement = this.layer.findOne('#' + element.attrs.id);
      
      if(realElement){
        realElement = realElement.children[0]; 
      }else{
        return;
      }

      //realElement.hide();

      if (realElement.isVisible()) {
        if (time > element.attrs.delay + element.attrs.duration) {
           realElement.hide();
        }
      } else {
        if (time > element.attrs.delay && time < element.attrs.delay + element.attrs.duration) {
          //realElement.show();
          //console.log('time', time);
            context.showRealElement(realElement);
        }
      }
    }
  }.bind(this));


};

CanvasRecorder.prototype.playAnimation = function playAnimation (frame, context) {
  var time = frame.time / 1000,
    timeDiff = frame.timeDiff,
    frameRate = frame.frameRate;

  //console.log('Play animation',time);
    

    if(this._lastRenderFunction)
    {
        this._lastRenderFunction();
    }

  if( context.capturer )
  {
    //context.capturer.capture( context.layer.getCanvas()); 
    context.capturer.capture( context.layer.getCanvas()._canvas );
    
  }
  if (time > context.getCurrentSlideDuration() + context.animationDuration) {
    context.animationDuration += context.getCurrentSlideDuration();

    if (context.getAnimationDuration() > context.animationDuration) {
      context.stopAnimation();
      context.goToNextSlide();
      context.startAnimation();
    }
  }


  
  context.animateSlide(frame, context);


  if (time > this.getAnimationDuration()) {
    console.log('stopTime',time);
    if( context.capturer ) {
      context.showDownloadButton();
      context.capturer.stop();
      context.capturer.save();
    }
    
    //if(context._hand)
    //{
    //    context._hand.destroy();
    //    context._hand = null;
    //}
    //if(context._currentTextElement)
    //{
    //    context._currentTextElement.text(context._currentText);
    //}    
    //
    //console.log('TotalPathDrawed',totalPathDrawed);
    
    context.stopAnimation();
    context.animation.stop();

    context.background.moveTo(context.backgroundLayer);
    context.background.moveToBottom();

    context.animationDuration = 0;
  }
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


CanvasRecorder.prototype.recordWhenReady = function recordWhenReady()
{
    var context = this;
    this.$.when.apply(this.$, this.defferedQueue).then( function(){
        context.recordVideo();         
    } );

};



CanvasRecorder.prototype.updateProgress = function updateProgress()
{
    if(this._updateProgress == 24)
    {
        this._updateProgress = 0;
        return this.$.post('/server-side/updateProgress.php', {
        requestId: this.requestId,
        frames: this._frameCount,
        }, function(data) {
            //console.log(data);

        });        
    }
    this._updateProgress++;

};
