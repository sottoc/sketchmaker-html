<script type="text/template" data-template="projectListItem">
  <a class="dropdown-item projectListItem" href="#" data-id="${id}" data-project='${data}'>${name}</a>
</script>

<script type="text/template" data-template="imagesDirectoryLink">
  <li class="nav-item">
    <a class="nav-link p-3" href="#${id}"  data-toggle="tab" role="tab" aria-controls="${id}" >${directoryName}</a>
  </li>
</script>

<script type="text/template" data-template="imagesDirectoryTab">
		<div class="tab-pane fade" id="${id}" role="tabpanel">
            ${items}
		</div>
</script>
<script type="text/template" data-template="imagesDirectoryImage">
    <div class="fix-square">
      <div class="content">
        <div class="${d} image-list-item">
          <img data-src="${path}/${file}" src="${thumb}" alt="" class="loadableImage lazy-load" />
          <button class="btn btn-success load-image" data-toggle="tooltip" title="Add Image"><i class="fa fa-plus"></i></button>
          <button class="btn btn-danger delete" data-toggle="tooltip" title="Delete Image"><i class="fa fa-trash"></i></button>
        </div>
      </div>
    </div>
</script>
<script type="text/template" data-template="imagesDirectoryImageSvg">
    <div class="fix-square">
      <div class="content">
          <div class="${d} image-list-item ">
            <img src="${path}/${file}" alt="" class="loadableImage img-svg" />
            <button class="btn btn-success load-image" data-toggle="tooltip" title="Add Image"><i class="fa fa-plus"></i></button>
            <button class="btn btn-danger delete" data-toggle="tooltip" title="Delete Image"><i class="fa fa-trash"></i></button>
          </div>
      </div>
    </div>
</script>
<script type="text/template" data-template="slidePreview">
  <div class="card bg-white text-dark slidePreviewContainer" data-id="${index}">
    <div class="card-img">
      <img src="img/no-image.png" class="slidePreview" data-slide-preview-id="${index}">
    </div>
    <div class="card-img-overlay text-center">
      <div class="slideControl slideId text-white">
        ${index}
      </div>
      <div class="slideControl slideMove slideMoveBackward">
        <span class="fa-stack">
          <i class="fa fa-circle fa-stack-2x text-muted"></i>
          <i class="fa fa-arrow-left fa-stack-1x text-white"></i>
        </span>
      </div>
      <div class="slideControl slideMove slideMoveForward">
        <span class="fa-stack">
          <i class="fa fa-circle fa-stack-2x text-muted"></i>
          <i class="fa fa-arrow-right fa-stack-1x text-white"></i>
        </span>
      </div>
      <div class="slideControl slideDelete" data-id="${index}">
        <span class="fa-stack">
          <i class="fa fa-circle fa-stack-2x text-danger"></i>
          <i class="fa fa-close fa-stack-1x text-white"></i>
        </span>
      </div>
    </div>
  </div>
</script>

<script type="text/template" data-template="designListItem">
  <div class="list-group-item list-group-item-action align-items-stretch designListItem d-flex justify-content-between" data-id="${id}">
    <div class="w-10 text-center sortable-handle"><i class="fa fa-arrows-v"></i></div>
    <div class="w-25 text-center">${data}</div>
    <div class="w-auto  d-flex flex-column">
      <div class="row">
        <div class="col-11 offset-1">
          <h5 class="mb-1">
            <span class="index">${index}</span>. ${type}
          </h5>
        </div>
      </div>
      <div class="row">
        <div class="col-5 offset-1">
            <div class="form-check">
                <label class="form-check-label"> 
                <input type="checkbox" class="form-check-input designListInput" data-name="noHand" ${noHand} value="1" data-id="${id}">
                No hand</label>
            </div>            
        </div>
      </div>
      <div class="form-group row mb-0 mt-auto">
        <div class="col-3 offset-1 col-form-label text-right pr-0">
          Duration:
        </div>
        <div class="col-2 px-1">
          <input type="text" class="form-control form-control-sm designListInput" data-name="beforeNext" value="${beforeNext}" data-id="${id}">
        </div>
        <div class="col-4 col-form-label col-sec pl-0">
          Secs
        </div>
      </div>
    </div>
    <div class="w-auto">
        <span class="fa fa-times pull-right removeItemButton mb-1" data-id="${id}"></span>
        <button class="btn btn-block btn-sm btn-primary attachAudioToItem ${attached_1}" data-attach-id="${id}_1" data-attach-type="1"><span style="display: none;">Using:</span>1</button>
        <button class="btn btn-block btn-sm btn-primary attachAudioToItem ${attached_2}" data-attach-id="${id}_2" data-attach-type="2"><span style="display: none;">Using:</span>2</button>
        <button class="btn btn-block btn-sm btn-primary attachAudioToItem ${attached_3}" data-attach-id="${id}_3" data-attach-type="3"><span style="display: none;">Using:</span>3</button>
    </div>   

  </div>
</script>
<script type="text/template" data-template="designListItemSvg">
  <div class="list-group-item list-group-item-action align-items-stretch designListItem d-flex justify-content-between" data-id="${id}">
    <div class="w-10 text-center sortable-handle"><i class="fa fa-arrows-v"></i></div>
    <div class="w-25 text-center">${data}</div>
    <div class="w-auto  d-flex flex-column">
      <div class="row">
        <div class="col-11 offset-1">
          <h5 class="mb-1">
            <span class="index">${index}</span>. ${type}
          </h5>
        </div>
      </div>
      <div class="row">
        <div class="col-5 offset-1">
            <div class="form-check">
                <label class="form-check-label"> 
                <input type="checkbox" class="form-check-input designListInput" data-name="noHand" ${noHand} value="1" data-id="${id}">
                No hand</label>
            </div>            
        </div>
      </div>
      <div class="row opt-thickness my-2">
        <div class="col-10 offset-1">
            <label class="pull-left">Thikness:</label>
            <select data-name="boldness" class="form-control form-control-sm designListInput pull-left" data-id="${id}" >${boldness}</select>
            
            <input data-name="strokeColor" class="form-control form-control-sm designListInput  jscolor" data-id="${id}" title="Color" value="${strokeColor}">            
        </div>
      </div>      
      <div class="form-group row mb-0 mt-auto">
        <div class="col-3 offset-1 col-form-label text-right pr-0">
          Duration:
        </div>
        <div class="col-2 px-1">
          <input type="text" class="form-control form-control-sm designListInput" data-name="beforeNext" value="${beforeNext}" data-id="${id}">
        </div>
        <div class="col-4 col-form-label col-sec pl-0">
          Secs
        </div>
      </div>
    </div>
    <div class="w-auto">
        <span class="fa fa-times pull-right removeItemButton mb-1" data-id="${id}"></span>
        <button class="btn btn-block btn-sm btn-primary attachAudioToItem ${attached_1}" data-attach-id="${id}_1" data-attach-type="1"><span style="display: none;">Using:</span>1</button>
        <button class="btn btn-block btn-sm btn-primary attachAudioToItem ${attached_2}" data-attach-id="${id}_2" data-attach-type="2"><span style="display: none;">Using:</span>2</button>
        <button class="btn btn-block btn-sm btn-primary attachAudioToItem ${attached_3}" data-attach-id="${id}_3" data-attach-type="3"><span style="display: none;">Using:</span>3</button>
    </div>   

  </div>
</script>



<script type="text/template" data-template="designListItemText">
  <div class="list-group-item list-group-item-action align-items-start designListItem d-flex justify-content-between align-items-stretch" data-id="${id}">
    <div class="w-10 text-center sortable-handle"><i class="fa fa-arrows-v"></i></div>
    <div class="w-25 text-center"><button class="btn btn-primary btn-sm  pull-right loadTextForEdit"><i class="fa fa-pencil"></i></button>${data} </div>
    <div class="w-auto d-flex flex-column">
      <div class="row">
        <div class="col-11 offset-1">
          <h5 class="mb-1">
            <span class="index">${index}</span>. ${type}
          </h5>
        </div>
      </div>
      <div class="row">
        <div class="col-5 offset-1">
            <div class="form-check">
                <label class="form-check-label"> 
                <input type="checkbox" class="form-check-input designListInput" data-name="noHand" ${noHand} value="1" data-id="${id}">
                No hand</label>
            </div>            
        </div>
      </div>
      <div class="form-group row mb-0 mt-auto">
        <div class="col-3 offset-1 col-form-label text-right pr-0">
          Duration:
        </div>
        <div class="col-2 px-1">
          <input type="text" class="form-control form-control-sm designListInput" data-name="beforeNext" value="${beforeNext}" data-id="${id}">
        </div>
        <div class="col-4 col-form-label col-sec pl-0">
          Secs
        </div>
      </div>
    </div>
    <div class="w-auto">
        <span class="fa fa-times pull-right removeItemButton mb-1" data-id="${id}"></span>
        <button class="btn btn-block btn-sm btn-primary attachAudioToItem ${attached_1}" data-attach-id="${id}_1" data-attach-type="1"><span style="display: none;">Using:</span>1</button>
        <button class="btn btn-block btn-sm btn-primary attachAudioToItem ${attached_2}" data-attach-id="${id}_2" data-attach-type="2"><span style="display: none;">Using:</span>2</button>
        <button class="btn btn-block btn-sm btn-primary attachAudioToItem ${attached_3}" data-attach-id="${id}_3" data-attach-type="3"><span style="display: none;">Using:</span>3</button>
    </div>        
  </div>
</script>

<script type="text/template" data-template="designListItemImage">
  <div href="#" class="list-group-item list-group-item-action align-items-stretch designListItem d-flex justify-content-between" data-id="${id}">
    <div class="w-10 text-center sortable-handle"><i class="fa fa-arrows-v"></i></div>
    <div class="w-25 text-center">${data}</div>
    <div class="w-auto d-flex flex-column">
      <div class="row">
        <div class="col-11 offset-1">

          <h5 class="mb-1">
            <span class="index">${index}</span>. ${type}
          </h5>
        </div>
      </div>
      <div class="row">
        <div class="col-5 offset-1">
            <div class="form-check">
                <label class="form-check-label"> 
                <input type="checkbox" class="form-check-input designListInput" data-name="noHand" ${noHand} value="1" data-id="${id}">
                No hand</label>
            </div>            
        </div>
      </div>
      <div class="row">
        <div class="col-6 offset-1">
            <div class="form-check">
                <label class="form-check-label"> 
                <input type="checkbox" class="form-check-input designListInput" data-name="drawingEffect" ${drawingEffect} value="1" data-id="${id}">
                Drawing effect</label>
            </div>            
        </div>
      </div>
      <div class="form-group row mb-0 mt-auto">
        <div class="col-3 offset-1 col-form-label text-right pr-0">
          Duration:
        </div>
        <div class="col-2 px-1">
          <input type="text" class="form-control form-control-sm designListInput" data-name="beforeNext" value="${beforeNext}" data-id="${id}">
        </div>
        <div class="col-4 col-form-label col-sec pl-0">
          Secs
        </div>
      </div>      
    </div>
    <div class="w-auto">
        <span class="fa fa-times pull-right removeItemButton mb-1" data-id="${id}"></span>
        <button class="btn btn-block btn-sm btn-primary attachAudioToItem ${attached_1}" data-attach-id="${id}_1" data-attach-type="1"><span style="display: none;">Using:</span>1</button>
        <button class="btn btn-block btn-sm btn-primary attachAudioToItem ${attached_2}" data-attach-id="${id}_2" data-attach-type="2"><span style="display: none;">Using:</span>2</button>
        <button class="btn btn-block btn-sm btn-primary attachAudioToItem ${attached_3}" data-attach-id="${id}_3" data-attach-type="3"><span style="display: none;">Using:</span>3</button>
    </div>    
  </div>
</script>

<script type="text/template" data-template="audioListItem">
    <li class="list-group-item playlist-item" data-audio-url="${path}${file}">
        <div class="d-flex w-100 justify-content-between">
            <h5>${name}</h5>
            <div class="btn-toolbar" role="toolbar" aria-label="Track toolbar">
                <div class="btn-group btn-group-sm mr-3 ">
                    <button type="button" class="btn btn-secondary audio-add rounded">Add</button>
                    <button type="button" class="btn btn-danger audio-remove rounded" style="display: none;">Remove</button>
                </div>
                <div class="btn-group btn-group-sm" role="group" aria-label="Play controls">
                    <button type="button" class="btn btn-primary audio-play" title="Play" data-toggle="tooltip"><i class="fa fa-play"></i></button>
                    <button type="button" class="btn btn-primary audio-pause rounded-left" title="Pause" data-toggle="tooltip" style="display: none;"><i class="fa fa-pause"></i></button>
                    <button type="button" class="btn btn-primary audio-stop"  title="Stop" data-toggle="tooltip" disabled><i class="fa fa-stop"></i></button>
                </div>
            </div>
        </div>        
    </li>
</script>
<script type="text/template" data-template="audioListItemUser">
    <li class="list-group-item playlist-item" data-audio-url="${path}${file}">
        <div class="d-flex w-100 justify-content-between">
            <h5>${name}</h5>
            <div class="btn-toolbar" role="toolbar" aria-label="Track toolbar">
                <button type="button" class="btn btn-danger audio-delete rounded mr-1 btn-sm"><i class="fa fa-trash"></i></button>
                <div class="btn-group btn-group-sm mr-3 ">
                    <button type="button" class="btn btn-secondary audio-add rounded">Add</button>
                    <button type="button" class="btn btn-danger audio-remove rounded" style="display: none;">Remove</button>
                </div>
                <div class="btn-group btn-group-sm" role="group" aria-label="Play controls">
                    <button type="button" class="btn btn-primary audio-play" title="Play" data-toggle="tooltip"><i class="fa fa-play"></i></button>
                    <button type="button" class="btn btn-primary audio-pause rounded-left" title="Pause" data-toggle="tooltip" style="display: none;"><i class="fa fa-pause"></i></button>
                    <button type="button" class="btn btn-primary audio-stop"  title="Stop" data-toggle="tooltip" disabled><i class="fa fa-stop"></i></button>
                </div>
            </div>
        </div>        
    </li>
</script>
