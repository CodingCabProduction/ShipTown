<template>
    <b-modal body-class="ml-0 mr-0 pl-1 pr-1" id="product-details-modal" size="xl" scrollable no-fade hide-header>
        <product-card v-if="product" :product="product" :expanded="true" :for-modal="true"/>
        <template #modal-footer>
            <b-button variant="secondary" class="float-right" @click="$bvModal.hide('product-details-modal');">
                Cancel
            </b-button>
            <b-button variant="primary" class="float-right" @click="$bvModal.hide('product-details-modal');">
                OK
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
        this.product = null;

        Modals.EventBus.$on('show::modal::product-details-modal', (data) => {
            this.loadProduct(data.product_id);
            this.$bvModal.show('product-details-modal');
        })
    },

    data() {
        return {
            product: null
        }
    },

    methods: {
        loadProduct(product_id) {
            let params = {
                'filter[id]': product_id,
                'include': 'inventory,tags,prices,aliases,inventory.warehouse,inventoryMovementsStatistics,inventoryTotals',
            };

            this.apiGetProducts(params)
                .then(response => {
                    this.product = response.data.data[0];
                })
                .catch(error => {
                    this.displayApiCallError(error);
                })
        },
    }
};

</script>
