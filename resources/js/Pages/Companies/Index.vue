<template>
    <AppLayout title="Companies">
        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Header -->
                        <div class="mb-6 flex justify-between">
                            <h2 class="text-xl font-semibold">Companies</h2>
                            <a-button
                                type="primary"
                                @click="showModal('create')"
                            >
                                Add Company
                            </a-button>
                        </div>

                        <!-- Search -->
                        <div class="mb-4">
                            <a-input-search
                                v-model:value="search"
                                placeholder="Search companies..."
                                @search="handleSearch"
                                class="max-w-xs"
                            />
                        </div>

                        <!-- Table -->
                        <a-table
                            :columns="columns"
                            :data-source="companies.data"
                            :pagination="pagination"
                            :loading="loading"
                            @change="handleTableChange"
                        >
                            <template #bodyCell="{ column, record }">
                                <template v-if="column.key === 'logo'">
                                    <img
                                        v-if="record.logo"
                                        :src="record.logo"
                                        class="h-10 w-10 object-cover rounded"
                                        alt="Company Logo"
                                    />
                                </template>

                                <template v-if="column.key === 'website'">
                                    <a
                                        v-if="record.website"
                                        :href="record.website"
                                        target="_blank"
                                        class="text-blue-600 hover:underline"
                                    >
                                        {{ record.website }}
                                    </a>
                                </template>

                                <template v-if="column.key === 'action'">
                                    <a-space>
                                        <a-button
                                            type="primary"
                                            @click="showModal('edit', record)"
                                        >
                                            Edit
                                        </a-button>
                                        <a-popconfirm
                                            title="Are you sure to delete this company?"
                                            @confirm="deleteCompany(record.id)"
                                        >
                                            <a-button type="primary" danger>
                                                Delete
                                            </a-button>
                                        </a-popconfirm>
                                    </a-space>
                                </template>
                            </template>
                        </a-table>

                        <!-- Modal -->
                        <a-modal
                            :visible="modalVisible"
                            :title="
                                modalMode === 'create'
                                    ? 'Add Company'
                                    : 'Edit Company'
                            "
                            @ok="handleOk"
                            @cancel="modalVisible = false"
                        >
                            <a-form :model="form">
                                <a-form-item label="Name" required>
                                    <a-input v-model:value="form.name" />
                                </a-form-item>
                                <a-form-item label="Email">
                                    <a-input v-model:value="form.email" />
                                </a-form-item>
                                <a-form-item label="Website">
                                    <a-input v-model:value="form.website" />
                                </a-form-item>
                                <a-form-item label="Logo">
                                    <a-upload
                                        list-type="picture-card"
                                        :before-upload="beforeUpload"
                                        :max-count="1"
                                    >
                                        <div>
                                            <plus-outlined />
                                            <div style="margin-top: 8px">
                                                Upload
                                            </div>
                                        </div>
                                    </a-upload>
                                </a-form-item>
                            </a-form>
                        </a-modal>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { PlusOutlined } from "@ant-design/icons-vue";
import { message } from "ant-design-vue";

const props = defineProps({
    companies: Object,
});

const loading = ref(false);
const search = ref("");
const modalVisible = ref(false);
const modalMode = ref("create");

const form = useForm({
    id: null,
    name: "",
    email: "",
    website: "",
    logo: null,
});

const columns = [
    { title: "#", dataIndex: "id", key: "id" },
    { title: "Name", dataIndex: "name", key: "name", sorter: true },
    { title: "Email", dataIndex: "email", key: "email" },
    { title: "Logo", dataIndex: "logo", key: "logo" },
    { title: "Website", dataIndex: "website", key: "website" },
    { title: "Actions", key: "action" },
];

const pagination = computed(() => ({
    total: props.companies?.total || 0,
    current: props.companies?.current_page || 1,
    pageSize: props.companies?.per_page || 10,
}));

const showModal = (mode, record = null) => {
    modalMode.value = mode;
    modalVisible.value = true;

    if (mode === "edit" && record) {
        form.id = record.id;
        form.name = record.name;
        form.email = record.email;
        form.website = record.website;
    } else {
        form.reset();
    }
};

const handleOk = () => {
    // Format website URL if provided
    if (form.website) {
        if (!form.website.match(/^https?:\/\//)) {
            form.website = "http://" + form.website.replace(/^\/+/, "");
        }
    }

    if (modalMode.value === "create") {
        form.post("/companies", {
            preserveScroll: true,
            onSuccess: () => {
                modalVisible.value = false;
                message.success("Company created successfully");
            },
            onError: (errors) => {
                console.error("Form submission errors:", errors);

                // Display error message for each validation error
                Object.keys(errors).forEach((key) => {
                    message.error(`${key}: ${errors[key]}`);
                });

                // Keep modal open to show errors
                modalVisible.value = true;
            },
        });
    } else {
        form.put(`/companies/${form.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                modalVisible.value = false;
                message.success("Company updated successfully");
            },
            onError: (errors) => {
                console.error("Form submission errors:", errors);

                // Display error message for each validation error
                Object.keys(errors).forEach((key) => {
                    message.error(`${key}: ${errors[key]}`);
                });

                // Keep modal open to show errors
                modalVisible.value = true;
            },
        });
    }
};

const deleteCompany = (id) => {
    form.delete(`/companies/${id}`, {
        onSuccess: () => message.success("Company deleted successfully"),
    });
};

const beforeUpload = (file) => {
    form.logo = file;
    return false;
};

const handleSearch = () => {
    form.get("/companies", {
        query: { search: search.value },
        preserveState: true,
    });
};

const handleTableChange = (pag, filters, sorter) => {
    loading.value = true;
    form.get("/companies", {
        query: {
            page: pag.current,
            per_page: pag.pageSize,
            sort_field: sorter.field,
            sort_order: sorter.order,
        },
        preserveState: true,
        onFinish: () => (loading.value = false),
    });
};
</script>
