<template>
<div>
    <card>
        <text-card label="transaction number" :text="transaction['transaction_number']"></text-card>
        <number-card label="total_to_pay" :number="transaction['total_to_pay']"></number-card>
        <number-card label="total paid" :number="transaction['total_paid']"></number-card>
        <number-card label="total_outstanding" :number="transaction['total_outstanding']"></number-card>
    </card>
    <card>
        <template v-for="payment in transaction['payments']">
            {{ payment['name'] }} {{ payment['amount'] }} <br>
        </template>
        <input id="payment_amount" class="form-control"  placeholder="Payment amount">
        <button type="button" class="btn btn-primary fa-pull-right m-1 btn-sm" @click="addPaymentCash">Add Payment Cash</button>
        <button type="button" class="btn btn-primary fa-pull-right m-1 btn-sm" @click="addPaymentCard">Add Payment Clover</button>
    </card>
    <div class="row mb-2 pl-1 pr-1">
        <div class="flex-fill">
            <barcode-input-field id="barcode_input" @barcodeScanned="addProductToTransaction" placeholder="Scan or type sku or product barcode" ref="barcode_input"/>
        </div>

        <button type="button" v-b-modal="'quick-actions-modal'" class="btn btn-primary ml-2"><font-awesome-icon icon="cog" class="fa-lg"></font-awesome-icon></button>
    </div>

    <table>
        <tr>
            <th>Barcode</th>
            <th>Quantity</th>
            <th>Full Price</th>
            <th>Current Price</th>
            <th>Total</th>
        </tr>
        <template v-for="transactionEntry in transaction['entries']" >
            <tr>
                <td>{{ transactionEntry['barcode'] }}</td>
                <td>{{ transactionEntry['quantity'] }}</td>
                <td>{{ transactionEntry['full_price'] }}</td>
                <td>{{ transactionEntry['current_price'] }}</td>
                <td>{{ transactionEntry['total'] }}</td>
            </tr>
        </template>
    </table>


    <b-modal id="quick-actions-modal" no-fade hide-header @hidden="setFocusElementById('barcode-input')">
        <stocktake-input v-bind:auto-focus-after="100" ></stocktake-input>
        <hr>
        <button type="button" class="btn btn-primary fa-pull-right m-1 btn-sm" @click="printReceipt">Print Receipt</button>
        <button type="button" class="btn btn-primary fa-pull-right m-1 col" @click="saveTransaction">Save Transaction</button>
        <button type="button" class="btn btn-primary fa-pull-right m-1 col" @click="clearTransaction">Clear Transaction</button>
        <template #modal-footer>
            <b-button variant="secondary" class="float-right" @click="$bvModal.hide('quick-actions-modal');">
                Cancel
            </b-button>
            <b-button variant="primary" class="float-right" @click="$bvModal.hide('quick-actions-modal');">
                OK
            </b-button>
        </template>
    </b-modal>
</div>
</template>

<script>
import helpers from "../mixins/helpers";
import api from "../mixins/api.vue";

export default {
    mixins: [helpers, api],

    data() {
        return {
            transaction: {
                transaction_number: '#123378819',
                entries: [
                    {
                        barcode: '593775',
                        quantity: 2,
                        full_price: 100,
                        current_price: 90
                    },
                    {
                        barcode: '45',
                        quantity: 1,
                        full_price: 50,
                        current_price: 45
                    }
                ],
                payments: [
                    {
                        'name': 'cash',
                        'amount': 1251
                    }
                ],
                total_to_pay: 12.51,
                total_paid: 12.51,
                total_outstanding: 3.45
            }
        }
    },
    watch: {
        'transaction': {
            handler: function () {
                this.saveTransaction();
            },
            deep: true
        },
        'transaction.entries': {
            handler: function (val, oldVal) {
                // this.notifyError('Implement update total functionality');
            },
            deep: true
        }
    },
    methods: {
        addProductToTransaction(barcode) {
            this.transaction['entries'].push({
                barcode: barcode,
                quantity: 2,
                full_price: 100,
                current_price: 90
            });

            // this.notifyError('Implement add product to transaction functionality');

            // this.notifyError('Implement update total functionality');
        },

        printReceipt() {
            // Please use RAW PRINT from printnode for this functionality
            // this.notifyError('Implement print receipt functionality');
        },

        addPayment(name, amount) {
            // this.notifyError('Implement add payment functionality');

            this.transaction['payments'].push({
                'name': name,
                'amount': amount
            });
        },

        addPaymentCash() {
            this.addPayment('cash', 1251);
        },

        addPaymentCard() {
            this.addPayment('clover', 1251);
            this.addPayment('auth id:', 23721973912);
            this.addPayment('las 4 digits:', 1234);
            this.addPayment('transaction time:', 202404120114);
        },

        clearTransaction() {
            this.transaction = {
                entries: [],
                payments: [],
                transaction_number: '',
                total_to_pay: 0,
                total_paid: 0,
                total_outstanding: 0
            };

            this.$bvModal.hide('quick-actions-modal');
        },

        saveTransaction() {
            // we should post the transaction to the server for immediate save only
            // this should be best way to make a backup of the transaction in case of any failures
            // /api/transactions/in-progress ??
            // this.notifyError('Implement save transaction functionality');

            // we might develop a way to retrieve the transaction from the server later too

           // $response =  this.apiPostTransaciotn(response->transaction_id, transaction);
           // user->update(['active_transafgion_id' => response->transaction_id]);
        },
    }

}
</script>


<style lang="scss">

</style>
