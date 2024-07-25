<template>
    <div>
        <data-collector-transaction-page v-if="data_collection" :data_collection_id="data_collection.id"></data-collector-transaction-page>
        <div v-else>
            <h1>Point of Sale</h1>
            <input type="text" placeholder="Username" class="form-control">
            <input type="password" placeholder="Password" class="form-control" >
            <button @click="startTransaction" class="btn btn-primary">Start Transaction</button>
        </div>
    </div>
</template>
<script>
import api from "../../mixins/api.vue";
import helpers from "../../helpers";

export default {
    name: "PointOfSalePage",

    mixins: [helpers, api],

    data() {
        return {
            data_collection: null,
        }
    },

    mounted() {
        this.startTransaction();
    },

    methods: {
        startTransaction() {
            let customUuid = 'TRANSACTION_IN_PROGRESS_FOR_' + 'USER_' + this.currentUser().id + '_' + this.currentUser().name;

            this.apiGetDataCollector({'filter[custom_uuid]': customUuid})
                .then(response => {
                    console.log(response.data.data);
                    if (response.data.data.length > 0) {
                        this.data_collection = response.data.data[0];
                    } else {
                        this.createTransactionNewTransaction(customUuid);
                    }
                })
                .catch(error => {
                    this.displayApiCallError(error);
                });
        },

        createTransactionNewTransaction: function (customUuid) {
            let data = {
                custom_uuid: customUuid,
                warehouse_id: this.currentUser().warehouse_id,
                name: 'TRANSACTION IN PROGRESS',
            };

            this.apiPostDataCollection(data)
                .then(response => {
                    this.data_collection = response.data.data;
                })
                .catch(error => {
                    this.displayApiCallError(error);
                });
        }
    }
}
</script>
<style scoped>

</style>
