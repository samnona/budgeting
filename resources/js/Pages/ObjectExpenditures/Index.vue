<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/inertia-vue3";
import { cloneDeep } from "lodash-es";
import { notification } from "ant-design-vue";
import { computed, defineComponent, reactive, ref, watch } from "vue";
import dayjs from "dayjs";
import { watchDebounced } from "@vueuse/shared";
import { Inertia } from "@inertiajs/inertia";

const props = defineProps({
    object_expenditures: Array,
});
const columns = [
    {
        title: "Account Code",
        dataIndex: "account_code",
        width: "25%",
        key: "account_code",
    },
    {
        title: "Expenditures",
        dataIndex: "expenditures",
        width: "25%",
        key: "expenditures",
    },
    {
        title: "Budget",
        dataIndex: "budget",
        width: "25%",
        key: "budget",
    },
    {
        title: "Balance",
        dataIndex: "balance",
        width: "25%",
        key: "balance",
    },
    {
        title: "Year",
        dataIndex: "year",
        width: "25%",
        key: "year",
    },
    {
        title: "operation",
        dataIndex: "operation",
        width: "1%",
    },
];

const form = useForm({
    id: "",
    expenditures: "",
    account_code: "",
    budget: "",
    balance: "",
    year: "",
});

const expenditureModal = ref(false);
// const isEdit = ref(false);
// const edit = (params) => {
//     form.errors = {};
//     isEdit.value = true;
//     expenditureModal.value = true;
//     form.id = params.id;
//     form.expenditures = params.expenditures;
//     form.account_code = params.account_code;
//     form.budget = params.budget;
//     form.year = params.year;
// };

const yearValue = ref("");
function submitForm() {
    form.year = yearValue.value
        ? dayjs(yearValue.value).format("YYYY")
        : undefined;
    form.post(route("object-expenditures.store"), {
        preserveScroll: false,
        onSuccess: () => {
            expenditureModal.value = false;
            form.reset();
            notification.success({
                message: "Expenditure Created Successfully",
            });
        },
    });
}

function handleDelete(params) {
    form.delete(route("object-expenditures.destroy", params), {
        preserveScroll: false,
        onSuccess: () => {
            notification.success({
                message: "Expenditure Deleted Successfully",
            });
        },
    });
}

// function hanldeEdit() {
//     form.put(route("object-expenditures.update", form.id), {
//         preserveScroll: false,
//         onSuccess: () => {
//             isEdit.value = false;
//             expenditureModal.value = false;
//             notification.success({
//                 message: "Expenditure Updated Successfully",
//             });
//         },
//     });
// }

function handleShowModal() {
    // isEdit.value = false;
    expenditureModal.value = true;
    form.reset();
    form.errors = {};
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
    total: props.object_expenditures.total,
    current: props.object_expenditures.current_page,
    pageSize: props.object_expenditures.per_page,
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
    <Head title="Expenditures" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <a-card
                    class="overflow-hidden shadow-sm sm:rounded-lg"
                    title="Expenditures"
                >
                    <template #extra>
                        <div class="space-x-4">
                            <a-input-search
                                placeholder="Search Expenditure"
                                style="width: 200px"
                                v-model:value="search"
                            />
                            <a-button type="primary" @click="handleShowModal"
                                >Add Expenditure</a-button
                            >
                        </div>
                    </template>
                    <a-table
                        :columns="columns"
                        :data-source="object_expenditures.data"
                        bordered
                        :pagination="pagination"
                        @change="handleTableChange"
                    >
                        <template #bodyCell="{ column, text, record }">
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
                    v-model:visible="expenditureModal"
                    title="Create Expenditure"
                >
                    <template #footer>
                        <a-button key="back" @click="expenditureModal = false"
                            >Cancel</a-button
                        >
                        <a-button
                            type="primary"
                            :loading="form.processing"
                            @click="submitForm"
                            >Submit</a-button
                        >
                    </template>
                    <div class="flex flex-col gap-2">
                        <a-form layout="vertical">
                            <a-form-item
                                label="Expenditure"
                                :validate-status="
                                    form.errors.expenditures ? 'error' : null
                                "
                                :help="form.errors.expenditures"
                            >
                                <a-input
                                    v-model:value="form.expenditures"
                                    placeholder="Expenditure"
                                    allow-clear
                                />
                            </a-form-item>
                            <a-form-item
                                label="Account Code"
                                :validate-status="
                                    form.errors.account_code ? 'error' : null
                                "
                                :help="form.errors.account_code"
                            >
                                <a-input
                                    v-model:value="form.account_code"
                                    placeholder="Account Code"
                                    allow-clear
                                />
                            </a-form-item>
                            <a-form-item
                                label="Budget"
                                :validate-status="
                                    form.errors.budget ? 'error' : null
                                "
                                :help="form.errors.budget"
                            >
                                <a-input
                                    v-model:value="form.budget"
                                    placeholder="Budget"
                                    allow-clear
                                />
                            </a-form-item>
                            <a-form-item
                                label="Year"
                                :validate-status="
                                    form.errors.year ? 'error' : null
                                "
                                :help="form.errors.year"
                            >
                                <a-date-picker
                                    v-model:value="yearValue"
                                    picker="year"
                                />
                            </a-form-item>
                        </a-form>
                    </div>
                </a-modal>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
