<!-- resources/js/Pages/Employees/Index.vue -->
<template>
    <AppLayout title="Employees">
        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4">
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="flex justify-between mb-6">
                        <h2 class="text-xl font-semibold">Employees</h2>
                        <a-button type="primary" @click="showModal('create')"
                            >Add Employee</a-button
                        >
                    </div>

                    <a-table
                        :columns="columns"
                        :data-source="employees.data"
                        :pagination="pagination"
                        :loading="loading"
                        @change="handleTableChange"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.key === 'company'">
                                <a
                                    @click="showCompanyDetails(record.company)"
                                    >{{ record.company.name }}</a
                                >
                            </template>
                            <template v-if="column.key === 'action'">
                                <a-space>
                                    <a-button
                                        type="primary"
                                        @click="showModal('edit', record)"
                                        >Edit</a-button
                                    >
                                    <a-popconfirm
                                        title="Delete this employee?"
                                        @confirm="deleteEmployee(record.id)"
                                    >
                                        <a-button type="primary" danger
                                            >Delete</a-button
                                        >
                                    </a-popconfirm>
                                </a-space>
                            </template>
                        </template>
                    </a-table>

                    <a-modal
                        :visible="modalVisible"
                        :title="
                            modalMode === 'create'
                                ? 'Add Employee'
                                : 'Edit Employee'
                        "
                        @ok="handleOk"
                        @cancel="modalVisible = false"
                    >
                        <a-form :model="form">
                            <a-form-item
                                label="First Name"
                                required
                                :help="form.errors.first_name"
                            >
                                <a-input v-model:value="form.first_name" />
                            </a-form-item>
                            <a-form-item
                                label="Last Name"
                                required
                                :help="form.errors.last_name"
                            >
                                <a-input v-model:value="form.last_name" />
                            </a-form-item>
                            <a-form-item
                                label="Company"
                                required
                                :help="form.errors.company_id"
                            >
                                <a-select v-model:value="form.company_id">
                                    <a-select-option
                                        v-for="company in companies"
                                        :key="company.id"
                                        :value="company.id"
                                    >
                                        {{ company.name }}
                                    </a-select-option>
                                </a-select>
                            </a-form-item>
                            <a-form-item
                                label="Email"
                                :help="form.errors.email"
                            >
                                <a-input v-model:value="form.email" />
                            </a-form-item>
                            <a-form-item
                                label="Phone"
                                :help="form.errors.phone"
                            >
                                <a-input v-model:value="form.phone" />
                            </a-form-item>
                        </a-form>
                    </a-modal>

                    <a-modal
                        :visible="companyModalVisible"
                        title="Company Details"
                        @ok="companyModalVisible = false"
                        @cancel="companyModalVisible = false"
                    >
                        <div v-if="selectedCompany">
                            <p>
                                <strong>Name:</strong>
                                {{ selectedCompany.name }}
                            </p>
                        </div>
                    </a-modal>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { message } from "ant-design-vue";

const props = defineProps({
    employees: Object,
    companies: Array,
});

const loading = ref(false);
const modalVisible = ref(false);
const modalMode = ref("create");
const companyModalVisible = ref(false);
const selectedCompany = ref(null);

const form = useForm({
    first_name: "",
    last_name: "",
    company_id: null,
    email: "",
    phone: "",
});

const columns = [
    { title: "Full Name", key: "full_name" },
    { title: "Company", key: "company" },
    { title: "Email", dataIndex: "email", key: "email" },
    { title: "Phone", dataIndex: "phone", key: "phone" },
    { title: "Actions", key: "action" },
];

const pagination = computed(() => ({
    total: props.employees?.total || 0,
    current: props.employees?.current_page || 1,
    pageSize: props.employees?.per_page || 10,
}));

const handleOk = () => {
    const url =
        modalMode.value === "create" ? "/employees" : `/employees/${form.id}`;
    const method = modalMode.value === "create" ? "post" : "put";

    form[method](url, {
        preserveScroll: true,
        onSuccess: () => {
            modalVisible.value = false;
            message.success(`Employee ${modalMode.value}d successfully`);
        },
        onError: (errors) => {
            Object.keys(errors).forEach((key) => {
                message.error(`${key}: ${errors[key]}`);
            });
        },
    });
};

const showModal = (mode, record = null) => {
    modalMode.value = mode;
    if (mode === "edit" && record) {
        form.id = record.id;
        form.first_name = record.first_name;
        form.last_name = record.last_name;
        form.company_id = record.company.id;
        form.email = record.email;
        form.phone = record.phone;
    } else {
        form.reset();
    }
    modalVisible.value = true;
};

const deleteEmployee = (id) => {
    form.delete(`/employees/${id}`, {
        preserveScroll: true,
        onSuccess: () => message.success("Employee deleted successfully"),
        onError: () => message.error("Error deleting employee"),
    });
};

const showCompanyDetails = (company) => {
    selectedCompany.value = company;
    companyModalVisible.value = true;
};

const handleTableChange = (pag) => {
    loading.value = true;
    form.get("/employees", {
        query: { page: pag.current },
        preserveState: true,
        onFinish: () => (loading.value = false),
    });
};
</script>
