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

const form = useForm({
    items: [
        { name: '', measurement_unit: '', quantity: '', image: null }
    ]
});

// --- 2. ACTIONS ---

// Add a new empty row
const addItemRow = () => {
    form.items.push({ 
        name: '', 
        measurement_unit: '', 
        quantity: '', 
        image: null 
    });
};

// Remove a specific row
const removeItemRow = (index: number) => {
    // Prevent removing the last row (optional, keeps UI clean)
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    }
};

// Handle File Selection (Tricky part!)
const handleFileChange = (event: Event, index: number) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.items[index].image = target.files[0];
    }
};

// Submit ALL items at once
const submit = () => {
    console.log('Submitting form data:', form.data());
    form.post(route('add_item_db'), {
        onSuccess: () => {
            form.reset();
            form.items = [{ name: '', measurement_unit: '', quantity: '', image: null }];
        },
        onError: (errors) => {
            console.error('Form errors:', errors);
        },
    });
};
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Add New Items',
        href: 'add_item',
    },
];
</script>
@route('add_item_db')
<template>
    <Head title="Add New Inventory" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="py-12 bg-gray-50/50">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 leading-tight">Add New Items</h2>
                        <p class="text-sm text-gray-900 mt-1">Register new stock into your inventory system.</p>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 md:p-8">
                        
                        <form @submit.prevent="submit">
                            <div class="space-y-6">
                                <div 
                                    v-for="(item, index) in form.items" 
                                    :key="index" 
                                    class="relative p-4 bg-gray-50 border border-gray-200 rounded-lg group transition-all duration-200 hover:border-gray-300"
                                >
                                    <div class="absolute -top-3 -left-2 bg-white border border-gray-200 text-xs font-bold text-gray-500 px-2 py-0.5 rounded shadow-sm">
                                        #{{ index + 1 }}
                                    </div>

                                    <div class="flex flex-col md:flex-row gap-4 items-start md:items-end">
                                        
                                        <div class="flex-1 w-full space-y-1.5">
                                            <Label class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Item Name</Label>
                                            <Input 
                                                v-model="item.name" 
                                                placeholder="e.g. Portland Cement" 
                                                required 
                                                class="bg-white"
                                            />
                                        </div>

                                        <div class="w-full md:w-40 space-y-1.5">
                                            <Label class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Unit</Label>
                                            <div class="relative">
                                                <select 
                                                    v-model="item.measurement_unit"
                                                    class="flex text-gray-900 h-10 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow disabled:opacity-50 disabled:cursor-not-allowed"
                                                    required
                                                >
                                                    <option value="" disabled selected>Select Type</option>
                                                    <option value="kg">Kilograms (kg)</option>
                                                    <option value="g">Grams (g)</option>
                                                    <option value="l">Liters (l)</option>
                                                    <option value="pcs">Pieces</option>
                                                    <option value="box">Box</option>
                                                    <option value="m">Meters (m)</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="w-full md:w-32 space-y-1.5">
                                            <Label class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Quantity</Label>
                                            <Input 
                                                v-model="item.quantity" 
                                                type="number" 
                                                step="0.01" 
                                                placeholder="0.00" 
                                                required 
                                                class="bg-white text-right font-mono"
                                            />
                                        </div>

                                        <div class="w-full md:w-auto flex justify-end">
                                            <Button 
                                                type="button" 
                                                variant="ghost" 
                                                size="icon" 
                                                class="text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors h-10 w-10"
                                                @click="removeItemRow(index)"
                                                :disabled="form.items.length === 1"
                                                title="Remove this row"
                                            >
                                                <Trash2 class="w-5 h-5" />
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 pt-6 border-t border-gray-100 flex flex-col sm:flex-row justify-between gap-4">
                                <Button 
                                    type="button" 
                                    variant="outline" 
                                    class="border-dashed border-2 border-gray-300 text-gray-600 hover:border-indigo-500 hover:text-indigo-600 hover:bg-indigo-50 w-full sm:w-auto justify-center"
                                    @click="addItemRow"
                                >
                                    <Plus class="w-4 h-4 mr-2" />
                                    Add Another Item
                                </Button>

                                <Button 
                                    type="submit" 
                                    :disabled="form.processing" 
                                    class="w-full sm:w-auto bg-gray-900 text-white hover:bg-gray-800 shadow-md justify-center"
                                >
                                    <Save v-if="!form.processing" class="w-4 h-4 mr-2" />
                                    <svg v-else class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Save Inventory
                                </Button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout> 
</template>
