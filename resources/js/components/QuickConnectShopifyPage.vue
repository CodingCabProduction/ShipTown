<template>
    <div class="row justify-content-center vh-100">
        <div class="col-md-9 d-flex align-items-center px-5">
            <div class="embed-responsive embed-responsive-16by9">
                <iframe
                    class="embed-responsive-item"
                    src="https://www.youtube.com/embed/TMUcZQjCQIU"
                    title="How to create API key in Magento API?"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen
                ></iframe>
            </div>
        </div>
        <div class="col-md-3 d-flex align-items-center bg-white px-5">
            <div class="w-100">
                <div class="mb-4">
                    <h4>Setup Magento API</h4>
                </div>

                <ValidationObserver ref="form">
                    <form class="form" @submit.prevent="submit" ref="loadingContainer">

                        <div class="form-group">
                            <label class="form-label" for="base_url">Base URL</label>
                            <ValidationProvider vid="base_url" name="base_url" v-slot="{ errors }">
                            <input v-model="config.base_url" :class="{
                                        'form-control': true,
                                        'is-invalid': errors.length > 0,
                                    }" id="base_url" type="url" required>
                            <div class="invalid-feedback">
                                {{ errors[0] }}
                            </div>
                            </ValidationProvider>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="magento_store_id">Store ID</label>
                            <ValidationProvider vid="magento_store_id" name="magento_store_id" v-slot="{ errors }">
                                <input v-model="config.magento_store_id" :class="{
                                    'form-control': true,
                                    'is-invalid': errors.length > 0,
                                }" id="magento_store_id" type="number" required>
                                <div class="invalid-feedback">
                                    {{ errors[0] }}
                                </div>
                            </ValidationProvider>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="api_access_token">API Access Token</label>
                            <ValidationProvider vid="api_access_token" name="api_access_token" v-slot="{ errors }">
                                <input v-model="config.api_access_token" id="api_access_token" type="text" required :class="{
                                    'form-control': true,
                                    'is-invalid': errors.length > 0,
                                }">
                                <div class="invalid-feedback">
                                    {{ errors[0] }}
                                </div>
                            </ValidationProvider>
                        </div>

                    </form>
                </ValidationObserver>
                <b-button @click="submit" variant="primary" class="btn btn-primary btn-block">Connect</b-button>
<!--                        <button class="btn btn-primary btn-block" type="submit">Save</button>-->
            </div>
        </div>
    </div>
</template>

<script>
import api from "../mixins/api";
import { ValidationObserver, ValidationProvider } from "vee-validate";
import Loading from "../mixins/loading-overlay";

export default {
    mixins: [api, Loading],
    components: {
        ValidationObserver,
        ValidationProvider
    },
    data() {
        return {
            config: {
                base_url: '',
            },
        };
    },
    mounted() {},
    methods: {
        submit() {
            this.showLoading();
            this.apiPostMagentoApiConnection({...this.config})
                .then(({ data }) => {
                    this.$snotify.success('Connection created.');
                    window.location.href = '/';
                })
                .catch((error) => {
                    if (error.response) {
                        if (error.response.status === 422) {
                            this.$refs.form.setErrors(error.response.data.errors);
                        }
                    }
                })
                .finally(this.hideLoading);
        },
    },
};
</script>
