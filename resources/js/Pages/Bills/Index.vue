<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/inertia-vue3";
import { cloneDeep } from "lodash-es";
import { notification } from "ant-design-vue";
import { defineComponent, reactive, ref } from "vue";

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
</script>

<template>
    <Head title="Employees" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <a-card
                    class="overflow-hidden shadow-sm sm:rounded-lg"
                    title="Utilities"
                >
                    <template #extra>
                        <a-button type="primary" @click="handleShowModal"
                            >Add Utility</a-button
                        >
                    </template>
                    <a-table :columns="columns" :data-source="bills" bordered>
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
