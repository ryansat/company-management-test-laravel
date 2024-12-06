<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link href="/dashboard">
                                    <span class="text-xl font-bold">{{
                                        title
                                    }}</span>
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex"
                            >
                                <NavLink
                                    href="/dashboard"
                                    :active="$page.url.startsWith('/dashboard')"
                                >
                                    Dashboard
                                </NavLink>
                                <NavLink
                                    v-if="$page.props.auth.user.is_admin"
                                    href="/companies"
                                    :active="$page.url.startsWith('/companies')"
                                >
                                    Companies
                                </NavLink>
                                <NavLink
                                    :href="'/employees'"
                                    :active="$page.url.startsWith('/employees')"
                                >
                                    Employees
                                </NavLink>
                            </div>
                        </div>

                        <!-- User Dropdown -->
                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <a-dropdown>
                                <a
                                    class="inline-flex items-center px-3 py-2 text-sm leading-4 font-medium hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                >
                                    {{ $page.props.auth.user.name }}
                                </a>
                                <template #overlay>
                                    <a-menu>
                                        <a-menu-item>
                                            <Link href="/profile">
                                                Profile
                                            </Link>
                                        </a-menu-item>
                                        <a-menu-item>
                                            <Link
                                                href="/logout"
                                                method="post"
                                                as="button"
                                            >
                                                Log Out
                                            </Link>
                                        </a-menu-item>
                                    </a-menu>
                                </template>
                            </a-dropdown>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import NavLink from "@/Components/NavLink.vue";

defineProps({
    title: {
        type: String,
        default: "Application",
    },
});
</script>
