/**
 * Slide transitions animation
 * How it works:
 * its create additions group with content of previous slide at current and animate it. It works this way because
 * of CCapture restrictions
 * @author n.z@software-art.com
 */
CanvasRecorder.prototype.slideTransitions = function slideTransitions(toSlide,fromSlide,options,callback)
{
    var context = this;
    
    this._slideTransitionInProcess  = true;
    
    options = $.extend( {
        effectIn: false,
        effectOut: false,
        duration: 0.5
    }, options );

    if(!options.effectIn && !options.effectOut)
    {
        this._slideTransitionInProcess  = false;
    }
    
    //@todo: change to better variant
    if(options.effectIn == 'random' )
    {
        var effectsSet = [{in:'slideInDown',out:'slideOutUp'},{in:'slideInUp',out:'slideOutDown'},{in:'slideInRight',out:'slideOutLeft'},{in:'slideInLeft',out:'slideOutRight'}];
        var effects = effectsSet[Math.floor(Math.random() * effectsSet.length)];
        options.effectIn = effects.in;
        options.effectOut = effects.out;
    }
    
    
    var node = toSlide.children[0];
    var args ={
        node: node,
        duration: options.duration,
    };
    
    context.animationDuration += options.duration;
    
    _getEffect(options.effectIn,args,node);
    
    args.onFinish = function(){
    
        context._slideTransitionInProcess = false;
        
        if(context.$.isFunction(callback))
        {
            callback();
        }

        //node.x(fromSlide.children[0].attrs.x);
    }.bind(this);
    
    var tween, tweenPrev;
    
    tween = new this.Konva.Tween(args);

    if(options.effectOut)
    {
        var nodePrev = new this.Konva.Group({
            x:0,
            y:0,
            name:'transition_group',
        });
        toSlide.add(nodePrev);
        
        fromSlide.children.forEach(function (element) {
            if(element.getName() !=='background'){
                this.add(element.clone());
            }
        }.bind(nodePrev));
       
       
        var argsPrev = {
            node:nodePrev,
            duration: options.duration,
        };
        
        _getEffect(options.effectOut,argsPrev,nodePrev);
        
        argsPrev.onFinish = function(){
            
            this.destroyChildren();
            this.destroy();
        }.bind(nodePrev);
        
        tweenPrev = new this.Konva.Tween(argsPrev);
    }
    if(options.effectIn)
    {
        tween.play();
    }
    
    if(options.effectOut)
    {
        tweenPrev.play();
    }

    
    function _getEffect(effect,args,node)
    {
        switch(effect)
        {
            case 'slideInUp':
                args.y = node.getY();
                node.y(args.y - context.stage.attrs.height);
            break;
            case 'slideInDown':
                args.y = node.getY();
                node.y(args.y + context.stage.attrs.height);                
            break;
            case 'slideOutUp':
                args.y = node.getY()- context.stage.attrs.height;
            break;
            case 'slideOutDown':
                args.y = node.getY() + context.stage.attrs.height;                
            break;        
            case 'slideInLeft':
                args.x = node.getX();
                node.x(args.x - context.stage.attrs.width);
            break;
            case 'slideInRight':
                args.x = node.getX();
                node.x(args.x + context.stage.attrs.width);
            break;
            case 'slideOutRight':
                args.x = context.stage.attrs.width + node.getX();
            break;
            case 'slideOutLeft':
                args.x = node.getX() - context.stage.attrs.width ;
            break;
            default:
                
            break;
        }        
    }
    
};

/**
 * Check is group relates to stage elements and not to transtions
 * @method
 * @author n.z@software-art.com
 */
CanvasRecorder.prototype._checkGroup = function(node)
{
    return node.nodeType === 'Group' && node.getName()!=='transition_group' && node.getName()!=='effect' && node.getName() !== 'slideBackgroundGroup';
};

/**
 * Load all google fonts, attached to stage
 * @author n.z@software-art.com
 */
CanvasRecorder.prototype.loadAllFontsFromStage = function loadAllFontsFromStage(callback,callback_fault)
{
    if(Array.isArray(this.stage.attrs.loadedFonts) && this.stage.attrs.loadedFonts.length > 0)
    {
            return this.loadFont(this.stage.attrs.loadedFonts,true,callback,callback_fault);
    }
}

/**
 * Load google font from internet
 * @method
 * @author n.z@software-art.com
 */
CanvasRecorder.prototype.loadFont = function loadFont(font,reload,callback,callback_fault)
{
    reload = reload || false;
    var context = this;
    var  webSafe = ['Arial','Times New Roman','Verdana'];
    if(webSafe.indexOf(font)!==-1)
    {
        if(callback && callback instanceof Function)
        {
            callback();
        }        
        return;
    }
    this.stage.attrs.loadedFonts = Array.isArray(this.stage.attrs.loadedFonts) ? this.stage.attrs.loadedFonts :  []; 
    
    if(this.stage.attrs.loadedFonts.indexOf(font)===-1 || reload)
    {
        if(context.overlay_full)
            this.overlay_full.show();
        WebFont.load({
            google: {
                families: Array.isArray(font) ? font : [font]
            },
    
            active: function()
            {
                if(callback && callback instanceof Function)
                {
                    callback();
                }
                if(this.overlay_full)
                    this.overlay_full.hide();
                if(!Array.isArray(font))
                {
                    if(this.stage.attrs.loadedFonts.indexOf(font)==-1)
                        this.stage.attrs.loadedFonts.push(font);
                }
            }.bind(context),
            
            inactive : function(){
                 if(this.overlay_full)
                    this.overlay_full.hide();
                if(callback_fault && callback_fault instanceof Function)
                {
                    callback_fault();
                }  
                console.warn('Can not load font!');
            }.bind(this)
        });
    }else{
        if(callback && callback instanceof Function)
        {
            callback();
        }        
        return;        
    }
};

/**
 * return stretched image data info
 * @method
 * @param imageData objet with image info - height, width, src
 * @return object adjusted information
 * @author n.z@software-art.com
 */
CanvasRecorder.prototype._stretchImage = function _stretchImage(imageData,position,fit)
{
    fit = fit || 'cover';
    var context = this;
    var aspectRatio = imageData.width / imageData.height;
    switch(position)
    {
        case 'center':
        default:
                //Landscape image
                if(aspectRatio >=1 )
                {
                    imageData.height = context.stage.attrs.height;
                    imageData.width = imageData.height * aspectRatio; 
                }else{
                    imageData.width = context.stage.attrs.width;
                    imageData.height = imageData.width / aspectRatio;
                }

                if(imageData.width < context.stage.attrs.width && fit == 'cover')
                {
                   imageData.width =  context.stage.attrs.width;
                   imageData.height = imageData.width / aspectRatio;
                }                    

                imageData.x = context.stage.attrs.width/2 - imageData.width/2;
                imageData.y = context.stage.attrs.height/2 - imageData.height/2;
            break;
    }
    return imageData;
};


/**
 * Show real element and perform all required animation for it
 * @method
 * @memberOf CanvasRecorder.prototype
 * @param element - real element for processing
 */
CanvasRecorder.prototype.showRealElement = function showRealElement(element)
{
    var context = this;
    if(this._animationInProgress.indexOf(element.getId())>=0 || this._slideTransitionInProcess )
    {
       return; 
    }
    
    //Skip if no hand
    if(element.attrs.noHand || element.parent.attrs.noHand)
    {
        element.show();
        return;
    }
    this._animationInProgress.push(element.getId());
    //console.log('showRealElement', element);
    switch(element.className)
    {
        case 'Image':
            if(element.attrs.src.split('.').pop()!=='svg')
            {
                if(element.attrs.drawingEffect || element.parent.attrs.drawingEffect)
                {
                    if(this.stage.attrs.hand_write)
                    {
                        this.drawImageWithHand(element);
                    }else{
                        element.show();
                    }                    
                }else{
                    if(this.stage.attrs.hand_drop)
                    {
                        this.showElementWithHand(element);
                    }else{
                        element.show();
                    }
                }
            }else{
                if(this.stage.attrs.hand_write)
                {
                        
                    //check if path can be drawen
                    if(!this.preloadedPaths[element.attrs.src].canDraw)
                    {
                        //If no, then we need to treat it as usual image
                        if(this.stage.attrs.hand_drop)
                        {
                            this.showElementWithHand(element);
                        }else{
                            element.show();
                        }
                        
                        return;
                    }else{
                        this.drawSvgElementWithHand(element);                        
                    }

                }else{
                    element.show();
                }
                
            }

            break;
        case 'Text':
            if(this.stage.attrs.hand_write)
            {
                if(context._currentTextTimeout)
                {
                    
                    clearTimeout(context._currentTextTimeout);
                    context._currentTextTimeout = null;
                }
                //we need to reset all if previous animation exist at same slide....
                if(context._hand)
                {
                    context._hand.destroy();
                    context._hand = null;
                }
                if(context._currentTextElement)
                {
                    context._currentTextElement.text(context._currentText);
                }            
                
                this.writeElementWithHand(element);
            }else{
                element.show();
            }
            break;
        default:
            element.show();
    }
    
    //Start audio if attached
    var el_id = element.parent.id();
    if( context.audioQueue[el_id] !== undefined )
    {
        context.audioQueue[el_id].play();
    }
};

/**
 * @method
 * @memberOf CanvasRecorder.prototype
 */
CanvasRecorder.prototype.showElementWithHand = function(element)
{
    var context = this;
    var hand, handLayer, elementCenterX = element.parent.getX() + element.getX() + element.getWidth() / 2,
    elementCenterY = element.parent.getY() + element.getY();
    
    
    var imageObj = new Image();
    
    imageObj.onload = function() {

    
        var handOffsetY, handOffsetX, heightCoef;
        
        var imgH =imageObj.height, imgW = imageObj.width, aspectRatio = imageObj.width / imageObj.height;
        
        if(context.stage.attrs.hand_drop.indexOf('image_hand.png') >=0 )
        {
            handOffsetY = imgH/100*30;
            heightCoef = 1.4;
            handOffsetX = imgW/100 * -5;
        }else{
            handOffsetY = 0;
            heightCoef = 1;
            handOffsetX = imgW/100 * 10;
        }

        
        //if(imgH > context.stage.getHeight())
        //{
            imgH = context.stage.getHeight() * heightCoef;
            imgW = ~~(imgH * aspectRatio);
        //}else{
        //    
        //}
        
        hand = new Konva.Image({
            x: elementCenterX - imgW/2 + handOffsetX,
            y: context.stage.getHeight() + imgH,
            image: imageObj,
            width: imgW,
            height: imgH
        });

        context._hand = hand;

        // add the shape to the layer
        context.layer.add(hand);

        // add the layer to the stage
        //context.stage.add(handLayer);
        
        
        var tween = new context.Konva.Tween({
            node: hand,
            y: elementCenterY - handOffsetY, //> 0 ? elementCenterY : 0
            duration:0.5,
            onFinish:function(){
                element.show();
                var tweenBack = new context.Konva.Tween({
                    node: hand,
                    y:context.stage.getHeight() + imgH,
                    duration:0.5,
                    onFinish:function(){
                        this.destroy();
                        context._animationInProgress  = context._animationInProgress.remove(element.getId());
                    }.bind(this)
                });
                tweenBack.play();
            }.bind(hand)
        });
        tween.play();
    };
    
    imageObj.src = this.stage.attrs.hand_drop;
    
};

/**
 * @method
 * @memberOf CanvasRecorder.prototype
 */
CanvasRecorder.prototype.writeElementWithHand = function(element)
{
    element.align('left');
    var context = this;
    var text = element.text();
    this._currentTextElement = element;
    this._currentText = text;
    var textLength = 0;
    var hand;
    var imageObj = new Image();
    var fontHeight = element.getTextHeight();

    imageObj.onload = function() {

    
        var imgH =imageObj.height, imgW = imageObj.width, aspectRatio = imageObj.width / imageObj.height;
        
        
        //if(imgH > context.stage.getHeight())
        //{
            imgH = context.stage.getHeight() * 1.3;
            imgW = ~~(imgH * aspectRatio);
        //}else{
        //    
        //}
        
        hand = new Konva.Image({
            x: element.parent.getX(),
            y: element.parent.getY() + fontHeight/2,
            image: imageObj,
            width: imgW,
            height: imgH,
            name: 'effect'
        });

        context._hand = hand;

        // add the shape to the layer
        context.layer.add(hand);
        
        var direction = -1;
        
        var duration = (parseFloat(element.parent.attrs.beforeNext)  * 1000 + parseFloat(element.parent.attrs.delay)  * 1000 + parseFloat(context.animationDuration) * 1000)- context.animation.frame.time;
        
        //console.log('durationFull',duration);
        //console.log('text.length',text.length);
         duration =  ~~(~~(duration)  / text.length ) ;
        //duration = duration > 100 ? 100 : duration;
        
        var tweenDuration = duration / 1000;
        //console.log('duration',duration);
        //console.log('textTweenDuration',tweenDuration);
        //console.log('element.parent.attrs.beforeNext',element.parent.attrs.beforeNext);
        //console.log('element.parent.attrs.delay',element.parent.attrs.delay);
        //console.log('context.animationDuration',context.animationDuration);
        //console.log('context.animation.frame.time',context.animation.frame.time);
        //console.log(Date.now());
        
        
        if(context.capturer)
        {
            duration =  ~~(~~(element.parent.attrs.beforeNext * ((1000 - 100)/30))   / text.length ) ;
            //console.log('durationFullRecorder',duration);
        //    //duration = duration > 100 ? 100 : duration;
        //
            
            tweenDuration = duration / 1000;
            
        }
        
        var newRowOffsetY = 0, textRow = 0;
        
        
        function type_text() {
            var prevRowsCount = element.textArr.length;
            
            element.text(text.substr(0, textLength++));

            if(prevRowsCount != element.textArr.length)
            {
                newRowOffsetY = (textRow+1) * fontHeight;
                textRow++;
            }
            
            if (textLength < text.length + 1) {
                context._currentTextTimeout = setTimeout(function(){
                    type_text();   
                } , duration );
                var pos = element.parent.position();
                var tween = new context.Konva.Tween({
                    node: hand,
                    x: pos.x + element.getX() + element.textArr[textRow].width,
                    y: pos.y + element.getY() +  newRowOffsetY +  fontHeight/2 + ((fontHeight/1.2)*direction*Math.random()),
                   // y:pos.y + ((fontHeight/2) *direction*Math.random()  ) ,
                    duration: tweenDuration,
                    rotation:3*direction * Math.random(),
                    easing: context.Konva.Easings.Linear,
                    onFinish: function(){
                        if(textLength >= text.length + 1)
                        {
                            hand.destroy();                            
                        }
                        this.destroy();
                    }
                });
                tween.play();
                
            } else {
                textLength = 0;
                text = '';
                hand.destroy();
            }
            
            direction = -1 * direction ;
        }

        element.text('');
        element.show();    
        type_text();

    };
    
    imageObj.src = this.stage.attrs.hand_write;

};


CanvasRecorder.prototype.drawSvgElementWithHand = function(element){
    var strokeThikness = element.parent.attrs.boldness ? parseInt(element.parent.attrs.boldness) : 1;
    var strokeColor = element.parent.attrs.strokeColor ? '#' + element.parent.attrs.strokeColor : 'black';
    var fadeDuration = 0.5;
    var svgDuration = 0;
    
    var animationDuration = parseFloat(element.parent.attrs.beforeNext)  * 1000;

    var context = this;

    var scaleRatio = this.preloadedPaths[element.attrs.src].width  ? parseFloat(element.getWidth()/this.preloadedPaths[element.attrs.src].width) : 1;
    
    var path = new Konva.Path({
        data:this.preloadedPaths[element.attrs.src].path,
        fill:strokeColor,
        stroke:strokeColor,
        //strokeWidth: 0.25,
        scale: {
            x:scaleRatio,
            y: scaleRatio
        },
    });

    var animationGroup = new this.Konva.Group({
        x:element.getX() - element.getOffsetX(),
        y:element.getY() - element.getOffsetY(),
        name:'transition_group',
    });
    
    var layer = element.parent;

        
    layer.add(animationGroup);
    
    element.hide();
    
    //context.stage.batchDraw();

    path.setAbsolutePosition({
        //x:element.parent.getX(),
        //y:element.parent.getY()
        x:0,
        y:0
    });


    var ca = path.dataArray;

    //Array with splited fragments
    var pathFragments = [];
    var pathFragment;
    
    //Total length of initial path...
    var totalPathLength = 0;
    var totalFragmentPathLength = 0;
    var fragmentPathLength = 0;
    var dataArrayBuffer = new Array();

    for (var i = 0; i < ca.length; i++) {
        var com = ca[i].command;
        totalPathLength +=ca[i].pathLength ;
        if(com!=='M' && com !=='m')
        {
            fragmentPathLength +=ca[i].pathLength ;
        }else{
            pathFragment = new Konva.Path({
                x:0,
                y:0,
                stroke:strokeColor,
                strokeWidth:strokeThikness*scaleRatio,
                scale: {
                    x: scaleRatio,
                    y: scaleRatio
                },                        
            });
            //console.log('FragmentPathLength',fragmentPathLength);
            pathFragment.attrs.totalPathLength = fragmentPathLength;
            pathFragment.dataArray = dataArrayBuffer;
            dataArrayBuffer = [];
            totalFragmentPathLength +=fragmentPathLength;
            fragmentPathLength = 0;

            pathFragments.push(pathFragment);
        }
        dataArrayBuffer.push(ca[i]);
    }

    
    pathFragment = new Konva.Path({
        stroke:'black',
        strokeWidth:1*scaleRatio,
        scale: {
            x: scaleRatio,
            y: scaleRatio
        },        
    });

    pathFragment.attrs.totalPathLength = fragmentPathLength;
    pathFragment.dataArray = dataArrayBuffer;    
    pathFragments.push(pathFragment);

    totalFragmentPathLength +=fragmentPathLength;
    
    var prevX = 0;
    var prevY = 0;            
    var prevTime = 0;
    var visible = 0;    
    
    var hand;
    var imageObj = new Image();
    
    
    imageObj.onload = function() {
    
            
            var imgH =imageObj.height, imgW = imageObj.width, aspectRatio = imageObj.width / imageObj.height;
            
            
            //if(imgH > context.stage.getHeight())
            //{
                imgH = context.stage.getHeight() * 1.3 ;
                imgW = ~~(imgH * aspectRatio);
            //}else{
            //    
            //}
        
                  
            hand = new Konva.Image({
                x: 0,
                y: 0,
                image: imageObj,
                width: imgW,
                height: imgH,
                name: 'effect',
                draggable:true,
            });
    
            //context._hand = hand;
            
            animationGroup.add(hand);
    
            hand.hide();
            
            // add the shape to the layer
            animationGroup.add(path);
            path.setZIndex(1);
    
            //console.log(path);
    
    
            hand.setPosition({
                x:path.dataArray[1].start.x * scaleRatio,
                y:path.dataArray[1].start.y * scaleRatio,
            });
            
            path.hide();
            hand.show();

            console.log(animationDuration);
            animationDuration = animationDuration - 250;
            

            
            var parent = path.getParent();
            
            var prevFragmentLength = 0;
            
            var step = (totalPathLength) / (animationDuration);
            console.log('step',step);
            var current = 0;
            var fragment = pathFragments[0];
            parent.add(fragment);
            draw(fragment,0);
            fragment.setZIndex(1);

            
            var anim = new Konva.Animation(function(frame) {
                var time = frame.time;
                visible = time * step - prevFragmentLength;
                //console.log('prevFragmentLength',prevFragmentLength);
                //console.log('visible',visible);
                if( fragment.attrs.totalPathLength <= visible )
                {     
                    draw( fragment, fragment.attrs.totalPathLength );
                    if(current >= pathFragments.length-1)
                    {
                        //Make time of animation bigger, then animation duration, so anim.stop() will be called
                        time = animationDuration +1;
                    }else{
                        current++;

                        prevFragmentLength += fragment.attrs.totalPathLength;
                        fragment = pathFragments[current];
                        parent.add(fragment);
                        draw(fragment,0);
                        fragment.setZIndex(1);
                    }                    
                }else{
                    draw(fragment, visible);
                }
                
                
                
                if(time >= animationDuration)
                {
                    anim.stop();
                    element.show();
                    hand.to({
                        y: context.stage.getHeight(),
                        opacity:0,
                        duration:0.25,
                        onFinish: function(){
                            if(context.capturer ==undefined)
                            {
                                animationGroup.destroyChildren();
                                animationGroup.destroy();
                            }
                            hand.destroy();                            
                        }
                    });
                    console.log('FINISH--------');
                    layer.draw();
                }
             }, element.parent);
           
            anim.start();                
               
            hand.setZIndex(9);
            context.stage.batchDraw();  
    };
        
    imageObj.src = this.stage.attrs.hand_write; 

    function draw(fragment, visiblePath) {
        
        fragment.dashOffset(fragment.attrs.totalPathLength - visiblePath);
        
        fragment.dash([fragment.attrs.totalPathLength, fragment.attrs.totalPathLength]);
        
        var pos = _getPointAtLength2(visiblePath, fragment);
        
        if(pos.x != 0)
        {

            //if(context.capturer)
            //{
                hand.setPosition({
                    x: pos.x*scaleRatio,
                    y:pos.y*scaleRatio
                });                        
            //}else{
                //hand.to({
                //    x:pos.x*scaleRatio,
                //    y:pos.y*scaleRatio,
                //    duration:step /100, //((svgDuration * 1000) / 100),
                //    
                //    //duration:0.1 ,//Konva.Path.getLineLength(prevX,prevY,pos.x,pos.y) / stepDuration  stepDuration- (stepDuration/fragment.attrs.totalPathLength))/1000
                //});
            //}
            prevX = pos.x*scaleRatio;
            prevY = pos.y*scaleRatio;
        }
        context.stage.batchDraw();
    }


    function _getPointAtLength(length, path)
    {
    
    
      var totalLength = 0;
      var prevX = 0, prevY = 0;
      for(let i = 0; i < path.dataArray.length; i++)
      {
          if(length > totalLength)
          {
              totalLength += path.dataArray[i].pathLength;
              prevX = path.dataArray.start ? path.dataArray.start.x : 0;
              prevY = path.dataArray.start ? path.dataArray.start.y : 0;
              continue;
          }
          return { x : path.dataArray[i].start ? path.dataArray[i].start.x : prevX, y: path.dataArray[i].start ? path.dataArray[i].start.y : prevY };
      }
      return {x:0,y:0};
    }        
};


/** 
 * Draw image with hand effect
 */ 
CanvasRecorder.prototype.drawImageWithHand = function drawImageWithHand(element)
{
    element.hide();
    var context = this;
    
    var animationDuration = parseFloat(element.parent.attrs.beforeNext)  * 1000;
    

    var hand;
    var imageObj = new Image();
    
    imageObj.onload = function() {
    
            var animationGroup = new this.Konva.Group({
                x:element.getX() - element.getOffsetX(),
                y:element.getY() - element.getOffsetY(),
                name:'transition_group',
            });    
            
            var groupClip = new this.Konva.Group({
                x:0,
                y:0,
                name:'transition_group_clip',
                clip: {
                   x : 0,
                   y : 0,
                   width : parseFloat(element.getWidth()),
                   height : 1
               },        
            });
            
            var source = element.clone();
            source.setX(0);
            source.setY(0);
            
            groupClip.add(source);
            
            animationGroup.add(groupClip);
            
            source.setZIndex(8);
            
            var layer = element.parent;
        
            source.show();            
            
            var imgH =imageObj.height, imgW = imageObj.width, aspectRatio = imageObj.width / imageObj.height;
            
            
            //if(imgH > context.stage.getHeight())
            //{
                imgH = context.stage.getHeight() * 1.3;
                imgW = ~~(imgH * aspectRatio);
            //}else{
            //    
            //}
        
                  
            hand = new Konva.Image({
                x: 0,
                y: 0,
                image: imageObj,
                width: imgW,
                height: imgH,
                name: 'effect',
                draggable:true,
            });
    
            //context._hand = hand;
            
            animationGroup.add(hand);
    

            hand.setZIndex(9);
        
            layer.add(animationGroup);            
        
            //layer.draw();
            context.stage.batchDraw();
            animationDuration = animationDuration - 500;
            var amplitude = source.getWidth()/2;
            var period = animationDuration/5;
            var halfPeriod = period / 2;
            // in ms
            var centerX = source.getWidth()/2;
            var clipStep = source.getHeight() / animationDuration ;
            var func, x, clip=1;
            var start = 0;
            var startTime = 0;
            var end = clipStep * halfPeriod;
            var clipBase = 1;
            //console.log('clipStep', clipStep);
            //console.log('end', end);
            var anim = new Konva.Animation(function(frame) {
                var time = frame.time;
                func = Math.cos(frame.time * 2 * Math.PI / period);                
                if((time - startTime) > halfPeriod || startTime === 0)
                {
                    startTime = time;
                    clipBase = startTime * clipStep + 1;
                }
                clip = clipBase + Konva.Easings.StrongEaseInOut(time-startTime,start,end,halfPeriod);//(halfPeriod-((halfPeriod/100)*25))
                //console.log('time', time);
                //console.log('clip',clip);
                
                x = centerX - amplitude * func ;
                hand.setX(x);
                groupClip.clipHeight(clip);
                hand.setY(clip);
                if(time > animationDuration)
                {
                    anim.stop();
                    hand.to({
                        y: context.stage.getHeight(),
                        opacity:0,
                        duration:0.5,
                    });
                    //console.log('FINISH--------');
                    layer.draw();
                }
               // update stuff
             }, layer);
           
             anim.start();            
    }.bind(this);
        
    imageObj.src = this.stage.attrs.hand_write;   
   
};



function _getPointAtLength2(length, path)
{

    var totalLength = 0;
    var prevX = 0, prevY = 0;
    var point = {x:0, y:0 };
    var prevPathLength  = 0;
    for(let i = 0; i < path.dataArray.length; i++)
    {
        if(length > totalLength)
        {
            prevPathLength = path.dataArray[i].pathLength;
            totalLength += prevPathLength;
            prevX = path.dataArray[i].start ? path.dataArray[i].start.x : 0;
            prevY = path.dataArray[i].start ? path.dataArray[i].start.y : 0;
            //console.log('PathLength',path.dataArray[i].pathLength);
            continue;
        }else{
            //var dist = 0.00000001; //totalLength - length;
            //var dist = totalLength - length;
            //dist = prevPathLength > 0 ? (dist / (prevPathLength/100)) / 100  : 0.01; 
            var dist = 0.1;
            //console.log('totalLength', totalLength);
            //console.log('length', length);
            //console.log('Dist', dist);
            switch (path.dataArray[i].command) {
                case 'L':
                    point = Konva.Path.getPointOnLine(
                        dist,
                        path.dataArray[i].start.x,
                        path.dataArray[i].start.y,
                        path.dataArray[i].points[0],
                        path.dataArray[i].points[1]
                    );
                    break;
                case 'C':
                    point = Konva.Path.getPointOnCubicBezier(
                        dist,
                        path.dataArray[i].start.x,
                        path.dataArray[i].start.y,
                        path.dataArray[i].points[0],
                        path.dataArray[i].points[1],
                        path.dataArray[i].points[2],
                        path.dataArray[i].points[3],
                        path.dataArray[i].points[4],
                        path.dataArray[i].points[5]
                    );
                    break;
                case 'Q':
                    point = Konva.Path.getPointOnQuadraticBezier(
                        dist,
                        path.dataArray[i].start.x,
                        path.dataArray[i].start.y,
                        path.dataArray[i].points[0],
                        path.dataArray[i].points[1],
                        path.dataArray[i].points[2],
                        path.dataArray[i].points[3]
                    );                    
                    break;
                case 'A':
                    //            break;
                default:
                    point = { x : path.dataArray[i].start ? path.dataArray[i].start.x : prevX, y: path.dataArray[i].start ? path.dataArray[i].start.y : prevY };
                    break;
            }
            return point;        
        }
    }

return {x:0, y:0 };
}



CanvasRecorder.prototype.preloadSvg = function(url,konvaObj)
{
    var context = this;
    if(this.preloadedPaths.indexOf(url) >=0)
    {
        return;
    }
    
    return context.$.get(url,function(data) {
        var path ='';
        var pathEl = context.$(data.documentElement);
        var paths = pathEl.find('path');
        paths.each(function(){
            path +=' '+context.$(this).attr('d');
        });
        
        context.preloadedPaths[url] = {};
        context.preloadedPaths[url].url = url;
        context.preloadedPaths[url].path = path;
        context.preloadedPaths[url].svgContent = pathEl[0].outerHTML;
        context.preloadedPaths[url].width = pathEl.attr('width');
        context.preloadedPaths[url].height = pathEl.attr('height');
        context.preloadedPaths[url].canDraw = paths.length <= 3;
        if(konvaObj && paths.length <=3)
        {
            konvaObj.attrs.canDraw = true;
            konvaObj.attrs.originUrl = url;
            //Restore colors
            if(konvaObj.nodeType == 'Group')
            {
                return;
            }
            if(konvaObj.parent!==undefined && konvaObj.parent.attrs.strokeColor !== undefined)
            {
                context._changeSvgPreviewColor(konvaObj.parent,konvaObj.parent.attrs.strokeColor);
            }
            
            if(konvaObj.parent!==undefined && konvaObj.parent.attrs.boldness !== undefined)
            {
                context._changeSvgThickness(konvaObj.parent,konvaObj.parent.attrs.boldness);
            }            
        }
    });
    
};

CanvasRecorder.prototype.preloadImage =  function (src) {
    
    var context = this;
    if(this.preloadedImages.indexOf(src) >=0)
    {
        return;
    }
    
    return new Promise(function (resolve, reject) {
      // Create a new image from JavaScript
      var image = new Image();
      // Bind an event listener on the load to call the `resolve` function
      image.onload  = resolve;
      // If the image fails to be downloaded, we don't want the whole system
      // to collapse so we `resolve` instead of `reject`, even on error
      image.onerror = resolve;
      // Apply the path as `src` to the image so that the browser fetches it
      image.src = src;
    });
};

CanvasRecorder.prototype.preloadAudio =  function (id, attached_id) {
    
    var context = this;
    if(this.audioQueue.indexOf(id) >=0)
    {
        return;
    }
    
    return new Promise(function (resolve, reject) {
        var audio = new Audio();
        context.audioQueue[id] = audio; 
        $(audio).on("loadedmetadata", resolve);
        audio.src = context.userFolder+'/projects/'+context.projectId+'/mp3/'+attached_id+'.mp3';
    });
};


/**
 * Clear stage from all not finished transitions
 */
CanvasRecorder.prototype._clearTransitions = function _clearTransitions()
{
    var context = this;
    //Remove hanged slides transitions, image drawing
    this.stage.find('.transition_group').forEach(function (groupNode) {
        
        groupNode.destroyChildren();
        groupNode.destroy();
    }.bind(this));
    
    //Removing hanged hands
    this.stage.find('.effect').forEach(function (node) {
        node.destroy();
    }.bind(this));

    //Stop all attached audio players
    for(var i in context.audioQueue)
    {
        if (context.audioQueue.hasOwnProperty(i)) { 
            context.audioQueue[i].pause();
            context.audioQueue[i].currentTime = 0;
        }
    }
    
};


CanvasRecorder.prototype._changeSvgPreviewColor = function(konvaSvgGroup, color)
{
    if(konvaSvgGroup.nodeType !== 'Group')
    {
        konvaSvgGroup = konvaSvgGroup.parent;
    }
    var context = this,
    originUrl = konvaSvgGroup.attrs.originUrl || konvaSvgGroup.children[0].attrs.originUrl ;
    if(originUrl !== undefined)
    {
        var svgContent = context.preloadedPaths[originUrl]['svgContent'];
        if(svgContent !==undefined)
            {
                var newSvg = svgContent.replace(/fill\:\s*\#[0-9a-fA-F]{1,6}/,'fill:#'+color).replace(/stroke\:\s*([^\s\;\"]*)?/,'stroke:#'+color)
            //console.log(svgContent[0].outerHTML);
            //console.log(newSvg);
            var newImage = new Image();
            newImage.src = 'data:image/svg+xml;base64,' + window.btoa(newSvg);
            //console.log( konvaSvgGroup.children[0]);
            konvaSvgGroup.children[0].attrs.image = newImage;
            context.preloadedPaths[originUrl]['svgContent'] = newSvg;
            context.stage.batchDraw();
            //konvaSvgGroup.children[0].attrs.src = newImage.src;
        }
    }
};

CanvasRecorder.prototype._changeSvgThickness = function(konvaSvgGroup, thickness)
{
    if(konvaSvgGroup.nodeType !== 'Group')
    {
        konvaSvgGroup = konvaSvgGroup.parent;
    }
    var context = this,
    originUrl = konvaSvgGroup.attrs.originUrl || konvaSvgGroup.children[0].attrs.originUrl ;
    if(originUrl !== undefined)
    {
        var svgContent = context.preloadedPaths[originUrl]['svgContent'];
        //console.log(svgContent);
        var color = svgContent.match(/fill\:\s*([^\s\;\"]*)/i)[1] || svgContent.match('/stroke\:\s*([^\s\;\"]*)?/')[1] || '#000000';
        //console.log('matched color:', color);
        //console.log('thickness:', thickness);
        if(svgContent !==undefined)
        {
            var newSvg;
            if(svgContent.indexOf('stroke-width')>=0)
            {
                newSvg = svgContent.replace(/stroke-width\:\s*([^\s\;\"]*)?/,'stroke-width:'+(parseInt(thickness)));
            }else{
                newSvg = svgContent.replace(/style\=\"/,'style="stroke-width:'+(parseInt(thickness))+';');
            }
            
            if(newSvg.indexOf('stroke:')== -1 )
            {
                newSvg = newSvg.replace(/style\=\"/,'style="stroke:'+color+';');
            }
            
            //console.log(svgContent[0].outerHTML);
            //console.log(newSvg);
            var newImage = new Image();
            newImage.src = 'data:image/svg+xml;base64,' + window.btoa(newSvg);
            //console.log( konvaSvgGroup.children[0]);
            konvaSvgGroup.children[0].attrs.image = newImage;
            context.preloadedPaths[originUrl]['svgContent'] = newSvg;
            
            context.stage.batchDraw();
            //konvaSvgGroup.children[0].attrs.src = newImage.src;
        }
    }
};



/**
 * Extend Array object
 */
Array.prototype.remove = function() {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};

Konva.Collection.prototype.reorder = function(from, to) {
    if(this[0].nodeType === 'Group' && this[0].getName() == 'slideBackgroundGroup' )
    {
        from++;
        to++;
    }
    this.splice(to, 0, this.splice(from, 1)[0]);
};