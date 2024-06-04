<template>
    <ValidationObserver ref="form">
        <form class="form" @submit.prevent="submit" ref="loadingContainer">
            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="edit-name">Name</label>
                <div class="col-sm-9">
                    <ValidationProvider vid="name" name="name" v-slot="{ errors }">
                        <input v-model="name" :class="{
                            'form-control': true,
                            'is-invalid': errors.length > 0,
                        }" id="edit-name" required>
                        <div class="invalid-feedback">
                            {{ errors[0] }}
                        </div>
                    </ValidationProvider>
                </div>
            </div>
            <div v-if="!isCurrentUser" class="form-group row">
                <label for="edit-role_id" class="col-sm-3 col-form-label">Role</label>
                <div class="col-sm-9">
                    <ValidationProvider vid="role_id" name="role_id" v-slot="{ errors }">
                        <select v-model="roleId" :class="{
                                'form-control': true,
                                'is-invalid': errors.length > 0,
                            }"
                            id="edit-role_id">
                            <option value="" disabled>Select role</option>
                            <option v-for="(role, i) in roles" :key="i" :value="role.id">
                                {{ role.name }}
                            </option>
                        </select>
                    </ValidationProvider>
                </div>
            </div>
            <div class="form-group row">
                <label for="create-warehouse_id" class="col-sm-3 col-form-label">Warehouse</label>
                <div class="col-sm-9">
                    <ValidationProvider vid="warehouse_id" name="warehouse_id" v-slot="{ errors }">
                        <select v-model="selectedWarehouse" :class="{
                                'form-control': true,
                                'is-invalid': errors.length > 0,
                            }"
                            id="create-warehouse_id">
                            <option value=null key="0">---</option>
                            <option v-for="warehouse in warehouses" :key="warehouse.id" :value="warehouse">
                                {{ warehouse.name }}
                            </option>
                        </select>
                        <div class="invalid-feedback">
                            {{ errors[0] }}
                        </div>
                    </ValidationProvider>
                </div>
            </div>
            <div class="form-group row">
                <label for="edit-dashboard-uri" class="col-sm-3 col-form-label">Dashboard URI</label>
                <div class="col-sm-9">
                    <ValidationProvider vid="name" name="name" v-slot="{ errors }">
                        <input v-model="default_dashboard_uri" :class="{
                        'form-control': true,
                        'is-invalid': errors.length > 0,
                    }" id="edit-dashboard-uri" required>
                        <div class="invalid-feedback">
                            {{ errors[0] }}
                        </div>
                    </ValidationProvider>
                </div>
            </div>
        </form>
    </ValidationObserver>
</template>

<script>
import { ValidationObserver, ValidationProvider } from 'vee-validate';

import Loading from '../../mixins/loading-overlay';
import api from "../../mixins/api";

export default {
    mixins: [api, Loading],

    components: {
        ValidationObserver, ValidationProvider
    },

    mounted() {
        this.showLoading();
        this.apiGetUsers({
                'filter[user_id]': this.id,
                'include': 'roles,warehouse',
            })
            .then(({ data }) => {
                const user = data.data[0];
                this.name = user.name;
                this.roleId = user['roles'][0]['id'];
                this.selectedWarehouse = user.warehouse;
                this.default_dashboard_uri = user.default_dashboard_uri;
            })
            .catch(e => {
                this.displayApiCallError(e);
            })
            .finally(
                this.hideLoading
            );
    },

    props: {
        id: Number,
        roles: Array,
        warehouses: Array
    },

    data: () => ({
        name: null,
        roleId: null,
        selectedWarehouse: null,
        warehouseCode: null,
        default_dashboard_uri: null,
    }),

    methods: {
        submit() {
            this.showLoading();
            this.apiPostUserUpdate(this.id, {
                    name: this.name,
                    role_id: this.roleId,
                    default_dashboard_uri: this.default_dashboard_uri,
                    warehouse_id: this.selectedWarehouse === 'null' ?  null : ['id'],
                    warehouse_code: this.selectedWarehouse === 'null' ?  null : ['code'],
                })
                .then(({ data }) => {
                    this.$snotify.success('User updated.');
                    this.$emit('onUpdated');
                })
                .catch(e => {
                    this.displayApiCallError(e);
                })
                .finally(() => {
                    this.hideLoading();
                });
        },
    },

    computed: {
        isCurrentUser() {
            let token = document.head.querySelector('meta[name="user-id"]');

            return parseInt(token.content) === this.id;
        }
    }
}
</script>
