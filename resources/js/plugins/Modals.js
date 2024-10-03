const Modals = {
    install(Vue, options) {
        this.EventBus = new Vue()

        Vue.prototype.$modal = {
            show(modal, data) {
                Modals.EventBus.$emit('show::modal::' + modal, data);
            },

            showRecentInventoryMovementsModal(inventory_id) {
                this.show('recent-inventory-movements-modal', {'inventory_id': inventory_id});
            },

            showDataCollectorQuantityRequestModal(data_collection_id, sku_or_alias, field_name) {
                this.show('data-collector-quantity-request-modal', {
                    'data_collection_id': data_collection_id,
                    'sku_or_alias': sku_or_alias,
                    'field_name': field_name
                });
            },

            showProductDetailsModal(product_id) {
                this.show('product-details-modal', {'product_id': product_id});
            },

            showBarcodeScanner(callback) {
                this.show('barcode-scanner', {'callback': callback});
            },

            showUpsertProductModal(product = null) {
                this.show('new-product-modal', {'product': product});
            },

            showFindProductModal(callback) {
                this.show('find-product-modal', {'callback': callback});
            },

            showAddNewQuantityDiscountModal(discount = null) {
                this.show('new-quantity-discount-modal', {'discount': discount});
            },

            showSetTransactionPrinterModal(printer = null) {
                this.show('set-transaction-printer-modal', {'printer': printer});
            },

            showFindAddressModal() {
                this.show('find-address-modal');
            },

            showAddNewAddressModal(address = null) {
                this.show('new-address-modal', {'address': address});
            },

            showSetPaymentTypeModal(paymentType = null) {
                this.show('data-collection-choose-payment-type-modal', {'paymentType': paymentType});
            },

            showAddPaymentModal(paymentDetails = null) {
                this.show('data-collection-data-collection-add-payment-modal', {'paymentDetails': paymentDetails});
            },

            showNewPaymentTypeModal(paymentType = null) {
                this.show('module-data-collector-payments-new-payment-type-modal', {'paymentType': paymentType});
            },
        }
    }
}

export default Modals
