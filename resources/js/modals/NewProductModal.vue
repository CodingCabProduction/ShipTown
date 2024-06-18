<template>
    <b-modal body-class="ml-0 mr-0 pl-1 pr-1" :id="modal_id" @hidden="emitNotification" size="xl" scrollable no-fade>
        <template #modal-header>
            <span>New Product</span>
        </template>

        <div class="container">
            <input id="newProductSku" type="text" :disabled="product !== null" v-model="newProduct.sku" class="form-control mb-2" placeholder="Product SKU">
            <input id="newProductName" type="text" v-model="newProduct.name" class="form-control mb-2" placeholder="Product Name">
            <input id="newProductPrice" type="number" :disabled="product !== null"  v-model="newProduct.price" class="form-control" placeholder="Product Price">
        </div>
        <template #modal-footer>
            <b-button variant="secondary" class="float-right" @click="$bvModal.hide(modal_id);">
                Cancel
            </b-button>
            <b-button variant="primary" class="float-right" @click="createNewProduct">
                Create
            </b-button>
        </template>
    </b-modal>
</template>

<script>

import ProductCard from "../components/Products/ProductCard.vue";
import api from "../mixins/api.vue";
import Modals from "../plugins/Modals";

export default {
    components: {ProductCard},
    mixins: [api],

    beforeMount() {
        Modals.EventBus.$on('show::modal::'+ this.modal_id , (data) => {
            this.product = data['product'];

            this.newProduct = {
                sku: '',
                name: '',
                price: '0.00',
            };

            if (this.product) {
                this.newProduct.sku = this.product.sku;
                this.newProduct.name = this.product.name;
                this.newProduct.price = this.product.price;
            }

            this.$bvModal.show(this.modal_id);
        })
    },

    data() {
        return {
            newProduct: {
                sku: '',
                name: '',
                price: '',
            },
            modal_id: 'new-product-modal',
            product: null
        }
    },

    methods: {
        createNewProduct() {
            this.apiPostProducts(this.newProduct)
                .then(response => {
                    this.$bvModal.hide(this.modal_id);
                })
                .catch(error => {
                    this.displayApiCallError(error);
                })
        },

        emitNotification() {
            Modals.EventBus.$emit('hide::modal::'+ this.modal_id, this.newProduct);
        }
    }
};

</script>
