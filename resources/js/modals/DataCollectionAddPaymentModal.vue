<template>
    <b-modal body-class="ml-0 mr-0 p-0" :id="modalId" size="md" scrollable no-fade
             hide-header hide-footer>
        <div class="card card-default mb-0">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>Add Payment</span>
                </div>
            </div>
            <div class="card-body">
                <div class="input-group mb-3">
                    <input id="transaction_payment_amount"
                           tabindex="0"
                           v-model="amount"
                           type="number"
                           inputmode="numeric"
                           min="0"
                           :max="maxAmount"
                           step="0.01"
                           class="form-control text-center"
                    >
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
import helpers from "../mixins/helpers";

export default {
    mixins: [api],

    beforeMount() {
        Modals.EventBus.$on(`show::modal::${this.modalId}`, (data) => {
            this.$bvModal.show(this.modalId);
            if (typeof data.paymentDetails !== 'undefined' && typeof data.paymentDetails.maxAmount !== 'undefined') {
                this.maxAmount = data.paymentDetails.maxAmount;
            }
        })
    },

    data() {
        return {
            modalId: 'data-collection-data-collection-add-payment-modal',
            amount: 0,
            maxAmount: 0
        }
    },

    methods: {
        closeModal(saveChanges = false) {
            this.$bvModal.hide(this.modalId);

            Modals.EventBus.$emit(`hide::modal::${this.modalId}`, {
                amount: this.amount,
                saveChanges
            });
        }
    }
};
</script>
