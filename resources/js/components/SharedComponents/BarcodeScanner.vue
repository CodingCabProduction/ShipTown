<template>
  <b-modal :id="getScannerModalID" @show="modalShown" @hide="modalHidden" hide-footer hide-header no-fade>
      <div id="qr-code-full-region" style="height: 250px; overflow: hidden"></div>

      <div class="row mb-2 mt-2">
          <div class="col">
              <div class="setting-title">On Screen Button</div>
              <div class="setting-desc">Shows camera barcode scanner helper button</div>
          </div>
          <div class="custom-control custom-switch m-auto text-right align-content-center float-right w-auto">
              <input type="checkbox" @change="toggleOnScreenScannerButton" class="custom-control-input" id="toggleOnScreenScannerButton" v-model="showOnScreenScannerButton">
              <label class="custom-control-label" for="toggleOnScreenScannerButton"></label>
          </div>
      </div>

      <select name="camera" id="cameraSelection" @change="changeCamera" class="form-control mt-2">
          <option v-for="camera in availableCameras" :value="camera['id']" :selected="camera['label'] === selectedCamera">{{ camera['label']}}</option>
      </select>

      <button @click="$bvModal.hide(getScannerModalID)" class="form-control btn">close</button>
  </b-modal>
</template>
<script>
import {Html5Qrcode, Html5QrcodeScannerState} from "html5-qrcode";
import Modals from "../../plugins/Modals";
import helpers from "../../helpers";

export default {
  name: 'barcode-scanner',
    mixins: [helpers],
    data() {
        return {
            html5QrcodeScanner: null,
            availableCameras: [],
            getScannerModalID: 'barcode-scanner-modal',
            onScanSuccess: null,
            selectedCamera: null,
            showOnScreenScannerButton: true,
        }
    },

    beforeMount() {
        Modals.EventBus.$on('show::modal::barcode-scanner', (data) => {
            this.onScanSuccess = data.callback;
            this.$bvModal.show(this.getScannerModalID);
        })
    },

    methods: {
        toggleOnScreenScannerButton() {
            localStorage.showOnScreenScannerButton = this.showOnScreenScannerButton;
            // this.stopScanner();
            this.showOnScreenScannerButton = !this.showOnScreenScannerButton;
            helpers.setCookie('showOnScreenScannerButton', this.showOnScreenScannerButton, 365);
            // this.$bvModal.hide(this.getScannerModalID);
        },

        modalHidden() {
            this.$emit('modalHidden');
            this.stopScanner();
        },

        modalShown() {
            if (localStorage.showOnScreenScannerButton) {
                this.showOnScreenScannerButton = localStorage.showOnScreenScannerButton === 'true';
            }

            if (this.availableCameras.length === 0) {
                Html5Qrcode.getCameras()
                    .then((cameras) => {
                        this.availableCameras = cameras;
                        this.selectedCamera = this.availableCameras[cameras.length - 1]['id'];
                        this.startScanner(null);
                    });
            } else {
                this.startScanner(null);
            }
        },

        changeCamera() {
            this.stopScanner();
            this.startScanner(document.getElementById('cameraSelection').value);
        },

        onScanSuccessCallback(qrCodeMessage) {
            // this.$emit('onScanSuccess', qrCodeMessage);
            this.stopScanner();
            this.$bvModal.hide(this.getScannerModalID);
            this.$nextTick(() => {
                this.onScanSuccess(qrCodeMessage);
            });
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
                this.html5QrcodeScanner.start(selectedCamera, config, this.onScanSuccessCallback);
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
