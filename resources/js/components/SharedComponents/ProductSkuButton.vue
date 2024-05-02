<template>
  <a href="" @click.prevent="showProductDetailsModal" class="font-weight-bold">{{ product_sku }}</a>
</template>
<script>
import api from "../../mixins/api.vue";

export default {
    mixins: [api],

    name: 'product-sku-button',

    props: {
        product_sku: {},
    },

    methods: {
        showProductDetailsModal() {
            this.apiGetProducts({'filter[sku_or_alias]': this.product_sku})
                .then(response => {
                    this.$modal.showProductDetailsModal(response.data.data[0]['id']);
                })
                .catch(error => {
                    this.displayApiCallError(error);
                });
        },
    }
}
</script>
