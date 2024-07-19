<template>
    <div>
        <div class="d-flex pl-1 pr-1">
            <breadcrumbs v-if="reportName === ''"/>
            <div v-else class="d-inline font-weight-bold text-uppercase small text-secondary align-content-center">
                {{ reportName }}
            </div>
            <div class="flex-grow-1">
                <div class="filter-container d-none d-lg-flex" ref="filterContainer">
                    <p class="text-primary small" v-for="filter in urlFilters">
                        <span v-html="urlFiltersToHumanString(filter)"></span>
                        <button @click="removeFilter(filter)" class="btn btn-link p-0 ml-1 mb-1">x</button>
                    </p>
                </div>
                <a href="#" @click.prevent="showFilters = !showFilters" class="float-right d-lg-none small">
                    {{ showFilters ? 'HIDE' : 'FILTERS' }} <span v-show="!showFilters">({{ urlFilters.length }})</span>
                </a>
            </div>
        </div>

        <div class="filter-container d-flex d-lg-none" ref="filterContainer" v-if="showFilters" >
          <p class="text-primary small" v-for="filter in urlFilters">
            <span v-html="urlFiltersToHumanString(filter)"></span>
            <button @click="removeFilter(filter)" class="btn btn-link p-0 ml-1 mb-1">x</button>
          </p>
        </div>
    </div>
</template>

<script>

import url from "../../mixins/url.vue";
import Breadcrumbs from "./Breadcrumbs.vue";

export default {
    components: {Breadcrumbs},
  mixins: [url],

    props: {
        reportName: String,
        autoExpandFilters: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            showFilters: false,
        }
    },

    mounted() {
        this.showFilters = this.autoExpandFilters;
    },

    methods: {
        removeFilter(filter) {
          this.removeUrlParameterAndGo(filter);
        },

        urlFiltersToHumanString(urlParameter) {
          let filterName = urlParameter.split('[')[1].split(']')[0];
          let filterValue = this.getUrlParameter(urlParameter);

          let fieldName = filterName.replaceAll('_equal','')
            .replaceAll('_contains','')
            .replaceAll('_between','')
            .replaceAll('_lower_than','')
            .replaceAll('_greater_than','');

          let filterOperator = filterName.replaceAll(fieldName, '')
              .replaceAll('_', ' ')
              .trim();

          if (filterOperator === "") {
            filterOperator = 'equals';
          }

          // uppercase first letter of each word
          let fieldNameFormatted = fieldName.replaceAll('_', ' ').replace(/(^\w{1})|(\s+\w{1})/g, letter => letter.toUpperCase());

          // we wrap it in inverted commas to better visualize filters with spaces at the start or end "Blue "
          let filterValueFormatted = ['"', filterValue, '"'].join('');

          if (filterOperator === 'between') {
            filterValueFormatted = filterValue.replaceAll(',' , ' & ');
          }

          return [fieldNameFormatted, filterOperator, filterValueFormatted].join(' ');
        },
    },

    computed: {
        urlFilters() {
          return Object.keys(this.$router.currentRoute.query)
              .filter(aFilter => aFilter.startsWith('filter['));
        }
    }
}
</script>

<style scoped>

.filter-container {
    flex-direction: row-reverse;
    flex-wrap: wrap;
    p {
        flex: 0 0 auto;
        margin: 0 0 0 10px;
        -webkit-user-select: none;
    }
}

.btn-link {
    outline: none;
    box-shadow: none;
    border-color: transparent;
}

</style>
