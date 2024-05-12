<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        Maintenance
                    </span>
                </div>
            </div>

            <div class="card-body">
                <table class="w-100 small">
                    <template v-for="job in jobs">
                        <tr>
                            <td>
                                {{ job['job_name'] }}
                            </td>
                            <td class="text-right">
                                <button :disabled="buttonDisabled[job['job_class']]" @click.prevent="runJobs(job)" class="btn btn-block btn-primary mb-2 btn-sm">RUN</button>
                            </td>
                        </tr>
                    </template>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import api from "../../mixins/api";

export default {
    mixins: [api],

    name: "MaintenanceSection",

    data: function () {
        return {
            buttonDisabled: {},
            jobs: {},
        }
    },

    mounted() {
        this.fetchJobs();
    },

    methods: {
        fetchJobs() {
            this.apiGetJobsRequest()
                .then((response) => {
                    this.jobs = response.data.data;
                })
                .catch((error) => {
                    this.displayApiCallError(error);
                });
        },

        runJobs(job) {
            this.apiPostJobsRequest({"job_id": job['id']})
                .then(() => {
                        this.$snotify.success('Job run requested');
                        this.buttonDisabled[job['job_class']] = true;
                    }
                )
                .catch((error) => {
                        this.displayApiCallError(error);
                    }
                );
        },
    }
}
</script>

<style scoped>

</style>
