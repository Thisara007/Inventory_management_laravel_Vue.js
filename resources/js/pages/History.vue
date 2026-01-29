<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import Appheader from '@/components/AppHeader.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {useForm} from '@inertiajs/vue3';
import { Label } from '@/components/ui/label';
import { Trash2, Plus, Save } from 'lucide-vue-next';
import { ref, reactive, computed } from 'vue';


interface setOperation {
    item_id: number,
    user_id: number,
    item_name: string,
    operation: '+' | '-',
    quantity: number,
    previous_quantity: number,
    new_quantity: number,
    updated_at: string,
    notes: string,
}
const searchQuery = ref('');

const props = defineProps<{
    history: setOperation[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Add Items',
        href: 'add_item',
    },
];

const filteredItems = computed(() => {
    if (!searchQuery.value) {
        return props.history;
    }
    return props.history.filter(history =>
        history.item_name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

</script>
<template>
    <Head title="Stock History" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="py-12 bg-gray-50/50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 leading-tight">Stock Movement History</h2>
                        <p class="text-sm text-gray-900 mt-1">Track all inventory additions and deductions.</p>
                    </div>
                    
                    <div class="relative w-full sm:w-72">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search by item name..."
                            class="text-gray-900 block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out shadow-sm"
                        />
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Reference ID
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Item Details
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Operation
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stock Change
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date & User
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="history in filteredItems" :key="history.id" class="hover:bg-gray-50 transition-colors duration-150">
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        #{{ history.id }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-xs">
                                                {{ history.item_name ? history.item_name.substring(0, 2).toUpperCase() : 'NA' }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ history.item_name }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span 
                                            :class="[
                                                history.operation === '+' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800',
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize'
                                            ]"
                                        >
                                            {{ history.operation === '+' ? 'Restock' : 'Deduction' }}
                                            <span class="ml-1 font-bold">({{ history.operation }})</span>
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm text-gray-900 font-bold">
                                            {{ history.new_quantity }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            was {{ history.previous_quantity }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">User ID: {{ history.user_id }}</div>
                                        <div class="text-xs text-gray-500">{{ history.updated_at }}</div>
                                    </td>

                                </tr>

                                <tr v-if="filteredItems.length === 0">
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-700">
                                        No history records found matching your search.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>