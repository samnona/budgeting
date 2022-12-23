<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/inertia-vue3";
import { cloneDeep } from "lodash-es";
import { notification } from "ant-design-vue";
import { computed, defineComponent, reactive, ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { watchDebounced } from "@vueuse/shared";

const props = defineProps({
    bills: Array,
});
const columns = [
    {
        title: "Name",
        dataIndex: "name",
        width: "25%",
        key: "name",
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
});

const billModal = ref(false);
const isEdit = ref(false);
const editableData = reactive({});
const edit = (params) => {
    form.errors = {};
    isEdit.value = true;
    billModal.value = true;
    form.id = params.id;
    form.name = params.name;
};

function submitForm() {
    form.post(route("bills.store"), {
        preserveScroll: false,
        onSuccess: () => {
            billModal.value = false;
            form.reset();
            notification.success({
                message: "Utility Created Successfully",
            });
        },
    });
}

function handleDelete(params) {
    form.delete(route("bills.destroy", params), {
        preserveScroll: false,
        onSuccess: () => {
            notification.success({
                message: "Utility Deleted Successfully",
            });
        },
    });
}

function hanldeEdit() {
    form.put(route("bills.update", form.id), {
        preserveScroll: false,
        onSuccess: () => {
            isEdit.value = false;
            billModal.value = false;
            notification.success({
                message: "Utility Updated Successfully",
            });
        },
    });
}

function handleShowModal() {
    isEdit.value = false;
    billModal.value = true;
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
    total: props.bills.total,
    current: props.bills.current_page,
    pageSize: props.bills.per_page,
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
    <Head title="Utilities" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <a-card
                    class="overflow-hidden shadow-sm sm:rounded-lg"
                    title="Utilities"
                >
                    <template #extra>
                        <div class="space-x-4">
                            <a-input-search
                                placeholder="Search Utility"
                                style="width: 200px"
                                v-model:value="search"
                            />
                            <a-button type="primary" @click="handleShowModal"
                                >Add Utility</a-button
                            >
                        </div>
                    </template>
                    <a-table
                        :columns="columns"
                        :data-source="bills.data"
                        bordered
                        :pagination="pagination"
                        @change="handleTableChange"
                    >
                        <template #bodyCell="{ column, text, record }">
                            <template
                                v-if="['name'].includes(column.dataIndex)"
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
                    v-model:visible="billModal"
                    :title="isEdit ? 'Update Utility' : 'Create Utility'"
                >
                    <template #footer>
                        <a-button key="back" @click="billModal = false"
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
                        <a-form>
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
                        </a-form>
                    </div>
                </a-modal>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
