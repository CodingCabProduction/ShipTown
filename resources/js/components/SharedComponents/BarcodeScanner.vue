<template>
  <b-modal :id="getScannerModalID" @show="startScanner(null)" @hide="stopScanner" hide-footer hide-header no-fade>
    <div id="qr-code-full-region" style="height: 250px; overflow: hidden"></div>
    <select name="camera" id="cameraSelection" @change="changeCamera" class="mt-2">
      <option v-for="camera in availableCameras" :value="camera['id']">{{ camera['label']}}</option>
    </select>
  </b-modal>
</template>
<script>
import {Html5Qrcode, Html5QrcodeScannerState} from "html5-qrcode";

export default {
  name: 'barcode-scanner',
    props: {
        getScannerModalID: {},
    },

    data() {
        return {
            html5QrcodeScanner: null,
            availableCameras: [],
            getScannerModalID: 'barcode-scanner-modal',
        }
    },

    mounted() {
        Html5Qrcode.getCameras()
            .then((cameras) => {
                this.availableCameras = cameras;
            });
    },

    methods: {
        changeCamera() {
            this.stopScanner();
            this.startScanner(document.getElementById('cameraSelection').value);
        },

        onScanSuccess(qrCodeMessage) {
            this.$emit('onScanSuccess', qrCodeMessage);
            this.stopScanner();
            this.$bvModal.hide(this.getScannerModalID);
        },

        stopScanner() {
            if (this.html5QrcodeScanner.getState() === Html5QrcodeScannerState.SCANNING) {
                this.html5QrcodeScanner.stop();
            }
        },

        startScanner(camera = null) {
            setTimeout(() => {
                if (this.html5QrcodeScanner === null) {
                    this.html5QrcodeScanner = new Html5Qrcode('qr-code-full-region');
                }

                let config = {
                    // aspectRatio: 2,
                    fps: 10,
                    qrbox: {width: 300, height: 300},
                    useBarCodeDetectorIfSupported: true
                };

                const selectedCamera = camera ? camera : this.availableCameras[this.availableCameras.length - 1]['id'];
                this.html5QrcodeScanner.start(selectedCamera, config, this.onScanSuccess);
            }, 10);
        },
  }
}
</script>
<style scoped>

.input-wrapper input {
  padding-right: 30px;
}

.input-wrapper button {
  position: absolute;
  top: 0;
  right: 0;
  border: none;
  background-color: transparent;
  cursor: pointer;
}
</style>
