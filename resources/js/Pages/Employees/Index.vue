<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/inertia-vue3";
import { cloneDeep } from "lodash-es";
import { notification } from "ant-design-vue";
import { computed, defineComponent, reactive, ref } from "vue";
import { watchDebounced } from "@vueuse/core";
import { Inertia } from "@inertiajs/inertia";

const props = defineProps({
    employees: Array,
});
const columns = [
    {
        title: "Name",
        dataIndex: "name",
        width: "25%",
        key: "name",
    },
    {
        title: "Status",
        dataIndex: "status",
        width: "25%",
        key: "status",
    },
    {
        title: "operation",
        dataIndex: "operation",
        width: "1%",
    },
];

const form = useForm({
    id: "",
    name: "",
    status: true,
});

const accountMondal = ref(false);
const isEdit = ref(false);
const editableData = reactive({});
const edit = (params) => {
    isEdit.value = true;
    form.errors = {};
    accountMondal.value = true;
    form.id = params.id;
    form.name = params.name;
    form.status = params.status;
};

function submitForm() {
    form.post(route("employees.store"), {
        preserveScroll: false,
        onSuccess: () => {
            accountMondal.value = false;
            form.reset();
            notification.success({
                message: "Accounts Created Successfully",
            });
        },
    });
}

function handleDelete(params) {
    form.delete(route("employees.destroy", params), {
        preserveScroll: false,
        onSuccess: () => {
            notification.success({
                message: "Account Deleted Successfully",
            });
        },
    });
}

function hanldeEdit() {
    form.put(route("employees.update", form.id), {
        preserveScroll: false,
        onSuccess: () => {
            isEdit.value = false;
            accountMondal.value = false;
            notification.success({
                message: "Account Updated Successfully",
            });
        },
    });
}

function handleShowModal() {
    isEdit.value = false;
    accountMondal.value = true;
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
    total: props.employees.total,
    current: props.employees.current_page,
    pageSize: props.employees.per_page,
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
    <Head title="Employees" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <a-card
                    class="overflow-hidden shadow-sm sm:rounded-lg"
                    title="Employees"
                >
                    <template #extra>
                        <div class="space-x-4">
                            <a-input-search
                                placeholder="Search Employee"
                                style="width: 200px"
                                v-model:value="search"
                            />

                            <a-button type="primary" @click="handleShowModal"
                                >Add Employee</a-button
                            >
                        </div>
                    </template>
                    <a-table
                        :columns="columns"
                        :data-source="employees.data"
                        bordered
                        :pagination="pagination"
                        @change="handleTableChange"
                    >
                        <template #bodyCell="{ column, text, record }">
                            <template
                                v-if="
                                    ['name', 'status'].includes(
                                        column.dataIndex
                                    )
                                "
                            >
                                {{ text }}
                            </template>
                            <template v-if="column.dataIndex === 'operation'">
                                <div class="editable-row-operations">
                                    <span class="space-x-4">
                                        <a @click="edit(record)">Edit</a>
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
                    v-model:visible="accountMondal"
                    :title="isEdit ? 'Update Employee' : 'Create Employee'"
                >
                    <template #footer>
                        <a-button key="back" @click="accountMondal = false"
                            >Cancel</a-button
                        >
                        <a-button
                            type="primary"
                            :loading="form.processing"
                            @click="isEdit ? hanldeEdit() : submitForm()"
                            >Submit</a-button
                        >
                    </template>
                    <div class="flex flex-col gap-2">
                        <a-form
                            :label-col="{ span: 4 }"
                            :wrapper-col="{ span: 20 }"
                        >
                            <a-form-item
                                label="Name"
                                :validate-status="
                                    form.errors.name ? 'error' : null
                                "
                                :help="form.errors.name"
                            >
                                <a-input
                                    v-model:value="form.name"
                                    placeholder="Name"
                                    allow-clear
                                />
                            </a-form-item>
                            <a-form-item
                                label="Status"
                                :validate-status="
                                    errors?.status ? 'error' : null
                                "
                                :help="errors?.status"
                                v-if="isEdit"
                            >
                                <a-switch v-model:checked="form.status" />
                            </a-form-item>
                        </a-form>
                    </div>
                </a-modal>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
