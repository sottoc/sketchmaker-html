(function () {
  var $audioInLevel, $audioInSelect, $cancel, $dateTime, $encoding, $encodingOption, $modalError, $modalLoading, $modalProgress, $record, $recording, $recordingList, $timeDisplay, BUFFER_SIZE, ENCODING_OPTION, MP3_BIT_RATE, OGG_KBPS, OGG_QUALITY, URL, audioContext, audioIn, audioInLevel, audioRecorder, defaultBufSz, disableControlsOnRecord, encodingProcess, iDefBufSz, minSecStr, mixer, onChangeAudioIn, onError, onGotAudioIn, onGotDevices, optionValue, plural, progressComplete, saveRecording, setProgress, startRecording, stopRecording, updateBufferSizeText, updateDateTime;

  navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;

  URL = window.URL || window.webkitURL;

  audioContext = new (window.AudioContext
    || window.webkitAudioContext)();

  if (audioContext.createScriptProcessor == null) {
    audioContext.createScriptProcessor = audioContext.createJavaScriptNode;
  }

  $audioInSelect = $('#audio-in-select');

  $audioInLevel = $('#audio-in-level');

  $encoding = $('input[name="encoding"]');

  $encodingOption = $('#encoding-option');

  $recording = $('#recording');

  $timeDisplay = $('#AudioRecorderTimer');

  $record = $('#btnStartStop');

  $cancel = $('#cancel');

  $dateTime = $('#date-time');

  $recordingList = $('#recording-list');

  $modalLoading = $('#modal-loading');

  $modalProgress = $('#modal-progress');

  $modalError = $('#modal-error');

  $audioInLevel.attr('disabled', false);

  $audioInLevel[0].valueAsNumber = 1;

  $encoding.attr('disabled', false);

  $encoding[0].checked = true;

  audioInLevel = audioContext.createGain();

  audioInLevel.gain.value = 0.8;

  mixer = audioContext.createGain();

  audioIn = void 0;

  audioInLevel.connect(mixer);

  timer = null;
  //mixer.connect(audioContext.destination);

  audioRecorder = new WebAudioRecorder(mixer, {
    workerDir: 'js/',
    encoding: 'wav'
  });

  onGotDevices = function (devInfos) {
    var index, info, name, options, _i, _len;
    options = '<option value="no-input" selected>(No input)</option>';
    index = 0;
    for (_i = 0, _len = devInfos.length; _i < _len; _i++) {
      info = devInfos[_i];
      if (info.kind !== 'audioinput') {
        continue;
      }
      name = info.label || ("Audio in " + (++index));
      options += "<option value=" + info.deviceId + ">" + name + "</option>";
    }
    $audioInSelect.html(options);
  };

  onError = function (msg) {
    console.log(msg);
  };

  if ((navigator.mediaDevices != null) && (navigator.mediaDevices.enumerateDevices != null)) {
    navigator.mediaDevices.enumerateDevices().then(onGotDevices)["catch"](function (err) {
      return onError("Could not enumerate audio devices: " + err);
    });
  } else {
    $audioInSelect.html('<option value="no-input" selected>(No input)</option><option value="default-audio-input">Default audio input</option>');
  }

  onGotAudioIn = function (stream) {
    if (audioIn != null) {
      audioIn.disconnect();
    }
    audioIn = audioContext.createMediaStreamSource(stream);
    audioIn.connect(audioInLevel);
    return;
  };

  onChangeAudioIn = function () {
    var constraint, deviceId;
    deviceId = $audioInSelect[0].value;
    if (deviceId === 'no-input') {
      if (audioIn != null) {
        audioIn.disconnect();
      }
      audioIn = void 0;
      $audioInLevel.addClass('hidden');
    } else {
      if (deviceId === 'default-audio-input') {
        deviceId = void 0;
      }
      constraint = {
        video: false,
        audio: {
          deviceId: deviceId != null ? {
            exact: deviceId
          } : void 0
        }
      };
      if ((navigator.mediaDevices != null) && (navigator.mediaDevices.getUserMedia != null)) {
        navigator.mediaDevices.getUserMedia(constraint).then(onGotAudioIn)["catch"](function (err) {
          return onError("Could not get audio media device: " + err);
        });
      } else {
        navigator.getUserMedia(constraint, onGotAudioIn, function () {
          return onError("Could not get audio media device: " + err);
        });
      }
    }
  };

  $audioInSelect.on('change', onChangeAudioIn);

  plural = function (n) {
    if (n > 1) {
      return 's';
    } else {
      return '';
    }
  };


  OGG_QUALITY = [-0.1, 0.0, 0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1.0];

  OGG_KBPS = [45, 64, 80, 96, 112, 128, 160, 192, 224, 256, 320, 500];

  MP3_BIT_RATE = [64, 80, 96, 112, 128, 160, 192, 224, 256, 320];

  ENCODING_OPTION = {
    wav: {
      label: '',
      hidden: true,
      max: 1,
      text: function (val) {
        return '';
      }
    },
    ogg: {
      label: 'Quality',
      hidden: false,
      max: OGG_QUALITY.length - 1,
      text: function (val) {
        return "" + (OGG_QUALITY[val].toFixed(1)) + " (~" + OGG_KBPS[val] + "kbps)";
      }
    },
    mp3: {
      label: 'Bit rate',
      hidden: false,
      max: MP3_BIT_RATE.length - 1,
      text: function (val) {
        return "" + MP3_BIT_RATE[val] + "kbps";
      }
    }
  };

  optionValue = {
    wav: null,
    ogg: 6,
    mp3: 5
  };

  $encoding.on('click', function (event) {
    var encoding, option;
    encoding = $(event.target).attr('encoding');
    audioRecorder.setEncoding(encoding);
    option = ENCODING_OPTION[encoding];
    $('#encoding-option-label').html(option.label);
    $('#encoding-option-text').html(option.text(optionValue[encoding]));
    $encodingOption.toggleClass('hidden', option.hidden).attr('max', option.max);
    $encodingOption[0].valueAsNumber = optionValue[encoding];
  });

  $encodingOption.on('input', function () {
    var encoding, option;
    encoding = audioRecorder.encoding;
    option = ENCODING_OPTION[encoding];
    optionValue[encoding] = $encodingOption[0].valueAsNumber;
    $('#encoding-option-text').html(option.text(optionValue[encoding]));
  });

  encodingProcess = 'background';

  defaultBufSz = (function () {
    var processor;
    processor = audioContext.createScriptProcessor(void 0, 2, 2);
    return processor.bufferSize;
  })();

  saveRecording = function (blob, enc) {
    //upload
    audioItemUploader.addBlobs(blob);
  };

  $recordingList.on('click', 'button', function (event) {
    var url;
    url = $(event.target).attr('recording');
    $("p[recording='" + url + "']").remove();
    URL.revokeObjectURL(url);
  });

  minSecStr = function (n) {
    return (n < 10 ? "0" : "") + n;
  };

  updateDateTime = function () {
    var sec;
    $dateTime.html((new Date).toString());
    sec = (15 - audioRecorder.recordingTime()) | 0;
    $timeDisplay.html("" + (minSecStr(sec / 60 | 0)) + ":" + (minSecStr(sec % 60)));
  };

  $timeDisplay.html("00:15");
  progressComplete = false;

  setProgress = function (progress) {
    var percent;
    percent = "" + ((progress * 100).toFixed(1)) + "%";
    $modalProgress.find('.progress-bar').attr('style', "width: " + percent + ";");
    $modalProgress.find('.text-center').html(percent);
    progressComplete = progress === 1;
  };

  $modalProgress.on('hide.bs.modal', function () {
    if (!progressComplete) {
      audioRecorder.cancelEncoding();
    }
  });

  disableControlsOnRecord = function (disabled) {
    $audioInSelect.attr('disabled', disabled);
  };

  startRecording = function () {
    $record.html('STOP');
    disableControlsOnRecord(true);
    audioRecorder.setOptions({
      timeLimit: 15,
      encodeAfterRecord: false,
      progressInterval: 1000
    });
    audioRecorder.startRecording();
    timer = window.setInterval(updateDateTime, 200);
  };

  stopRecording = function (finish) {
    $record.html('RECORD');
    clearInterval(timer);
    disableControlsOnRecord(false);
    if (finish) {
      audioRecorder.finishRecording();
      if (audioRecorder.options.encodeAfterRecord) {
        $modalProgress.find('.modal-title').html("Encoding " + (audioRecorder.encoding.toUpperCase()));
        $modalProgress.modal('show');
      }
    } else {
      audioRecorder.cancelRecording();
    }
  };

  $record.on('click', function () {
    if (audioRecorder.isRecording()) {
      stopRecording(true);
    } else {
      startRecording();
    }
  });

  audioRecorder.onTimeout = function (recorder) {
    stopRecording(true);
  };


  audioRecorder.onComplete = function (recorder, blob) {
    if (recorder.options.encodeAfterRecord) {
      $modalProgress.modal('hide');
    }
    saveRecording(blob, recorder.encoding);
  };

  audioRecorder.onError = function (recorder, message) {
    onError(message);
  };

}).call(this);
