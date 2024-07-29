<template>
    <div>
        <search-and-option-bar-observer/>
        <search-and-option-bar :isStickable="true">
            <barcode-input-field
                :input_id="'barcode_input'"
                placeholder="Search"
                ref="barcode"
                :url_param_name="'search'"
                @refreshRequest="reloadDiscountsList"
                @barcodeScanned="findText"
            />
            <template v-slot:buttons>
                <button @click="showNewDiscountModal" type="button" class="btn btn-primary ml-2">
                    <font-awesome-icon icon="plus" class="fa-lg"></font-awesome-icon>
                </button>
                <top-nav-button v-b-modal="'quick-actions-modal'"/>
            </template>
        </search-and-option-bar>

        <div class="row pl-2 p-0">
            <breadcrumbs></breadcrumbs>
        </div>

        <template
            v-if="isLoading === false && discounts !== null && Array.isArray(discounts) && discounts.length === 0">
            <div class="text-secondary small text-center mt-3">
                No records found<br>
                Click + to create one<br>
            </div>
        </template>

        <template v-if="discounts" v-for="discount in discounts">
            <swiping-card :disable-swipe-right="true" :disable-swipe-left="true">
                <template v-slot:content>
                    <div role="button" class="row" @click="openDiscount(discount['id'])">
                        <div class="col-12">
                            <div class="text-primary">{{ discount['name'] }}</div>
                            <div class="text-secondary small">
                                {{ formatDateTime(discount['created_at'], 'dddd - MMM D HH:mm') }}
                            </div>
                            <div class="text-secondary small">{{ discountTypes[discount['type']] }}</div>
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

        <b-modal id="quick-actions-modal" no-fade hide-header @hidden="setFocusElementById('barcode_input')">
            <stocktake-input></stocktake-input>
            <template #modal-footer>
                <b-button variant="secondary" class="float-right" @click="$bvModal.hide('quick-actions-modal');">
                    Cancel
                </b-button>
                <b-button variant="primary" class="float-right" @click="$bvModal.hide('quick-actions-modal');">
                    OK
                </b-button>
            </template>
        </b-modal>
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
        discounts: null,
        per_page: 20,
        scroll_percentage: 70,
        discountTypes: {
            'BUY_X_GET_Y_FOR_Z_PRICE': 'Buy X, get Y for Z price',
            'BUY_X_GET_Y_FOR_Z_PERCENT_DISCOUNT': 'Buy X, get Y for Z percent discount',
            'BUY_X_GET_Y_PRICE': 'Buy X for Y price',
            'BUY_X_FOR_Y_PERCENT_DISCOUNT': 'Buy X for Y percent discount'
        },
    }),

    mounted() {
        window.onscroll = () => this.loadMore();

        this.reloadDiscountsList();
    },

    methods: {
        findText(search) {
            this.setUrlParameter('search', search);
            this.reloadDiscountsList();
        },

        reloadDiscountsList() {
            this.discounts = null;

            this.findDiscounts();
        },

        findDiscounts(page = 1) {
            this.showLoading();

            const params = {...this.$router.currentRoute.query};
            params['filter[search]'] = this.getUrlParameter('search') ?? '';
            params['per_page'] = this.per_page;
            params['page'] = page;

            this.apiGetQuantityDiscounts(params)
                .then(({data}) => {
                    this.discounts = this.discounts ? this.discounts.concat(data.data) : data.data
                    this.reachedEnd = data.data.length === 0;
                    this.pagesLoadedCount = page;

                    this.scroll_percentage = (1 - this.per_page / this.discounts.length) * 100;
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

        showNewDiscountModal() {
            this.$modal.showAddNewQuantityDiscountModal();
        },

        loadMore() {
            if (this.isMoreThanPercentageScrolled(this.scroll_percentage) && this.hasMorePagesToLoad() && !this.isLoading) {
                this.findProductsContainingSearchText(++this.pagesLoadedCount);
            }
        },

        hasMorePagesToLoad() {
            return this.reachedEnd === false;
        },

        openDiscount(id) {
            window.location.href = '/admin/settings/modules/quantity-discounts/' + id;
        }
    },
}
</script>

<style lang="scss" scoped>

</style>