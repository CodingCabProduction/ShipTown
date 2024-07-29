<template>
    <b-modal body-class="ml-0 mr-0 pl-1 pr-1" :id="modal_id" @hidden="emitNotification" size="xl" scrollable no-fade>
        <template #modal-header>
            <span>New Quantity Discount</span>
        </template>

        <div class="container">
            <input id="discountName" type="text" :disabled="!isCreatingNewDiscount" v-model="newDiscount.name"
                   class="form-control mb-2" placeholder="Discount name" required>
            <select name="discountType" id="discountType" :disabled="!isCreatingNewDiscount" v-model="newDiscount.type"
                    class="form-control mb-2" required>
                <option value="">-</option>
                <option value="BUY_X_GET_Y_FOR_Z_PRICE">Buy X, get Y for Z price</option>
                <option value="BUY_X_GET_Y_FOR_Z_PERCENT_DISCOUNT">Buy X, get Y for Z percent discount</option>
                <option value="BUY_X_GET_Y_PRICE">Buy X for Y price</option>
                <option value="BUY_X_FOR_Y_PERCENT_DISCOUNT">Buy X for Y percent discount</option>
            </select>
        </div>
        <template #modal-footer>
            <b-button variant="secondary" class="float-right" @click="$bvModal.hide(modal_id);">
                Cancel
            </b-button>
            <b-button variant="primary" class="float-right" @click="createNewDiscount">
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
            newDiscount: {
                name: '',
                type: '',
            },
            modal_id: 'new-quantity-discount-modal',
            discount: undefined,
        }
    },

    beforeMount() {
        Modals.EventBus.$on('show::modal::' + this.modal_id, (data) => {
            this.discount = data['discount'];

            this.newDiscount = {
                name: '',
                type: '',
            };

            if (this.discount) {
                this.newDiscount.name = this.discount.name;
                this.newDiscount.type = this.discount.type;
            }

            this.$bvModal.show(this.modal_id);
        })
    },

    computed: {
        isCreatingNewDiscount() {
            return this.discount === null || (this.discount === undefined);
        }
    },

    methods: {
        createNewDiscount() {
            this.apiPostQuantityDiscount(this.newDiscount)
                .then(({data}) => {
                    this.$bvModal.hide(this.modal_id);
                    if (typeof data.data !== 'undefined' && typeof data.data.id !== 'undefined') {
                        window.location.href = '/admin/settings/modules/quantity-discounts/' + data.data.id;
                    }
                })
                .catch(error => {
                    this.displayApiCallError(error);
                })
        },

        emitNotification() {
            Modals.EventBus.$emit('hide::modal::' + this.modal_id, this.newProduct);
        }
    }
};

</script>
