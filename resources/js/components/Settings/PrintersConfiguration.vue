<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        Printer Selection
                    </span>
                </div>
            </div>
            <div class="card-body">

                <div class="row mb-2" v-for="printer in printers" :key="printer['id']" :class="{'table-primary': isDefaultPrinter(printer['id'])}">
                    <div class="col-lg-6 col-md-12">
                        {{ printer['name'] }} <br>
                        <span class="small text-secondary">
                                    {{ printer['computer']['name'] }}<br>
                                    <status-icon :status="printer['state'] === 'online'" class="small" /> {{ printer['id'] }}
                                </span>
                    </div>
                    <div class="col text-right">
                        <button type="button" @click.prevent="printTestPage(printer)" class="btn btn-primary btn-sm mb-1">PDF Test</button>
                        <button type="button" @click.prevent="printEplTestPage(printer)" class="btn btn-primary btn-sm mb-1">EPL Test</button>
                        <button type="button" @click.prevent="printRawTestPage(printer)" class="btn btn-primary btn-sm mb-1">Raw Text Test</button>
                        <button type="button" @click.prevent="setUserPrinter(printer['id'])" v-if="!isDefaultPrinter(printer['id'])" class="btn btn-primary btn-sm mb-1">Use</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import api from "../../mixins/api";
import Vue from "vue";
import helpers from "../../mixins/helpers";
import StatusIcon from "./Automations/StatusIcon";

export default {
    components: {
        'status-icon': StatusIcon
    },

    mixins: [api, helpers],

    mounted() {
        this.defaultPrinter = Vue.prototype.$currentUser['printer_id'];

        this.apiGetPrintNodePrinters()
            .then(({ data }) => {
                this.printers = data.data;
            })
            .catch(e => {
                this.displayApiCallError(e);
            });
    },

    data: () => ({
        defaultPrinter: 0,
        printers: [],
        errorMessage: null,
    }),

    methods: {
        setUserPrinter(printerId) {
            this.apiPostUserMe({
                    'printer_id': printerId
                })
                .then(({ data }) => {
                    this.defaultPrinter = data.data.printer_id;
                })
                .catch(e => {
                    this.displayApiCallError(e);
                });
        },

        printRawTestPage(printer) {
            let data = {
                'printer_id': printer.id,
                'content_type': 'raw_base64',
                'content': btoa('Hello World/n' +
                    'blue rectangle/n' +
                    'A50,50'
                ),
            };

            this.apiPostPrintnodePrintJob(data)
                .then(() => {
                    this.$snotify.info('Test page sent to PrintNode');
                })
                .catch(e => {
                    this.displayApiCallError(e);
                });
        },

        printTestPage(printer) {
            let data = {
                'printer_id': printer.id,
                'pdf_url': 'https://api.printnode.com/static/test/pdf/label_4in_x_6in.pdf'
            };

            this.apiPostPrintnodePrintJob(data)
                .then(() => {
                    this.$snotify.info('Test page sent to PrintNode');
                })
                .catch(e => {
                    this.displayApiCallError(e);
                });
        },

        printEplTestPage(printer) {
            let data = {
                'printer_id': printer.id,
                'content_type': 'raw_base64',
                'base64raw_url': 'http://management.products.api.test/label_4in_x_6in.epl'
            };

            this.apiPostPrintnodePrintJob(data)
                .then(() => {
                    this.$snotify.info('Test page sent to PrintNode');
                })
                .catch(e => {
                    this.displayApiCallError(e);
                });
        },

        isDefaultPrinter(printerId) {
            return this.defaultPrinter != null && printerId === this.defaultPrinter;
        }
    }
}
</script>
