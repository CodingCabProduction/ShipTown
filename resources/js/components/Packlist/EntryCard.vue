<template>
    <div class="row">
        <div class="col-lg-6">
            <div class="text-primary h5">{{ entry['name_ordered'] }}</div>
            <div>
                sku ordered: <b :class="entry['product_id'] ? '' : 'bg-warning'">{{ entry['sku_ordered'] }}</b>
            </div>
            <div>
                product: <b><a href="" @click.prevent="showProductDetailsModal">{{ productSku }}</a></b>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="row">
                <div class="col-6 small">
                    <div >
                        ordered: <b>{{ dashIfZero(Number(entry['quantity_ordered'])) }}</b>
                    </div>
                    <div >
                        price: <b>{{ dashIfZero(Number(entry['price'])) }}</b>
                    </div>
                    <div class="bg-warning" v-if="Number(entry['quantity_split']) > 0">
                        split: <b>{{ dashIfZero(Number(entry['quantity_split'])) }}</b>
                    </div>
                    <div>
                        picked: <b>{{ dashIfZero(Number(entry['quantity_picked'])) }}</b>
                    </div>
                    <div>
                        shipped: <b>{{ dashIfZero(Number(entry['quantity_shipped'])) }}</b>
                    </div>
                    <div v-bind:class="{ 'bg-warning': Number(entry['inventory_source_quantity']) <= 0 }">
                        inventory: <b>{{ dashIfZero(Number(entry['inventory_source_quantity'])) }}</b>
                    </div>
                </div>
                <div class="col-3 text-center" v-bind:class="{ 'bg-warning': Number(entry['quantity_ordered']) !== 1 }">
                    <small>to ship</small>
                    <h3>{{ dashIfZero(Number(entry['quantity_to_ship'])) }}</h3>
                </div>
                <div class="col-3 text-center">
                    <small>shelf</small>
                    <h3>{{ entry['inventory_source_shelf_location'] }}</h3>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "EntryCard",

        props: {
            entry: Object,
        },
        computed: {
            productSku() {
               return this.entry['product'] ? this.entry['product']['sku'] : '';
            },
            productUrl() {
                return '/products?filter[sku]=' + this.productSku;
            }
        },
        methods: {
            showProductDetailsModal() {
                this.$modal.showProductDetailsModal(this.entry['product']['id']);
            },

            dashIfZero(value) {
                return value === 0 ? '-' : value;
            },
        }
    }
</script>

<style scoped>

</style>
