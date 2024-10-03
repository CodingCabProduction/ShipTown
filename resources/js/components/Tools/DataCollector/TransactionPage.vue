<template>
    <div v-if="dataCollection">
        <template v-if="dataCollection && dataCollection['currently_running_task'] != null">
            <div class="alert alert-danger">Please wait while stock being updated</div>
        </template>

        <swiping-card>
            <template v-slot:content>
                <div class="row setting-list">
                    <div class="col-sm-12 col-lg-3">
                        <div id="data_collection_name" class="text-primary">{{ dataCollection['name'] }}</div>
                        <div class="text-secondary small">
                            {{ formatDateTime(dataCollection['created_at'], 'dddd - MMM D HH:mm') }}
                        </div>
                        <div class="text-secondary small">{{ collectionTypes[dataCollection['type']] }}</div>
                    </div>
                    <div v-if="dataCollection['billing_address']" class="col-sm-12 col-lg-3">
                        <div class="text-primary">
                            Selected Billing Address
                        </div>
                        <div class="text-secondary small">
                            {{ dataCollection['billing_address']['gender'] ?? '' }}
                            {{ dataCollection['billing_address']['first_name'] ?? '' }}
                            {{ dataCollection['billing_address']['last_name'] ?? '' }}
                            <br>
                            {{ dataCollection['billing_address']['company'] ?? '' }}
                            <br>
                            {{ dataCollection['billing_address']['address1'] ?? '' }}
                            <br>
                            {{ dataCollection['billing_address']['address2'] ?? '' }}
                            <br>
                            {{ dataCollection['billing_address']['city'] ?? '' }}
                            {{ dataCollection['billing_address']['postcode'] ?? '' }}
                            <br>
                            {{ dataCollection['billing_address']['country_name'] ?? '' }}
                        </div>
                    </div>
                    <div v-if="dataCollection['shipping_address']" class="col-sm-12 col-lg-3">
                        <div class="text-primary">
                            Selected Shipping Address
                        </div>
                        <div class="text-secondary small">
                            {{ dataCollection['shipping_address']['gender'] ?? '' }}
                            {{ dataCollection['shipping_address']['first_name'] ?? '' }}
                            {{ dataCollection['shipping_address']['last_name'] ?? '' }}
                            <br>
                            {{ dataCollection['shipping_address']['company'] ?? '' }}
                            <br>
                            {{ dataCollection['shipping_address']['address1'] ?? '' }}
                            <br>
                            {{ dataCollection['shipping_address']['address2'] ?? '' }}
                            <br>
                            {{ dataCollection['shipping_address']['city'] ?? '' }}
                            {{ dataCollection['shipping_address']['postcode'] ?? '' }}
                            <br>
                            {{ dataCollection['shipping_address']['country_name'] ?? '' }}
                        </div>
                    </div>
                    <div class="col-sm-12 col-lg-4" v-if="dataCollection && dataCollection['deleted_at']">
                        <text-card class="fa-pull-right"
                                   :label="formatDateTime(dataCollection ? dataCollection['deleted_at'] : '', 'dddd - MMM D HH:mm')"
                                   text="ARCHIVED"></text-card>
                    </div>
                    <div>
                        <number-card :label="'quantity'"
                                     :number="dataCollection && dataCollection['total_quantity_scanned']"></number-card>
                        <number-card :label="'total to pay'"
                                     :number="dataCollection && dataCollection['total_sold_price']"></number-card>
                        <number-card :label="'total paid'"
                                     :number="dataCollection && dataCollection['total_paid']"></number-card>
                        <number-card :label="'total outstanding'"
                                     :number="dataCollection && dataCollection['total_outstanding']"></number-card>
                    </div>
                </div>
            </template>
        </swiping-card>

        <search-and-option-bar-observer/>
        <search-and-option-bar :isStickable="true">
            <div class="d-flex flex-nowrap">
                <div class="flex-fill">
                    <barcode-input-field :input_id="'barcode_input'"
                                         :showManualSearchButton="true"
                                         @barcodeScanned="onBarcodeScanned"
                                         @findBarcodeManually="onBarcodeScanned"
                                         placeholder="Scan sku or alias"
                                         class="text-center font-weight-bold">
                    </barcode-input-field>
                </div>
            </div>
            <template v-slot:buttons>
                <top-nav-button v-b-modal="'configuration-modal'"/>
            </template>
        </search-and-option-bar>

        <div v-show="manuallyExpandComments" class="row mb-2 mt-1 my-1">
            <input id="comment-input" ref="newCommentInput" v-model="input_comment" class="form-control"
                   placeholder="Add comment here" @keypress.enter="addComment"/>
        </div>

        <div class="mb-1" v-if="commentsToShow.length">
            <div class="d-flex mx-1" v-for="(comment, index) in commentsToShow" @click="toggleExpandComments">
                <div>
                    <b>{{ comment.user ? comment.user.name : 'AutoPilot' }}: </b>{{ comment.comment }}
                </div>
                <div class="ml-auto" v-if="index === 0">
                    <font-awesome-icon v-if="manuallyExpandComments" icon="chevron-up"
                                       class="fa fa-xs"></font-awesome-icon>
                    <font-awesome-icon v-if="!manuallyExpandComments" icon="chevron-down"
                                       class="fa fa-xs"></font-awesome-icon>
                </div>
            </div>
        </div>
        <div v-else class="row text-center text-secondary" @click="toggleExpandComments">
            <div class="col">
                <font-awesome-icon v-if="manuallyExpandComments" icon="chevron-up" class="fa fa-xs"></font-awesome-icon>
                <font-awesome-icon v-if="!manuallyExpandComments" icon="chevron-down"
                                   class="fa fa-xs"></font-awesome-icon>
            </div>
        </div>

        <data-collector-quantity-request-modal
            @hidden="onQuantityRequestModalHidden"></data-collector-quantity-request-modal>

        <div v-if="(dataCollectionRecords !== null) && (dataCollectionRecords.length === 0)"
             class="text-secondary small text-center mt-3">
            No records found<br>
            Scan or type in SKU to start<br>
        </div>

        <template v-for="record in dataCollectionRecords">
            <swiping-card :disable-swipe-right="true" :disable-swipe-left="true">
                <template v-slot:content>
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <product-info-card :product="record['product']" :show-tags="false"></product-info-card>
                        </div>
                        <div class="col-12 col-md-3 text-left small">
                            <div>in stock:
                                <strong>{{ dashIfZero(Number(record['inventory']['quantity'])) }}</strong>
                            </div>
                            <div v-if="record['price_source'] !== 'FULL_PRICE'">full price:
                                <strong>{{ dashIfZero(Number(record['unit_full_price'])) }}</strong
                                ></div>
                            <div v-if="record['price_source'] && record['price_source'] !== 'FULL_PRICE'">price source:
                                <strong>{{ record['price_source'] }}</strong>
                            </div>
                            <div v-if="record['discount']">discount name:
                                <strong>{{ record['discount']['name'] ?? '' }}</strong>
                            </div>
                        </div>
                        <div class="col-12 col-md-5 text-right">
                            <number-card label="quantity" :number="record['quantity_scanned']"
                                         v-bind:class="{'bg-warning': record['quantity_scanned'] > 0 && record['quantity_requested'] &&  record['quantity_requested'] < record['quantity_scanned'] + record['total_transferred_out'] + record['total_transferred_in']}"></number-card>
                            <number-card label="unit price" :number="record['unit_sold_price']"
                                         v-bind:class="{'bg-warning': record['unit_discount'] > 0 }"></number-card>
                            <number-card label="total price" :number="record['total_price']"></number-card>
                        </div>
                    </div>
                </template>
            </swiping-card>
        </template>

        <div class="row">
            <div class="col">
                <div ref="loadingContainerOverride" style="height: 32px"></div>
            </div>
        </div>

        <b-modal id="configuration-modal" no-fade hide-header
                 @shown="onShownConfigurationModal"
                 @hidden="setFocusElementById('barcode_input')"
        >
            <div v-if="dataCollection">
                <stocktake-input></stocktake-input>
                <div v-if="dataCollection['deleted_at'] === null" :class="{ 'disabled': true }">
                    <hr>
                    <div class="row mb-2">
                        <div class="col">
                            <div class="setting-title">Single Scan mode</div>
                            <div class="setting-desc">It will not ask for quantity when scanned <br> 1 will be used as
                                default
                            </div>
                        </div>
                        <div
                            class="custom-control custom-switch m-auto text-right align-content-center float-right w-auto">
                            <input type="checkbox" @change="toggleSingleScanMode" class="custom-control-input"
                                   id="singleScanToggle" v-model="singleScanEnabled">
                            <label class="custom-control-label" for="singleScanToggle"></label>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <div class="setting-title">Scan into Quantity Requested</div>
                            <div class="setting-desc">When product scanned, quantity requested will be amended <br>
                                instead of quantity scanned
                            </div>
                        </div>
                        <div
                            class="custom-control custom-switch m-auto text-right align-content-center float-right w-auto">
                            <input type="checkbox" @change="toggleAddToRequested" class="custom-control-input"
                                   id="toggleAddToRequested" v-model="addToRequested">
                            <label class="custom-control-label" for="toggleAddToRequested"></label>
                        </div>
                    </div>
                    <hr>
                    <div v-if="selectedPrinter" class="row mb-2">
                        <div class="col">
                            <div class="setting-title">Selected Printer</div>
                            <div class="setting-desc">{{ selectedPrinter.name }}</div>
                        </div>
                    </div>
                    <button :disabled="! buttonsEnabled" @click.prevent="selectPrinter" v-b-toggle
                            class="col btn mb-2 btn-primary">
                        <template v-if="selectedPrinter">Change printer</template>
                        <template v-else>Select printer</template>
                    </button>
                    <button :disabled="! buttonsEnabled" @click.prevent="selectCustomer" v-b-toggle
                            class="col btn mb-2 btn-primary">Select Customer
                    </button>
                    <button :disabled="! buttonsEnabled" @click.prevent="selectPayment" v-b-toggle
                            class="col btn mb-2 btn-primary">Payment
                    </button>
                    <button :disabled="! buttonsEnabled" @click.prevent="autoScanAll" v-b-toggle
                            class="col btn mb-2 btn-primary">AutoScan ALL Records
                    </button>
                    <br>
                    <br>
                    <button id="transferInButton" :disabled="! buttonsEnabled" @click.prevent="transferStockIn"
                            v-b-toggle class="col btn mb-2 btn-primary">Transfer In
                    </button>
                    <button :disabled="! buttonsEnabled" @click.prevent="transferToWarehouseClick" v-b-toggle
                            class="col btn mb-2 btn-primary">Transfer To...
                    </button>
                    <button :disabled="! buttonsEnabled" @click.prevent="archiveTransaction" v-b-toggle
                            class="col btn mb-2 btn-primary">Archive Transaction
                    </button>
                    <button :disabled="! buttonsEnabled" @click.prevent="saveTransaction" v-b-toggle
                            class="col btn mb-2 btn-primary">Save Transaction
                    </button>
                    <br>
                    <br>
                    <button :disabled="! buttonsEnabled" @click.prevent="printReceipt" v-b-toggle
                            class="col btn mb-2 btn-primary">Print Receipt
                    </button>
                    <button :disabled="! buttonsEnabled" @click.prevent="emailPdfReceipt" v-b-toggle
                            class="col btn mb-2 btn-primary">Email Receipt
                    </button>
                </div>
                <br>
                <a :href="getDownloadLink" @click.prevent="downloadFileAndHideModal" v-b-toggle
                   class="col btn mb-1 btn-primary">Download</a>
                <div v-if="dataCollection['deleted_at'] === null">
                    <hr>
                    <vue-csv-import
                        v-model="csv"
                        headers
                        canIgnore
                        autoMatchFields
                        loadBtnText="Load"
                        :map-fields="['product_sku', 'quantity_requested', 'quantity_scanned']">

                        <template slot="hasHeaders" slot-scope="{headers, toggle}">
                            <label>
                                <input type="checkbox" id="hasHeaders" :value="headers" @change="toggle">
                                Headers?
                            </label>
                        </template>

                        <template slot="error">
                            File type is invalid
                        </template>

                        <template slot="thead">
                            <tr>
                                <th>My Fields</th>
                                <th>Column</th>
                            </tr>
                        </template>

                        <template slot="submit" slot-scope="{submit}">
                            <button @click.prevent="submit">send!</button>
                        </template>
                    </vue-csv-import>

                    <button v-if="csv" type="button" @click.prevent="postCsvRecordsToApiAndCloseModal"
                            class="col btn mb-1 btn-primary">Import Records
                    </button>
                </div>
            </div>

            <template #modal-footer>
                <b-button variant="secondary" class="float-right" @click="$bvModal.hide('configuration-modal');">
                    Cancel
                </b-button>
                <b-button variant="primary" class="float-right" @click="$bvModal.hide('configuration-modal');">
                    OK
                </b-button>
            </template>
        </b-modal>

        <b-modal id="transferToModal" no-fade hide-header @hidden="setFocusElementById('barcode_input')">
            <template v-for="warehouse in warehouses">
                <button @click.prevent="transferToWarehouse(warehouse)"
                        v-if="dataCollection && warehouse['id'] !== dataCollection['warehouse_id']" v-b-toggle
                        class="col btn mb-2 btn-primary">{{ warehouse.name }}
                </button>
            </template>

            <template #modal-footer>
                <b-button variant="secondary" class="float-right" @click="$bvModal.hide('transferToModal');">Cancel
                </b-button>
            </template>
        </b-modal>

        <set-transaction-printer-modal/>
        <find-address-modal :transaction-details="dataCollection"/>
        <new-address-modal/>
        <data-collection-choose-payment-type-modal/>
        <data-collection-data-collection-add-payment-modal/>
    </div>
</template>

<script>
import beep from '../../../mixins/beep';
import loadingOverlay from '../../../mixins/loading-overlay';

import FiltersModal from "./../../Packlist/FiltersModal";
import url from "../../../mixins/url";
import api from "../../../mixins/api";
import helpers from "../../../mixins/helpers";
import Vue from "vue";
import NumberCard from "./../../SharedComponents/NumberCard";
import SwipingCard from "./../../SharedComponents/SwipingCard";
import {VueCsvImport} from 'vue-csv-import';
import Modals from "../../../plugins/Modals";

export default {
    mixins: [loadingOverlay, beep, url, api, helpers],

    components: {
        FiltersModal,
        NumberCard,
        SwipingCard,
        VueCsvImport,
    },

    props: {
        data_collection_id: null,
    },

    data() {
        return {
            minShelfLocation: '',
            singleScanEnabled: true,
            addToRequested: false,
            scannedDataCollectionRecord: null,
            scannedProduct: null,
            dataCollection: null,
            dataCollectionRecords: [],
            nextUrl: null,
            page: 1,
            per_page: 50,
            csv: null,
            warehouses: [],
            buttonsEnabled: false,
            selectedInventoryId: null,
            manuallyExpandComments: false,
            input_comment: '',
            collectionTypes: {
                'App\\Models\\DataCollectionTransferIn': 'Transfer In',
                'App\\Models\\DataCollectionTransferOut': 'Transfer Out',
                'App\\Models\\DataCollectionStocktake': 'Stocktake',
            },
            selectedPrinter: null,
            selectedPaymentType: null,
            selectedBillingAddress: null,
            selectedShippingAddress: null,
            paymentTypeAlreadySelected: false,
            paymentAmount: 0,
        };
    },

    mounted() {
        if (!Vue.prototype.$currentUser['warehouse_id']) {
            this.$snotify.error('You do not have warehouse assigned. Please contact administrator', {timeout: 50000});
            return;
        }

        this.selectedPrinter = this.getSelectedPrinter();

        window.onscroll = () => this.loadMoreWhenNeeded();

        Modals.EventBus.$on('hide::modal::set-transaction-printer-modal', (printer) => {
            this.selectedPrinter = printer;
        });

        Modals.EventBus.$on('hide::modal::data-collection-choose-payment-type-modal', (data) => {
            if (data.paymentType) {
                this.selectedPaymentType = data.paymentType;
            }

            if ((typeof data.saveChanges !== 'undefined' && data.saveChanges) && this.selectedPaymentType) {
                this.$modal.showAddPaymentModal({maxAmount: this.dataCollection['total_outstanding']});
            }
        });

        Modals.EventBus.$on('show::modal::data-collection-data-collection-add-payment-modal', this.onAddPaymentModalShown);

        Modals.EventBus.$on('hide::modal::data-collection-data-collection-add-payment-modal', (data) => {
            if (data.amount) {
                this.paymentAmount = data.amount;
            }

            if ((typeof data.saveChanges !== 'undefined' && data.saveChanges) && this.paymentAmount) {
                this.setTransactionPayment();
            }
        });

        Modals.EventBus.$on('hide::modal::find-address-modal', (data) => {
            if (data.billingAddress) {
                this.selectedBillingAddress = data.billingAddress;
            }

            if (data.shippingAddress) {
                this.selectedShippingAddress = data.shippingAddress;
            }

            if (
                (typeof data.saveChanges !== 'undefined' && data.saveChanges) &&
                (this.selectedShippingAddress || this.selectedBillingAddress) &&
                (this.selectedShippingAddress !== this.dataCollection['shipping_address_id'] || this.selectedBillingAddress !== this.dataCollection['billing_address_id'])
            ) {
                this.setTransactionCustomer();
            }
        });

        this.getUrlFilterOrSet('warehouse_code', Vue.prototype.$currentUser['warehouse']['code']);

        this.loadWarehouses();

        this.reloadDataCollection();
    },

    shown() {
        this.reloadDataCollection();
    },

    methods: {
        toggleAddToRequested() {
            setTimeout(() => {
                this.hideBvModal('configuration-modal');
            }, 200)
        },

        reloadDataCollection() {
            this.loadDataCollectorDetails();
            this.loadDataCollectorRecords();
        },

        onQuantityRequestModalHidden() {
            this.setFocusElementById('barcode_input');
            this.reloadDataCollection();
        },

        onShownConfigurationModal() {
            this.setFocusElementById('stocktake-input', true, true);
            this.buttonsEnabled = true;
        },

        onBarcodeScanned: function (barcode) {
            if (barcode === '') {
                return;
            }

            if (this.dataCollection['deleted_at'] !== null) {
                this.notifyError('This collection is already archived');
                return;
            }

            if (this.singleScanEnabled) {
                this.addSinglePiece(barcode);
            } else {
                this.$modal.showDataCollectorQuantityRequestModal(this.dataCollection['id'], barcode, this.addToRequested ? 'quantity_requested' : 'quantity_scanned');
            }
        },

        addSinglePiece(barcode) {
            let data = {
                'data_collection_id': this.dataCollection['id'],
                'sku_or_alias': barcode,
                'quantity_scanned': 1,
            };

            if (this.addToRequested) {
                data['quantity_requested'] = 1;
            } else {
                data['quantity_scanned'] = 1;
            }

            this.apiPostDataCollectorActionsAddProduct(data)
                .then(() => {
                    this.notifySuccess('1 x ' + barcode);
                    this.reloadDataCollection();
                })
                .catch((error) => {
                    this.displayApiCallError(error);
                });
        },

        toggleSingleScanMode() {
            setTimeout(() => {
                this.hideBvModal('configuration-modal');
            }, 200)
        },

        loadWarehouses: function () {
            this.apiGetWarehouses({'per_page': 999, 'sort': 'name'})
                .then(response => {
                    this.warehouses = response.data.data;
                });
        },

        loadDataCollectorDetails: function () {
            let params = {
                'filter[id]': this.data_collection_id,
                'filter[with_archived]': true,
                'include': 'comments,comments.user,shippingAddress,billingAddress,payments'
            }

            this.apiGetDataCollector(params)
                .then(response => {
                    this.dataCollection = response.data.data[0];
                    if (this.dataCollection.shipping_address_id) {
                        this.selectedShippingAddress = this.dataCollection.shipping_address_id;
                    }
                    if (this.dataCollection.billing_address_id) {
                        this.selectedBillingAddress = this.dataCollection.billing_address_id;
                    }
                })
                .catch(error => {
                    console.log(error);
                    this.displayApiCallError(error);
                });
        },

        transferToWarehouseClick() {
            this.$bvModal.hide('configuration-modal');
            this.$bvModal.show('transferToModal');
        },

        transferToWarehouse(warehouse) {
            let data = {
                'action': 'transfer_to_scanned',
                'destination_warehouse_id': warehouse['id'],
            }

            this.apiUpdateDataCollection(this.data_collection_id, data)
                .then(response => {
                    this.$bvModal.hide('configuration-modal');
                    location.href = '/data-collector';
                    // setTimeout(() => {
                    //     this.reloadDataCollection();
                    // }, 500);
                })
                .catch(error => {
                    this.showException(error);
                });

            this.$bvModal.hide('transferToModal');
        },

        transferStockIn() {
            let data = {
                'action': 'transfer_in_scanned',
            }

            this.apiUpdateDataCollection(this.data_collection_id, data)
                .then(response => {
                    this.$snotify.success('Stock transferred in successfully');
                    this.$bvModal.hide('configuration-modal');
                    setTimeout(() => {
                        this.reloadDataCollection();
                    }, 500);
                })
                .catch(error => {
                    this.showException(error);
                });
        },

        archiveTransaction() {
            this.apiUpdateDataCollection(this.data_collection_id, {
                'custom_uuid': null,
                'deleted_at': new Date().toISOString(),
            })
                .then(response => {
                    this.$snotify.success('Transaction archived successfully');
                    this.$emit('transactionFinished');
                    this.$bvModal.hide('configuration-modal');
                    setTimeout(() => {
                        this.reloadDataCollection();
                    }, 500);
                })
                .catch(error => {
                    this.showException(error);
                });
        },

        saveTransaction() {
            this.apiUpdateDataCollection(this.data_collection_id, {
                'name': ``,
                'custom_uuid': ``,
            })
                .then(() => {
                    this.$snotify.success('Transaction saved successfully');
                    this.$emit('transactionFinished');
                    this.$bvModal.hide('configuration-modal');
                })
                .catch(error => {
                    this.showException(error);
                });
        },

        selectPrinter() {
            this.$modal.showSetTransactionPrinterModal(this.selectedPrinter);
        },

        selectCustomer() {
            this.$modal.showFindAddressModal();
        },

        selectPayment() {
            this.$modal.showSetPaymentTypeModal(this.selectedPaymentType);
        },

        autoScanAll() {
            let data = {
                'action': 'auto_scan_all_requested',
            }

            this.apiUpdateDataCollection(this.data_collection_id, data)
                .then(response => {
                    this.$snotify.success('Auto scan completed successfully');
                    this.$bvModal.hide('configuration-modal');
                    setTimeout(() => {
                        this.reloadDataCollection();
                    }, 500);
                })
                .catch(error => {
                    this.showException(error);
                });
        },

        loadMoreWhenNeeded() {
            if (this.isLoading) {
                return;
            }

            if (this.isMoreThanPercentageScrolled(70) === false) {
                return;
            }

            if (this.nextUrl === null) {
                return;
            }

            // we double per_page every second page load to avoid hitting the API too hard
            // and we will limit it to 100-ish per_page
            if ((this.page % 2 === 0) && (this.per_page < 100)) {
                this.page = this.page / 2;
                this.per_page = this.per_page * 2;
            }

            this.loadDataCollectorRecords(++this.page);
        },

        loadDataCollectorRecords(page = 1) {
            this.showLoading();

            const params = this.$router.currentRoute.query;
            params['filter[data_collection_id]'] = this.data_collection_id;
            params['include'] = 'product,inventory,product.tags,product.aliases,prices,discount';
            params['per_page'] = this.per_page;
            params['page'] = page;

            this.apiGetDataCollectorRecords(params)
                .then((response) => {
                    if (page === 1) {
                        this.dataCollectionRecords = response.data.data;
                    } else {
                        this.dataCollectionRecords = this.dataCollectionRecords.concat(response.data.data);
                    }

                    this.page = response.data['meta']['current_page'];
                    this.nextUrl = response.data['links']['next'];
                })
                .catch((error) => {
                    this.displayApiCallError(error);
                })
                .finally(() => {
                    this.hideLoading();
                });
        },

        postCsvRecordsToApiAndCloseModal() {
            const data = this.csv.map(record => ({
                'product_sku': record.product_sku,
                'quantity_requested': record.quantity_requested,
                'quantity_scanned': record.quantity_scanned,
            }));

            //we removing header row from csv
            data.shift();

            const payload = {
                'data_collection_id': this.data_collection_id,
                'data': data,
            }

            this.apiPostCsvImport(payload)
                .then(() => {
                    this.notifySuccess('Records imported');
                    this.$bvModal.hide('configuration-modal');
                })
                .catch(e => {
                    this.displayApiCallError(e);
                })
                .finally(() => {
                    this.reloadDataCollection();
                });
        },

        downloadFileAndHideModal() {
            window.open(this.getDownloadLink, '_blank');

            this.hideBvModal('configuration-modal')
        },

        hideBvModal(ref) {
            this.$bvModal.hide(ref);
        },

        addComment() {
            let data = {
                "data_collection_id": this.dataCollection.id,
                "comment": this.input_comment
            };

            // quick hack to immediately display comment
            this.dataCollection.comments.unshift(data);

            this.apiPostDataCollectionComment(data)
                .then(() => {
                    this.loadDataCollectorDetails();
                    this.input_comment = '';
                    this.manuallyExpandComments = false;
                    this.setFocusElementById('barcode_input');

                })
                .catch((error) => {
                    console.log(error)
                    this.displayApiCallError(error);
                });
        },

        toggleExpandComments() {
            this.manuallyExpandComments = !this.manuallyExpandComments;
            if (this.manuallyExpandComments) {
                this.setFocusElementById('comment-input', true);
            } else {
                this.setFocusElementById('barcode_input', false);
            }
        },

        getSelectedPrinter() {
            const printer = localStorage.getItem('selectedTransactionsPrinter');

            if (printer) {
                return JSON.parse(printer);
            } else {
                return null;
            }
        },

        printReceipt() {
            if (this.selectedPrinter === null) {
                this.$snotify.error('Please select printer first');
                return;
            }

            let data = {
                id: this.dataCollection.id,
                printer_id: this.selectedPrinter.id,
            };

            this.apiPrintTransactionReceipt(data)
                .then(() => {
                    this.notifySuccess('Receipt sent to printer');
                })
                .catch(error => {
                    this.displayApiCallError(error);
                })
        },

        emailPdfReceipt() {
            if (!this.selectedBillingAddress && !this.selectedShippingAddress) {
                this.$snotify.error('Please select customer first');
                return;
            }

            this.apiSendTransactionReceipt({id: this.dataCollection.id})
                .then(() => {
                    this.notifySuccess('Receipt has been sent to selected customer');
                })
                .catch(error => {
                    this.displayApiCallError(error);
                })
        },

        setTransactionCustomer() {
            this.apiPutTransaction(this.dataCollection.id, {
                shipping_address_id: this.selectedShippingAddress ? this.selectedShippingAddress : this.selectedBillingAddress,
                billing_address_id: this.selectedBillingAddress ? this.selectedBillingAddress : this.selectedShippingAddress
            })
                .then(() => {
                    this.notifySuccess('Customer selected');
                    this.reloadDataCollection();
                })
                .catch(error => {
                    this.displayApiCallError(error);
                });
        },

        setTransactionPayment() {
            // if (this.paymentTypeAlreadySelected) {
            //     const payment = this.dataCollection.payments[this.dataCollection.payments.length - 1];
            //     this.apiPutTransactionPayment(payment.id, {
            //         payment_type_id: this.selectedPaymentType.id,
            //         amount: this.paymentAmount
            //     })
            //         .then(() => {
            //             this.notifySuccess('Payment type updated.');
            //             this.reloadDataCollection();
            //         })
            //         .catch(error => {
            //             this.displayApiCallError(error);
            //         });
            // } else {
            // }
            this.apiPostTransactionPayment({
                transaction_id: this.dataCollection.id,
                payment_type_id: this.selectedPaymentType.id,
                amount: this.paymentAmount
            })
                .then(() => {
                    this.notifySuccess('Payment type selected.');
                    this.reloadDataCollection();
                })
                .catch(error => {
                    this.displayApiCallError(error);
                });
        },

        onAddPaymentModalShown() {
            this.setFocusElementById('transaction_payment_amount', true);
        }
    },

    computed: {
        getDownloadLink() {
            let routeData = this.$router.resolve({
                path: this.$router.currentRoute.fullPath,
                query: {
                    'select': 'product_sku,product_name,total_transferred_in,total_transferred_out,quantity_requested,quantity_to_scan,quantity_scanned,inventory_quantity,product_price,product_sale_price,product_sale_price_start_date,product_sale_price_end_date,product_cost,last7days_sales,last14days_sales,last28days_sales',
                    'filter[data_collection_id]': this.data_collection_id,
                    filename: this.dataCollection['name'] + ".csv"
                }
            });

            return routeData.href;
        },

        commentsToShow() {
            return this.dataCollection.comments.length
                ? (this.manuallyExpandComments ? this.dataCollection.comments : [this.dataCollection.comments[0]])
                : [];
        }
    },
}
</script>


<style lang="scss">
.setting-list {
    width: 100%;
    color: #495057;
    display: flex;
    align-items: flex-start;
    margin-bottom: 5px;
}

.setting-list:hover, .setting-list:focus {
    color: #495057;
    text-decoration: none;
    background-color: #f8f9fa;
}

.setting-icon {
    padding: 1rem;
    margin-right: 1rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
}

.setting-icon:hover {
    background-color: unset;
}

.setting-title {
    color: #3490dc;
    font-weight: bolder;
    /*font-size: 1rem;*/
    /*line-height: 1.2;*/
    margin-bottom: 2px;
}

.setting-desc {
    color: #6c757d;
    font-size: 10pt;
}
</style>
