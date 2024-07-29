<template>
    <div v-if="discount">
        <swiping-card>
            <template v-slot:content>
                <div class="row setting-list">
                    <div class="col-sm-12 col-lg-4">
                        <div class="text-primary">{{ discount['name'] }}</div>
                        <div class="text-secondary small">
                            {{ formatDateTime(discount['created_at'], 'dddd - MMM D HH:mm') }}
                        </div>
                        <div class="text-secondary small">{{ discountTypes[discount['type']] }}</div>
                    </div>
                    <div class="col-sm-12 col-lg-4">
                        <div class="text-primary">Configuration</div>
                        <template
                            v-if="discount.type === 'BUY_X_GET_Y_FOR_Z_PRICE' || discount.type === 'BUY_X_GET_Y_FOR_Z_PERCENT_DISCOUNT'">
                            <div class="text-secondary small">
                                Quantity full price:
                                {{ dashIfEmpty(discount['configuration']?.quantity_full_price ?? '') }}
                            </div>
                            <div class="text-secondary small">
                                Quantity discounted:
                                {{ dashIfEmpty(discount['configuration']?.quantity_discounted ?? '') }}
                            </div>
                        </template>
                        <template v-if="discount.type === 'BUY_X_GET_Y_FOR_Z_PRICE'">
                            <div class="text-secondary small">
                                Discounted price:
                                {{ dashIfEmpty(discount['configuration']?.discounted_price ?? '') }}
                            </div>
                        </template>
                        <template
                            v-if="discount.type === 'BUY_X_GET_Y_PRICE' || discount.type === 'BUY_X_FOR_Y_PERCENT_DISCOUNT'">
                            <div class="text-secondary small">
                                Quantity required:
                                {{ dashIfEmpty(discount['configuration']?.quantity_required ?? '') }}
                            </div>
                        </template>
                        <template v-if="discount.type === 'BUY_X_GET_Y_PRICE'">
                            <div class="text-secondary small">
                                Discounted unit price:
                                {{ dashIfEmpty(discount['configuration']?.discounted_unit_price ?? '') }}
                            </div>
                        </template>
                        <template
                            v-if="discount.type === 'BUY_X_GET_Y_FOR_Z_PERCENT_DISCOUNT' || discount.type === 'BUY_X_FOR_Y_PERCENT_DISCOUNT'">
                            <div class="text-secondary small">
                                Discount percent: {{ dashIfEmpty(discount['configuration']?.discount_percent ?? '') }}
                            </div>
                        </template>
                    </div>
                    <div class="col-sm-12 col-lg-4 text-right">
                        <top-nav-button v-b-modal="'configuration-modal'" icon="edit"/>
                    </div>
                </div>
            </template>
        </swiping-card>

        <search-and-option-bar-observer/>
        <search-and-option-bar :isStickable="true">
            <div class="d-flex flex-nowrap">
                <div class="flex-fill">
                    <barcode-input-field :input_id="'barcode_input'"
                                         placeholder="Enter SKU"
                                         ref="barcode"
                                         :url_param_name="'search'"
                                         :showManualSearchButton="true"
                                         @barcodeScanned="addProductToDiscount">
                    </barcode-input-field>
                </div>
            </div>
        </search-and-option-bar>

        <div v-if="(products !== null) && (products.length === 0)" class="text-secondary small text-center mt-3">
            No products found<br>
            Scan or type in SKU to add products to this discount<br>
        </div>

        <template v-for="product in products">
            <swiping-card :disable-swipe-right="true" :disable-swipe-left="true">
                <template v-slot:content>
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <product-info-card :product="product['product']"></product-info-card>
                        </div>
                        <div class="col-12 col-md-3 text-left small">
                            <div>Price: <strong>{{ dashIfZero(Number(product['product']['price'])) }}</strong></div>
                            <div>Sale price: <strong>{{ dashIfZero(Number(product['product']['sale_price'])) }}</strong></div>
                        </div>
                        <div class="col-12 col-md-5 d-flex align-items-center justify-content-end">
                            <button class="remove-product d-inline-flex align-items-center justify-content-center"
                                    @click="removeProductFromDiscount(product['id'])">
                                <font-awesome-icon icon="trash" class="fa-lg"></font-awesome-icon>
                            </button>
                        </div>
                    </div>
                </template>
            </swiping-card>
        </template>

        <b-modal id="configuration-modal" no-fade hide-header @hidden="setFocusElementById('barcode_input')">
            <ValidationObserver v-slot="{ handleSubmit }">
                <form @submit.prevent="handleSubmit(saveDiscountConfiguration)" ref="loadingContainer">
                    <template
                        v-if="discount.type === 'BUY_X_GET_Y_FOR_Z_PRICE' || discount.type === 'BUY_X_GET_Y_FOR_Z_PERCENT_DISCOUNT'">
                        <div class="form-group">
                            <label class="form-label" for="quantity_full_price">Quantity Full Price</label>
                            <ValidationProvider vid="quantity_full_price" name="quantity_full_price"
                                                v-slot="{ errors }">
                                <input v-model="configuration.quantity_full_price" type="number" min="1" :class="{
                                        'form-control': true,
                                        'is-invalid': errors.length > 0,
                                    }" id="quantity_full_price" placeholder="1" required>
                                <div class="invalid-feedback">
                                    {{ errors[0] }}
                                </div>
                            </ValidationProvider>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="quantity_discounted">Quantity Discounted</label>
                            <ValidationProvider vid="quantity_discounted" name="quantity_discounted"
                                                v-slot="{ errors }">
                                <input v-model="configuration.quantity_discounted" type="number" min="1" :class="{
                                        'form-control': true,
                                        'is-invalid': errors.length > 0,
                                    }" id="quantity_discounted" placeholder="1" required>
                                <div class="invalid-feedback">
                                    {{ errors[0] }}
                                </div>
                            </ValidationProvider>
                        </div>
                    </template>
                    <template v-if="discount.type === 'BUY_X_GET_Y_FOR_Z_PRICE'">
                        <div class="form-group">
                            <label class="form-label" for="discounted_price">Discounted Price</label>
                            <ValidationProvider vid="discounted_price" name="discounted_price"
                                                v-slot="{ errors }">
                                <input v-model="configuration.discounted_price" type="number" min="0" step="0.01"
                                       :class="{
                                        'form-control': true,
                                        'is-invalid': errors.length > 0,
                                    }" id="discounted_price" placeholder="10" required>
                                <div class="invalid-feedback">
                                    {{ errors[0] }}
                                </div>
                            </ValidationProvider>
                        </div>
                    </template>
                    <template
                        v-if="discount.type === 'BUY_X_GET_Y_PRICE' || discount.type === 'BUY_X_FOR_Y_PERCENT_DISCOUNT'">
                        <div class="form-group">
                            <label class="form-label" for="quantity_required">Quantity Required</label>
                            <ValidationProvider vid="quantity_required" name="quantity_required"
                                                v-slot="{ errors }">
                                <input v-model="configuration.quantity_required" type="number" min="1" :class="{
                                        'form-control': true,
                                        'is-invalid': errors.length > 0,
                                    }" id="quantity_required" placeholder="1" required>
                                <div class="invalid-feedback">
                                    {{ errors[0] }}
                                </div>
                            </ValidationProvider>
                        </div>
                    </template>
                    <template v-if="discount.type === 'BUY_X_GET_Y_PRICE'">
                        <div class="form-group">
                            <label class="form-label" for="discounted_unit_price">Discounted Unit Price</label>
                            <ValidationProvider vid="discounted_unit_price" name="discounted_unit_price"
                                                v-slot="{ errors }">
                                <input v-model="configuration.discounted_unit_price" type="number" min="0" step="0.01"
                                       :class="{
                                        'form-control': true,
                                        'is-invalid': errors.length > 0,
                                    }" id="discounted_unit_price" placeholder="1" required>
                                <div class="invalid-feedback">
                                    {{ errors[0] }}
                                </div>
                            </ValidationProvider>
                        </div>
                    </template>
                    <template
                        v-if="discount.type === 'BUY_X_GET_Y_FOR_Z_PERCENT_DISCOUNT' || discount.type === 'BUY_X_FOR_Y_PERCENT_DISCOUNT'">
                        <div class="form-group">
                            <label class="form-label" for="discount_percent">Discount Percent</label>
                            <ValidationProvider vid="discount_percent" name="discount_percent"
                                                v-slot="{ errors }">
                                <input v-model="configuration.discount_percent" type="number" min="0" :class="{
                                        'form-control': true,
                                        'is-invalid': errors.length > 0,
                                    }" id="discount_percent" placeholder="10" required>
                                <div class="invalid-feedback">
                                    {{ errors[0] }}
                                </div>
                            </ValidationProvider>
                        </div>
                    </template>
                    <div class="d-flex justify-content-end">
                        <b-button variant="secondary" type="button" @click="$bvModal.hide('configuration-modal');"
                                  class="mr-2">
                            Cancel
                        </b-button>
                        <b-button variant="primary" type="submit">Save</b-button>
                    </div>
                </form>
            </ValidationObserver>
            <template #modal-footer>
                <div></div>
            </template>
        </b-modal>
    </div>
</template>

<script>
import api from "../../mixins/api.vue";
import helpers from "../../mixins/helpers";
import SwipingCard from "../SharedComponents/SwipingCard.vue";
import loadingOverlay from "../../mixins/loading-overlay";
import beep from "../../mixins/beep";
import url from "../../mixins/url.vue";
import {ValidationObserver, ValidationProvider} from "vee-validate";

export default {
    mixins: [loadingOverlay, beep, url, api, helpers],

    components: {
        SwipingCard,
        ValidationObserver,
        ValidationProvider
    },

    props: {
        initialDiscount: {
            type: String,
            default: null
        },
        initialProducts: {
            type: String,
            default: null
        }
    },

    data() {
        return {
            discount: null,
            products: null,
            configuration: {
                quantity_full_price: null,
                quantity_discounted: null,
                discounted_price: null,
                discount_percent: null,
                quantity_required: null,
                discounted_unit_price: null
            },
            discountTypes: {
                'BUY_X_GET_Y_FOR_Z_PRICE': 'Buy X, get Y for Z price',
                'BUY_X_GET_Y_FOR_Z_PERCENT_DISCOUNT': 'Buy X, get Y for Z percent discount',
                'BUY_X_GET_Y_PRICE': 'Buy X for Y price',
                'BUY_X_FOR_Y_PERCENT_DISCOUNT': 'Buy X for Y percent discount'
            },
        }
    },

    mounted() {
        if (this.initialDiscount) {
            this.discount = JSON.parse(this.initialDiscount);
            this.discount = {...this.discount, configuration: JSON.parse(this.discount.configuration)};
            this.configuration = {...this.configuration, ...this.discount.configuration};
        }

        this.reloadQuantityDiscountProducts();
    },

    methods: {
        reloadQuantityDiscountProducts() {
            let params = {
                'filter[quantity_discount_id]': this.discount.id,
                'include': 'product',
                'sort': '-updated_at'
            };

            this.apiGetQuantityDiscountProduct(params)
                .then(response => {
                    this.products = response.data;
                })
                .catch(error => {
                    this.displayApiCallError(error);
                });
        },

        addProductToDiscount(barcode) {
            if (barcode.trim() === '') {
                return;
            }

            this.apiGetProducts({'filter[sku_or_alias]': barcode, 'include': 'prices'})
                .then(response => {
                    if (response.data.data.length === 0) {
                        this.notifyError('Product "' + barcode + '" not found.');
                        return;
                    }

                    const product = response.data.data[0];

                    if (this.products === null) {
                        this.products = [];
                    }

                    if (!this.products.some(p => p.id === product.id)) {
                        this.apiPostQuantityDiscountProduct({
                            quantity_discount_id: this.discount.id,
                            product_id: product.id
                        })
                            .then(response => {
                                this.reloadQuantityDiscountProducts();
                                this.notifySuccess('Product added to discount.');
                            })
                            .catch(error => {
                                this.displayApiCallError(error);
                            });
                    } else {
                        this.notifyError(`Product "${barcode}" is already added to this discount.`);
                    }
                })
                .catch(error => {
                    this.displayApiCallError(error);
                });
        },

        removeProductFromDiscount(discountProductId) {
            this.apiRemoveQuantityDiscountProduct(discountProductId)
                .then(response => {
                    if (typeof response.data !== 'undefined' && typeof response.data !== 'undefined') {
                        this.reloadQuantityDiscountProducts();
                        this.notifySuccess('Product removed from discount.');
                    }
                })
                .catch(error => {
                    this.displayApiCallError(error);
                });
        },

        saveDiscountConfiguration() {
            this.showLoading();
            const config = Object.fromEntries(Object.entries(this.configuration).filter(([_, v]) => v !== null));
            this.apiPutQuantityDiscount(this.discount.id, {...this.discount, configuration: JSON.stringify(config)})
                .then(response => {
                    if (typeof response.data !== 'undefined' && typeof response.data.data !== 'undefined') {
                        this.discount = {
                            ...response.data.data,
                            configuration: JSON.parse(response.data.data.configuration)
                        };
                        this.notifySuccess('Discount configuration saved.');
                    }
                })
                .catch(error => {
                    this.displayApiCallError(error);
                })
                .finally(() => {
                    this.hideLoading();
                    this.$bvModal.hide('configuration-modal');
                });
        },
    }
}
</script>

<style lang="scss" scoped>
.setting-list {
    width: 100%;
    color: #495057;
    display: flex;
    align-items: flex-start;
    margin-bottom: 5px;
}

.remove-product {
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
