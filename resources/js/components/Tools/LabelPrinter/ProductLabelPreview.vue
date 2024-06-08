<template>
    <container>
        <search-and-option-bar>
            <array-dropdown-select class="pt-2 ml-0 ml-sm-2" :items="templates" :item-selected.sync="templateSelected" :align-menu-right="true"/>
            <template v-slot:buttons>
<!--                <top-nav-button v-b-modal="'quick-actions-modal'"/>-->
                <top-nav-button @click.native="printPDF" icon="print"/>
            </template>
        </search-and-option-bar>

        <card class="bg-dark">
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

import url from "../../../mixins/url.vue";
import helpers  from "../../../helpers";
import helpersMixin from "../../../mixins/helpers";
import VuePdfEmbed from 'vue-pdf-embed/dist/vue2-pdf-embed'
import api from "../../../mixins/api";
import loadingOverlay from "../../../mixins/loading-overlay";

export default {
    mixins: [loadingOverlay, url, helpersMixin, api],

    components: {VuePdfEmbed},

    props: {
        product: Object,
    },

    data() {
        return {
            viewDirectory: 'product-labels/',
            templates: [],
            templateSelected: '',
            pdfUrl: '',
            previewLimited: false,
            downloadInProgress: false,
        }
    },

    mounted() {
        this.templates = [
            '57x32mm_Price_Tag',
            '57x32mm_Barcode_Label',
            '4x6in_Price_Tag',
            '4x6in_Barcode_Label',
        ];

        this.templateSelected = this.getUrlParameter('template-selected', helpers.getCookie('templateSelected', this.templates[0]));
    },

    methods: {
        downloadPDF() {
            this.downloadInProgress = true;

            let data = {
                data: { product_sku: this.getUrlParameter('search') },
                template: this.templateSelected,
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

            let data = {
                data: { product_sku: this.getUrlParameter('search') },
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

        loadPdfIntoIframe() {
            this.showLoading();
            this.previewLimited = false;

            // clone label array and limit label array to 25 labels
            let labels = _.cloneDeep(this.getLabelArray());
            if (labels.length > 25) {
                this.previewLimited = true;
                labels = labels.slice(0, 25);
            }

            let data = {
                data: { product_sku: this.product['sku'] },
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
