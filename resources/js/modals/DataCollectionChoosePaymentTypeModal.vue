<template>
    <b-modal body-class="ml-0 mr-0 p-0" :id="modalId" size="md" scrollable no-fade
             hide-header hide-footer>
        <div class="card card-default mb-0">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>Payment Type Selection</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row align-items-center mb-2" v-for="type in paymentTypes" :key="type['id']"
                     :class="{'table-primary': isSelectedPaymentType(type['id'])}" style="min-height: 29px;">
                    <div class="col-6">{{ type['name'] }}</div>
                    <div class="col-6 text-right">
                        <button type="button" @click.prevent="setChosenPaymentType(type)"
                                v-show="!isSelectedPaymentType(type['id'])" class="btn btn-primary btn-sm">Select
                        </button>
                    </div>
                </div>

                <hr class="mt4">

                <div class="row mt-4 d-flex justify-content-end">
                    <b-button variant="secondary" class="mr-2" @click="closeModal">Cancel</b-button>
                    <b-button variant="primary" @click="closeModal(true)">Save</b-button>
                </div>
            </div>
        </div>
    </b-modal>
</template>

<script>

import api from "../mixins/api.vue";
import Modals from "../plugins/Modals";

export default {
    mixins: [api],

    beforeMount() {
        Modals.EventBus.$on(`show::modal::${this.modalId}`, (data) => {
            this.loadPaymentTypes();
            this.selectedPaymentType = data.paymentType;
            this.$bvModal.show(this.modalId);
        })
    },

    data() {
        return {
            modalId: 'data-collection-choose-payment-type-modal',
            selectedPaymentType: null,
            paymentTypes: [],
        }
    },

    methods: {
        loadPaymentTypes() {
            this.apiGetPaymentTypes()
                .then(({data}) => {
                    this.paymentTypes = data.data;
                })
                .catch(e => {
                    this.displayApiCallError(e);
                });
        },

        setChosenPaymentType(paymentType) {
            this.selectedPaymentType = paymentType;
        },

        isSelectedPaymentType: function (paymentTypeId) {
            if (this.selectedPaymentType === null) {
                return false;
            }

            if (typeof this.selectedPaymentType.payment_type_id !== 'undefined') {
                return this.selectedPaymentType && this.selectedPaymentType.payment_type_id && paymentTypeId === this.selectedPaymentType.payment_type_id;
            } else {
                return this.selectedPaymentType && this.selectedPaymentType.id && paymentTypeId === this.selectedPaymentType.id;
            }
        },

        closeModal(saveChanges = false) {
            this.$bvModal.hide(this.modalId);

            Modals.EventBus.$emit(`hide::modal::${this.modalId}`, {
                paymentType: this.selectedPaymentType,
                saveChanges: saveChanges
            });
        }
    }
};
</script>
