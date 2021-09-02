class MediaControl {
    static get MEDIA_TYPE_MIC()         { return "audioinput"   }
    static get MEDIA_TYPE_AUDIO()       { return "audiooutput"  }
    static get MEDIA_TYPE_CAMERA()      { return "videoinput"   }

    checkGetableDevice() {
        if ('mediaDevices' in navigator && 'getUserMedia' in navigator.mediaDevices) {
            return true;
        }
        return false;
    }

    async getAllConnectedDevices() {
        if (!this.checkGetableDevice()) {
            return null;
        }

        var devices = await navigator.mediaDevices.enumerateDevices()
        return devices;
    }

    async getConnectedDevicesBy(type) {
        if (!this.checkGetableDevice()) {
            return null;
        }

        var devices = await this.getAllConnectedDevices();
        var selectDevice = [];
        devices.forEach(function(device) {
            if (device.kind == type && device.deviceId != 'default') selectDevice.push(device);
        });

        return selectDevice;
    }

    async getAvailableDevices() {
        if (!this.checkGetableDevice()) {
            return null;
        }

        var result = {
            camera : false,
            audio : false,
            mic : false,
        };

        var cameraInfo = await this.getConnectedDevicesBy(MediaControl.MEDIA_TYPE_CAMERA);
        if (cameraInfo.length != 0) {
            result.camera = true;
        }

        var audioInfo = await this.getConnectedDevicesBy(MediaControl.MEDIA_TYPE_AUDIO);
        if (audioInfo.length != 0) {
            result.audio = true;
        }

        var micInfo = await this.getConnectedDevicesBy(MediaControl.MEDIA_TYPE_MIC);
        if (micInfo.length != 0) {
            result.mic = true;
        }

        return result;
    }

    /*
     * https://www.twilio.com/blog/choosing-cameras-javascript-mediadevices-api-html
     * https://dev.to/morinoko/stopping-a-webcam-with-javascript-4297
     * videoConstraints.deviceId = { exact: "abcdfefasdfawdfasdf" };
     * const constraints = {
          video: videoConstraints,
          audio: false
     * };
    */
    async getStreamBy(constraints) {
        if (!this.checkGetableDevice()) {
            return null;
        }

        var selectStream = null;
        await navigator.mediaDevices
        .getUserMedia(constraints)
        .then(stream => {
            selectStream = stream;
        })
        .catch(error => {
            console.error(error);
        });

        return selectStream;
    }

    muteVideo(stream) {
        var videoTrack = stream.getVideoTracks()[0];
        videoTrack.stop;
    }
    unmuteVideo(stream) {
        var videoTrack = stream.getVideoTracks()[0];
        videoTrack.start();
    }

    //https://www.pazru.net/html5/Video/040.html
    muteAudio(stream) {
        var audioTrack = stream.getAudioTracks()[0];
        audioTrack.enabled = false
    }
    unmuteAudio(stream) {
        var audioTrack = stream.getAudioTracks()[0];
        audioTrack.enabled = true
    }

}