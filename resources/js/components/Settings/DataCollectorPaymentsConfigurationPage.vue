<template>
    <div>
        <search-and-option-bar-observer/>
        <search-and-option-bar :isStickable="true">
            <barcode-input-field
                :input_id="'payment_type_input'"
                placeholder="Search"
                ref="barcode"
                :url_param_name="'search'"
                @refreshRequest="reloadPaymentTypesList"
                @barcodeScanned="findText"
            />
            <template v-slot:buttons>
                <button @click="showNewPaymentTypeModal" type="button" class="btn btn-primary ml-2">
                    <font-awesome-icon icon="plus" class="fa-lg"></font-awesome-icon>
                </button>
            </template>
        </search-and-option-bar>

        <div class="row pl-2 p-0">
            <breadcrumbs></breadcrumbs>
        </div>

        <template
            v-if="isLoading === false && paymentTypes !== null && Array.isArray(paymentTypes) && paymentTypes.length === 0">
            <div class="text-secondary small text-center mt-3">
                No records found<br>
                Click + to create one<br>
            </div>
        </template>

        <template v-if="paymentTypes" v-for="type in paymentTypes">
            <swiping-card :disable-swipe-right="true" :disable-swipe-left="true">
                <template v-slot:content>
                    <div role="button" class="row">
                        <div class="col-12 col-md-9">
                            <div class="text-primary">{{ type['name'] }}</div>
                            <div class="text-secondary small">
                                {{ type['code'] }}
                            </div>
                        </div>
                        <div class="col-12 col-md-3 d-flex align-items-center justify-content-end">
                            <button class="remove-button d-inline-flex align-items-center justify-content-center"
                                    @click="removePaymentType(type['id'])">
                                <font-awesome-icon icon="trash" class="fa-lg"></font-awesome-icon>
                            </button>
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
    </div>
</template>

<script>
import loadingOverlay from "../../mixins/loading-overlay";
import url from "../../mixins/url.vue";
import api from "../../mixins/api.vue";
import helpers from "../../mixins/helpers";
import Breadcrumbs from "../Reports/Breadcrumbs.vue";
import BarcodeInputField from "../SharedComponents/BarcodeInputField.vue";
import SwipingCard from "../SharedComponents/SwipingCard.vue";
import Modals from "../../plugins/Modals";

export default {
    mixins: [loadingOverlay, url, api, helpers],

    components: {
        Breadcrumbs,
        BarcodeInputField,
        SwipingCard,
    },

    data: () => ({
        pagesLoadedCount: 1,
        reachedEnd: false,
        paymentTypes: null,
        per_page: 20,
        scroll_percentage: 70,
    }),

    mounted() {
        window.onscroll = () => this.loadMore();

        Modals.EventBus.$on('hide::modal::module-data-collector-payments-new-payment-type-modal', () => {
            this.reloadPaymentTypesList();
        });

        this.reloadPaymentTypesList();
    },

    methods: {
        findText(search) {
            this.setUrlParameter('search', search);
            this.reloadPaymentTypesList();
        },

        reloadPaymentTypesList() {
            this.paymentTypes = null;

            this.findPaymentTypes();
        },

        findPaymentTypes(page = 1) {
            this.showLoading();

            const params = {...this.$router.currentRoute.query};
            params['filter[search]'] = this.getUrlParameter('search') ?? '';
            params['per_page'] = this.per_page;
            params['page'] = page;

            this.apiGetPaymentTypes(params)
                .then(({data}) => {
                    this.paymentTypes = this.paymentTypes ? this.paymentTypes.concat(data.data) : data.data
                    this.reachedEnd = data.data.length === 0;
                    this.pagesLoadedCount = page;

                    this.scroll_percentage = (1 - this.per_page / this.paymentTypes.length) * 100;
                    this.scroll_percentage = Math.max(this.scroll_percentage, 70);
                })
                .catch((error) => {
                    this.displayApiCallError(error);
                })
                .finally(() => {
                    this.hideLoading();
                });

            return this;
        },

        showNewPaymentTypeModal() {
            this.$modal.showNewPaymentTypeModal();
        },

        loadMore() {
            if (this.isMoreThanPercentageScrolled(this.scroll_percentage) && this.hasMorePagesToLoad() && !this.isLoading) {
                this.findPaymentTypes(++this.pagesLoadedCount);
            }
        },

        hasMorePagesToLoad() {
            return this.reachedEnd === false;
        },

        removePaymentType(id) {
            this.showLoading();

            this.apiDeletePaymentType(id)
                .then(() => {
                    this.reloadPaymentTypesList();
                })
                .catch((error) => {
                    this.displayApiCallError(error);
                })
                .finally(() => {
                    this.hideLoading();
                });
        },
    },
}
</script>

<style lang="scss" scoped>
.remove-button {
    cursor: pointer;
    width: 40px;
    height: 40px;
    border: 1px solid #dc3545;
    color: #dc3545;
    background-color: transparent;
    transition: background-color 0.3s, color 0.3s;

    &:hover {
        background-color: #dc3545;
        color: white;
    }
}
</style>
