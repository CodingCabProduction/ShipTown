<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        Automations
                    </span>
                    <button type="button" class="action-link btn btn-sm btn-light" @click="showCreateForm()">
                        Add New
                    </button>
                </div>
            </div>

            <div class="card-body">
                <table v-if="automations.length > 0" class="table table-borderless table-responsive mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Priority</th>
                            <th>Enable</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(automation, i) in automations" :key="i">
                            <td>{{ automation.name }}</td>
                            <td>{{ automation.priority }}</td>
                            <td align="center">
                                <status-icon :status="automation.enabled" />
                            </td>
                            <td>
                                <a @click.prevent="showEditForm(automation)">
                                    <font-awesome-icon icon="edit"></font-awesome-icon>
                                </a>
                                <a @click.prevent="confirmDelete(automation)">
                                    <font-awesome-icon icon="trash"></font-awesome-icon>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p v-else class="mb-0">
                    No automations found.
                </p>
            </div>
        </div>
        <!-- The modals -->
        <create-modal id="createForm" @onCreated="addAutomations" :events="events"></create-modal>
        <edit-modal id="editForm" :selectedAutomation="selectedAutomation" @onUpdated="updateAutomations" :events="events"></edit-modal>
    </div>
</template>

<script>

import CreateModal from './Automations/CreateModal';
import EditModal from './Automations/EditModal';
import StatusIcon from './Automations/StatusIcon';
import api from "../../mixins/api.vue";

export default {
    mixins: [api],
    components: {
        'create-modal': CreateModal,
        'edit-modal': EditModal,
        'status-icon': StatusIcon
    },

    mounted() {
        this.apiGetAutomations()
            .then(({ data }) => {
                this.automations = data.data;
            })

        this.apiGetAutomationConfig()
            .then(({ data }) => {
                this.events = data.when;
            })
    },

    data: () => ({
        automations: [],
        selectedAutomation: {},
        events: [],
    }),

    methods: {
        showCreateForm(){
            $('#createForm').modal('show');
        },
        showEditForm(automation) {
            this.selectedAutomation = automation;
            $('#editForm').modal('show');
        },
        addAutomations(orderStatus){
            this.automations.push(orderStatus)
        },
        updateAutomations(newValue) {
            const indexAutomations = this.automations.findIndex(automation => automation.id == newValue.id)
            this.$set(this.automations, indexAutomations, newValue)
        },
        confirmDelete(automation) {
            const indexAutomations = this.automations.findIndex(automation => automation.id == automation.id)
            this.$snotify.confirm('After delete data cannot restored', 'Are you sure?', {
                position: 'centerCenter',
                buttons: [
                    {
                        text: 'Yes',
                        action: (toast) => {
                            this.delete(automation.id, indexAutomations)
                            this.$snotify.remove(toast.id);
                        }
                    },
                    {text: 'Cancel'},
                ]
            });
        },
        delete(id, index) {
            this.apiDeleteAutomations(id)
                .then(() => {
                    Vue.delete(this.automations, index);
                    this.$snotify.success('Automation deleted.');
                });
        }
    },
}
</script>