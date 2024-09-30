<template>
    <div>
        <data-collector-transaction-page v-if="dataCollection" :data_collection_id="dataCollection.id"
                                         @transactionFinished="clearSelectedDataCollection"/>
        <div v-else>
            <h1>Point of Sale</h1>
            <input type="text" placeholder="Username" class="form-control">
            <input type="password" placeholder="Password" class="form-control">
            <button @click="startNewTransaction" class="btn btn-primary">Start Transaction</button>
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
            dataCollection: null,
            dataCollectionType: 'App\\Models\\DataCollectionTransaction'
        }
    },

    mounted() {
        this.startNewTransaction();
    },

    methods: {
        clearSelectedDataCollection() {
            this.dataCollection = null;
        },

        startNewTransaction() {
            let customUuid = `TRANSACTION_IN_PROGRESS_FOR_USER_${this.currentUser().id}_${this.currentUser().name}`;

            this.apiGetDataCollector({'filter[custom_uuid]': customUuid})
                .then(response => {
                    if (response.data.data.length > 0) {
                        this.dataCollection = response.data.data[0];
                    } else {
                        this.createNewTransaction(customUuid);
                    }
                })
                .catch(error => {
                    this.displayApiCallError(error);
                });
        },

        createNewTransaction(customUuid) {
            let data = {
                custom_uuid: customUuid,
                warehouse_code: this.currentUser().warehouse_code,
                warehouse_id: this.currentUser().warehouse_id,
                name: 'TRANSACTION IN PROGRESS',
                type: this.dataCollectionType
            };

            this.apiPostDataCollection(data)
                .then(response => {
                    this.dataCollection = response.data.data;
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
