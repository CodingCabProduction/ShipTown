<template>

</template>

<script>
    import VueRouter from "vue-router";
    import {History} from "swiper";

    Vue.use(VueRouter);

    const Router = new VueRouter({
        mode: 'history',
    });


    export default {
        name: "url",

        router: Router,

        methods: {
            getUrlFilterOrSet: function(name, defaultValue) {
                let value = this.getUrlParameter(name);

                if (value === null) {
                    this.setUrlParameter(name, defaultValue);
                    return defaultValue;
                }

                return value;
            },

            setUrlFilter: function(name, defaultValue = null) {
                const $fullFilterName = 'filter['+name+']';
                return this.setUrlParameter($fullFilterName, defaultValue);
            },

            getUrlFilter: function(name, defaultValue = null) {
                const $fullFilterName = 'filter['+name+']';
                return this.getUrlParameter($fullFilterName, defaultValue);
            },

            getUrlParameter: function(param, defaultValue = null) {
                const $urlParameters = this.$router.currentRoute.query;
                return this.getValueOrDefault($urlParameters[param], defaultValue);
            },

            setUrlParameter: function(param, value) {
                // we need to clone the object because we are going to modify it
                const urlParameters = JSON.parse(JSON.stringify(this.$router.currentRoute.query));
                urlParameters[param] = value;

                this.$router.push({query: urlParameters});

                return this;
            },

            removeUrlParameter: function(param) {
                this.$router.currentRoute.query[param] = null;
                this.updateUrl(this.$router.currentRoute.query);

                return this;
            },

            removeUrlParameterAndGo: function(param) {
                this.removeUrlParameter(param);
                window.location.reload();

                return this;
            },

            setUrlParameterAngGo: function(param, value) {
                this.setUrlParameter(param, value);
                this.$nextTick(() => {
                    this.$router.go();
                });
                return this;
            },

            updateUrlParameters(params) {
                for (let parameter in params) {
                    this.setUrlParameter(parameter, params[parameter]);
                }

                return this;
            },

            pushUrl: function (url) {
                this.$router.push(url).catch(err => {
                    // Ignore the vuex err regarding  navigating to the page they are already on.
                    if (
                        err.name !== 'NavigationDuplicated' &&
                        !err.message.includes('Avoided redundant navigation to current location')
                    ) {
                        // But print any other errors to the console
                        console.error(err);
                    }
                });
            },

            updateUrl: function(params) {
                let url = this.$router.currentRoute.path + '?';

                for (let element in params) {
                    if( params[element] != null) {
                        url += element +'=' + params[element] + '&';
                    }
                }

                // we setting url twice because sometimes when only parameter is updated
                // but path stays NavigationDuplicated error might occur
                // this.pushUrl('/');
                this.pushUrl(url);

                return this;
            },

            isSet: function (value) {
                return (value === undefined) || (value === null);
            },

            getValueOrDefault: function (value, defaultValue){
                return this.isSet(value) ? defaultValue : value;
            },
        }
    }
</script>

<style scoped>

</style>
