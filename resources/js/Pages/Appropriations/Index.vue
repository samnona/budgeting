<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/inertia-vue3";
import { cloneDeep } from "lodash-es";
import { notification } from "ant-design-vue";
import { computed, defineComponent, reactive, ref, watch } from "vue";
import dayjs from "dayjs";
import { Inertia } from "@inertiajs/inertia";
import { watchDebounced } from "@vueuse/shared";

const props = defineProps({
    appropriations: Array,
    users: Array,
    bills: Array,
    object_expenditures: Array,
});
const columns = [
    {
        title: "Appropriation",
        dataIndex: "appropriation",
        width: "25%",
        key: "appropriation",
    },
    {
        title: "Object of expenditure",
        dataIndex: ["object_expenditure", "expenditures"],
        width: "25%",
        key: "expenditures",
    },
    {
        title: "Type",
        dataIndex: "type",
        width: "25%",
        key: "type",
    },
    {
        title: "Expense",
        dataIndex: "expense",
        width: "25%",
        key: "expense",
    },
    {
        title: "operation",
        dataIndex: "operation",
        width: "1%",
    },
];

const form = useForm({
    id: "",
    type: "employee",
    user_id: "",
    bill_id: "",
    object_expenditure_id: "",
    expense: "",
});

const appropriationModal = ref(false);
// const isEdit = ref(false);
// const edit = (params) => {
//     form.errors = {};
//     isEdit.value = true;
//     appropriationModal.value = true;
//     form.id = params.id;
//     form.expenditures = params.expenditures;
//     form.account_code = params.account_code;
//     form.budget = params.budget;
//     form.year = params.year;
// };

function submitForm() {
    form.post(route("appropriations.store"), {
        preserveScroll: false,
        onSuccess: () => {
            appropriationModal.value = false;
            form.reset();
            notification.success({
                message: "Appropriation Created Successfully",
            });
        },
    });
}

function handleDelete(params) {
    form.delete(route("appropriations.destroy", params), {
        preserveScroll: false,
        onSuccess: () => {
            notification.success({
                message: "Appropriation Deleted Successfully",
            });
        },
    });
}

// function hanldeEdit() {
//     form.put(route("appropriations.update", form.id), {
//         preserveScroll: false,
//         onSuccess: () => {
//             isEdit.value = false;
//             appropriationModal.value = false;
//             notification.success({
//                 message: "Appropriation Updated Successfully",
//             });
//         },
//     });
// }

function handleShowModal() {
    // isEdit.value = false;
    appropriationModal.value = true;
    form.reset();
    form.errors = {};
}

function filterOptions(input, option) {
    return String(option.label).toLowerCase().indexOf(input.toLowerCase()) >= 0;
}
function handleChangeType(event) {
    form.user_id = "";
    form.bill_id = "";
}

const search = ref("");
watchDebounced(
    search,
    () => {
        pagination.value.current = 1;
        Inertia.get(
            window.location.pathname,
            { search: search.value },
            {
                preserveScroll: true,
                replace: true,
                preserveState: true,
            }
        );
    },
    {
        debounce: 300,
    }
);

const pagination = computed(() => ({
    total: props.appropriations.total,
    current: props.appropriations.current_page,
    pageSize: props.appropriations.per_page,
    showTotal: (total, range) => `${range[0]}-${range[1]} of ${total} items`,
    showSizeChanger: false,
}));

function handleTableChange(event) {
    Inertia.get(
        window.location.pathname,
        {
            search: search.value,
            page: event.current,
        },
        {
            replace: true,
            preserveState: true,
        }
    );
}
</script>

<template>
    <Head title="Appropriations" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <a-card
                    class="overflow-hidden shadow-sm sm:rounded-lg"
                    title="Appropriations"
                >
                    <template #extra>
                        <div class="space-x-4">
                            <a-input-search
                                placeholder="Search Appropriation"
                                style="width: 200px"
                                v-model:value="search"
                            />
                            <a-button type="primary" @click="handleShowModal"
                                >Add
                            </a-button>
                        </div>
                    </template>
                    <a-table
                        :columns="columns"
                        :data-source="appropriations.data"
                        bordered
                        :pagination="pagination"
                        @change="handleTableChange"
                    >
                        <template #bodyCell="{ column, text, record }">
                            <template
                                template
                                v-if="column.dataIndex === 'appropriation'"
                            >
                                {{ record.user?.name ?? record.bill?.name }}
                            </template>
                            <template
                                template
                                v-if="column.dataIndex === 'operation'"
                            >
                                <div class="editable-row-operations">
                                    <span class="space-x-4">
                                        <a-popconfirm
                                            title="Sure to delete?"
                                            @confirm="handleDelete(record.id)"
                                        >
                                            <a>Delete</a>
                                        </a-popconfirm>
                                    </span>
                                </div>
                            </template>
                        </template>
                    </a-table>
                </a-card>

                <!-- Modal -->
                <a-modal
                    :maskClosable="true"
                    v-model:visible="appropriationModal"
                    title="Create Appropriation"
                >
                    <template #footer>
                        <a-button key="back" @click="appropriationModal = false"
                            >Cancel</a-button
                        >
                        <a-button
                            type="primary"
                            :loading="form.processing"
                            @click="submitForm"
                            >Submit</a-button
                        >
                    </template>
                    <div>
                        <a-form layout="vertical" class="w-full">
                            <a-form-item
                                label="Payee"
                                :validate-status="
                                    form.errors.type ? 'error' : null
                                "
                                :help="form.errors.type"
                            >
                                <a-select
                                    @change="handleChangeType($event)"
                                    v-model:value="form.type"
                                    placeholder="Select a payee"
                                    style="width: 100%"
                                    :options="[
                                        {
                                            label: 'employee',
                                            value: 'employee',
                                        },
                                        {
                                            label: 'utility',
                                            value: 'utility',
                                        },
                                    ]"
                                ></a-select>
                            </a-form-item>
                            <a-form-item
                                v-if="form.type === 'employee'"
                                label="User"
                                :validate-status="
                                    form.errors.user_id ? 'error' : null
                                "
                                :help="form.errors.user_id"
                            >
                                <a-select
                                    v-model:value="form.user_id"
                                    placeholder="Select a type"
                                    style="width: 100%"
                                    :options="
                                        users.map((element) => ({
                                            label: element.name,
                                            value: element.id,
                                        }))
                                    "
                                    allowClear
                                    show-search
                                    :filter-option="filterOptions"
                                ></a-select>
                            </a-form-item>
                            <a-form-item
                                v-else
                                label="Utility"
                                :validate-status="
                                    form.errors.bill_id ? 'error' : null
                                "
                                :help="form.errors.bill_id"
                            >
                                <a-select
                                    v-model:value="form.bill_id"
                                    placeholder="Select a type"
                                    style="width: 100%"
                                    :options="
                                        bills.map((element) => ({
                                            label: element.name,
                                            value: element.id,
                                        }))
                                    "
                                    allowClear
                                    show-search
                                    :filter-option="filterOptions"
                                ></a-select>
                            </a-form-item>
                            <a-form-item
                                label="Object of expenditure"
                                :validate-status="
                                    form.errors.object_expenditure_id
                                        ? 'error'
                                        : null
                                "
                                :help="form.errors.object_expenditure_id"
                            >
                                <a-select
                                    v-model:value="form.object_expenditure_id"
                                    placeholder="Select a type"
                                    style="width: 100%"
                                    :options="
                                        object_expenditures.map((element) => ({
                                            label: element.expenditures,
                                            value: element.id,
                                        }))
                                    "
                                    allowClear
                                    show-search
                                    :filter-option="filterOptions"
                                ></a-select>
                            </a-form-item>
                            <a-form-item
                                label="Expense"
                                :validate-status="
                                    form.errors.expense ? 'error' : null
                                "
                                :help="form.errors.expense"
                            >
                                <a-input
                                    type="number"
                                    v-model:value="form.expense"
                                    placeholder="Select a type"
                                    style="width: 100%"
                                ></a-input>
                            </a-form-item>
                        </a-form>
                    </div>
                </a-modal>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
