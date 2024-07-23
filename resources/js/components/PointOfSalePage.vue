<template>
    <div>
        <div class="card my-1">
            <div class="card-body m-auto p-0 row">
                <!--            <text-card label="transaction number" :text="transaction['transaction_number']"></text-card>-->
                <div class="col-4">
                    <number-card label="total_to_pay" :number="transactionTotal"></number-card>
                </div>
                <div class="col-4">
                    <number-card label="total paid" :number="transactionTotalPaid"></number-card>
                </div>
                <div class="col-4">
                    <number-card label="total_outstanding"
                                 :number="transactionTotal - transactionTotalPaid"></number-card>
                </div>
            </div>
        </div>

        <search-and-option-bar-observer/>
        <search-and-option-bar :isStickable="true">
            <barcode-input-field
                :input_id="'barcode_input'"
                placeholder="Enter SKU"
                ref="barcode"
                :url_param_name="'search'"
                @barcodeScanned="addProductToTransaction"
            />
            <template v-slot:buttons>
                <button v-b-modal="'payments-modal'" type="button" class="btn btn-primary ml-2">
                    <font-awesome-icon icon="credit-card" class="fa-lg"></font-awesome-icon>
                </button>
                <top-nav-button v-b-modal="'quick-actions-modal'"/>
            </template>
        </search-and-option-bar>

        <div class="table-responsive">
            <table class="table-hover w-100">
                <thead>
                <tr>
                    <th>Barcode</th>
                    <th>Quantity</th>
                    <!--                    <th>Cost Price</th>-->
                    <th>Full Price</th>
                    <!--                    <th>Current Price</th>-->
                    <th>Sold Price</th>
                    <!--                    <th>Total Cost Price</th>-->
                    <th>Total</th>
                    <th>Price Source</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="transactionEntry in transaction['entries']" :key="transactionEntry['barcode']">
                    <td>{{ transactionEntry['barcode'] }}</td>
                    <td>{{ transactionEntry['quantity'] }}</td>
                    <!--                        <td>{{ transactionEntry['cost_price'] }}</td>-->
                    <td>{{ transactionEntry['full_price'] }}</td>
                    <!--                        <td>{{ transactionEntry['current_price'] }}</td>-->
                    <td>{{ transactionEntry['sold_price'] }}</td>
                    <!--                        <td>{{ transactionEntry['total_cost_price'] }}</td>-->
                    <td>{{ transactionEntry['total_sold_price'] }}</td>
                    <td>{{ transactionEntry['price_source'] }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <b-modal id="payments-modal" no-fade hide-header @hidden="setFocusElementById('barcode_input')">
            <card>
                <template v-for="payment in transaction['payments']">
                    {{ payment['name'] }} {{ payment['amount'] }} <br>
                </template>
                <input id="payment_amount" class="form-control" placeholder="Payment amount" v-model="paymentAmount">
                <button type="button" class="btn btn-primary fa-pull-right m-1 btn-sm" @click="addPaymentCash">
                    Add Payment Cash
                </button>
                <button type="button" class="btn btn-primary fa-pull-right m-1 btn-sm" @click="addPaymentCard">
                    Add Payment Clover
                </button>
            </card>
        </b-modal>

        <b-modal id="quick-actions-modal" no-fade hide-header @hidden="setFocusElementById('barcode_input')">
            <stocktake-input v-bind:auto-focus-after="100"></stocktake-input>
            <hr>
            <button type="button" class="btn btn-primary m-1 col" @click="printReceipt">Print Receipt</button>
            <button type="button" class="btn btn-primary m-1 col" @click="saveTransaction">Save Transaction</button>
            <button type="button" class="btn btn-primary m-1 col" @click="clearTransaction">Clear Transaction</button>
            <button type="button" class="btn btn-primary m-1 col" @click="clearTransaction" disabled>Return</button>
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

    mounted() {
        this.loadTransaction();
    },

    computed: {
        transactionTotal() {
            return this.transaction.entries.reduce((acc, entry) => acc + Number(entry.sold_price) * Number(entry.quantity), 0);
        },
        transactionTotalPaid() {
            return this.transaction.payments.reduce((acc, payment) => acc + Number(payment.amount), 0);
        },
    },

    data() {
        return {
            paymentAmount: 0,
            transaction: {
                transaction_number: '', //'#123378819',
                entries: [
                    // {
                    //     barcode: '593775',
                    //     quantity: 2,
                    //     cost_price: 50,
                    //     full_price: 100,
                    //     current_price: 90,
                    //     sold_price: 90,
                    // },
                    // {
                    //     barcode: '45',
                    //     quantity: 1,
                    //     cost_price: 50,
                    //     full_price: 50,
                    //     current_price: 45,
                    //     sold_price: 45,
                    // }
                ],
                payments: [
                    // {
                    //     'payment_time': '202404120114',
                    //     'name': 'cash',
                    //     'amount': 100,
                    //     'auth_id': '123456',
                    //     'last_4_digits': '1234',
                    // }
                ],
                total_to_pay: 0,
                total_paid: 0,
                total_outstanding: 0
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
    },
    methods: {
        addProductToTransaction(barcode) {
            if (barcode.trim() === '') {
                return;
            }

            this.apiGetProducts({'filter[sku_or_alias]': barcode, 'include': 'prices'})
                .then(response => {
                    if (response.data.data.length === 0) {
                        this.notifyError('Product "' + barcode + '" not found');
                        return;
                    }

                    this.beep();

                    const product = response.data.data[0];

                    const productPrices = product['prices'][this.currentUser().warehouse_code ?? 'DUB'];

                    let quantity = 1;

                    if (this.transaction['entries'].findIndex(entry => entry['barcode'] === barcode) !== -1) {
                        const entry = this.transaction['entries'].find(entry => entry['barcode'] === barcode);
                        entry['quantity'] += 1;
                        entry['total_cost_price'] = entry['quantity'] * productPrices['current_price'];
                        entry['total_sold_price'] = entry['quantity'] * productPrices['current_price'];

                        this.setPriceSource(entry, productPrices);
                    } else {
                        const entry = {
                            barcode: product.sku,
                            quantity: quantity,
                            cost_price: productPrices['cost'],
                            full_price: productPrices['price'],
                            current_price: productPrices['current_price'],
                            sold_price: productPrices['current_price'],
                            total_cost_price: quantity * productPrices['current_price'],
                            total_sold_price: quantity * productPrices['current_price'],
                            price_source_id: 0,
                        };

                        this.setPriceSource(entry, productPrices);
                        this.transaction['entries'].unshift(entry);
                    }
                }).catch(error => {
                this.displayApiCallError(error);
            });
        },

        printReceipt() {
            this.apiPostPrintJob({
                'printer_id': this.currentUser().printer_id,
                'content_type': 'raw_base64',
                'content': JSON.stringify(this.transaction)
            })
                .then(() => {
                    this.notifySuccess('Receipt printed', false);
                })
                .catch(error => {
                    this.displayApiCallError(error);
                })
            // Please use RAW PRINT from printnode for this functionality
            // this.notifyError('Implement print receipt functionality');
        },

        addPayment(name, amount) {
            // this.notifyError('Implement add payment functionality');

            this.transaction['payments'].push({
                'name': name,
                'amount': amount,
            });
        },

        addPaymentCash() {
            this.addPayment('cash', this.paymentAmount);
        },

        addPaymentCard() {
            this.addPayment('clover', this.paymentAmount);
            // this.addPayment('auth id:', 23721973912);
            // this.addPayment('card 4 digits:', 1234);
            // this.addPayment('transaction time:', 202404120114);
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

        loadTransaction() {
            if (!this.currentUser().active_transaction_id) {
                return;
            }

            this.apiGetTransactions({
                'filter[id]': this.currentUser().active_transaction_id
            })
                .then(response => {
                    this.transaction = response.data.data[0]['raw_data'];
                })
                .catch(error => {
                    this.displayApiCallError(error);
                });
        },

        saveTransaction() {
            if (this.currentUser().active_transaction_id) {
                this.apiPutTransaction(this.currentUser().active_transaction_id, {'raw_data': this.transaction})
                    .catch(error => {
                        this.displayApiCallError(error);
                    });
                return;
            }

            this.apiPostTransaction({'raw_data': this.transaction})
                .then(response => {
                    this.currentUser().active_transaction_id = response.data.data.id;

                    this.apiPostUserUpdate(this.currentUser().id, {'active_transaction_id': response.data.data.id})
                        .catch(error => {
                            this.displayApiCallError(error);
                        });
                })
                .catch(error => {
                    this.displayApiCallError(error);
                });
            // we should post the transaction to the server for immediate save only
            // this should be the best way to make a backup of the transaction in case of any failures
            // /api/transactions/in-progress ??
            // this.notifyError('Implement save transaction functionality');

            // we might develop a way to retrieve the transaction from the server later too

            // $response =  this.apiPostTransaction(response->transaction_id, transaction);
            // user->update(['active_transaction_id' => response->transaction_id]);
        },

        setPriceSource(entry, prices) {
            if (prices['price'] > prices['current_price']) {
                entry['price_source'] = 'SALE_PRICE';
            } else {
                entry['price_source'] = 'FULL_PRICE';
            }
        }
    }
}
</script>
