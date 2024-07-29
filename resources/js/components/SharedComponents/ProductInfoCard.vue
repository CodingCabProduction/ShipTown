<template>
    <div>
        <div class="text-primary h5">{{ product ? product['name'] : '&nbsp;' }}</div>
        <div>
            sku:
            <font-awesome-icon icon="copy" class="fa-xs btn-link" role="button"
                               @click="copyToClipBoard((product ? product['sku'] : '') )"></font-awesome-icon>
            <strong>&nbsp;<product-sku-button :product_sku="product['sku']"/></strong>
        </div>
        <div v-if="showTags && product">
            <template v-for="tag in product['tags']">
                <a class="badge text-uppercase btn btn-outline-primary" :key="tag.id"
                   @click.prevent="setUrlParameterAngGo('filter[product_has_tags]', getTagName(tag))">
                    {{ getTagName(tag) }} </a>
            </template>
        </div>
    </div>
</template>

<script>
import helpers from "../../mixins/helpers";
import url from "../../mixins/url";
import ProductSkuButton from "./ProductSkuButton.vue";

export default {
    components: {ProductSkuButton},
    mixins: [helpers, url],

    name: "ProductInfoCard",

    props: {
        product: null,
        showTags: {
            type: Boolean,
            default: true
        }
    },

    methods: {
        getTagName(tag) {
            return tag.name instanceof Object ? tag.name['en'] : tag.name
        },
    }
}
</script>

