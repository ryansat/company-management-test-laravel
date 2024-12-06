<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        Welcome {{ $page.props.auth.user.name }}!

                        <!-- Show different content based on user type -->
                        <div v-if="$page.props.auth.user.is_admin" class="mt-4">
                            <p class="mb-4">
                                You have admin access. You can manage:
                            </p>
                            <div class="space-y-2">
                                <Link
                                    href="/companies"
                                    class="block p-4 bg-gray-50 hover:bg-gray-100 rounded-lg"
                                >
                                    Companies
                                </Link>
                                <Link
                                    href="/employees"
                                    class="block p-4 bg-gray-50 hover:bg-gray-100 rounded-lg"
                                >
                                    Employees
                                </Link>
                            </div>
                        </div>
                        <div v-else class="mt-4">
                            <p>You are logged in as a regular user.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
</script>
