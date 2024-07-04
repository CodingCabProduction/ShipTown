<template>
    <div>
        <div id="loading"></div>
        <div v-if="! order && getUrlParameter('step', '')=== 'select'" class="row col text-center mt-3" >
            <div v-for="bookmark in bookmarks" class="col-12 mt-1">
                <a dusk="startAutopilotButton" type="button" class="btn btn-primary col" :href="bookmark['url']">
                    {{ bookmark['name'] }}
                </a>
            </div>
<!--            <div class="col">-->
<!--            <button dusk="startAutopilotButton" type="button"  class="btn btn-primary" @click.prevent="loadNextOrder">-->
<!--                    Start AutoPilot Packing-->
<!--            </button>-->
<!--            </div>-->
        </div>

        <div v-if="finished" class="m-auto text-center">
            You've finished packing all orders!<br>
            <span class="small">There are no more orders to pack with specified filters</span>
        </div>

        <template v-if="order">
            <packsheet-page
                :key="'order_id_' + order.id"
                :order_id="order.id"
                :previous_order_id="previous_order_id"
                @orderCompleted="loadNextOrder">
            </packsheet-page>
        </template>
    </div>
</template>

<script>
    import url from "../mixins/url";
    import api from "../mixins/api";
    import beep from "../mixins/beep";
    import Vue from "vue";
    import helpers from "../mixins/helpers";
    import loadingOverlay from "../mixins/loading-overlay";

    export default {
        mixins: [loadingOverlay, api, beep, url, helpers],

        data: function() {
            return {
                finished: false,
                order: null,
                order_id: null,
                previous_order_id: null,
                bookmarks: [],
            };
        },

        mounted() {
            if(Vue.prototype.$currentUser['warehouse_id']) {
                this.setUrlParameter('inventory_source_warehouse_id', Vue.prototype.$currentUser['warehouse_id']);
            }

            this.apiGetNavigationMenu({
                'filter[group]': 'packlist'
            })
                .then((response) => {
                    this.bookmarks = response.data.data;
                })

            if (this.getUrlParameter('step', '') === '') {
                this.loadNextOrder();
            }
        },

        methods: {
            loadNextOrder() {
                if(! Vue.prototype.$currentUser['warehouse_id']) {
                    this.notifyError('User does not have warehouse assigned! Please assign in Settings->User');
                    return;
                }

                this.showLoading();

                let params = {
                    'filter[status]': this.getUrlParameter('status',''),
                    'filter[inventory_source_warehouse_id]': this.getUrlParameter('inventory_source_warehouse_id', this.currentUser()['warehouse_id']),
                    'sort': this.getUrlParameter('sort', 'order_placed_at'),
                };

                this.apiGetPacklistOrder(params)
                    .then((response) => {
                        this.previous_order_id = this.order ? this.order['id'] : null;

                        // we use array here so we can use v-for component
                        // and auto destroy when loading next order
                        this.order = response.data.data;
                        this.hideLoading();
                    })
                    .catch((error) => {
                        this.hideLoading();

                        let msg = error.response.data.errors;

                        if (error.response.status === 404) {
                            this.finished = true;
                            // msg = "No orders available with specified filters"
                            return;
                        }
                        this.notifyError(msg);
                        this.errorBeep();
                    })
            },
        },
    }
</script>


<style lang="scss">

</style>
