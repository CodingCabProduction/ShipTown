<template>
    <container>
        <search-and-option-bar>
            <barcode-input-field :input_id="'barcode-input'" :url_param_name="'search'" @barcodeScanned="setCustomLabelText" placeholder="Enter custom label text" ref="barcode"/>
            <template v-slot:buttons>
                <top-nav-button v-b-modal="'quick-actions-modal'"/>
                <top-nav-button @click.native="printPDF" icon="print"/>
            </template>
        </search-and-option-bar>
        <div class="grid-col-12 pl-2 p-1">
            <div class="col-span-6 md:col-span-4 xl:col-span-6 mb-2 mb-sm-0">
                <header-upper>TOOLS > LABEL PRINTER</header-upper>
            </div>
            <div class="col-span-6 md:col-span-4 xl:col-span-4">
                <div class="col-span-8 d-flex justify-content-end justify-content-md-center justify-content-xl-end">
                    <header-upper class="small sd-none xs:sd-block">FROM:</header-upper>
                    <input type="text" v-model="fromLetter" @keyup="changeNonSearchValue" @focus="$selectAllInputText" class="form-control mx-1 inline-input-sm px-1 text-center"/>
                    <input type="text" v-model.number="fromNumber" @keyup="changeNonSearchValue" @focus="$selectAllInputText" class="form-control mx-1 inline-input-sm px-1 text-center"/>
                    <header-upper class="small">TO</header-upper>
                    <input type="text" v-model="toLetter" @keyup="changeNonSearchValue" @focus="$selectAllInputText" class="form-control mx-1 inline-input-sm px-1 text-center"/>
                    <input type="text" v-model.number="toNumber" @keydown.enter="setFocusElementById('barcode-input')" @keyup="changeNonSearchValue" @focus="$selectAllInputText" class="form-control mx-1 inline-input-sm px-1 text-center"/>
                </div>
            </div>
            <div class="col-span-12 xs:col-span-12 md:col-span-4 xl:col-span-2 d-flex justify-content-end">
                <array-dropdown-select class="ml-0 ml-sm-2" :items="templates" :item-selected.sync="templateSelected" :align-menu-right="true"/>
            </div>
        </div>
        <card class="mt-sm-2 bg-dark">
            <vue-pdf-embed ref="pdfRef" :source="pdfUrl" :page="null"/>
            <div v-if="previewLimited" class="text-center text-white">Preview limited to 25 labels</div>
        </card>

        <b-modal id="quick-actions-modal" no-fade hide-header @hidden="setFocusElementById('barcode-input')">
            <stocktake-input v-bind:auto-focus-after="100" ></stocktake-input>

            <hr>
            <b-button variant="primary" block @click="downloadPDF" :disabled="downloadInProgress">
                {{ downloadInProgress ? 'Please Wait...' : 'Download PDF' }}
            </b-button>
            <template #modal-footer>
                <b-button variant="secondary" class="float-right" @click="$bvModal.hide('quick-actions-modal');">
                    Cancel
                </b-button>
                <b-button variant="primary" class="float-right" @click="$bvModal.hide('quick-actions-modal');">
                    OK
                </b-button>
            </template>
        </b-modal>

    </container>
</template>

<script>

import url from "../../mixins/url.vue";
import helpers  from "../../helpers";
import helpersMixin from "../../mixins/helpers";
import VuePdfEmbed from 'vue-pdf-embed/dist/vue2-pdf-embed'
import api from "../../mixins/api";
import loadingOverlay from "../../mixins/loading-overlay";

export default {
    mixins: [loadingOverlay, url, helpersMixin, api],
    components: {
        VuePdfEmbed
    },
    data() {
        return {
            customLabelText: this.getUrlParameter('search', ''),
            fromLetter: this.getUrlParameter('from-letter', 'A'),
            fromNumber: this.getUrlParameter('from-number', 1),
            toLetter: this.getUrlParameter('to-letter', 'B'),
            toNumber: this.getUrlParameter('to-number', 2),
            viewDirectory: 'shelf-labels/',
            templates:[
                '6x4in-1-per-page',
                '4x6in-2-per-page',
                '6x4in-3-per-page',
                '57x32mm-1-per-page',
            ],
            templateSelected: '',
            pdfUrl: '',
            previewLimited: false,
            downloadInProgress: false,
        }
    },

    mounted() {
        this.templateSelected = this.getUrlParameter('template-selected', helpers.getCookie('templateSelected', this.templates[0]));
    },

    methods: {
        setCustomLabelText(text) {
            this.customLabelText = text;
            this.loadPdfIntoIframe();
        },

        downloadPDF() {
            this.downloadInProgress = true;
            this.buildUrl();

            let data = {
                data: { labels: this.getLabelArray() },
                template: this.viewDirectory + this.templateSelected,
            };

            this.apiPostPdfDownload(data).then(response => {
                let url = window.URL.createObjectURL(new Blob([response.data]));
                let filename = this.templateSelected.replace('/', '_') + '.pdf';
                helpers.downloadFile(url, filename);
            }).catch(error => {
                this.displayApiCallError(error);
            }).finally(() => {
                this.downloadInProgress = false;
            });
        },

        printPDF() {
            if(this.currentUser().printer_id === null){
                this.notifyError('Please select your printer on your profile page');
                return;
            }

            this.showLoading();
            this.buildUrl();

            let data = {
                data: { labels: this.getLabelArray() },
                template: this.viewDirectory + this.templateSelected,
                printer_id: this.currentUser().printer_id,
            };

            this.apiPostPdfPrint(data).then(() => {
                this.notifySuccess('PDF sent to printer');
            }).catch(error => {
                this.displayApiCallError(error);
            }).finally(() => {
                this.hideLoading();
                this.setFocusElementById('barcode-input')
            });
        },

        changeNonSearchValue() {
            this.customLabelText = '';
            this.loadPdfIntoIframe();
        },

        loadPdfIntoIframe() {
            this.showLoading();
            this.buildUrl();
            this.previewLimited = false;

            // clone label array and limit label array to 25 labels
            let labels = _.cloneDeep(this.getLabelArray());
            if (labels.length > 25) {
                this.previewLimited = true;
                labels = labels.slice(0, 25);
            }


            let data = {
                data: { labels: labels },
                template: this.viewDirectory + this.templateSelected,
            };

            this.apiPostPdfPreview(data)
                .then(response => {
                    let blob = new Blob([response.data], { type: 'application/pdf' });
                    this.pdfUrl = URL.createObjectURL(blob);
                }).catch(error => {
                    this.displayApiCallError(error);
                }).finally(() => {
                    this.hideLoading();
                });
        },

        buildUrl() {
            // for some reason, when we already have the search param in place it
            // will not update it like the others, so we need to remove it first
            this.removeUrlParameter('search');
            const params = this.customLabelText ?
                { 'search': this.customLabelText, 'template-selected': this.templateSelected } :
                {
                    'from-letter': this.fromLetter,
                    'from-number': this.fromNumber,
                    'to-letter': this.toLetter,
                    'to-number': this.toNumber,
                    'template-selected': this.templateSelected
                };
            this.updateUrl(params);
        },

        getLabelArray() {
            if (!this.allNumbersAndLettersFilled) return [];
            if (this.customLabelText) return [this.customLabelText];

            let labels = [];
            let fromLetter = this.fromLetter.toUpperCase().charCodeAt(0);
            let toLetter = this.toLetter.toUpperCase().charCodeAt(0);

            for (let i = fromLetter; i <= toLetter; i++) {
                for (let j = i === fromLetter ? this.fromNumber : 1; j <= this.toNumber; j++) {
                    labels.push(String.fromCharCode(i) + j);
                }
            }

            return labels;
        },
    },
    computed: {
        allNumbersAndLettersFilled() {
            return this.fromLetter && this.fromNumber && this.toLetter && this.toNumber;
        }
    },
    watch: {
        templateSelected() {
            helpers.setCookie('templateSelected', this.templateSelected);
            this.loadPdfIntoIframe();
        },
    },
}
</script>

<style scoped>
.inline-input-sm{
    max-width: 30px;
    height: 19px;
    padding: 0;
}
</style>
