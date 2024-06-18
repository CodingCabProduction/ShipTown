<template>
    <div>
        <b-modal @ok="submitCount" id="data-collector-quantity-request-modal" scrollable no-fade hide-header
                 @shown="modalShown"
                 @hidden="onHidden"
        >
            <div class="row">
                <div class="col-lg-6">
                    <product-info-card :product="product"></product-info-card>
                </div>
                <div class="col">
                    <div class="row text-right mt-0 mb-3">
                        <div class="col">
                            <text-card label="price" :text="prices && dataCollection ? prices[dataCollection['warehouse_code']]['current_price'] : 0" :class="{ 'bg-warning': prices && dataCollection ? prices[dataCollection['warehouse_code']]['is_on_sale'] : '-' }"></text-card>
                            <number-card label="in stock" :number="inventory && dataCollection ? inventory[dataCollection['warehouse_code']]['quantity'] : 0" :class="{ 'bg-warning': inventory && dataCollectionRecord && dataCollectionRecord['quantity_requested'] >= inventory[dataCollection['warehouse_code']]['quantity']}" ></number-card>
                            <text-card label="shelf" :text="inventory && dataCollection ? inventory[dataCollection['warehouse_code']]['shelf_location'] : 0"></text-card>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col text-right mt-3 mb-3">
                <number-card label="reserved" :number="product ? product['inventory'][dataCollection['warehouse_code']]['quantity_reserved'] : 0" :class="{ 'bg-warning': product ? product['inventory'][dataCollection['warehouse_code']]['quantity_reserved'] : 0 > 0}"></number-card>
                <number-card label="requested" :number="dataCollectionRecord ? dataCollectionRecord['quantity_requested'] : 0"></number-card>
                <number-card label="scanned" :class="{ 'bg-warning': dataCollectionRecord && dataCollectionRecord['quantity_scanned'] > dataCollectionRecord['quantity_requested']}":number="dataCollectionRecord ? dataCollectionRecord['quantity_scanned'] : 0"></number-card>
                <number-card label="to scan" :class="{ 'bg-warning': dataCollectionRecord && dataCollectionRecord['quantity_scanned'] > 0 && dataCollectionRecord['quantity_to_scan'] > 0}" :number="dataCollectionRecord ? dataCollectionRecord['quantity_to_scan'] : 0"></number-card>
            </div>

            <div class="row-col">
                <div class="col-12">
                    <input class="form-control" :placeholder="'quantity to add'" :class="{ 'border-danger': this.quantity_to_add < 0, 'border-success': this.quantity_to_add > 0}"
                           id="data-collection-record-quantity-request-input"
                           name="data-collection-record-quantity-request-input"
                           ref="data-collection-record-quantity-request-input"
                           dusk="data-collection-record-quantity-request-input"
                           v-model="quantity_to_add"
                           type="number"
                           inputmode="numeric"
                           @keyup.enter="submitCount"
                    />
                </div>
            </div>

            <template #modal-footer="{ ok, cancel }" class="text-left">
                <b-button style="width: 75px" class="btn mr-auto small" variant="secondary" @click="cancel()">Cancel</b-button>
                <b-button style="width: 75px" class="btn small" variant="primary" @click="ok()">OK</b-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
    import loadingOverlay from '../../mixins/loading-overlay';
    import BarcodeInputField from "../SharedComponents/BarcodeInputField";
    import api from "../../mixins/api";
    import helpers from "../../mixins/helpers";
    import url from "../../mixins/url";
    import Modals from "../../plugins/Modals";

    export default {
        mixins: [loadingOverlay, url, api, helpers],

        components: {
            BarcodeInputField
        },

        props: {
            placeholder: '',
        },

        data: function() {
            return {
                data_collection_id: null,
                sku_or_alias: null,

                quantity_to_add: null,
                field_name: 'quantity_scanned',

                dataCollection: null,
                product: null,
                inventory: null,
                prices: null,
                dataCollectionRecord: null,
            };
        },

        beforeMount() {
            Modals.EventBus.$on('show::modal::data-collector-quantity-request-modal', (data) => {
                this.data_collection_id = data['data_collection_id'];
                this.sku_or_alias = data['sku_or_alias'];
                this.field_name = data['field_name'];

                this.quantity_to_add = null;
                this.dataCollection = null;
                this.product = null;
                this.inventory = null;
                this.prices = null;
                this.dataCollectionRecord = null;

                this.loadDataAndShowModal();
            })
        },

        methods: {
            onHidden() {
                this.$emit('hidden');
            },

            loadDataAndShowModal: function () {
                this.loadProduct();
                this.loadDataCollection();
                this.loadDataCollectionRecord();
            },

            modalShown() {
                this.setFocusElementById('data-collection-record-quantity-request-input', true, true, 10);
            },

            loadProduct: function () {
                this.apiGetProducts({
                    'filter[sku_or_alias]': this.sku_or_alias,
                    'include': 'inventory,prices',
                })
                .then(response => {
                    if (response.data.data.length === 0) {
                        this.notifyError('No product found with barcode "' + this.sku_or_alias + '"');
                        return;
                    }
                    this.product = response.data.data[0];
                    this.inventory = this.product['inventory'];
                    this.prices = this.product['prices'];

                    this.$bvModal.show('data-collector-quantity-request-modal');
                })
                .catch((error) => {
                    this.displayApiCallError(error);
                });
            },

            loadDataCollection: function () {
                this.apiGetDataCollector({
                    'filter[id]': this.data_collection_id,
                })
                .then(response => {
                    if (response.data.data.length === 0) {
                        this.notifyError('No collection found');
                        return;
                    }
                    this.dataCollection = response.data.data[0];
                })
                .catch((error) => {
                    this.displayApiCallError(error);
                });
            },

            loadDataCollectionRecord() {
                this.apiGetDataCollectorRecords({
                    'filter[data_collection_id]': this.data_collection_id,
                    'filter[sku_or_alias]': this.sku_or_alias,
                })
                    .then(response => {
                        if (response.data.data.length === 0) {
                            this.dataCollectionRecord = null;
                            return;
                        }
                        this.dataCollectionRecord = response.data.data[0];
                    })
                    .catch((error) => {
                        this.displayApiCallError(error);
                    });
            },

            submitCount: function () {
                if (this.quantity_to_add === null) {
                    return;
                }

                if (this.quantity_to_add === "") {
                    return;
                }

                if (Math.abs(this.quantity_to_add) > 99999) {
                    this.notifyError('Quantity is too large', {'timeout': 3000});
                    this.setFocusElementById('data-collection-record-quantity-request-input', true);
                    return;
                }

                let data = {
                    'data_collection_id': this.dataCollection['id'],
                    'sku_or_alias': this.sku_or_alias,
                };

                data[this.field_name] = this.quantity_to_add;

                this.apiPostDataCollectorActionsAddProduct(data)
                    .then(() => {
                        this.$bvModal.hide('data-collector-quantity-request-modal');
                        this.notifySuccess(this.quantity_to_add + ' x ' + this.sku_or_alias);
                    })
                    .catch((error) => {
                        this.displayApiCallError(error);
                    });

            }
        },
    }
</script>

<style lang="scss" scoped>
</style>
