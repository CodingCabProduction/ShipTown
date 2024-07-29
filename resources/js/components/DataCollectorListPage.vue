<template>
    <div>
        <search-and-option-bar-observer/>
        <search-and-option-bar :isStickable="true">
            <barcode-input-field placeholder="Search" :url_param_name="'filter[name_contains]'" @barcodeScanned="loadData(1)"></barcode-input-field>
            <template v-slot:buttons>
                <b-dropdown dropleft no-caret variant="primary" class="ml-2" dusk="new_data_collection" id="new_data_collection">
                    <template #button-content>
                        <font-awesome-icon icon="plus" class="fa-lg"></font-awesome-icon>
                    </template>
                    <b-dropdown-item @click="createTransferIn">Transfer In</b-dropdown-item>
                    <b-dropdown-item @click="createTransferOut">Transfer Out</b-dropdown-item>
                    <b-dropdown-item @click="createStocktake">Stocktake</b-dropdown-item>
                    <b-dropdown-divider></b-dropdown-divider>
                    <b-dropdown-item @click="createBlankCollection" name="create_blank_collection_button" dusk="create_blank_collection_button"  id="create_blank_collection_button" >Blank</b-dropdown-item>
                </b-dropdown>
            </template>
        </search-and-option-bar>

        <div class="row pl-2 p-0 text-uppercase small text-secondary">
            <div class="col-8 text-nowrap text-left align-bottom pb-0 m-0 font-weight-bold">
                Tools > Data Collector
            </div>
            <div class="col-4 text-nowrap">
                <div class="custom-control custom-switch m-auto text-right align-bottom">
                    <input type="checkbox" @change="toggleArchivedFilter" class="custom-control-input" id="switch" v-model="showArchived">
                    <label class="custom-control-label" for="switch">Archived</label>
                </div>
            </div>
        </div>

        <div v-if="(data !== null) && (data.length === 0)" class="text-secondary small text-center mt-3">
            No records found<br>
            Click + to create one<br>
        </div>

        <template v-for="record in data">
            <swiping-card :disable-swipe-right="true" :disable-swipe-left="true">
                <template v-slot:content>
                    <div role="button" dusk="data_collection_record" class="row" @click="openDataCollection(record['id'])">
                        <div class="col-sm-12 col-lg-6">
                            <div class="text-primary">{{ record['name'] }}</div>
                            <div class="text-secondary small">{{ formatDateTime(record['created_at'], 'dddd - MMM D HH:mm') }}</div>
                            <div class="text-secondary small">{{ collectionTypes[record['type']] }}</div>
                        </div>
                        <div class="col-cols col-sm-12 col-lg-6 bottom text-right">
                            <text-card v-if="record['deleted_at'] !== null" :label="formatDateTime(record['deleted_at'], 'dddd - MMM D HH:mm')" text="ARCHIVED" class="float-left text-left"></text-card>
                            <text-card label="warehouse" :text="record['warehouse_code']"></text-card>
                            <number-card label="differences" :number="record['differences_count']"></number-card>
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

        <b-modal id="new-collection-modal" no-fade hide-header title="New Data Collection" @ok="createCollectionAndRedirect" @shown="prepareNewCollectionModal">
            <input dusk="collection_name_input" id="collection_name_input" v-model="newCollectionName" type="text" @keyup.enter="createCollectionAndRedirect" class="form-control" placeholder="New Collection name">
            <hr>
            <vue-csv-import
                v-model="csv"
                headers
                canIgnore
                autoMatchFields
                loadBtnText="Load"
                :map-fields="map_fields">

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

            <button v-if="csv" type="button" @click.prevent="postCsvRecordsToApiAndCloseModal" class="col btn mb-1 btn-primary">Import Records</button>
        </b-modal>

        <b-modal id="configuration-modal" autofocus centered no-fade title="Data Collection">
            <button type="button" @click.prevent="downloadFile" class="col btn mb-1 btn-primary">Download</button>
            <template #modal-footer>
                <b-button variant="secondary" class="float-right" @click="$bvModal.hide('configuration-modal');">
                    Cancel
                </b-button>
            </template>
        </b-modal>

    </div>
</template>

    <script>
    import beep from '../mixins/beep';
    import loadingOverlay from '../mixins/loading-overlay';

    import FiltersModal from "./Packlist/FiltersModal";
    import url from "../mixins/url";
    import api from "../mixins/api";
    import helpers from "../mixins/helpers";
    import Vue from "vue";
    import NumberCard from "./SharedComponents/NumberCard";
    import SwipingCard from "./SharedComponents/SwipingCard";

    export default {
            mixins: [loadingOverlay, beep, url, api, helpers],

            components: {
                FiltersModal,
                NumberCard,
                SwipingCard,
            },

            data: function() {
                return {
                    showArchived: false,
                    map_fields: [],
                    csv: null,
                    data: null,
                    reachedEnd: false,
                    per_page: 50,
                    page: 1,
                    newCollectionName: null,
                    newCollectionType: null,
                    newCollectionDestinationWarehouseID: null,
                    collectionTypes: {
                        'App\\Models\\DataCollectionTransferIn': 'Transfer In',
                        'App\\Models\\DataCollectionTransferOut': 'Transfer Out',
                        'App\\Models\\DataCollectionStocktake': 'Stocktake',
                    },
                };
            },

            mounted() {
                if (! Vue.prototype.$currentUser['warehouse_id']) {
                    this.$snotify.error('You do not have warehouse assigned. Please contact administrator', {timeout: 50000});
                    return;
                }

                this.getUrlFilterOrSet('filter[warehouse_code]', Vue.prototype.$currentUser['warehouse']['code']);
                this.showArchived = this.getUrlFilterOrSet('filter[only_archived]', 'false') === 'true';

                window.onscroll = () => this.loadMoreWhenNeeded();

                this.loadData();

                this.apiGetWarehouses()
                    .then(response => {
                        this.map_fields = ['product_sku'].concat(response.data.data.map(warehouse => warehouse.code));
                    });
            },

            methods: {
                createTransferIn() {
                    this.newCollectionType = 'App\\Models\\DataCollectionTransferIn';
                    this.newCollectionDestinationWarehouseID = this.currentUser()['warehouse_id'];
                    this.$bvModal.show('new-collection-modal');
                },

                createTransferOut() {
                    this.newCollectionType = 'App\\Models\\DataCollectionTransferOut';
                    this.newCollectionDestinationWarehouseID = null;
                    this.$bvModal.show('new-collection-modal');
                },

                createStocktake() {
                    this.newCollectionType = 'App\\Models\\DataCollectionStocktake';
                    this.newCollectionDestinationWarehouseID = null;
                    this.$bvModal.show('new-collection-modal');
                },

                createBlankCollection() {
                    this.newCollectionType = null;
                    this.newCollectionDestinationWarehouseID = null;
                    this.$bvModal.show('new-collection-modal');
                },

                toggleArchivedFilter(event) {
                    this.setUrlParameter('filter[only_archived]', event.target.checked);
                    this.loadData();
                },

                postCsvRecordsToApiAndCloseModal() {
                    this.csv.shift();

                    const payload = {
                        'data_collection_name_prefix': this.newCollectionName,
                        'data': this.csv,
                    }

                    this.apiPostCsvImportDataCollections(payload)
                        .then(() => {
                            this.notifySuccess('Records imported');
                            this.$bvModal.hide('configuration-modal');
                        })
                        .catch(e => {
                            this.displayApiCallError(e);
                        })
                        .finally(() => {
                            this.loadData();
                        });

                    this.$bvModal.hide('new-collection-modal');
                },

                prepareNewCollectionModal() {
                    this.csv = null;
                    this.newCollectionName = null;
                    this.setFocusElementById('collection_name_input', true);
                },

                openDataCollection(data_collection_id)  {
                    window.location.href = '/data-collector/' + data_collection_id;
                },

                createCollectionAndRedirect(event) {
                    const payload = {
                        'warehouse_id': this.currentUser()['warehouse_id'],
                        'name': this.newCollectionName,
                        'type': this.newCollectionType,
                        'destination_warehouse_id': this.newCollectionDestinationWarehouseID,
                    }

                    this.apiPostDataCollection(payload)
                        .then(() => {
                            this.notifySuccess('Data collected');
                            this.$bvModal.hide('new-collection-modal');
                        })
                        .catch(e => {
                            this.displayApiCallError(e);
                        })
                        .finally(() => {
                            this.loadData();
                        });
                },

                loadMoreWhenNeeded() {
                    if (this.isLoading) {
                        return;
                    }

                    if (this.isMoreThanPercentageScrolled(70) === false) {
                        return;
                    }

                    if (this.reachedEnd === true) {
                        return;
                    }

                    this.loadData(++this.page);
                },

                loadData(page = 1) {
                    this.showLoading();

                    const params = this.$router.currentRoute.query;
                    params['sort'] = this.getUrlParameter('sort', '-created_at');
                    params['per_page'] = this.getUrlParameter('per_page', this.per_page);
                    params['page'] = page;

                    this.apiGetDataCollector(params)
                        .then((response) => {
                            this.reachedEnd = response.data.data.length === 0;

                            if (page === 1) {
                                this.data = response.data.data;
                            } else {
                                this.data = this.data.concat(response.data.data);
                            }
                        })
                        .catch((error) => {
                            this.displayApiCallError(error);
                        })
                        .finally(() => {
                            this.hideLoading();
                        });
                },

                downloadFile() {
                    let routeData = this.$router.resolve({
                        path: this.$router.currentRoute.fullPath,
                        query: {filename: "DataCollections.csv"}
                    });
                    window.open(routeData.href, '_blank');
                },
            },
    }
    </script>


<style lang="scss">
.setting-list:hover, .setting-list:focus {
    color: #495057;
    text-decoration: none;
    background-color: #f8f9fa;
}
</style>
