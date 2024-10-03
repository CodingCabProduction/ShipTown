<template>
    <b-modal body-class="ml-0 mr-0 pl-1 pr-1" :id="modalId" size="xl" scrollable no-fade hide-header>
        <search-and-option-bar>
            <barcode-input-field
                :input_id="'customer_search_input'"
                placeholder="Search"
                ref="barcode"
                :showKeyboardOnFocus="true"
                @barcodeScanned="findText"
            />
            <template v-slot:buttons>
                <button @click="showNewAddressModal" type="button" class="btn btn-primary ml-2">
                    <font-awesome-icon icon="plus" class="fa-lg"></font-awesome-icon>
                </button>
            </template>
        </search-and-option-bar>

        <template v-if="isLoading === false && addresses !== null && addresses.length === 0">
            <div class="text-secondary small text-center mt-3">
                No records found<br>
                Click + to create one<br>
            </div>
        </template>

        <div class="addresses">
            <div v-for="address in addresses" class="addresses__item"
                 :class="{'addresses__item--selected': selectedShippingAddress === address.id || selectedBillingAddress === address.id}">
                <p><strong>Company: </strong>{{ address?.company ?? '-' }}</p>
                <p><strong>Address 1: </strong>{{ address?.address1 ?? '-' }}</p>
                <p><strong>Address 2: </strong>{{ address?.address2 ?? '-' }}</p>
                <p><strong>City: </strong>{{ address?.city ?? '-' }}</p>
                <p><strong>Post Code: </strong>{{ address?.postcode ?? '-' }}</p>
                <div class="addresses__itemButtons d-flex">
                    <button class="addresses__itemButton"
                            :class="{'addresses__itemButton--clicked': selectedBillingAddress === address.id}"
                            @click="selectBillingAddress(address.id)">
                        <template v-if="selectedBillingAddress === address.id">Selected as Billing Address</template>
                        <template v-else>Select as Billing Address</template>
                    </button>
                    <button class="addresses__itemButton"
                            :class="{'addresses__itemButton--clicked': selectedShippingAddress === address.id}"
                            @click="selectShippingAddress(address.id)">
                        <template v-if="selectedShippingAddress === address.id">Selected as Shipping Address</template>
                        <template v-else>Select as Shipping Address</template>
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div ref="loadingContainerOverride" style="height: 32px"></div>
            </div>
        </div>

        <template #modal-footer>
            <b-button variant="secondary" class="float-right" @click="closeModal">
                Cancel
            </b-button>
            <b-button variant="primary" class="float-right" @click="closeModal(true)">
                OK
            </b-button>
        </template>
    </b-modal>
</template>

<script>
import api from "../mixins/api.vue";
import url from "../mixins/url.vue";
import loadingOverlay from "../mixins/loading-overlay";
import Modals from "../plugins/Modals";

export default {
    components: {},

    mixins: [loadingOverlay, api, url],

    props: {
        transactionDetails: {
            type: Object,
            required: true
        }
    },

    beforeMount() {
        Modals.EventBus.$on(`show::modal::${this.modalId}`, () => {
            this.$bvModal.show(this.modalId);
        })
    },

    mounted() {
        if (this.transactionDetails) {
            this.selectedBillingAddress = this.transactionDetails?.billing_address_id ?? null;
            this.selectedShippingAddress = this.transactionDetails?.shipping_address_id ?? null;
        }

        Modals.EventBus.$on('hide::modal::new-address-modal', (data) => {
            if (typeof data.addressSaved !== 'undefined' && data.addressSaved) {
                this.searchText = '';
                this.addresses.unshift(data.address);
            }
        });
    },

    data() {
        return {
            callback: null,
            modalId: 'find-address-modal',
            addresses: [],
            selectedBillingAddress: null,
            selectedShippingAddress: null,
            searchText: ''
        }
    },

    methods: {
        selectBillingAddress(addressId) {
            this.selectedBillingAddress = addressId;
        },

        selectShippingAddress(addressId) {
            this.selectedShippingAddress = addressId;
        },

        findText(searchText) {
            this.searchText = searchText;
            this.findCustomersContainingSearchText();
        },

        findCustomersContainingSearchText() {
            this.showLoading();

            const params = {};
            params['filter[search]'] = this.searchText;

            this.apiGetAddresses(params)
                .then(({data}) => {
                    this.addresses = data.data;
                })
                .catch((error) => {
                    this.displayApiCallError(error);
                })
                .finally(() => {
                    this.hideLoading();
                });
            return this;
        },

        closeModal(saveChanges = false) {
            this.$bvModal.hide(this.modalId);

            Modals.EventBus.$emit(`hide::modal::${this.modalId}`, {
                billingAddress: this.selectedBillingAddress,
                shippingAddress: this.selectedShippingAddress,
                saveChanges: saveChanges
            });
        },

        showNewAddressModal() {
            this.$modal.showAddNewAddressModal();
        }
    }
};

</script>

<style lang="scss" scoped>
.addresses {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;

    &__item {
        flex: 0 0 calc(33.33333% - 20px + (20px / 3));
        max-width: calc(33.33333% - 20px + (20px / 3));
        padding: 10px;
        border: 1px solid #ced4da;
        border-radius: 4px;

        &--selected {
            border-color: #227dc7;
        }
    }

    &__itemButtons {
        gap: 10px;
    }

    &__itemButton {
        padding: 5px 10px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        background-color: #f8f9fa;
        cursor: pointer;
        transition: background-color 0.3s, color 0.3s;

        &--clicked {
            background-color: #227dc7;
            color: white;
        }
    }

    @media all and (max-width: 576px) {
        &__item {
            flex: 0 0 calc(50% - 20px + (20px / 2));
            max-width: calc(50% - 20px + (20px / 2));
        }
    }
}
</style>
