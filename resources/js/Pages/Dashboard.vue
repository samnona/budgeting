<script setup>
import { useMoneyFormat } from "@/Composables/useMoneyFormat";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/inertia-vue3";
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="grid grid-cols-3 gap-4 mt-6 ml-40">
            <div class="mb-4 md:mb-0">
                <a-card
                    :body-style="{
                        padding: '20px 15px',
                    }"
                    class="w-full px-4 rounded"
                >
                    <div class="flex">
                        <div class="mr-3 flex-1">
                            <div class="mb-2 text-gray-500 font-bold text-2xl">
                                Total budget for this year
                            </div>
                            <div class="text-3xl font-semibold">
                                {{
                                    useMoneyFormat($page.props["total-budget"])
                                }}
                            </div>
                        </div>
                    </div>
                </a-card>
            </div>
            <div class="mb-4 md:mb-0">
                <a-card
                    :body-style="{
                        padding: '20px 15px',
                    }"
                    class="w-full px-4 rounded"
                >
                    <div class="flex">
                        <div class="mr-3 flex-1">
                            <div class="mb-2 font-bold text-gray-500 text-2xl">
                                Balance budget for this year
                            </div>
                            <div class="text-3xl font-semibold">
                                {{ useMoneyFormat($page.props.balance) }}
                            </div>
                        </div>
                    </div>
                </a-card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
