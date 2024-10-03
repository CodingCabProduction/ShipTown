<template>
    <b-modal body-class="ml-0 mr-0 pl-1 pr-1" :id="modalId" @hidden="emitNotification" size="xl" scrollable no-fade>
        <template #modal-header>
            <span>New Payment Type</span>
        </template>

        <div class="container">
            <input id="paymentTypeName" type="text" :disabled="!isCreatingNewPaymentType" v-model="newPaymentType.name"
                   class="form-control mb-2" placeholder="Discount name" required>
            <input id="paymentTypeCode" type="text" :disabled="!isCreatingNewPaymentType" v-model="newPaymentType.code"
                   class="form-control mb-2" placeholder="Discount name" required>
        </div>

        <template #modal-footer>
            <b-button variant="secondary" class="float-right" @click="$bvModal.hide(modalId);">
                Cancel
            </b-button>
            <b-button variant="primary" class="float-right" @click="createNewPaymentType">
                Create
            </b-button>
        </template>
    </b-modal>
</template>

<script>

import api from "../mixins/api.vue";
import Modals from "../plugins/Modals";

export default {
    mixins: [api],

    data() {
        return {
            newPaymentType: {
                name: '',
                code: '',
            },
            modalId: 'module-data-collector-payments-new-payment-type-modal',
            paymentType: undefined,
        }
    },

    beforeMount() {
        Modals.EventBus.$on(`show::modal::${this.modalId}`, (data) => {
            this.paymentType = data['paymentType'];

            this.newDiscount = {
                name: '',
                code: '',
            };

            if (this.paymentType) {
                this.newPaymentType.name = this.paymentType.name;
                this.newPaymentType.code = this.paymentType.code;
            }

            this.$bvModal.show(this.modalId);
        })
    },

    computed: {
        isCreatingNewPaymentType() {
            return this.paymentType === null || (this.paymentType === undefined);
        }
    },

    methods: {
        createNewPaymentType() {
            this.apiPostPaymentTypes(this.newPaymentType)
                .then(({data}) => {
                    this.$bvModal.hide(this.modalId);
                    if (typeof data.data !== 'undefined' && typeof data.data.id !== 'undefined') {
                    }
                })
                .catch(error => {
                    this.displayApiCallError(error);
                })
        },

        emitNotification() {
            Modals.EventBus.$emit(`hide::modal::${this.modalId}`, this.newProduct);
        }
    }
};

</script>
