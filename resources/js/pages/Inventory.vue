<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import Appheader from '@/components/AppHeader.vue';
import { Button } from '@/components/ui/button';
import { useForm } from '@inertiajs/vue3';
import { ref, reactive, computed } from 'vue';
import { Search, Plus, Minus, Edit2, X, Package, Save } from 'lucide-vue-next';

interface item {
    id: number;
    name: string;
    quantity: number;
    measurement_unit: string;
    created_at: string;
    updated_at: string;
    image_path?: string;
}

const props = defineProps<{
    items: item[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Inventory',
        href: 'inventory',
    },
];

const isEditing = ref(false);
const adjustments = reactive<Record<number, { amount: number; operation: '+' | '-' }>>({});
const searchQuery = ref('');

const toggleEditing = () => {
    isEditing.value = !isEditing.value;
    if (isEditing.value) {
        // Initialize adjustments for all items when entering edit mode
        props.items.forEach(item => {
            if (!adjustments[item.id]) {
                adjustments[item.id] = { amount: 0, operation: '+' };
            }
        });
    } else {
        // Reset adjustments when exiting edit mode
        Object.keys(adjustments).forEach(key => delete adjustments[parseInt(key)]);
    }
};

const setOperation = (id: number, operation: '+' | '-') => {
    if (!adjustments[id]) {
        adjustments[id] = { amount: 0, operation: '+' };
    }
    adjustments[id].operation = operation;
};

const saveChanges = () => {
    const itemsToUpdate = Object.entries(adjustments)
        .filter(([_, adj]) => adj.amount !== 0)
        .map(([id, adj]) => ({
            id: parseInt(id),
            adjustment: adj.amount,
            operation: adj.operation,
        }));

    if (itemsToUpdate.length === 0) {
        alert('No changes to save.');
        return;
    }

    router.post('/update-stock', { items: itemsToUpdate }, {
        onSuccess: () => {
            isEditing.value = false;
            Object.keys(adjustments).forEach(key => delete adjustments[parseInt(key)]);
        },
    });
};

const goToAddItem = () => {
    router.visit('/add_item');
};

const getAdjustment = (id: number) => {
    return adjustments[id] || { amount: 0, operation: '+' };
};

const filteredItems = computed(() => {
    if (!searchQuery.value) {
        return props.items;
    }
    return props.items.filter(item =>
        item.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});
</script>

<template>
    <Head title="Inventory Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="py-12 bg-gray-50/50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                    
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 leading-tight">Current Inventory</h2>
                        <p class="text-sm text-gray-900 mt-1">Manage stock levels and make quick adjustments.</p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                        
                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Search class="h-4 w-4 text-gray-400" />
                            </div>
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search items..."
                                class="text-gray-900 block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm shadow-sm transition duration-150"
                            />
                        </div>

                        <div class="flex gap-2">
                            <Button @click="goToAddItem" variant="outline" class="whitespace-nowrap bg-white">
                                <Plus class="w-4 h-4 mr-2" />
                                Add Item
                            </Button>
                            
                            <Button 
                                @click="toggleEditing" 
                                :variant="isEditing ? 'destructive' : 'default'"
                                class="whitespace-nowrap shadow-sm"
                            >
                                <component :is="isEditing ? 'X' : 'Edit2'" class="w-4 h-4 mr-2" />
                                {{ isEditing ? 'Cancel Edit' : 'Adjust Stock' }}
                            </Button>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden relative">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                                        ID
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Item Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Current Stock
                                    </th>
                                    
                                    <th v-if="isEditing" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-50/50 text-indigo-700">
                                        Stock Adjustment
                                    </th>
                                    
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                        Unit
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="item in filteredItems" :key="item.id" class="hover:bg-gray-50 transition-colors duration-150 group">
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-mono">
                                        #{{ item.id }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 mr-3">
                                                <Package class="w-4 h-4" />
                                            </div>
                                            <span class="text-sm font-semibold text-gray-900">{{ item.name }}</span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-50 text-blue-700">
                                            {{ item.quantity }}
                                        </span>
                                    </td>

                                    <td v-if="isEditing" class="px-6 py-4 whitespace-nowrap bg-indigo-50/20">
                                        <div class="flex items-center space-x-2 bg-white border border-gray-200 rounded-lg p-1 w-fit shadow-sm">
                                            <button
                                                @click="setOperation(item.id, '-')"
                                                :class="[
                                                    'p-1.5 rounded transition-all duration-200',
                                                    adjustments[item.id]?.operation === '-' 
                                                        ? 'bg-red-500 text-white shadow-sm' 
                                                        : 'text-gray-400 hover:bg-red-50 hover:text-red-500'
                                                ]"
                                                title="Deduct Stock"
                                            >
                                                <Minus class="w-4 h-4" />
                                            </button>

                                            <div class="relative">
                                                <input
                                                    type="number"
                                                    v-model.number="adjustments[item.id].amount"
                                                    class="w-20 p-1 text-center border-0 focus:ring-0 text-gray-900 font-bold bg-transparent text-sm appearance-none"
                                                    placeholder="0"
                                                    min="0"
                                                />
                                            </div>

                                            <button
                                                @click="setOperation(item.id, '+')"
                                                :class="[
                                                    'p-1.5 rounded transition-all duration-200',
                                                    adjustments[item.id]?.operation === '+' 
                                                        ? 'bg-green-500 text-white shadow-sm' 
                                                        : 'text-gray-400 hover:bg-green-50 hover:text-green-500'
                                                ]"
                                                title="Add Stock"
                                            >
                                                <Plus class="w-4 h-4" />
                                            </button>
        
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">
                                        {{ item.measurement_unit }}
                                    </td>

                                </tr>
                            </tbody>
                        </table>
                        
                        <div v-if="filteredItems.length === 0" class="p-12 text-center text-gray-500">
                            No items found matching your search.
                        </div>
                    </div>
                </div>
            </div>

            <transition
                enter-active-class="transform transition ease-out duration-300"
                enter-from-class="translate-y-full opacity-0"
                enter-to-class="translate-y-0 opacity-100"
                leave-active-class="transform transition ease-in duration-200"
                leave-from-class="translate-y-0 opacity-100"
                leave-to-class="translate-y-full opacity-0"
            >
                <div v-if="isEditing" class="fixed bottom-0 left-0 right-0 z-50">
                    <div class="bg-white border-t border-gray-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] p-4">
                        <div class="max-w-7xl mx-auto flex justify-between items-center px-4 sm:px-6 lg:px-8">
                            <div class="text-sm text-gray-500 hidden sm:block">
                                <span class="font-medium text-indigo-600">Editing Mode Active</span> — Enter adjustments and click save.
                            </div>
                            <div class="flex gap-3 w-full sm:w-auto justify-end">
                                <Button @click="toggleEditing" variant="ghost" class="text-gray-500">
                                    Cancel
                                </Button>
                                <Button @click="saveChanges" class="bg-gray-900 text-white hover:bg-gray-800 shadow-md">
                                    <Save class="w-4 h-4 mr-2" />
                                    Save Changes
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>

        </div>
    </AppLayout>
</template>